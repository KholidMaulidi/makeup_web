<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use JsonResponseTrait;

    public function updateAvatar(Request $request){
        try {

            $user_id = Auth::id();
            $user = User::find($user_id);
            
            if (!$user) {
                return $this->errorResponse('User not found', [], 404);
            }

            $request->validate([
                'avatar' => 'required|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('avatar')) {
                if (!empty($user->avatar)) {
                    Storage::delete('public/images/avatars/' . $user->avatar);
                }
                $avatar = $request->file('avatar');
                $extension = $avatar->getClientOriginalExtension();
                $avatarName = time() . '.' . $extension;
                $avatar->storeAs('public/images/avatars', $avatarName);



                $user->avatar = $avatarName;
                $user->save();

                return $this->successResponse(
                    ['user'=> $user],
                    'Avatar Upload successfully',
                    200
                );
            }

            return $this->errorResponse('Failed to upload image', [], 400);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }
}
