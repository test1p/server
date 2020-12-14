<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use App;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;

class VerifyEmail extends VerifyEmailBase
{
    public function toMail($user)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $user);
        }

        return (new MailMessage)
            ->subject(Lang::get('mail.verify_email.subject'))
            ->line(Lang::get('mail.verify_email.line_01'))
            ->action(Lang::get('mail.verify_email.action'), $this->verificationUrl($user))
            ->line(Lang::get('mail.verify_email.line_02'));
    }
    protected function verificationUrl($user)
    {
        $prefix = config('frontend.url') .config('frontend.email_verify_url');
        $routeName = 'email.verify';
        $temporarySignedURL = URL::temporarySignedRoute(
            $routeName, Carbon::now()->addMinutes(60), ['id' => $user->getKey()]
        );
        $serverUrl = str_replace('8081', '8080', config('frontend.url'));
        $temporarySignedURL = str_replace($serverUrl, '', $temporarySignedURL);

        return $prefix . urlencode($temporarySignedURL);
    }
}