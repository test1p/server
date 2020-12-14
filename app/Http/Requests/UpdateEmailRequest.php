<?php

namespace App\Http\Requests;

class UpdateEmailRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'current_email' => [
                'required',
                function ($attribute, $value, $fail) {
                    if($value !== auth()->user()->email) {
                      return $fail('現在のメールアドレスを正しく入力してください');
                    }
                },
            ],
            'new_email' => 'required|string|email|unique:users,email|different:current_email',
        ];
    }
}