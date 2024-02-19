<?php

namespace App\Http\Controllers\Api\V1\User;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\AddUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Laravel\Passport\Token;
use Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        if (Auth::user()->level !== 'admin') {
            abort(403, 'Unauthorized action');
        }
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddUserRequest $request)
    {
        try {
            if ($request->password !== $request->password_confirmation) {
                return response()->json(['message' => 'Password confirmation does not match'], 400);
            }
    
            User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'level'    => $request->level,
                'active'   => '1',
                'password' => bcrypt($request->password),
            ]);
    
            return response()->json(['message' => 'User created successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create user'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            if ($request->has('password')) {
                $request['password'] = bcrypt($request->password);
            }
    
            $user->update($request->all());
    
            return new UserResource($user);
        } catch (Exception $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->level !== 'admin') {
            abort(403, 'Unauthorized action');
        }
        
        try {
            $user = User::findOrFail($id);
            $this->authorize('delete', $user);

            $activeTokens = Token::where('user_id', $user->id)->where('revoked', false)->get();
            if ($activeTokens->isNotEmpty()) {
                foreach ($activeTokens as $token) {
                    $token->revoke();
                }
            }

            $user->delete();
    
            return response()->json([
                'message' => 'User deleted successfully'
            ], 204);
        } catch (Exception $e) {
            return response()->json(['message' => 'User not found'], 404);
        }       
    }
}
