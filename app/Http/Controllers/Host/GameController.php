<?php

namespace App\Http\Controllers\Host;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateGameRequest;
use App\Entry;
use App\EventClass;
use App\Game;
use App\GameCategory;
use App\GamePlan;
use App\TimekeepingCard;
use App\User;

class GameController extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $games = Game::with(['event' => function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        }])->get();
        
        return response()->json(['data' => $games], 200);
    }

    public function store(Request $request, $id)
    {
        $event = auth()->user()->events()->find($id);
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
        $user_id = auth()->user()->id;
        $game = Game::with(['classes', 'gameCategory', 'gamePlan', 'timekeepingCard', 'event' => function ($q) use ($user_id) {
            $q->where('user_id', $user_id)->with('files', 'transferAccount');
        }])->find($id);
        
        if (!$game) return response()->json(['errors' => '権限がありません'], 404);
        
        $game = collect($game)->put('transfer_account_label', "{$game['event']['transferAccount']['destination_bank']} {$game['event']['transferAccount']['destination_branch']} {$game['event']['transferAccount']['holder']}");
        
        return response()->json(['data' => $game], 200);
    }

    public function update(UpdateGameRequest $request, $id)
    {
        $user_id = auth()->user()->id;
        $game = Game::with(['event' => function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        }])->find($id);
        
        if (!$game) return response()->json(['errors' => '権限がありません'], 404);
        
        $game->fill($request->gameAttributes())->save();
        
        if ($request->classes) {
            $game->classes()->sync($request->classes);
        }
        
        return response()->json(['data' => $game], 201);
    }

    public function destroy($id)
    {
        $user_id = auth()->user()->id;
        $game = Game::with(['event' => function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        }])->find($id);
        
        if (!$game) return response()->json(['errors' => '権限がありません'], 404);
        
        $game->delete();
        
        return response()->json(['data' => $game], 201);
    }
    
    public function readClasses()
    {
        $classes = EventClass::all();
        
        return response()->json(['data' => $classes], 200);
    }
    
    public function readGameCategories()
    {
        $game_categories = GameCategory::all();
        
        return response()->json(['data' => $game_categories], 200);
    }

    public function readGameEntries($id)
    {
        $game_entries = Entry::with(['user', 'eventClass'])->where('game_id', $id)->get();
        
        return response()->json(['data' => $game_entries], 200);
    }
    
    public function readGamePlans()
    {
        $game_plans = GamePlan::all();
        
        return response()->json(['data' => $game_plans], 200);
    }
    
    public function readTimekeepingCards()
    {
        $timekeeping_cards = TimekeepingCard::all();
        
        return response()->json(['data' => $timekeeping_cards], 200);
    }

    public function readEntryUser($game_id, $user_id)
    {
        $entry = Entry::with(['user', 'eventClass'])->where([['user_id', $user_id], ['game_id', $game_id]])->first();
        
        if ($entry['user']['customer_id']) {
            \Stripe\Stripe::setApiKey(config('services.stripe.api_secret'));
            $customer = \Stripe\Customer::retrieve($entry['user']['customer_id']);
            $entry['user']['postcode'] = $customer['address']['postal_code'];
            $entry['user']['address'] = $customer['address']['line1'];
            $entry['user']['phone'] = $customer['phone'];
            $entry['user']['emg_phone'] = $customer['metadata']['emg_phone'];
            $entry['user']['emg_relation'] = $customer['metadata']['emg_relation'];
        }
        
        return response()->json(['data' => $entry], 200);
    }

    public function createEntryList($id)
    {
        $game_entries = Entry::with(['user', 'eventClass'])->where('game_id', $id)->get();

        $csvExporter = new \Laracsv\Export();

        $csvExporter->beforeEach(function ($game_entry) {
            $game_entry->name = $game_entry['user']['name'];
            $game_entry->game_class = $game_entry['eventClass']['class_name'];
        });

        $csvExporter->build($game_entries, [
            'game_class' => 'クラス',
            'name' => '氏名',
            'belonging' => '所属',
            'timekeeping_card_num' => 'カード番号',
        ]);

        $csvReader = $csvExporter->getReader();

        $csvReader->setOutputBOM(\League\Csv\Reader::BOM_UTF8);

        $filename = 'entlylist.csv';
        
        return response((string) $csvReader)
        ->header('Content-Type', 'text/csv; charset=UTF-8')
        ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }
}
