<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $guarded = [
        'id',
    ];
    
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    
    public function gameCategory()
    {
        return $this->belongsTo(GameCategory::class)->withTrashed();
    }
    
    public function gamePlan()
    {
        return $this->belongsTo(GamePlan::class)->withTrashed();
    }
    
    public function timekeepingCard()
    {
        return $this->belongsTo(TimekeepingCard::class)->withTrashed()->withDefault();
    }
    
    public function classes()
    {
        return $this->belongsToMany(EventClass::class, 'game_classes', 'game_id', 'event_class_id')->using(GameClass::class)->withPivot('id', 'capacity');
    }
    
    public function entries()
    {
        return $this->belongsToMany(User::class, 'entries', 'game_id', 'user_id')->using(Entry::class)->withPivot('id', 'event_class_id', 'ticket_cost', 'timekeeping_card_num', 'belonging', 'canceled_at');
    }
}
