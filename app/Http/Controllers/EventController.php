<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;

class EventController extends Controller
{
    public function index()
    {
        $today = Carbon::today();                         // 今日
        $twoWeeksLater = Carbon::today()->addWeeks(2);      // 2週間後
        
        $events = Event::whereBetween('event_date', [$today, $twoWeeksLater])
                    ->orderBy('event_date', 'desc')
                    ->get();

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $categories = Category::all(); // categories テーブルの全データ取得
        return view('events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $categoryTableHasData = Category::exists();

        $validated = $request->validate([
            'category_id' => $categoryTableHasData
                ? 'required|exists:categories.id'  // 通常：カテゴリIDはDBに存在する必要あり
                : 'required|in:0',                 // テーブルが空なら 0 のみ許可
            'event_name' => 'required|string|max:50',
            'description' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'location' => 'required|string|max:50',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // カテゴリID取得
        $categoryId = $validated['category_id'];
        if (!$categoryTableHasData) {
            $categoryId = 0; // テーブルが空のときは必ず 0
        }

        $eventDateTime = date('Y-m-d H:i:s', strtotime($validated['date'] . ' ' . $validated['time']));
        $filenames = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image) {
                    $path = $image->store('event_images', 'public');
                    $filenames[] = $path;
                }
            }
        }

        $event = Event::create([
            'event_name' => $validated['event_name'],
            'category_id' => $categoryId,
            'filename' => $filenames ? json_encode($filenames) : null,
            'description' => $validated['description'],
            'event_date' => $eventDateTime,
            'location' => $validated['location'],
            'created_by' => Auth::id() ?? 0,
            'updated_by' => Auth::id() ?? 0,
        ]);

        // 全ユーザー分 通知を作成
        $users = User::all();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'type' => 'new event',
                'created_by' => Auth::id() ?? 0,
                'updated_by' => Auth::id() ?? 0,
            ]);
        }
        
        return redirect()->route('login');
    }
}
