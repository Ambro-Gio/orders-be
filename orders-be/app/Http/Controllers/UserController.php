<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponses;

    /**
     * Creates a new user.
     * Returns the user access token.
     * 
     * @param \App\Http\Requests\StoreUserRequest $request
     * 
     * @return array
     */
    public function store(StoreUserRequest $request)
    {

        if (Auth::attempt($request->credentials))
            // Note: this is, of course, a huge security issue. A real application would implement proper login and register functionality.
            return $this->error("user already exists", 400);

        User::create([
            "name" => $request->name,
            "email" => $request->credentials["email"],
            "password" => Hash::make($request->credentials["password"]),
        ]);

        if (!Auth::attempt($request->credentials))
            return $this->error("failed login");

        $user = Auth::user();

        //creating token based on given role
        $token = $user->createToken('admin-token', [$request->role]);

        return $this->data(
            [
                "token" => $token->plainTextToken,
                "role" => $request->role,
            ]
        );
    }
}
