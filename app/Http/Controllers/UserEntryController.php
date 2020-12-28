<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entry;
use App\Game;
use Carbon\Carbon;

class UserEntryController extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $user_entries = Entry::where('user_id', $user_id)->with(['eventClass', 'game' => function ($q) {
            $q->with('event');
        }])->get()->map(function ($item) {
            $item['date'] = $item['game']['date'];
            $item['name'] = $item['game']['event']['event_name'] . $item['game']['game_name'];
            $item['class'] = $item['eventClass']['class_name'];
            return $item;
        })->sortByDesc('date')->values();
        
        return response()->json(['data' => $user_entries], 200);
    }

    public function store(Request $request, $id)
    {
        $user = auth()->user();
        
        if ($user->entries()->find($id)) return response()->json(['errors' => ['error' => ['エントリー済みです']]], 422);
        
        $tickets_count = $user->tickets()->count();
        $ticket_cost = $request->ticket_cost;
        
        if ($tickets_count < $ticket_cost) return response()->json(['errors' => ['error' => ['チケットが足りません']]], 422);
        
        $user_entry = $user->entries()->attach($id, [
            'event_class_id' => $request->event_class_id,
            'ticket_cost' => $ticket_cost,
            'timekeeping_card_num' => $request->timekeeping_card_num,
            'belonging' => $request->belonging,
        ]);
        
        $user_entry = $user->entries()->find($id);
        
        $user->tickets()->oldest()->limit($ticket_cost)->update(['entry_id' => $user_entry['pivot']['id']]);
        $user->tickets()->whereNotNull('entry_id')->where('entry_id', $user_entry['pivot']['id'])->delete();
        
        return response()->json(['data' => $user_entry, 'user' => true], 201);
    }

    public function show($id)
    {
        $user_id = auth()->user()->id;
        $user_entry = Entry::where([['user_id', $user_id], ['game_id', $id]])->with(['eventClass', 'game' => function ($q) {
            $q->with('classes', 'gameCategory', 'gamePlan', 'event', 'timekeepingCard');
        }])->first();
        
        if (!$user_entry) return response()->json(['errors' => '権限がありません'], 404);
        
        $user_entry = collect($user_entry)->put('name', $user_entry['game']['event']['event_name'] . $user_entry['game']['game_name']);
        
        return response()->json(['data' => $user_entry], 200);
    }

    public function update(Request $request, $id)
    {
        $user_entry = auth()->user()->entries()->updateExistingPivot($id, ['event_class_id' => $request->event_class_id, 'timekeeping_card_num' => $request->timekeeping_card_num, 'belonging' => $request->belonging]);
        
        return response()->json(['data' => $user_entry], 201);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        
        $user_entry = $user->entries()->find($id);
        
        $entry_ended_at = new Carbon($user_entry['entry_ended_at']);
        $now = Carbon::now();
        
        if ($entry_ended_at >= $now) {
            $user->tickets()->onlyTrashed()->where('entry_id', $user_entry['pivot']['id'])->restore();
            $user->tickets()->whereNotNull('entry_id')->where('entry_id', $user_entry['pivot']['id'])->update(['entry_id' => null]);
            $user->entries()->detach($id);
        }
        else {
            $user->entries()->updateExistingPivot($id, ['canceled_at' => $now]);
        }
        
        return response()->json(['data' => $user_entry, 'user' => true], 201);
    }

    public function simplify($id)
    {
        $user = auth()->user();
        
        $sex = 0;
        
        if ($user->sex === '女') $sex = 1;
        
        
        $game = Game::with(['event', 'gamePlan', 'classes' => function ($q) use ($sex) {
            $q->where('women_only', '<=', $sex);
        }])->find($id);
        
        $priority_difficulty = $user->priority_difficulty;
        $age = Carbon::parse($user->birth_date)->age;
        $priority_age = $age - $user->priority_age;
        
        if ($priority_difficulty === 0) {
            $difficulty['sign'] = '>=';
            $difficulty['order'] = 'asc';
        }
        else {
            $difficulty['sign'] = '<=';
            $difficulty['order'] = 'desc';
        }
        if ($user->priority_age === 0) {
            $min_age['sign'] = '<=';
            $min_age['order'] = 'desc';
        }
        else {
            $min_age['sign'] = '>=';
            $min_age['order'] = 'asc';
            if ($priority_age < 30) $priority_age = 0;
        }
        
        $game_class = $game->classes()
            ->where('women_only', $sex)
            ->where('difficulty', $difficulty['sign'], $priority_difficulty)
            ->where('min_age', $min_age['sign'], $priority_age)
            ->where('max_age', '>=', $age)
            ->orderBy('difficulty', $difficulty['order'])
            ->orderBy('min_age', $min_age['order'])
            ->orderBy('distance', 'asc')
            ->first();
            
        if (!$game_class) return response()->json(['errors' => ['error' => ['自動選択できるクラスがありません。通常エントリーをご利用ください。']]], 422);
        
        $ticket_cost = $game->gamePlan->general_ticket_cost;
        
        if ($age <= 24) {
            $ticket_cost = $game->gamePlan->student_ticket_cost;
        }
        
        $timekeeping_card = $game->timekeepingCard;
        $timekeeping_card_num = 'レンタル';
        
        if ($user->timekeepingCards) {
            $user_timekeeping_card = $user->timekeepingCards()->find($timekeeping_card->id);
            
            if ($user_timekeeping_card) $timekeeping_card_num = $user_timekeeping_card->pivot->timekeeping_card_num;
        }
        
        $belonging = $user->belonging;
        
        $entry = [
            'event_name' => $game->event->event_name . $game->game_name,
            'game_classes' => $game->classes,
            'event_class_id' => $game_class->id,
            'event_class_name' => $game_class->class_name,
            'belonging' => $belonging,
            'timekeeping_card_name' => $timekeeping_card->timekeeping_card_name,
            'timekeeping_card_num' => $timekeeping_card_num,
            'ticket_cost' => $ticket_cost,
        ];
        return response()->json(['data' => $entry], 200);
    }
}
