<?php

namespace App\Http\Controllers\Host;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        $events = auth()->user()->events()->with(['games' => function ($q) {
            $q->with('gamePlan');
        }])->get()->map(function ($item) {
            $item['start_date'] = $item['games'][0]['date'];
            $item['end_date'] = $item['games'][count($item['games']) - 1]['date'];
            $item['venues'] = $item['games']->pluck('venue')->map(function ($item) {
                return explode(' ', $item)[0];
            })->unique()->join(', ');
            return $item;
        })->sortByDesc('end_date')->values();
        
        return response()->json(['data' => $events], 200);
    }

    public function store(Request $request)
    {
        $event = auth()->user()->events()->create([
            'event_name' => $request->event_name,
            'organizer' => $request->organizer
        ]);
        foreach ($request->games as $game) {
            $event->games()->create([
                'game_category_id' => $game['game_category_id'],
                'game_plan_id' => $game['game_plan_id'],
                'game_name' => $game['game_name'],
                'date' => $game['date'],
                'venue' => $game['venue']
            ]);
        }
        
        return response()->json(['data' => $event], 201);
    }

    public function show($id)
    {
        $event = auth()->user()->events()->with(['files', 'transferAccount', 'games' => function ($q) {
            $q->with('gamePlan');
        }])->find($id);
        
        if (!$event) return response()->json(['errors' => '権限がありません'], 404);
        
        if ($event['transferAccount']) {
            $event = collect($event)->put('transfer_account_label', "{$event['transferAccount']['destination_bank']} {$event['transferAccount']['destination_branch']} {$event['transferAccount']['holder']}");
        }
        
        return response()->json(['data' => $event], 200);
    }

    public function update(Request $request, $id)
    {
        $event = auth()->user()->events()->find($id);
        
        if (!$event) return response()->json(['errors' => '権限がありません'], 404);
        
        $event->fill([
            'organizer' => $request->organizer,
            'transfer_account_id' => $request->transfer_account_id,
        ])->save();
        
        return response()->json(['data' => $event], 201);
    }

    public function destroy($id)
    {
        $event = auth()->user()->events()->find($id);
        
        if (!$event) return response()->json(['errors' => '権限がありません'], 404);
        
        $event->delete();
        
        return response()->json(['data' => $event], 201);
    }
}
