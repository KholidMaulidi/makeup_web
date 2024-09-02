<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PackageResource;

class PackageDetailController extends Controller
{
    use JsonResponseTrait;

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
            $package = Package::find($id);
            $package->delete();

            return $this->successResponse([], 'Package deleted successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }
}
