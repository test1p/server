<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class Entry extends Pivot
{
    use Uuid;
    
    protected $table = "entries";
    
    protected $keyType = 'string';
    
    public $incrementing = false;
    
    protected $guarded = [
        'id',
    ];
    
    public function game()
    {
        return $this->belongsTo(Game::class)->withTrashed();
    }
    
    public function eventClass()
    {
        return $this->belongsTo(EventClass::class)->withTrashed();
    }
    
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
