<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
            'name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'no_hp' => 'nullable|string|max:15',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if (isset($data['name'])) {
            $makeupArtist->update(['name' => $data['name']]);
            unset($data['name']); // Hapus nama dari data profil agar tidak ikut tersimpan di tabel profileuser
        }

        $profile = $makeupArtist->makeupArtistProfile()->updateOrCreate(
            ['user_id' => $makeupArtist->id],
            $data
        );

        return $this->successResponse([
            'makeupArtist' => $makeupArtist,
            'profile' => $profile,
        ], 'Profile updated successfully', 200);
    }

    public function showTopMua()
    {
        $data = User::where('role_id', 2)
        ->withCount('requests')
        ->orderBy('requests_count', 'desc')
        ->simplePaginate(4);

        return $this->successResponse($data, 'Data Top MUA', 200);
    }

    public function showMoreMua(Request $request)
    {
        if($request->has('name')) {
            $data = User::where('role_id', 2)
            ->where('name', 'like', '%'.$request->search.'%')
            ->withCount('requests')
            ->orderBy('requests_count', 'desc')
            ->simplePaginate(6);
        } else {
            $data = User::where('role_id', 2)
            ->withCount('requests')
            ->orderBy('requests_count', 'desc')
            ->simplePaginate(6);
        }

        return $this->successResponse($data, 'Data MUA', 200);
    }
}
