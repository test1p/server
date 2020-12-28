<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransferPlan;

class UserTransferController extends Controller
{
    public function index()
    {
        $user_transfers = auth()->user()->transfers()->with('transferPlan')->get();
        
        return response()->json(['data' => $user_transfers], 200);
    }

    public function store(Request $request)
    {
        $user_transfer = auth()->user()->transfers()->create(['transfer_plan_id' => $request->price_id]);
        
        return response()->json(['data' => $user_transfer], 201);
    }

    public function show($id)
    {
        $user_transfer = auth()->user()->transfers()->with('transferPlan')->find($id);
        
        return response()->json(['data' => $user_transfer], 200);
    }

    public function destroy($id)
    {
        $user_transfer = auth()->user()->transfers()->find($id);
        $user_transfer->delete();
        
        return response()->json(['data' => $user_transfer], 201);
    }
    
    public function readTransferPlans()
    {
        $transfer_plans = TransferPlan::all();
        
        return response()->json(['data' => $transfer_plans], 200);
    }
}
