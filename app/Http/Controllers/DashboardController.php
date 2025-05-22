<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Notification;
use App\Enums\Type;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            ->where('type', Type::EventReminder->value)
            ->whereIn('event_id', $joinedEventIds)
            ->whereHas('event', function ($query) use ($today) {
                $query->whereBetween('event_date', [$today->copy(), $today->copy()->addDays(7)]);
            })
            ->get()
            ->filter(function ($notification) use ($today) {
                $eventDate = optional($notification->event)->event_date;
                return $eventDate && Carbon::parse($eventDate)->gte($today);
            })
            ->map(function ($notification) {
                $notification->virtual_start_at = $notification->created_at;
                return $notification;
            });

        // NewEvent：ログインユーザの通知かつイベント作成から10日以内
        $newEventNotifications = Notification::with('event')
            ->where('user_id', $user->id)
            ->where('type', Type::NewEvent->value)
            ->whereHas('event', function ($query) {
                $query->where('created_at', '>=', now()->subDays(10));
            })
            ->get()
            ->filter(function ($notification) {
                return $notification->created_at->gte(now()->subDays(10));
            })
            ->unique('event_id')
            ->map(function ($notification) {
                $notification->virtual_start_at = $notification->created_at;
                return $notification;
            });

        // マージして通知開始日の降順でソート
        $notifications = $eventReminderNotifications
            ->merge($newEventNotifications)
            ->sortByDesc('virtual_start_at')
            ->values(); // インデックスをリセット

        return view('dashboard', compact('events', 'notifications'));
    }
}
