<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;

class PaymentController extends Controller
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.api_secret'));
    }
    
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->customer_id) return response()->json(['errors' => '先にプロフィールを登録してください'], 403);
        
        $invoices = \Stripe\Invoice::all([
            'customer' => $user->customer_id,
            'limit' => 100
        ]);
        return response()->json(['data' => $invoices['data']], 200);
    }
    
    public function store(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->customer_id) return response()->json(['errors' => '先にプロフィールを登録してください'], 403);
        
        if (!$user->payment_method_id) return response()->json(['errors' => '先に支払方法を登録してください'], 403);
        
        $invoice_item = \Stripe\InvoiceItem::create([
            'customer' => $user->customer_id,
            'price' => $request->price_id,
            'metadata' => [
                'integration_check' => 'accept_a_payment',
            ],
            'tax_rates' => config('services.stripe.default_tax_rates'),
        ]);
        
        \Stripe\Invoice::create([
            'customer' => $user->customer_id,
            'auto_advance' => true,
        ]);
        
        for ($i = 0; $i < $invoice_item['price']['metadata']['add']; $i++)
        {
            $tickets[] = new Ticket;
        }
        $user->tickets()->saveMany($tickets);
        
        return response()->json(['data' => $invoice_item], 201);
    }
    
    public function show($id)
    {
        $user = auth()->user();
        
        if (!$user->customer_id) return response()->json(['errors' => '先にプロフィールを登録してください'], 403);
        
        if (!$user->payment_method_id) return response()->json(['errors' => '先に支払方法を登録してください'], 403);
        
        $invoice = \Stripe\Invoice::retrieve($id);
        
        if (!$invoice || $invoice['customer'] !== $user->customer_id) return response()->json(['errors' => '決済が存在しません'], 403);
        
        return response()->json(['data' => $invoice], 200);
    }
    
    public function destroy($id)
    {
        $user = auth()->user();
        
        if (!$user->customer_id) return response()->json(['errors' => '先にプロフィールを登録してください'], 403);
        
        if (!$user->payment_method_id) return response()->json(['errors' => '先に支払方法を登録してください'], 403);
        
        $invoice = \Stripe\Invoice::retrieve($id);
        
        if (!$invoice || $invoice['customer'] !== $user->customer_id) return response()->json(['errors' => '決済が存在しません'], 403);
        
        if ($invoice['status'] !== 'draft') return response()->json(['errors' => '支払完了のためキャンセルできません'], 403);
        
        $tickets = $user->tickets()->latest()->limit($invoice['lines']['data'][0]['price']['metadata']['add'])->get();
        foreach ($tickets as $ticket) {
            $ticket->forceDelete();
        }
        
        $invoice->delete();
        
        return response()->json(['data' => $invoice], 201);
    }
    
    public function readPrices()
    {
        $prices = \Stripe\Price::all(['active' => true, 'type' => 'one_time']);
        $type = 'general';
        if (auth()->user()->subscription_id) $type = 'subscription';
        $prices = collect($prices['data'])->where('metadata.type', $type)->values();
        return response()->json(['data' => $prices], 200);
    }
}
