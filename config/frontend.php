<?php
return [
    'url' => env('CORS_ALLOWED_ORIGIN'),
    'password_reset_url' => env('FRONTEND_PASSWORD_RESET_URL', '/password/reset?queryURL='),
    'email_verify_url' => env('FRONTEND_EMAIL_VERIFY_URL', '/user/email/verify?queryURL='),
];