<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if(!(Hash::check($value, auth()->user()->password))) {
                      return $fail('現在のパスワードを正しく入力してください');
                    }
                },
            ],
            'new_password' => 'required|string|min:8|confirmed|different:current_password',
        ];
    }
}