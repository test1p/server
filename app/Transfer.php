<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $guarded = [
        'id',
    ];
    
    public function transferPlan()
    {
        return $this->belongsTo(TransferPlan::class)->withTrashed();
    }
    
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
