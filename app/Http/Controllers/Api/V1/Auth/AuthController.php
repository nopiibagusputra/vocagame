<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Resources\AuthLoginResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Auth;
use Laravel\Passport\Token;

class AuthController extends Controller
{
    /**
     * Login user and create a new token
     *
     * @param AuthLoginRequest $request
     * @return AuthLoginResource
     */
    public function login(AuthLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();

        if (!Auth::attempt($credentials) OR $user->active != 1) {
            return response()->json(['message' => 'Authentication failed or Account is not active'], 403);
        }

        $existingToken = $user->tokens()->where('revoked', false)->first();

        if ($existingToken) {
            $existingToken->delete();
        }

        return new AuthLoginResource($request->user());
    }

    /**
     * Logout the authenticated user and revoke their tokens.
     *
     * @param  \App\Http\Requests\LogoutRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {   
        $user = Auth::user();
        $tokens = Token::where('user_id', $user->id)->get();

        foreach ($tokens as $token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Logout successful'
        ],200);
    }
}
