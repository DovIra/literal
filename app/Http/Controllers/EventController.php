<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\User;
use App\Models\Notification;
use App\Enums\Type;



class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('category')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $keyword = $request->input('search');

            $query->where(function ($q) use ($keyword) {
                $q->where('event_name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%")
                ->orWhere('location', 'like', "%{$keyword}%")
                ->orWhereDate('event_date', 'like', "%{$keyword}%") // 開催日
                ->orWhereTime('event_date', 'like', "%{$keyword}%") // 開催時刻
                ->orWhereHas('category', function ($q2) use ($keyword) {
                    $q2->where('category_name', 'like', "%{$keyword}%");
                });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $events = $query->get();

        $categories = Category::all(); // カテゴリ一覧

        return view('events.index', compact('events', 'categories'));
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
                ? 'required|exists:categories,id'  // 通常：カテゴリIDはDBに存在する必要あり
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
                'type' => Type::NewEvent->value,
                'created_by' => Auth::id() ?? 0,
                'updated_by' => Auth::id() ?? 0,
            ]);
        }
        
        return redirect()->route('login');
    }
}
