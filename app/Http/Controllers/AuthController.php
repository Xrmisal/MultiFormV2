<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        $data = $request->validated();
        $user = new User($data);
        $user->id = Str::uuid();
        $user->save();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
            'leadStatus' => $user->Lead()->status ?? 'empty'
        ]);
    }
    public function login(AuthRequest $request) {
        $credentials = $request->validated();
        if(!Auth::attempt($credentials)) {
            return response([
                'message' => 'Email or password is incorrect'
            ], 422);
        }
        $user = Auth::user();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
            'leadStatus' => $user->Lead()->status ?? 'empty'
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