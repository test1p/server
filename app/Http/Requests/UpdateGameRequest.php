<?php

namespace App\Http\Requests;

class UpdateGameRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'timekeeping_card_id' => 'nullable|string',
            'entry_started_at' => 'required|date_format:Y-m-d H:i',
            'entry_ended_at' => 'required|date_format:Y-m-d H:i',
            'capacity' => 'nullable|integer',
        ];
    }
    public function gameAttributes()
    {
        return $this->only([
            'timekeeping_card_id',
            'entry_started_at',
            'entry_ended_at',
            'capacity',
        ]);
    }
}
