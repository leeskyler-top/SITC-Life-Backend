<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function login(Request $request)
    {
        $data = $request->only([
           "username",
           "password"
        ]);
        $request->validate([
            "username" => "required",
            "password" => "required"
        ]);
        if (Auth::once($data)) {
            $user = Auth::user();
            $user->token = md5($user->username . Carbon::now()->format("Y-m-d H:i:s"));
            $user->save();
            return $this->ok("success", $user);
        }
        throw new AuthenticationException;
    }

    function logout()
    {
        $user = Auth::user();
        $user->token = null;
        $user->save();
        return $this->ok();
    }
}
