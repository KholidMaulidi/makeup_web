<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserProfile;
use App\Models\MakeupArtistProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\JsonResponseTrait;

class RegisterController extends Controller
{
    use JsonResponseTrait;

    public function register(Request $request)
    {
        try {
            // Validasi input dengan ID numerik untuk role
            $validateUser = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
                'role_id' => ['required', 'numeric', 'in:1,2,3'], // 1=user, 2=mua, 3=admin
            ]);

            if ($validateUser->fails()) {
                return $this->errorResponse('Validation Error', $validateUser->errors(), 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            // Buat profil berdasarkan role_id
            if ($request->role_id == 1) {
                UserProfile::create(['user_id' => $user->id]);
            } elseif ($request->role_id == 2) {
                MakeupArtistProfile::create(['user_id' => $user->id]);
            }

            return $this->successResponse([
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 'User successfully registered', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }
}
