<!-- 次回変更点：その月ではない部分は灰色、その月の部分は青色で下線あり、
 今日は薄ピンクの背景、それ以外は白の背景、その月の部分だけイベント表示 -->
<x-app-layout>
    <div class="py-12 px-4 max-w-7xl mx-auto w-full"> 

        <!-- 上部ヘッダー -->
        <div class="text-4xl font-bold mb-4">カレンダー</div>
        <div class="flex justify-between items-center mb-4">
            <div class="text-4xl font-bold">{{ $year }}年{{ $month }}月</div>
            <div class="space-x-2">
                <a href="{{ route('calendar.index', ['year' => now()->year, 'month' => now()->month]) }}"
                   class="bg-blue-100 text-blue-800 px-3 py-1 rounded">Today</a>
                <a href="{{ route('calendar.index', ['year' => \Carbon\Carbon::create($year, $month)->subMonth()->year, 'month' => \Carbon\Carbon::create($year, $month)->subMonth()->month]) }}"
                   class="px-3 py-1 bg-gray-200 rounded">&lt;</a>
                <a href="{{ route('calendar.index', ['year' => \Carbon\Carbon::create($year, $month)->addMonth()->year, 'month' => \Carbon\Carbon::create($year, $month)->addMonth()->month]) }}"
                   class="px-3 py-1 bg-gray-200 rounded">&gt;</a>
            </div>
        </div>

        <!-- カレンダー -->
        @php
            $start = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
            $end = $startOfMonth->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
        @endphp

        <div class="grid gap-2 text-center text-sm" style="grid-template-columns: repeat(7, minmax(0, 1fr));"> 
            @foreach (['日','月','火','水','木','金','土'] as $day)
                <div class="font-bold">{{ $day }}</div>
            @endforeach

            @for ($date = $start->copy(); $date->lte($end); $date->addDay()) 
                @php
                    $dateStr = $date->format('Y-m-d');
                    $isToday = $date->isToday();
                @endphp

                <div class="p-2 border rounded h-32 text-left overflow-y-auto {{ $isToday ? 'bg-pink-100' : 'bg-white' }}">
                    <div class="text-xs font-semibold">{{ $date->day }}</div>

                    @if (isset($events[$dateStr]))
                        @foreach ($events[$dateStr] as $event)
                            <div class="text-xs text-blue-700 mt-1">
                                {{ $event->event_name }}
                            </div>
                        @endforeach
                    @endif
                </div>
            @endfor
        </div>
    </div>
</x-app-layout>
