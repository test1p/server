<?php

namespace App\Http\Controllers\Host;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;

class EventFileController extends Controller
{
    public function index($event_id)
    {
        $event = auth()->user()->events()->find($event_id);
        
        if (!$event) return response()->json(['errors' => '権限がありません'], 404);
        
        $event_files = $event->files;
        
        return response()->json(['data' => $event_files], 200);
    }

    public function store(Request $request, $event_id)
    {
        $event = auth()->user()->events()->find($event_id);
        
        if (!$event) return response()->json(['errors' => '権限がありません'], 404);
        
        $path = Storage::disk('s3')->put('/', $request->file('file_data'), 'public');
        
        $event_file = $event->files()->create([
            'file_name' => $request->file_name,
            'url' => Storage::disk('s3')->url($path),
            'path' => $path
        ]);
        
        return response()->json(['data' => $event_file], 201);
    }

    public function show($event_id, $id)
    {
        $event = auth()->user()->events()->find($event_id);
        
        if (!$event) return response()->json(['errors' => '権限がありません'], 404);
        
        $event_file = $event->files()->find($id);
        
        return response()->json(['data' => $event_file], 200);
    }

    public function update(Request $request, $event_id, $id)
    {
        $event = auth()->user()->events()->find($event_id);
        
        if (!$event) return response()->json(['errors' => '権限がありません'], 404);
        
        $path = Storage::disk('s3')->put('/', $request->file('file_data'), 'public');
        
        $event_file = $event->files()->find($id);
        Storage::disk('s3')->delete($event_file->path);
        $event_file->url = Storage::disk('s3')->url($path);
        $event_file->path = $path;
        $event_file->save();
        
        return response()->json(['data' => $event_file], 201);
    }

    public function destroy($event_id, $id)
    {
        $event = auth()->user()->events()->find($event_id);
        
        if (!$event) return response()->json(['errors' => '権限がありません'], 404);
        
        $event_file = $event->files()->find($id);
        Storage::disk('s3')->delete($event_file->path);
        $event_file->delete();
        
        return response()->json(['data' => $event_file], 201);
    }
}
