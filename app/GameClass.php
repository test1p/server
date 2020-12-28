<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class GameClass extends Pivot
{
    use Uuid;
    
    protected $table = "game_classes";
    
    protected $keyType = 'string';
    
    public $incrementing = false;
    
    protected $guarded = [
        'id',
    ];
}
