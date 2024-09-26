<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Models\User;
use App\Models\DayOff;
use App\Http\Resources\RequestResource;
use App\Models\Package;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RequestController extends Controller
{
    public function create(HttpRequest $request) //before do req add information packet MUA
    {
        $validatedData = $request->validate([
            // 'id_mua' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'package_id' => 'required|exists:packages,id',
        ]);

        $date = Carbon::parse($validatedData['date']);
        $startTime = Carbon::parse($validatedData['start_time']);
        $endTime = Carbon::parse($validatedData['end_time']);

        $approvedRequests = Request::where('package_id', $validatedData['package_id'])->get()->filter(function ($approvedRequest) use ($date) {
            return Carbon::parse($approvedRequest->date)->isSameDay($date) &&
                $approvedRequest->status === 'approved';
        });

        $conflictingRequests = $approvedRequests->filter(function ($approvedRequest) use ($startTime, $endTime) {
            $approvedStartTime = Carbon::parse($approvedRequest->start_time);
            $approvedEndTime = Carbon::parse($approvedRequest->end_time);

            return ($approvedStartTime->lt($endTime) && $approvedEndTime->gt($startTime)) ||
                $approvedEndTime->eq($startTime);
        });

        if ($conflictingRequests->isNotEmpty()) {
            return response()->json([
                'message' => 'The selected time slot conflicts with an existing approved request.'
            ], 400);
        }

        if (is_null(Auth::user()->userProfile)) {
            return response()->json([
                'message' => 'Please complete your profile.'
            ], 400);
        }

        $id_mua = Package::find($validatedData['package_id'])->mua_id;

        $dayOff = DayOff::where('id_mua', $id_mua)
        ->whereDate('date', $date)
        ->exists();

        if ($dayOff) {
            return response()->json([
                'message' => 'The selected date is a day off for this MUA. Please choose another date.'
            ], 400);
        }

        $newRequest = Request::create([
            'id_user' => Auth::id(),
            'id_mua' => $id_mua,
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime->format('H:i'),
            'package_id' => $validatedData['package_id'],
            'status' => 'pending',
        ]);

        return new RequestResource($newRequest);
    }

    public function approve($id) //add information
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
                $approvedEndTime->eq($startTime);
        });

        if ($conflictingRequests->isNotEmpty()) {
            return response()->json([
                'message' => 'The MUA is already booked for the selected time slot.'
            ], 400);
        }

        $requestToApprove->update(['status' => 'approved']);

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

        if ($requests->isEmpty()) {
            return response()->json([
                'message' => 'No requests available',
            ], 200);
        }

        return RequestResource::collection($requests);
    }
}
