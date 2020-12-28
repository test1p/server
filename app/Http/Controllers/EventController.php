<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index()
    {
        $today = Carbon::parse('now');
        $events = Event::with(['games' => function ($q) {
            $q->with('gamePlan')->withCount('entries');
        }])->get()->map(function ($item) {
            $item['start_date'] = $item['games'][0]['date'];
            $item['end_date'] = $item['games'][count($item['games']) - 1]['date'];
            $item['venues'] = $item['games']->pluck('venue')->map(function ($item2) {
                return explode(' ', $item2)[0];
            })->unique()->join(', ');
            $item['games'] = $item['games']->map(function ($item2) {
                if ($item2['capacity']) {
                    $item2['available'] = $item2['capacity'] - $item2['entries_count'];
                }
                return $item2;
            });
            return $item;
        })->where('end_date', '>=', $today)->sortBy('start_date')->values();
        
        return response()->json(['data' => $events], 200);
    }
    
    public function show($id)
    {
        $event = Event::with('files', 'games')->find($id);
        
        return response()->json(['data' => $event], 200);
    }
}
