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
            ->get()
            ->filter(function ($notification) {
                $today = now()->startOfDay();

                $eventDate = optional($notification->event)->event_date;
                if (!$eventDate) {
                    return false;
                }
                $eventDay = Carbon::parse($eventDate)->startOfDay();

                // 今日が「イベント開催日の7日前以上かつ当日以下」か判定
                // つまり eventDay - 7日 <= today <= eventDay
                return $today->between($eventDay->copy()->subDays(7), $eventDay);
            })
            ->map(function ($notification) {
                $notification->virtual_start_at = $notification->created_at;
                return $notification;
            });


        // NewEvent：ログインユーザの通知かつイベント作成から10日以内
        $newEventNotifications = Notification::with('event')
            ->where('user_id', $user->id)
            ->where('type', Type::NewEvent->value)
            ->get()
            ->filter(function ($notification) {
                $today = now()->startOfDay(); // 今日の日付（時刻なし）
                $createdAt = $notification->created_at->startOfDay(); // 通知作成日

                // 通知作成から10日以内か？
                $within10Days = $today->lte($createdAt->copy()->addDays(9));

                // イベント開催日が存在し、今日が開催日以前（当日含む）か？
                $eventDate = optional($notification->event)->event_date;
                if (!$eventDate) {
                    return false;
                }
                $eventDay = Carbon::parse($eventDate)->startOfDay();
                $beforeOrOnEventDay = $today->lte($eventDay);

                return $within10Days && $beforeOrOnEventDay;
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
