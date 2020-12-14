<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends ResetPasswordBase
{
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->subject(Lang::get('mail.password_reset.subject'))
            ->line(Lang::get('mail.password_reset.line_01'))
            ->action(Lang::get('mail.password_reset.action'), $this->resetUrl($notifiable))
            ->line(Lang::get('mail.password_reset.line_02', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('mail.password_reset.line_03'));
    }
    protected function resetUrl($notifiable)
    {
        $prefix = config('frontend.url') .config('frontend.password_reset_url');
        $url = $prefix . '?' . http_build_query(['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]);

        return $url;
    }
}