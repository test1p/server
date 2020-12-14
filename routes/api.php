<?php

use Illuminate\Http\Request;

Route::group(["middleware" => "guest:api"], function () {
    Route::post('/signup', 'Auth\RegisterController@register');
    Route::post('/login', 'Auth\LoginController@login');
    Route::get('/login/{provider}', 'Auth\ProviderAuthController@getRedirectUrl');
    Route::get('/login/{provider}/callback', 'Auth\ProviderAuthController@handleProviderCallback');
    Route::post('/password/forgot', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
});

Route::group(["middleware" => "auth:api"], function () {
    Route::get('/logout', 'Auth\LoginController@logout');
    Route::apiResource('/user', 'UserController', ['only' => ['index', 'store']]);
    Route::put('/user', 'UserController@update');
    Route::delete('/user', 'UserController@destroy');
    Route::get('/user/email/verify/{id}', 'Auth\VerificationController@verify')->name('email.verify');
    Route::post('/user/email/resend', 'Auth\VerificationController@resend')->name('email.resend');
    Route::put('/user/email', 'UserController@updateEmail');
    Route::put('/user/password', 'UserController@updatePassword');
    Route::get('/user/referralCode', 'UserController@readReferralCode');
    Route::apiResource('/user/paymentMethod', 'PaymentMethodController', ['only' => ['index', 'store']]);
    Route::delete('/user/paymentMethod', 'PaymentMethodController@destroy');
    Route::get('/subscriptionPlan', 'SubscriptionController@readPrices');
    Route::apiResource('/user/subscription', 'SubscriptionController', ['only' => ['store']]);
    Route::put('/user/subscription', 'SubscriptionController@update');
    Route::delete('/user/subscription', 'SubscriptionController@destroy');
    Route::get('/paymentPlan', 'PaymentController@readPrices');
    Route::apiResource('/user/payment', 'PaymentController', ['only' => ['index', 'store', 'show', 'destroy']]);
});

Route::group(['middleware' => ['auth:api', 'admin'], 'prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::apiResource('/user', 'UserController');
});

