<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


//quick and dirty stub method to generate a token
Route::get('/setup', function(){
    $credentials = [
        'email' => 'gambrosi4@test.com',
        'password' => 'password',
    ];

    if(!Auth::attempt($credentials)){
        $user = new \App\Models\User();

        $user->name = "gambrosi4";
        $user->email = $credentials["email"];
        $user->password = Hash::make($credentials["password"]);

        $user->save();

        if(Auth::attempt($credentials)){
            $user = Auth::user();

            $adminToken = $user->createToken('admin-token', ['products']);
            $normalToken = $user->createToken('normal-token', ['none']);
            return [$adminToken->plainTextToken, $normalToken->plainTextToken];
        }
    }
});