<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\JsonResponseTrait;

class LogoutController extends Controller
{
    use JsonResponseTrait;

    public function logout(Request $request)
    {
        try {
            // Ensure user is authenticated
            if (!Auth::check()) {
                return $this->errorResponse('User not authenticated', [], 401);
            }

            // Delete the user's current access token
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse([], 'User logged out successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }
}
