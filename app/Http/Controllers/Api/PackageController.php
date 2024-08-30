<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
     use JsonResponseTrait;

     public function index()
     {
          try {
               $packages = Package::all();

               if ($packages->isEmpty()) {
                    return $this->successResponse([], 'No packages available', 200);
               }

               return $this->successResponse($packages, 'Packages fetched successfully', 200);
          } catch (\Throwable $th) {
               return $this->errorResponse($th->getMessage(), [], 500);
          }
     }

     public function show($id)
     {
          try {
               $package = Package::find($id);

               return $this->successResponse([
                    'package' => $package,
               ], 'Get Package successfully', 200);
          } catch (\Throwable $th) {
               return $this->errorResponse($th->getMessage(), [], 500);
          }
     }

     public function store(Request $request)
     {
          try {
               $request['mua_id'] = Auth::id();

               $data = $request->validate([
                    'package_name' => 'required|string',
                    'visit_type' => 'required|string',
                    'price' => 'required|numeric',
                    'mua_id' => 'required|exists:users,id',
               ]);

               $package = new Package;
               $package->package_name = $data['package_name'];
               $package->visit_type = $data['visit_type'];
               $package->price = $data['price'];
               $package->mua_id = $data['mua_id'];
               $package->save();

               return $this->successResponse($package, 'Package created successfully', 201);
          } catch (\Throwable $th) {
               return $this->errorResponse($th->getMessage(), [], 500);
          }
     }
     public function update(Request $request, $id)
     {
          try {
               $request['mua_id'] = Auth::id();

               $data = $request->validate([
                    'package_name' => 'required|string',
                    'visit_type' => 'required|string',
                    'price' => 'required|numeric',
                    'mua_id' => 'required|exists:users,id',
               ]);

               $package = Package::find($id);
               $package->package_name = $data['package_name'];
               $package->visit_type = $data['visit_type'];
               $package->price = $data['price'];
               $package->mua_id = $data['mua_id'];
               $package->save();

               return $this->successResponse($package, 'Package updated successfully', 200);
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
