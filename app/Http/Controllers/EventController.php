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
use Illuminate\Support\Facades\DB;
use App\Models\EventParticipant;
use App\Models\EventReview;


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
            'filename' => $filenames ?: null,
            'description' => $validated['description'],
            'event_date' => $eventDateTime,
            'location' => $validated['location'],
            'created_by' => Auth::id() ?? 0,
            'updated_by' => Auth::id() ?? 0,
        ]);

        // 全ユーザー分 通知を作成
        $users = User::all();

        foreach ($users as $user) {
            Notification::firstOrCreate([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'type' => Type::NewEvent->value,
            ], [
                'created_by' => Auth::id() ?? 0,
                'updated_by' => Auth::id() ?? 0,
            ]);
        }
        
        return redirect()->route('login');
    }


    public function show($id)
    {
        $event = Event::with(['eventParticipants.user', 'participants', 'reviews.user'])->findOrFail($id);
        $currentUserId = Auth::id();

        // 参加順（created_at順）でソート
        $participants = $event->eventParticipants->sortBy('created_at')->values();

        // ログインユーザーがいれば最上位に
        $sortedParticipants = $participants->sortBy(function ($participant) use ($currentUserId) {
            return $participant->user_id === $currentUserId ? 0 : 1;
        })->values();

        // イベント日や今日の比較
        $eventDate = Carbon::parse($event->event_date);
        $isTodayOrAfter = Carbon::today()->greaterThanOrEqualTo($eventDate->copy()->startOfDay());

        // このユーザーがレビュー済みか？
        $hasReviewed = $event->reviews->contains('user_id', $currentUserId);

        // このユーザーが参加者か？
        $isParticipant = $event->participants->contains('id', $currentUserId);

        return view('events.show', compact(
            'event',
            'sortedParticipants',
            'currentUserId',
            'eventDate',
            'isTodayOrAfter',
            'hasReviewed',
            'isParticipant',
        ));
    }

    public function join(Request $request, $eventId)
    {
        $userId = auth()->id();

        // 既に参加しているか確認
        $exists = DB::table('event_participants')
            ->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();

        if (! $exists) {
            // 新規参加：レコードを挿入
            DB::table('event_participants')->insert([
                'event_id'   => $eventId,
                'user_id'    => $userId,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        } else {
            // 既存レコードがある場合：必要に応じて更新
            DB::table('event_participants')
                ->where('event_id', $eventId)
                ->where('user_id', $userId)
                ->update([
                    'updated_at' => now(),
                    'updated_by' => $userId,
                ]);
        }

        // 新たに通知 type=event reminder を作成
        DB::table('notifications')->insertOrIgnore([
            'event_id'   => $eventId,
            'user_id'    => $userId,
            'type'       => Type::EventReminder->value,
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        return redirect()->back();
    }


    public function cancelParticipation(Request $request, $eventId)
    {
        $userId = auth()->id();

        // 参加者テーブルから該当レコードを削除
        DB::table('event_participants')
            ->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->delete();

        // 通知テーブルから「event reminder」のみ物理削除（new eventは残す）
        DB::table('notifications')
            ->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->where('type', Type::EventReminder->value)
            ->delete();

        return redirect()->back()->with('status', '参加をキャンセルしました。');
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $categories = Category::all(); // カテゴリ一覧を渡す
        return view('events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'event_name' => 'required|string|max:50',
            'description' => 'required',
            'date' => 'required|date',
            'time' => 'required',
            'location' => 'required|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images.*' => 'nullable|integer',
        ]);

        $eventDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time)->toDateTimeString();

        // 元画像ファイル名
        $filenames = $event->filename ?? [];

        // チェックされた画像インデックス
        $deleteIndexes = $request->input('delete_images', []);

        // 削除：インデックスのズレを防ぐため降順にソート
        rsort($deleteIndexes);
        foreach ($deleteIndexes as $index) {
            if (isset($filenames[$index])) {
                Storage::disk('public')->delete('images/' . $filenames[$index]); // 実ファイル削除
                unset($filenames[$index]); // 配列から削除
            }
        }

        // 配列を再構成（インデックス詰め）
        $filenames = array_values($filenames);

        // 新規追加画像
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $path = $image->store('event_images', 'public'); // 同じフォルダに統一
                    $filenames[] = $path; // basename() は使わない
                }
            }
        }

        // 更新
        $event->update([
            'category_id' => $validated['category_id'],
            'event_name' => $validated['event_name'],
            'description' => $validated['description'],
            'event_date' => $eventDateTime,
            'location' => $validated['location'],
            'filename' => $filenames,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('events.show', $event->id)->with('success', 'イベントを更新しました');;
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $eventName = $event->event_name;        // 削除前に名前を別変数に保存しておく

        // 作成者チェック
        if (auth()->id() !== $event->created_by) {
            abort(403, '権限がありません');
        }

        // 参加者がいるかチェック
        $hasParticipants = EventParticipant::where('event_id', $id)->exists();

        if ($hasParticipants) {
            // 参加者がいるので削除不可。元ページへリダイレクトしてメッセージ表示
            return redirect()->back()->with('error', 'このイベントには参加者がいるため削除できません。');
        }

        // 参加者がいなければ削除
        $event->delete();

        return redirect()->route('events.index')->with('success', "イベント「{$eventName}」を削除しました");
    }

    public function reviewForm($id)
    {
        $event = Event::findOrFail($id);
        return view('events.review', compact('event'));
    }

    public function reviewStore(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        EventReview::create([
            'event_id'   => $id,
            'user_id'    => Auth::id(),
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('events.show', $id)
            ->with('success', 'レビューを投稿しました。');
    }

    public function reviewEdit($reviewId)
    {
        $review = EventReview::with('event')->where('id', $reviewId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $event = $review->event; // リレーションを使って取得

        return view('events.review_edit', compact('event', 'review'));
    }

    public function reviewDestroy($reviewId)
    {
        $review = EventReview::where('id', $reviewId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $eventId = $review->event_id;
        $review->delete();

        return redirect()->route('events.show', $eventId)->with('success', 'レビューを削除しました。');
    }
    
    public function reviewUpdate(Request $request, $reviewId)
    {
        $review = EventReview::where('id', $reviewId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review->update($validated);

        return redirect()->route('events.show', $review->event_id)->with('success', 'レビューを更新しました。');
    }

}
