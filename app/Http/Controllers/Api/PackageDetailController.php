<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;

use Illuminate\Http\Request;
use App\Models\PackageDetail;
use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PackageResource;

class PackageDetailController extends Controller
{
    use JsonResponseTrait;

    public function index()
    {
        try {
            $id = Auth::id();
            $packages = Package::where('mua_id', $id)->get();

            if ($packages->isEmpty()) {
                return $this->successResponse([], 'No packages available', 200);
            }

            return $this->successResponse(PackageResource::collection($packages), 'Get Packages successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    public function store(Request $request, $package_id)
    {
        try {
            $data = $request->validate([
                'details' => 'required|array',
                'details.*.item_name' => 'required|string',
                'details.*.description' => 'required|string',
            ]);

            $package = Package::find($package_id);

            if (!$package) {
                return $this->errorResponse('Package not found', [], 404);
            }

            $details = $data['details'];

            foreach ($details as $detail) {
                $package->details()->create($detail);
            }

            return $this->successResponse(
                new PackageResource($package->load('details')),
                'Package details created successfully',
                201
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    public function showByPackage($package_id)
    {
        try {
            $package = Package::find($package_id);

            if (!$package) {
                return $this->errorResponse('Package not found', [], 404);
            }

            return $this->successResponse(
                new PackageResource($package->load('details')),
                'Package details retrieved successfully',
                200
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    public function update(Request $request, $package_id)
    {
        try {
            $data = $request->validate([
                'details' => 'required|array',
                'details.*.id' => 'required|exists:package_details,id',
                'details.*.item_name' => 'required|string',
                'details.*.description' => 'required|string',
            ]);

            $package = Package::find($package_id);

            if (!$package) {
                return $this->errorResponse('Package not found', [], 404);
            }

            $details = $data['details'];

            foreach ($details as $detail) {
                $packageDetail = $package->details()->find($detail['id']);

                if ($packageDetail) {
                    $packageDetail->update([
                        'item_name' => $detail['item_name'],
                        'description' => $detail['description'],
                    ]);
                } else {
                    return $this->errorResponse('Detail not found', [], 404);
                }
            }

            return $this->successResponse(
                new PackageResource($package->load('details')),
                'Package Details Updated successfully',
                201
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }


   public function destroy($id)
    {
        try {
            $packageDetail = PackageDetail::find($id);

            if (!$packageDetail) {
                return $this->errorResponse('Detail not found', [], 404);
            }

            $packageDetail->delete();

            return $this->successResponse(
                [],
                'Package Detail deleted successfully',
                200
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }
}
