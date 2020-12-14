<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\User;

class VerificationController extends Controller
{
    use VerifiesEmails;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
    
    public function verify(Request $request)
    {
        $user = User::find($request->route('id'));
        if (!$user->email_verified_at) {
            $user->markEmailAsVerified();
            event(new Verified($user));
            return response()->json(['message' => 'メールアドレス認証が完了しました'], 201);
        }
        return response()->json(['errors' => '既にメールアドレス認証は完了しています'], 401);
    }
    
    public function resend(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['errors' => 'ユーザーが存在しません'], 401);
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json(['errors' => '既にメールアドレス認証は完了しています'], 401);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => '認証メールを送信しました'], 201);
    }
}