<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [
        'id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    
    public function transferAccount()
    {
        return $this->belongsTo(TransferAccount::class)->withTrashed()->withDefault();
    }
    
    public function games()
    {
        return $this->hasMany(Game::class)->withTrashed()->orderBy('date', 'asc');
    }
    
    public function files()
    {
        return $this->hasMany(EventFile::class)->orderBy('updated_at', 'desc');
    }
}
