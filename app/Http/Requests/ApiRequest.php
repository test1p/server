<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

abstract class ApiRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        $data = [
            'data' => [],
            'status' => 'error',
            'summary' => 'Failed validation.',
            'errors' => $validator->errors()->toArray(),
        ];

        throw new HttpResponseException(response()->json($data, 422));
    }
}