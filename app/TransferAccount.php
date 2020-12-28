<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransferAccount extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $guarded = [
        'id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    
    public function games()
    {
        return $this->hasMany(Game::class)->withTrashed();
    }
}
