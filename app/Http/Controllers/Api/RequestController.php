<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\DayOff;
use App\Http\Resources\RequestResource;
use App\Models\Package;
use App\Models\Request;
use App\Models\Transaction;
use App\Models\RequestPackage;
use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request as HttpRequest;

class RequestController extends Controller
{
    use JsonResponseTrait;

    public function show(HttpRequest $request)
    {
        try {
            $rules = [
                'packages' => 'required|array',
                'packages.*.id' => 'required|exists:packages,id',
                'packages.*.quantity' => 'required|integer|min:1',
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'visit_type' => 'required|string|in:offsite,onsite',
            ];

            if ($request->input('visit_type') === 'offsite') {
                $rules['latitude'] = 'required|numeric';
                $rules['longitude'] = 'required|numeric';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();

            $totalQuantity = 0;
            $totalPrice = 0;
            $packageDetails = [];

            if ($validatedData['visit_type'] === 'offsite') {
                $distance = $this->calculate_distance($validatedData['latitude'], $validatedData['longitude'], 'K', $validatedData['packages'][0]['id']);
                $postage = $this->calculate_postage($distance);
            } else {
                $distance = 0;
                $postage = 0;
            }

            foreach ($validatedData['packages'] as $package) {
                
                $packageData = Package::find($package['id']);
                $initialPrice = $packageData->price;

                $totalPerPackage = $initialPrice * $package['quantity'];

                $packageDetails[] = [
                    'package_id' => $package['id'],
                    'package_name' => $packageData->package_name,
                    'initial_price' => $initialPrice,
                    'quantity' => $package['quantity'],
                    'total_per_package' => $totalPerPackage
                ];

                $totalQuantity += $package['quantity'];
                $totalPrice += $totalPerPackage;
            }

            return response()->json([
                'status' => true,
                'message' => 'Get request details success',
                'data' =>[
                    'packages' => $packageDetails,
                    'total_quantity' => $totalQuantity,
                    'total_price' => $totalPrice,
                    'postage' => $postage,
                    'distance' => $distance,
                    'visit_type' => $validatedData['visit_type'],
                ]
            ],200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }





    public function create(HttpRequest $request)
    {
        try {
            $rules = [
                'packages' => 'required|array',
                'packages.*.id' => 'required|exists:packages,id',
                'packages.*.quantity' => 'required|integer|min:1',
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'visit_type' => 'required|string|in:offsite,onsite',
            ];

            if ($request->input('visit_type') === 'offsite') {
                $rules['latitude'] = 'required|numeric';
                $rules['longitude'] = 'required|numeric';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();

            $date = Carbon::parse($validatedData['date']);
            $startTime = Carbon::parse($validatedData['start_time']);
            $endTime = Carbon::parse($validatedData['end_time']);

            $id_mua = Package::find($validatedData['packages'][0]['id'])->mua_id;

            $existingApprovedRequest = Request::where('id_mua', $id_mua)
            ->whereDate('date', $date->format('Y-m-d'))
            ->whereTime('date', '=', $date->format('H:i:s'))  // Menambahkan seleksi berdasarkan jam dan menit
            ->where('status', 'approved')
            ->exists();
            $dayOff = DayOff::where('id_mua', $id_mua)
                ->whereDate('date', $date)
                ->exists();

            if ($existingApprovedRequest) {
                return response()->json([
                    'message' => 'The MUA is already booked for the selected date.'
                ], 400);
            }

            if ($dayOff) {
                return response()->json([
                    'message' => 'The selected date is a day off for this MUA. Please choose another date.'
                ], 400);
            }

            // Calculate total price and distance
            $totalQuantity = 0;
            $totalPrice = 0;

            if ($validatedData['visit_type'] === 'offsite') {
                $distance = $this->calculate_distance($validatedData['latitude'], $validatedData['longitude'], 'K', $validatedData['packages'][0]['id']);
                $postage = $this->calculate_postage($distance);
            } else {
                $distance = 0;
                $postage = 0;
            }

            foreach ($validatedData['packages'] as $package) {
                $totalQuantity += $package['quantity'];
                $totalPrice += $this->calculate_total_price($package['id'], $package['quantity']);
            }

            // Create the request
            $newRequest = Request::create([
                'id_user' => Auth::id(),
                'id_mua' => $id_mua,
                'date' => $date->format('Y-m-d'),
                'start_time' => $startTime->format('H:i'),
                'end_time' => $endTime->format('H:i'),
                'postage' => $postage,
                'total_price' => $totalPrice,
                'visit_type' => $validatedData['visit_type'],
                'status' => 'pending',
                'distance' => $distance,
            ]);

            foreach ($validatedData['packages'] as $package) {
                $packageData = Package::find($package['id']);
                $totalPerPackage = $packageData->price * $package['quantity'];

                $packageDetails = [
                    'id' => $packageData->id,
                    'package_name' => $packageData->package_name,
                    'price' => $packageData->price,
                    'image' => $packageData->image,
                    'total_per_package' => $totalPerPackage,
                ];

                RequestPackage::create([
                    'request_id' => $newRequest->id,
                    'package_id' => $package['id'],
                    'quantity' => $package['quantity'],
                    'package_details' => json_encode($packageDetails),
                ]);
            }

            $requestResource = new RequestResource($newRequest);

            return response()->json($requestResource, 201);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }



    public function approve($id)
    {
        $requestToApprove = Request::findOrFail($id);

        if (Auth::id() !== $requestToApprove->id_mua) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $date = Carbon::parse($requestToApprove->date);
        $startTime = Carbon::parse($requestToApprove->start_time);
        $endTime = Carbon::parse($requestToApprove->end_time);

        $approvedRequests = Request::where('id_mua', $requestToApprove->id_mua)
            ->whereDate('date', $date->format('Y-m-d'))
            ->where('status', 'approved')
            ->get();

        $conflictingRequests = $approvedRequests->filter(function ($approvedRequest) use ($startTime, $endTime) {
            $approvedStartTime = Carbon::parse($approvedRequest->start_time);
            $approvedEndTime = Carbon::parse($approvedRequest->end_time);

            return ($approvedStartTime->lt($endTime) && $approvedEndTime->gt($startTime)) ||
                ($approvedEndTime->eq($startTime) || $approvedStartTime->eq($endTime));
        });

        if ($conflictingRequests->isNotEmpty()) {
            return response()->json([
                'message' => 'The MUA is already booked for the selected time slot.'
            ], 400);
        }

        $requestToApprove->update(['status' => 'approved']);

        Transaction::create([
            'request_id' => $requestToApprove->id,
            'payment_status' => 'unpaid'
        ]);

        return new RequestResource($requestToApprove);
    }


    public function reject($id)
    {
        $request = Request::findOrFail($id);

        if (Auth::id() !== $request->id_mua) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->update(['status' => 'rejected']);

        return new RequestResource($request);
    }

    public function viewAllRequests() //add filter dates & paginate 
    {
        $muaId = Auth::id();
        $requests = Request::where('id_mua', $muaId)->paginate(10);

        if ($requests->has('date')) {
            $requests = Request::where('id_mua', $muaId)->where('date', $requests->date)->paginate(10);
        }

        if ($requests->isEmpty()) {
            return response()->json([
                'message' => 'No requests available',
            ], 200);
        }

        return RequestResource::collection($requests);
    }

    public function viewRequest($id)
    {
        $request = Request::findOrFail($id);

        if (Auth::id() !== $request->id_mua) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new RequestResource($request);
    }



    public function calculate_distance($lat2, $lon2, $unit, $package_id)
    {
        $package = Package::find($package_id);
        $mua = User::find($package->mua_id);
        $lat1 = $mua->makeupArtistProfile->latitude;
        $lon1 = $mua->makeupArtistProfile->longitude;

        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                $distance = ($miles * 1.609344);
            } else if ($unit == "N") {
                $distance = ($miles * 0.8684);
            } else {
                $distance = $miles;
            }

            return round($distance, 2);
        }
    }

    public function calculate_postage($distance)
    {
        if ($distance <= 5) {
            return $distance * 5000;
        } else if ($distance > 5 && $distance <= 10) {
            return $distance * 7000;
        } else if ($distance > 10) {
            return $distance * 10000;
        } else {
            return 0;
        }
    }

    public function calculate_total_price($package_id, $quantity)
    {
        $package = Package::find($package_id);
        return $package->price * $quantity;
    }

    public function requestCancel($id)
    {
        try {
            $request = Request::findOrFail($id);

            if (Auth::id() !== $request->id_user) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            if ($request->status === 'approved') {
                return response()->json(['message' => 'Cannot cancel, the request has already been approved.'], 403);
            }

            $request->update(['status' => 'request cancel']);

            return response()->json(['message' => 'Request to cancel the request has been sent.'], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function showCancelRequest()
    {
        try {
            $requests = Request::where('status', 'request cancel')
            ->get();

            return response()->json([
                'status' => 'success',
                'data' => $requests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approveCancel($id)
    {
        try {
            $request = Request::findOrFail($id);

            if (Auth::id() !== $request->id_mua) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $request->update(['status' => 'canceled']);
            

            return response()->json(['message' => 'Request has been canceled.'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to cancel the request.'], 400);
        }
    }

    public function rejectCancel($id)
    {
        try {
            $request = Request::indOrFail($id);

            if (Auth::id() !== $request->id_mua) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $request->update(['status' => 'pending']);

            return response()->json(['message' => 'Request has been rejected.'], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reject the request.'
            ], 400);
        }
    }

}
