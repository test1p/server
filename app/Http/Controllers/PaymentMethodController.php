<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.api_secret'));
    }
    
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->customer_id) return response()->json(['errors' => '先にプロフィールを登録してください'], 403);
        
        $setup = \Stripe\SetupIntent::create([
            'customer' => $user->customer_id,
            'payment_method_types' => ['card'],
        ]);
        return response()->json(['data' => $setup], 200);
    }
    
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $payment_method_id = $request->payment_method;
        
        \Stripe\Customer::update(
            $user->customer_id,
            [
                'invoice_settings' => ['default_payment_method' => $payment_method_id],
            ]
        );
        
        if ($user->payment_method_id)
        {
            $payment_method = \Stripe\PaymentMethod::retrieve($user->payment_method_id);
            $payment_method->detach();
        }
        
        $user->payment_method_id = $payment_method_id;
        $user->save();
        
        return response()->json(['data' => $user], 201);
    }
    
    public function destroy()
    {
        $user = auth()->user();
        
        if (!$user->payment_method_id) return response()->json(['errors' => '決済方法が未登録です'], 403);
        
        $payment_method = \Stripe\PaymentMethod::retrieve($user->payment_method_id);
        $payment_method->detach();
    
        $user->payment_method_id = null;
        $user->save();
        
        return response()->json(['data' => $user], 201);
    }
}
