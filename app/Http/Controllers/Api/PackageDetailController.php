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
            $packages = PackageDetail::where('mua_id', $id)->get();

            if ($packages->isEmpty()) {
                return $this->successResponse([], 'No package details available', 200);
            }

            return $this->successResponse(
                $packages, 'Get Package details successfully', 200);
            
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $mua_id = Auth::id();

            // Validasi input sebagai array
            $data = $request->validate([
                '*.item_name' => 'required|string',
                '*.description' => 'required|string',
            ]);

            // Menyimpan banyak data
            $packageDetails = [];
            foreach ($data as $detail) {
                $packageDetail = new PackageDetail;
                $packageDetail->mua_id = $mua_id;
                $packageDetail->item_name = $detail['item_name'];
                $packageDetail->description = $detail['description'];
                $packageDetail->save();

                $packageDetails[] = $packageDetail;
            }

            return $this->successResponse(
                $packageDetails,
                'Package details created successfully',
                201
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }


    public function show($id)
    {
        try {
            $packageDetail = PackageDetail::find($id);

            if (!$packageDetail) {
                return $this->errorResponse('Detail not found', [], 404);
            }

            return $this->successResponse(
                $packageDetail,
                'Get Package detail successfully',
                200
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {

            $mua_id = Auth::id();
            $data = $request->validate([
                'item_name' => 'required|string',
                'description' => 'required|string',
            ]);

            $detail = PackageDetail::findOrFail($id);

            if ($detail->mua_id !== $mua_id) {
                return $this->errorResponse('Unauthorized', [], 401);
            }

            $detail->item_name = $data['item_name'];
            $detail->description = $data['description'];
            $detail->save();

            return response()->json([
                'success' => true,
                'message' => 'Package detail updated successfully',
                'data' => $detail
            ], 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }



    public function destroy($id)
    {
        try {
            $mua_id = Auth::id();
            $packageDetail = PackageDetail::find($id);

            if (!$packageDetail) {
                return $this->errorResponse('Detail not found', [], 404);
            }
            if ($packageDetail->mua_id !== $mua_id) {
                return $this->errorResponse('Unauthorized', [], 401);
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
