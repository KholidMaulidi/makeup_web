<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\JsonResponseTrait;

class MakeupArtistProfileController extends Controller
{
    use JsonResponseTrait;

    public function showProfile(Request $request)
    {
        $makeupArtist = Auth::user();
        $profile = $makeupArtist->makeupArtistProfile;

        return $this->successResponse([
            'makeupArtist' => $makeupArtist,
            'profile' => $profile,
        ], 'Profile Information', 200);
    }

    public function updateProfile(Request $request)
    {
        $makeupArtist = Auth::user();

        $data = $request->validate([
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'no_hp' => 'nullable|string|max:15',
            'description' => 'nullable|string',
        ]);

        $profile = $makeupArtist->makeupArtistProfile()->updateOrCreate(
            ['user_id' => $makeupArtist->id],
            $data
        );

        return $this->successResponse([
            'makeupArtist' => $makeupArtist,
            'profile' => $profile,
        ], 'Profile updated successfully', 200);
    }
}
