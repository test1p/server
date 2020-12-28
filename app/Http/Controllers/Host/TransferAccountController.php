<?php

namespace App\Http\Controllers\Host;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransferAccountRequest;

class TransferAccountController extends Controller
{
    public function index()
    {
        $transfer_accounts = auth()->user()->transferAccounts()->get()->map(function ($item) {
            $item['label'] = "{$item['destination_bank']} {$item['destination_branch']} {$item['holder']}";
            return $item;
        });
        
        return response()->json(['data' => $transfer_accounts], 200);
    }

    public function store(TransferAccountRequest $request)
    {
        $transfer_account = auth()->user()->transferAccounts()->create($request->TransferAccountAttributes());
        
        return response()->json(['data' => $transfer_account], 201);
    }

    public function show($id)
    {
        $transfer_account = auth()->user()->transferAccounts()->find($id);
        
        return response()->json(['data' => $transfer_account], 200);
    }

    public function destroy($id)
    {
        $transfer_account = auth()->user()->transferAccounts()->find($id);
        $transfer_account->delete();
        
        return response()->json(['data' => $transfer_account], 201);
    }
}
