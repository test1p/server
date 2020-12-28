<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class UserTimekeepingCard extends Pivot
{
    use Uuid;
    
    protected $table = "user_timekeeping_cards";
    
    protected $keyType = 'string';
    
    public $incrementing = false;
    
    protected $guarded = [
        'id',
    ];
}
