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

    public function store(StoreUserRequest $request){

        if( Auth::attempt($request->credentials))
            return $this->error("user already exists");

        User::create([
            "name" => $request->name,
            "email" => $request->credentials["email"],
            "password" => Hash::make($request->credentials["password"]),
        ]);

        if( !Auth::attempt($request->credentials))
            return $this->error("failed login");
        
        $user = Auth::user();

        //creating token based on given role
        $token = $user->createToken('admin-token', [$request->role]);

        return $this->ok($token->plainTextToken);
    }  
}
