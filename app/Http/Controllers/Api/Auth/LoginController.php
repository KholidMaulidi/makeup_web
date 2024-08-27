<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\JsonResponseTrait;

class LoginController extends Controller
{
    use JsonResponseTrait;

    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if ($validateUser->fails()) {
                return $this->errorResponse('Validation Error', $validateUser->errors(), 401);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->errorResponse('Email & Password do not match our records', [], 422);
            }

            $role = '';
            switch ($user->role_id) {
                case 1:
                    $role = 'Customer';
                    break;
                case 2:
                    $role = 'MUA';
                    break;
                case 3:
                    $role = 'Admin';
                    break;
            }

            return $this->successResponse([
                'token' => $user->createToken("API TOKEN")->plainTextToken,
            ], 'User logged in successfully as ' . $role, 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }
}

    