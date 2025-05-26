<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // 年・月をリクエストから取得 or 現在の年月を設定
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // 月初と月末をCarbonで作成
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

        // 月内のイベントを日時昇順で取得し、日付ごとにグルーピング
        $events = Event::whereBetween('event_date', [$startOfMonth, $endOfMonth])
            ->orderBy('event_date', 'asc')
            ->get()
            ->groupBy(function ($event) {
                return $event->event_date->format('Y-m-d'); // '2025-05-26' の形でグルーピング
            });

        // ビューに必要な情報を渡して返す
        return view('calendar.index', [
            'year' => $year,
            'month' => $month,
            'startOfMonth' => $startOfMonth,
            'events' => $events,
        ]);
    }
}
