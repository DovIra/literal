<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    //
    public function index()
    {
        $events = Event::all();
        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'location' => 'required|string|max:255',
        ]);

        
        Event::create(array_merge($validated, ['user_id' => Auth::id()]));

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function show(Event $id)
    {
        return view('events.show', ['event' => $id]);
    }

    public function edit(Event $id){
        return view('events.edit', ['event' => $id]);
    }

    public function update(Request $request, Event $id){
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'location' => 'required|string|max:255',
        ]);

        $id->update($validated);

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $id){
        $id->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
