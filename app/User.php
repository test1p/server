<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use GoldSpecDigital\LaravelEloquentUUID\Foundation\Auth\User as Authenticatable;
use App\Notifications\VerifyEmail; 
use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    use Notifiable;

    protected $guarded = [
        'id',
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
    
    public function events()
    {
        return $this->hasMany(Event::class);
    }
    
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    
    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }
    
    public function transferAccounts()
    {
        return $this->hasMany(TransferAccount::class);
    }
    
    public function entries()
    {
        return $this->belongsToMany(Game::class, 'entries', 'user_id', 'game_id')->using(Entry::class)->withPivot('id', 'event_class_id', 'ticket_cost', 'timekeeping_card_num', 'belonging', 'canceled_at');
    }
    
    public function timekeepingCards()
    {
        return $this->belongsToMany(TimekeepingCard::class, 'user_timekeeping_cards', 'user_id', 'timekeeping_card_id')->using(UserTimekeepingCard::class)->withPivot('id', 'timekeeping_card_num');
    }
}
