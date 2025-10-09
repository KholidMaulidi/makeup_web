<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\DayOff;
use App\Models\Review;
use App\Models\Gallery;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\MuaResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReviewResource;
use App\Models\MakeupArtistProfile;

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

        return $this->successResponse(MuaResource::collection($data), 'Get MUA successfully', 200);
    }

    public function showMoreMua(Request $request)
    {
        $name = $request->input('name');
        $service = $request->input('service_id');
        $province = $request->input('province');
        $city = $request->input('regency');
        $district = $request->input('district');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');


        $query = User::where('role_id', 2)->with(['makeupArtistProfile', 'packages', 'reviews']);
        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        if ($service) {
            $query->whereHas('packages', function ($q) use ($service) {
                $q->where('service_id', $service);
            });
        }
        if ($province) {
            $query->whereHas('makeupArtistProfile', function ($q) use ($province) {
                $q->where('province', 'like', '%' . $province . '%');
            });
        }
        if ($city) {
            $query->whereHas('makeupArtistProfile', function ($q) use ($city) {
                $q->where('city', 'like', '%' . $city . '%');
            });
        }
        if ($district) {
            $query->whereHas('makeupArtistProfile', function ($q) use ($district) {
                $q->where('subdistrict', 'like', '%' . $district . '%');
            });
        }
        if ($minPrice) {
            $query->whereHas('packages', function ($q) use ($minPrice) {
                $q->where('price', '>=', $minPrice);
            });
        }

        if ($maxPrice) {
            $query->whereHas('packages', function ($q) use ($maxPrice) {
                $q->where('price', '<=', $maxPrice);
            });
        }
        $data = $query
            ->withCount('requests')
            ->orderBy('requests_count', 'desc')
            ->get();

        return $this->successResponse(MuaResource::collection($data), 'Get MUA successfully', 200);
    }


    public function showMuaProfile($id)
    {
        try {
            $mua = User::where('role_id', 2)->where('id', $id)->with('makeupArtistProfile')->first();

            if (!$mua) {
                return $this->errorResponse('Mua not found', [], 404);
            }

            $countGallery = Gallery::where('user_id', $mua->id)->count();

            $jobDone = Transaction::whereHas('request', function ($query) use ($id) {
                $query->where('id_user', $id);
            })->where('payment_status', 'paid off')->count();

            $rate = Review::where('mua_id', $id)->avg('rating');

            return $this->successResponse([
                'mua' => [
                    'id' => $mua->id,
                    'name' => $mua->name,
                    'email' => $mua->email,
                    'avatar' => $mua->avatar,
                    'makeup_artist_profile' => [
                        'address' => $mua->makeupArtistProfile->address,
                        'city' => $mua->makeupArtistProfile->city,
                        'latitude' => $mua->makeupArtistProfile->latitude,
                        'longitude' => $mua->makeupArtistProfile->longitude,
                    ]
                ],
                'countGallery' => $countGallery,
                'jobDone' => $jobDone,
                'rate' => $rate,
            ], 'Mua Profile Information', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }
}
