<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    protected function authenticated(Request $request, $user)
    {
        $user->tickets_count = $user->withCount('tickets')->find($user->id)->tickets_count;
        $user->age = Carbon::parse($user->birth_date)->age;
        $user->entries = $user->entries()->get()->pluck('id');
        
        return response()->json(['message' => 'ログインしました', 'data' => $user], 200);
    }
    
    protected function loggedOut(Request $request)
    {
        return response()->json(['message' => 'ログアウトしました'], 200);
    }
}