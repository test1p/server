<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateUserRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'name' => 'required|string',
            'furigana' => [
                'required',
                'string',
                'regex:/^[ぁ-んー]+$/u',
            ],
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
            'name',
            'furigana',
            'sex',
        ]);
    }
}