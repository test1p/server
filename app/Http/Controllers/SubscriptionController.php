<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.api_secret'));
    }
    
    public function store(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->customer_id) return response()->json(['errors' => '先にプロフィールを登録してください'], 403);
        
        if (!$user->payment_method_id) return response()->json(['errors' => '先に支払方法を登録してください'], 403);
        
        if ($user->subscription_id) return response()->json(['errors' => '既にプラン登録済みです'], 403);
        
        $code = $request->code;
        $coupon = null;
        
        if ($code) {
            $promotion_codes = \Stripe\PromotionCode::all(['code' => $code]);
            $promotion_code = $promotion_codes['data'][0];
        
            if (!$promotion_code) return response()->json(['errors' => ['done' => [0 => '紹介コードに誤りがあります']]], 422);
            
            if ($promotion_code['customer']) {
                \Stripe\Customer::update(
                    $promotion_code['customer'],
                    [
                        'promotion_code' => $promotion_code['id'],
                        'metadata' => [
                            'coupon' => $promotion_code['coupon']['id'],
                        ],
                    ]
                );
            }
            
            $coupon = config('services.stripe.referral_coupon');
        }
        
        $subscription = \Stripe\Subscription::create([
            'coupon' => $coupon,
            'customer' => $user->customer_id,
            'default_tax_rates' => config('services.stripe.default_tax_rates'),
            'items' => [
                [
                    'price' => $request->price_id,
                ],
            ],
            'metadata' => [
                'integration_check' => 'accept_a_payment',
            ],
        ]);
        
        $user->subscription_id = $subscription['id'];
        $user->save();
        
        return response()->json(['data' => $user], 201);
    }
    
    public function update(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->customer_id) return response()->json(['errors' => '先にプロフィールを登録してください'], 403);
        
        if (!$user->payment_method_id) return response()->json(['errors' => '先に支払方法を登録してください'], 403);
        
        $subscription_id = $user->subscription_id;
        
        if (!$subscription_id) return response()->json(['errors' => 'プランが未登録です'], 403);
        
        $subscription = \Stripe\Subscription::retrieve($subscription_id);
        $subscription = \Stripe\Subscription::update(
            $subscription_id,
            [
                'items' => [
                    [
                        'id' => $subscription['items']['data'][0]['id'],
                        'price' => $request->price_id,
                    ],
                ],
                'proration_behavior' => 'none',
            ]
        );
        
        return response()->json(['data' => $subscription], 201);
    }
    
    public function destroy()
    {
        $user = auth()->user();
        
        $subscription_id = $user->subscription_id;
        
        if (!$subscription_id) return response()->json(['errors' => 'プランが未登録です'], 403);
        
        $subscription = \Stripe\Subscription::retrieve($subscription_id);
        $subscription->cancel();
        
        $user->subscription_id = null;
        $user->save();
        
        return response()->json(['data' => $user], 201);
    }
    
    public function readPrices()
    {
        $prices = \Stripe\Price::all(['type' => 'recurring']);
        return response()->json(['data' => $prices['data']], 200);
    }
}
