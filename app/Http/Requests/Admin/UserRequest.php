<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class UserRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'name' => 'required|string',
            'furigana' => [
                'required',
                'string',
                'regex:/^[ぁ-んー]+$/u',
            ],
            'birth_date' => 'required|date',
            'sex' => [
                'required',
                Rule::in(['男', '女']),
            ],
            'phone' => 'required|digits_between:10,11',
            'postcode' => 'required|digits:7',
            'address' => 'required|string',
            'emg_phone' => 'required|digits_between:10,11',
            'emg_relation' => 'required|string',
        ];
    }

    public function userAttributes()
    {
        return $this->only([
            'email',
            'payment_method_id',
            'subscription_id',
            'name',
            'furigana',
            'birth_date',
            'sex',
        ]);
    }
}
