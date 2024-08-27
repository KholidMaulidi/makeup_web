<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\JsonResponseTrait;

class UserProfileController extends Controller
{
    use JsonResponseTrait;

    public function showProfile(Request $request)
    {
        $user = Auth::user();
        $profile = $user->userProfile;

        return $this->successResponse([
            'user' => $user,
            'profile' => $profile,
        ], 'Profile Information', 200);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'no_hp' => 'nullable|string|max:15',
        ]);

        $profile = $user->userProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return $this->successResponse([
            'user' => $user,
            'profile' => $profile,
        ], 'Profile updated successfully', 200);
    }
}
