<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    protected function authenticated(Request $request, $user)
    {
        return response()->json(['message' => 'ログインしました', 'user' => $user], 200);
    }
    
    protected function loggedOut(Request $request)
    {
        return response()->json(['message' => 'ログアウトしました'], 200);
    }
}