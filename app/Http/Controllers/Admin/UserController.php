<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Http\Requests\Admin\UserRequest;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.api_secret'));
    }
    
    public function index()
    {
        $users = User::withCount('tickets')->get();
        return response()->json(['data' => $users], 200);
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->userAttributes());
        
        $customer = \Stripe\Customer::create([
            'address' => ['line1' => $request->address, 'postal_code' => $request->postcode],
            'email' => $user->email,
            'metadata' => [
                'user_id' => $user->id,
                'emg_phone' => $request->emg_phone, 
                'emg_relation' => $request->emg_relation, 
                'coupon' => 0,
            ],
            'name' => $request->name,
            'phone' => $request->phone,
        ]);
        $user->customer_id = $customer['id'];
        $user->password = Hash::make($request->password);
        $user->email_verified_at = Carbon::now();
        
        $user->save();
        
        return response()->json(['data' => $user], 201);
    }

    public function show($id)
    {
        $user = User::find($id);
        
        if ($user->customer_id) {
            $customer = \Stripe\Customer::retrieve($user->customer_id);
            $user->postcode = $customer['address']['postal_code'];
            $user->address = $customer['address']['line1'];
            $user->phone = $customer['phone'];
            $user->emg_phone = $customer['metadata']['emg_phone'];
            $user->emg_relation = $customer['metadata']['emg_relation'];
            $user->coupon = $customer['metadata']['coupon'];
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

    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);
        
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
        
        return response()->json(['data' => $user], 201);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        
        if ($user->customer_id) {
            $customer = \Stripe\Customer::retrieve($user->customer_id);
            $customer->delete();
        }
        
        $user->delete();
        
        return response()->json(['data' => $user], 201);
    }
}
