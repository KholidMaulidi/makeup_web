<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gallery;
use Illuminate\Support\Facades\Validator;
use App\Traits\JsonResponseTrait;

class GalleryController extends Controller
{
    use JsonResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $galleries = Auth::user()->galleries;

            if ($galleries->isEmpty()) {
                return $this->successResponse([], 'No galleries available', 200);
            }

            return $this->successResponse($galleries, 'Galleries fetched successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }


        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'image' => 'required|string',
                'description' => 'required|string|max:255',
            ]);

            $data['user_id'] = Auth::id();
            $gallery = Gallery::create($data);

            return $this->successResponse($gallery, 'Gallery created successfully', 201);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $gallery = Auth::user()->galleries()->findOrFail($id);
            return $this->successResponse($gallery, 'Gallery fetched successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validateGallery = Validator::make($request->all(), [
                'description' => ['required', 'string'],
            ]);

            if ($validateGallery->fails()) {
                return $this->errorResponse('Validation Error', $validateGallery->errors(), 401);
            }

            $gallery = Gallery::where('user_id', Auth::id())->findOrFail($id);
            $gallery->update(['description' => $request->description]);

            return $this->successResponse($gallery, 'Gallery successfully updated', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $gallery = Auth::user()->galleries()->findOrFail($id);
            $gallery->delete();

            return $this->successResponse([], 'Gallery deleted successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }
}
