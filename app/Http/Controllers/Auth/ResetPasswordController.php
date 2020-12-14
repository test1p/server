<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;
    
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());
        
        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );
        
        return $response == Password::PASSWORD_RESET
            ? response()->json(['message' => 'パスワード再設定が完了しました'], 201)
            : response()->json(['errors' => 'パスワード再設定に失敗しました'], 401);
    }
}