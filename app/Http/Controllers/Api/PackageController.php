<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;

use Illuminate\Http\Request;
use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PackageResource;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
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

     public function show($id)
     {
          try {
               $package = Package::with('details')->find($id);

               return $this->successResponse([
                    'package' => new PackageResource($package),
               ], 'Get Package successfully', 200);
          } catch (\Throwable $th) {
               return $this->errorResponse($th->getMessage(), [], 500);
          }
     }

     public function store(Request $request)
     {
          try {
               $request['mua_id'] = Auth::id();

               if (is_null(Auth::user()->makeupArtistProfile)) {
                    return response()->json([
                         'message' => 'Please complete your profile.'
                    ], 400);
               }

               $data = $request->validate([
                    'package_name' => 'required|string',
                    'description' => 'required|string',
                    'price' => 'required|numeric',
                    'mua_id' => 'required|exists:users,id',
                    'details' => 'required|array',
                    'details.*' => 'exists:package_details,id',
               ]);

               $package = new Package;
               $package->package_name = $data['package_name'];
               $package->description = $data['description'];
               $package->price = $data['price'];
               $package->mua_id = $data['mua_id'];
               $package->save();

               $package->details()->sync($data['details']);

               return $this->successResponse(
                    new PackageResource($package->load('details')),
                    'Package created successfully',
                    201
               );
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
                    'description' => 'required|string',
                    'price' => 'required|numeric',
                    'mua_id' => 'required|exists:users,id',
                    'details' => 'required|array',
                    'details.*' => 'exists:package_details,id',
               ]);

               $package = Package::findOrFail($id);

               // Update data package
               $package->package_name = $data['package_name'];
               $package->description = $data['description'];
               $package->price = $data['price'];
               $package->mua_id = $data['mua_id'];
               $package->save();

               // Sinkronkan relasi details (many-to-many)
               $package->details()->sync($data['details']); // Sinkronisasi detail package

               // Return success response dengan package yang diperbarui
               return $this->successResponse(
                    new PackageResource($package->load('details')),
                    'Package updated successfully',
                    200
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

     public function show_mua_packages($id_mua)
     {
          try {

               $packages = Package::with('details')->where('mua_id', $id_mua)->get();

               if ($packages->isEmpty()) {
                    return $this->successResponse([], 'No packages available', 200);
               }

               return $this->successResponse(PackageResource::collection($packages), 'Get Packages successfully', 200);
          } catch (\Throwable $th) {
               return $this->errorResponse($th->getMessage(), [], 500);
          }
     }

     public function uploadImage(Request $request, $id)
     {
          try {
               $package = Package::findOrFail($id);

               if (Auth::id() !== $package->mua_id) {
                    return $this->errorResponse('Unauthorized', [], 403);
               }

               $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
               ]);

               if ($request->hasFile('image')) {
                    if (!empty($package->image)) {
                         Storage::delete('public/images/packages/' . $package->image);
                    }
                    $image = $request->file('image');
                    $extension = $image->getClientOriginalExtension();
                    $imageName = time() . '.' . $extension;
                    $image->storeAs('public/images/packages', $imageName);



                    $package->image = $imageName;
                    $package->save();

                    return $this->successResponse(
                         new PackageResource($package->load('details')),
                         'Image Upload successfully',
                         200
                    );
               }

               return $this->errorResponse('Failed to upload image', [], 400);
          } catch (\Throwable $th) {
               return $this->errorResponse($th->getMessage(), [], 500);
          }
     }
}
