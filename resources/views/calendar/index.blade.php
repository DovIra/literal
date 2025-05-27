<x-app-layout>
    <div class="py-12 px-4 max-w-7xl mx-auto w-full"> 

        <!-- 上部ヘッダー -->
        <div class="text-4xl font-bold mb-4">カレンダー</div>
        <div class="flex justify-between items-center mb-4">
            <div class="text-4xl font-bold">{{ $year }}年{{ $month }}月</div>
            <div class="flex text-xl">
                {{-- today ボタン（右だけ余白） --}}
                <a href="{{ route('calendar.index', ['year' => now()->year, 'month' => now()->month]) }}"
                class="bg-gray-500 text-white px-3 py-1 rounded mr-4">today</a>

                {{-- ＜ 前月ボタン：左端なので左のみ角丸 --}}
                <a href="{{ route('calendar.index', ['year' => \Carbon\Carbon::create($year, $month)->subMonth()->year, 'month' => \Carbon\Carbon::create($year, $month)->subMonth()->month]) }}"
                class="px-3 py-1 bg-black text-white rounded-l">＜</a>

                {{-- ＞ 翌月ボタン：右端なので右のみ角丸 --}}
                <a href="{{ route('calendar.index', ['year' => \Carbon\Carbon::create($year, $month)->addMonth()->year, 'month' => \Carbon\Carbon::create($year, $month)->addMonth()->month]) }}"
                class="px-3 py-1 bg-black text-white rounded-r -ml-px">＞</a>
            </div>
        </div>

        <!-- カレンダー -->
        @php
            $start = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
            $end = $startOfMonth->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
        @endphp


        <div class="w-full overflow-x-auto">
            <table class="table-fixed border-collapse w-full text-sm text-right">
                <thead>
                    <tr>
                        @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                            <th class="border text-blue-600 text-center underline font-bold py-2">{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $date = $start->copy();
                    @endphp

                    @while ($date <= $end)
                        <tr>
                            @for ($i = 0; $i < 7; $i++)
                                @php
                                    $dateStr = $date->format('Y-m-d');
                                    $isToday = $date->isToday();
                                    $isCurrentMonth = $date->month === $startOfMonth->month;
                                    $cellBg = $isToday ? 'bg-pink-100' : 'bg-white';
                                @endphp

                                <td class="border align-top h-24 p-1 {{ $cellBg }}">
                                    @if ($isCurrentMonth)
                                        {{-- 表示月：青文字＋下線の日付＋イベント --}}
                                        <div class="font-semibold text-blue-600 underline">
                                            {{ $date->day }}
                                        </div>

                                        @if (isset($events[$dateStr]))
                                            @foreach ($events[$dateStr] as $event)
                                                <div class="text-left bg-blue-600 text-white mt-1 px-2 py-1 rounded-md block">
                                                    {{ $event->event_name }}
                                                </div>
                                            @endforeach
                                        @endif
                                    @else
                                        {{-- 表示月以外：灰色の日付のみ --}}
                                        <div class="font-semibold text-gray-400">
                                            {{ $date->day }}
                                        </div>
                                    @endif

                                    @php $date->addDay(); @endphp
                                </td>
                            @endfor
                        </tr>
                    @endwhile
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
