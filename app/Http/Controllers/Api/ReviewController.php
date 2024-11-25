<?php

namespace App\Http\Controllers\Api;

use App\Models\Review;
use Illuminate\Http\Request as HttpRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\HistoryRequest;
use Illuminate\Support\Facades\Auth;
use App\Traits\JsonResponseTrait;
use App\Models\Request;

class ReviewController extends Controller
{
    use JsonResponseTrait;

    public function showByMua($id)
    {
        try {
            $reviews = Review::where('mua_id', $id)->with('user')->get();

            if ($reviews->isEmpty()) {
                return $this->successResponse([], 'No Reviews available', 200);
            }

            return $this->successResponse(
                ReviewResource::collection($reviews),
                'Get Reviews successfully',
                200
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }


    public function store(HttpRequest $request, $mua_id)
    {
        try {
            $user_id = Auth::id();
            $status = Request::where('id_user', $user_id)
                ->where('id_mua', $mua_id)
                ->where('status', 'approved')
                ->first();

            if (!$status) {
                return response()->json([
                    'message' => 'You can only review MUA that you have completed the request',
                ], 400);
            }

            $data = $request->validate([
                'review' => 'required|string',
                'rating' => 'required|integer|between:1,5',
            ]);


            $review = new Review;
            $review->user_id = $user_id;
            $review->mua_id = $mua_id;
            $review->review = $data['review'];
            $review->rating = $data['rating'];
            $review->save();

            return $this->successResponse(
                $review,
                'Create Reviews successfully',
                202
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    public function update(HttpRequest $request, $id)
    {
        try {
            $user_id = Auth::id();

            $data = $request->validate([
                'review' => 'required|string',
                'rating' => 'required|integer|between:1,5',
            ]);

            $review = Review::where('id', $id)->where('user_id', $user_id)->first();

            if (!$review) {
                return response()->json([
                    'message' => 'Review not found',
                ], 404);
            }

            $review->review = $data['review'];
            $review->rating = $data['rating'];
            $review->save();

            return $this->successResponse(
                $review,
                'Review has been updated',
                200
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user_id = Auth::id();

            $review = Review::where('id', $id)->where('user_id', $user_id)->first();

            if (!$review) {
                return response()->json([
                    'message' => 'Review not found',
                ], 404);
            }

            $review->delete();

            return $this->successResponse([], 'Review has been deleted',200 );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }


}
