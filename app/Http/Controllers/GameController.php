<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Game;
use App\Entry;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::all();
        
        return response()->json(['data' => $games], 200);
    }
    
    public function show($id)
    {
        $game = Game::with(['classes', 'gameCategory', 'gamePlan', 'timekeepingCard', 'event' => function ($q) {
            $q->with('files');
        }])->withCount('entries')->find($id);
        
        if ($game['capacity']) {
            $game = collect($game)->put('available', $game['capacity'] - $game['entries_count']);
        }
        
        return response()->json(['data' => $game], 200);
    }

    public function readGameClasses($id)
    {
        $game = Game::find($id);
        $game_classes = $game->gameClasses;
        
        return response()->json(['data' => $game_classes], 200);
    }

    public function readGameEntries($id)
    {
        $game_entries = Entry::with(['user', 'eventClass'])->where('game_id', $id)->get();
        
        return response()->json(['data' => $game_entries], 200);
    }
}
