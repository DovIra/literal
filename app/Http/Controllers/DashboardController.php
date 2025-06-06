<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Notification;
use App\Enums\Type;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();                         // 今日
        $twoWeeksLater = Carbon::today()->addWeeks(2);      // 2週間後
        
        $events = Event::whereBetween('event_date', [$today, $twoWeeksLater])
                    ->orderBy('event_date', 'desc')
                    ->get();

        // 申し込んでいるイベントID一覧
        $joinedEventIds = $user->joinedEvents()->pluck('events.id');

        // EventReminder：ログインユーザの通知かつ申込済＋開催1週間前〜当日のイベント
        $eventReminderNotifications = Notification::with('event')
            ->where('user_id', $user->id)
            ->where('type', Type::EventReminder)
            ->whereIn('event_id', $joinedEventIds)
            ->whereHas('event', function ($query) {
                // SQL上で「event_date BETWEEN 今日 〜 今日 + 7日」
                $query->whereBetween(DB::raw('DATE(event_date)'), [
                    now()->startOfDay()->toDateString(),
                    now()->addDays(7)->endOfDay()->toDateString(),
                ]);
            })
            ->get();


        // NewEvent：ログインユーザの通知かつイベント作成から10日以内
        $newEventNotifications = Notification::with('event')
            ->where('user_id', $user->id)
            ->where('type', Type::NewEvent)
            ->whereDate('created_at', '>=', now()->subDays(9)->startOfDay()) // 10日以内
            ->whereHas('event', function ($query) {
                $today = now()->toDateString();
                $query->whereDate('event_date', '>=', $today); // 今日以降のイベント
            })
            ->get()
            ->unique('event_id');


        // マージして通知開始日の降順でソート
        $notifications = $eventReminderNotifications
            ->merge($newEventNotifications)
            ->sortByDesc('virtual_start_at')
            ->values() // インデックスをリセット
            ->map(function ($notification) {
                $notification->title;
                $notification->message;

                $notification->virtual_start_at = $notification->created_at;

                return $notification;
            });


        return view('dashboard', compact('events', 'notifications'));
    }
}
