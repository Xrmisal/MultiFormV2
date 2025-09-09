<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    public function register(AuthRequest $request) {
        $data = $request->validated();
        $user = new User($data);
        $user->id = Str::uuid();
        $user->save();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
            'remember' => 'boolean'
        ]);
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);
        if(!Auth::attempt($credentials, $remember)) {
            return response([

            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }
    public function logout(Request $request) { 
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response()->noContent();
    }
    public function getUser(Request $request) {
        return new UserResource($request->user());
    }
}