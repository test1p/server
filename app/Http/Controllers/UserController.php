<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Carbon\Carbon;
use App\Http\Requests\UpdateEmailRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Hash;
use App\TimekeepingCard;

class UserController extends Controller
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.api_secret'));
    }
    
    public function index()
    {
        $user = auth()->user();
        $user->tickets_count = $user->withCount('tickets')->find($user->id)->tickets_count;
        $user->age = Carbon::parse($user->birth_date)->age;
        $user->entries = $user->entries()->get()->pluck('id');
        
        if ($user->customer_id) {
            $customer = \Stripe\Customer::retrieve($user->customer_id);
            $user->postcode = $customer['address']['postal_code'];
            $user->address = $customer['address']['line1'];
            $user->phone = $customer['phone'];
            $user->emg_phone = $customer['metadata']['emg_phone'];
            $user->emg_relation = $customer['metadata']['emg_relation'];
        }
        
        if ($user->payment_method_id) {
            $payment_method = \Stripe\PaymentMethod::retrieve($user->payment_method_id);
            $user->payment_method = $payment_method['card'];
        }
        
        if ($user->subscription_id) {
            $subscription = \Stripe\Subscription::retrieve($user->subscription_id);
            $user->subscription = $subscription['items']['data'][0]['plan'];
        }
        
        return response()->json(['data' => $user], 200);
    }
    
    public function store(StoreUserRequest $request)
    {
        $user = auth()->user();
        
        $customer = \Stripe\Customer::create([
            'address' => ['line1' => $request->address, 'postal_code' => $request->postcode],
            'email' => $user->email,
            'metadata' => [
                'user_id' => $user->id,
                'emg_phone' => $request->emg_phone, 
                'emg_relation' => $request->emg_relation,
            ],
            'name' => $request->name,
            'phone' => $request->phone,
        ]);
        $user->customer_id = $customer['id'];
        
        $user->fill($request->userAttributes())->save();
        
        return response()->json(['data' => $user], 201);
    }
    
    public function update(UpdateUserRequest $request)
    {
        $user = auth()->user();
        
        $customer = \Stripe\Customer::update(
            $user->customer_id,
            [
                'address' => ['line1' => $request->address, 'postal_code' => $request->postcode],
                'metadata' => [
                    'emg_phone' => $request->emg_phone, 
                    'emg_relation' => $request->emg_relation,
                ],
                'name' => $request->name,
                'phone' => $request->phone,
            ]
        );
        
        $user->fill($request->userAttributes())->save();
        
        return response()->json(['data' => $user, 'user' => true], 201);
    }
    
    public function destroy()
    {
        $user = auth()->user();
        
        if ($user->customer_id) {
            $customer = \Stripe\Customer::retrieve($user->customer_id);
            $customer->delete();
        }
        
        $user->delete();
        
        return response()->json(['data' => $user], 201);
    }
    
    public function createReferralCode()
    {
        $user = auth()->user();
        
        if (!$user->customer_id) return response()->json(['errors' => '先にプロフィールを登録してください'], 403);
        
        if (!$user->payment_method_id) return response()->json(['errors' => '先に支払方法を登録してください'], 403);
        
        if (!$user->subscription_id) return response()->json(['errors' => '先にプランを登録してください'], 403);
        
        
        $referral_code = \Stripe\PromotionCode::create([
            'coupon' => config('services.stripe.referral_coupon'),
            'metadata' => [
                'customer_id' => $user->customer_id
            ],
            'expires_at' => Carbon::now('Asia/Tokyo')->addWeek()->timestamp,
        ]);
        
        return response()->json(['data' => $referral_code], 200);
    }
    
    public function updateEmail(UpdateEmailRequest $request)
    {
        $user = auth()->user();
        
        if ($user->customer_id)
        {
            $customer = \Stripe\Customer::update(
                $user->customer_id,
                [
                    'email' => $request->new_email,
                ]
            );
        }
        $user->email = $request->new_email;
        $user->email_verified_at = null;
        $user->save();
        $user->sendEmailVerificationNotification();

        return response()->json(['message' => '認証メールを送信しましたのでご確認ください'], 201);
    }
    
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = auth()->user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'パスワードを変更しました'], 201);
    }
    
    public function readSimplify()
    {
        $user = auth()->user();
        $user->tickets_count = $user->withCount('tickets')->find($user->id)->tickets_count;
        $user->age = Carbon::parse($user->birth_date)->age;
        
        if ($user->timekeepingCards) {
            $user->timekeeping_cards = $user->timekeepingCards;
        }
        
        return response()->json(['data' => $user], 200);
    }
    
    public function updateSimplify(Request $request)
    {
        $user = auth()->user();
        $user->priority_difficulty = $request->priority_difficulty;
        $user->priority_age = $request->priority_age;
        $user->belonging = $request->belonging;
        $user->save();
        if ($request->timekeeping_cards) {
            foreach ($request->timekeeping_cards as $timekeeping_card) {
                if ($user->timekeepingCards()->find($timekeeping_card['id'])) {
                    $user->timekeepingCards()->detach($timekeeping_card['id']);
                }
                $user->timekeepingCards()->attach($timekeeping_card['id'], [
                    'timekeeping_card_num' => $timekeeping_card['timekeeping_card_num']
                ]);
            }
        }

        return response()->json(['data' => $user], 201);
    }
    
    public function readTimekeepingCards()
    {
        $timekeeping_cards = TimekeepingCard::all();
        
        return response()->json(['data' => $timekeeping_cards], 200);
    }
}
