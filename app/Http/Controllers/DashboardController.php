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

        // EventReminder：申込済＋開催1週間前〜当日のイベント
        $eventReminderNotifications = Notification::with('event')
            ->where('type', Type::EventReminder->value)
            ->whereIn('event_id', $joinedEventIds)
            ->whereHas('event', function ($query) use ($today) {
                $query->whereBetween('event_date', [$today->copy(), $today->copy()->addDays(7)]);
            })
            ->get()
            ->filter(function ($notification) use ($today) {
                // 開催当日まで通知有効
                $eventDate = optional($notification->event)->event_date;
                return $eventDate && Carbon::parse($eventDate)->gte($today);
            })
            ->map(function ($notification) {
                // 通知開始日は 開催日の7日前
                $notification->virtual_start_at = optional($notification->event)->event_date
                    ? Carbon::parse($notification->event->event_date)->subDays(7)
                    : $notification->created_at;
                return $notification;
            });

        // NewEvent：イベント作成から10日以内
        $newEventNotifications = Notification::with('event')
            ->where('type', Type::NewEvent->value)
            ->whereHas('event', function ($query) {
                $query->where('created_at', '>=', now()->subDays(10));
            })
            ->get()
            ->filter(function ($notification) {
                // 通知作成日時から10日以内
                return $notification->created_at->gte(now()->subDays(10));
            })
            ->unique('event_id')  // イベントごとにユニークにする
            ->map(function ($notification) {
                // 通知開始日は通知作成日時
                $notification->virtual_start_at = $notification->created_at;
                return $notification;
            });

        // 通知をマージして、通知開始日の降順でソート
        $notifications = $eventReminderNotifications
            ->merge($newEventNotifications)
            ->sortByDesc('virtual_start_at')
            ->values(); // ループ制御用にキーリセット

        return view('dashboard', compact('events', 'notifications'));
    }
}
