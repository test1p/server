<?php

use Illuminate\Http\Request;

Route::get('/', 'EventController@index');
Route::apiResource('/game', 'GameController', ['only' => ['index', 'show']]);
    
Route::group(["middleware" => "guest:api"], function () {
    Route::post('/signup', 'Auth\RegisterController@register');
    Route::post('/login', 'Auth\LoginController@login');
    Route::get('/login/{provider}', 'Auth\ProviderAuthController@getRedirectUrl');
    Route::get('/login/{provider}/callback', 'Auth\ProviderAuthController@handleProviderCallback');
    Route::post('/password/forgot', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
    Route::get('/game/{id}/entrylist', 'GameController@readGameEntries');
    Route::apiResource('/event', 'EventController', ['only' => ['index', 'show']]);
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
    Route::get('/user/referralCode', 'UserController@createReferralCode');
    Route::apiResource('/user/paymentMethod', 'PaymentMethodController', ['only' => ['index', 'store']]);
    Route::delete('/user/paymentMethod', 'PaymentMethodController@destroy');
    Route::get('/subscriptionPlan', 'SubscriptionController@readPrices');
    Route::apiResource('/user/subscription', 'SubscriptionController', ['only' => ['store']]);
    Route::put('/user/subscription', 'SubscriptionController@update');
    Route::delete('/user/subscription', 'SubscriptionController@destroy');
    Route::get('/paymentPlan', 'PaymentController@readPrices');
    Route::apiResource('/user/payment', 'PaymentController', ['only' => ['index', 'store', 'show', 'destroy']]);
    Route::get('/game/{id}/class', 'GameController@readGameClasses');
    Route::get('/game/{id}/entrylist', 'GameController@readGameEntries');
    Route::get('/game/{id}/entry', 'UserEntryController@simplify');
    Route::post('/game/{id}/entry', 'UserEntryController@store');
    Route::apiResource('/event', 'EventController', ['only' => ['index', 'show']]);
    Route::get('/transferPlan', 'UserTransferController@readTransferPlans');
    Route::apiResource('/user/entry', 'UserEntryController', ['only' => ['index', 'show', 'update', 'destroy']]);
    Route::get('/user/simplify', 'UserController@readSimplify');
    Route::put('/user/simplify', 'UserController@updateSimplify');
    Route::get('/timekeepingCard', 'UserController@readTimekeepingCards');
    Route::apiResource('/user/transfer', 'UserTransferController', ['only' => ['index', 'store', 'show', 'destroy']]);
});

Route::group(['middleware' => ['auth:api', 'host'], 'prefix' => 'host', 'namespace' => 'Host'], function () {
    Route::apiResource('/event', 'EventController');
    Route::get('/class', 'GameController@readClasses');
    Route::get('/gameCategory', 'GameController@readGameCategories');
    Route::get('/gamePlan', 'GameController@readGamePlans');
    Route::get('/timekeepingCard', 'GameController@readTimekeepingCards');
    Route::apiResource('/game', 'GameController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
    Route::get('/game/{id}/entry', 'GameController@readGameEntries');
    Route::get('/game/{game_id}/entry/{user_id}', 'GameController@readEntryUser');
    Route::get('/game/{id}/entrylist', 'GameController@createEntryList');
    Route::post('/event/{id}', 'GameController@store');
    Route::apiResource('/transferAccount', 'TransferAccountController', ['only' => ['index', 'store', 'show', 'destroy']]);
    Route::apiResource('/event/{event_id}/file', 'EventFileController');
});

Route::group(['middleware' => ['auth:api', 'admin'], 'prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::apiResource('/user', 'UserController');
    Route::apiResource('/transfer', 'TransferController');
    Route::get('/transferPlan', 'TransferController@readTransferPlans');
    Route::post('/transfer/{id}', 'TransferController@check');
    Route::apiResource('/ticket', 'TicketController');
});

