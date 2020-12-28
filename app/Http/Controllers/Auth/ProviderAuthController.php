<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;
use App\User;

class ProviderAuthController extends Controller
{
    public function getRedirectUrl($provider)
    {
        $redirect_url = Socialite::driver($provider)->redirect()->getTargetUrl();

        return response()->json([
            'redirect_url' => $redirect_url
        ]);
    }
    
    public function handleProviderCallback($provider)
    {
        $provider_user = Socialite::driver($provider)->user();
        
        $email = $provider_user->getEmail();
        
        $user = User::where('email', $email)->where('provider_name', '<>', $provider)->first();
        
        if ($user) return response()->json(['errors' => 'メールアドレスが既に使用されています'], 401);
        
        $user = User::firstOrCreate(
            [
                'provider_name'   => $provider,
                'provider_uid' => $provider_user->getId(),
            ],
            [
                'email' => $email,
                'email_verified_at' => Carbon::now(),
                'provider_name'   => $provider,
                'provider_uid' => $provider_user->getId(),
                
            ]
        );
        auth()->login($user, true);
        $user = auth()->user();
        
        if (!$user) return response()->json(['errors' => 'ログインに失敗しました'], 401);
        
        $user->tickets_count = $user->withCount('tickets')->find($user->id)->tickets_count;
        $user->age = Carbon::parse($user->birth_date)->age;
        $user->entries = $user->entries()->get()->pluck('id');
        
        return response()->json(['message' => 'ログインしました', 'data' => $user], 200);
    }
}