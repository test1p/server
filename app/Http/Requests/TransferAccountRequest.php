<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class TransferAccountRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'destination_bank' => 'required|string',
            'destination_branch' => 'required|string',
            'type' => [
                'required',
                Rule::in(['普通', '当座']),
            ],
            'num' => 'required|integer',
            'holder' => 'required|string',
        ];
    }

    public function transferAccountAttributes()
    {
        return $this->only([
            'destination_bank',
            'destination_branch',
            'type',
            'num',
            'holder',
        ]);
    }
}
