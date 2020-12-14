<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use GoldSpecDigital\LaravelEloquentUUID\Foundation\Auth\User as Authenticatable;
use App\Notifications\VerifyEmail; 
use App\Notifications\ResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
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
}
