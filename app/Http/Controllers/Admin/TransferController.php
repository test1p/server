<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transfer;
use App\TransferPlan;
use App\User;
use App\Ticket;

class TransferController extends Controller
{
    public function index()
    {
        $transfers = Transfer::with('transferPlan', 'user')->orderBy('created_at', 'desc')->get();
        
        return response()->json(['data' => $transfers], 200);
    }

    public function store(Request $request)
    {
        $user = User::find($request->user_id);
        $transfer = $user->transfers()->create(['transfer_plan_id' => $request->price_id]);
        
        return response()->json(['data' => $transfer], 201);
    }

    public function show($id)
    {
        $transfer = Transfer::with('transferPlan', 'user')->find($id);
        
        return response()->json(['data' => $transfer], 200);
    }

    public function update(Request $request, $id)
    {
        $transfer = Transfer::find($id)->fill($request->all())->save();
        
        return response()->json(['data' => $transfer], 201);
    }

    public function destroy($id)
    {
        $transfer = Transfer::find($id);
        $transfer->delete();
        
        return response()->json(['data' => $transfer], 201);
    }
    
    public function readTransferPlans()
    {
        $transfer_plans = TransferPlan::all();
        
        return response()->json(['data' => $transfer_plans], 200);
    }

    public function check($id)
    {
        $transfer = Transfer::find($id);
        $transfer->status = 1;
        $transfer->save();
        $transfer_plan = $transfer->transferPlan;
        $user = $transfer->user;
        
        for ($i = 0; $i < $transfer_plan->add_ticket_num; $i++)
        {
            $tickets[] = new Ticket;
        }
        $user->tickets()->saveMany($tickets);
        
        return response()->json(['data' => $user], 201);
    }
}
