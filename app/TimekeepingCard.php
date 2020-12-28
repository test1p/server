<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimekeepingCard extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $guarded = [
        'id',
    ];
    
    public function users()
    {
        return $this->hasMany(User::class)->withTrashed();
    }
    
    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_timekeeping_cards', 'timekeeping_card_id', 'game_id')->using(GameTimekeepingCard::class)->withPivot('id');
    }
}
