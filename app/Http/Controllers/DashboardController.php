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
                $query->whereBetween('event_date', [$today->copy()->subDays(0), $today->copy()->addDays(7)]);
            })
            ->get();

        // NewEvent：イベント作成から10日以内
        $newEventNotifications = Notification::with('event')
            ->where('type', Type::NewEvent->value)
            ->whereHas('event', function ($query) {
                $query->where('created_at', '>=', now()->subDays(10));
            })
            ->get();

        // 通知をマージ
        $notifications = $eventReminderNotifications->merge($newEventNotifications);

        return view('dashboard', compact('events', 'notifications'));
    }
}
