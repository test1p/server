<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventClass extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $guarded = [
        'id',
    ];
    
    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_classes', 'event_class_id', 'game_id')->using(GameClass::class)->withPivot('id', 'capacity');
    }
}
