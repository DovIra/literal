<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @auth
                @php $userId = auth()->id(); @endphp
            @endauth

            <div class="text-4xl font-bold text-gray-800 mb-6">
                開催が近いイベント
            </div>

            @if ($events->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    @foreach ($events as $event)
                        <div class="bg-white border border-gray-300 rounded-lg p-4 flex flex-col items-start space-y-2">
                            
                            {{-- ラベル --}}
                            @if ($event->participants->contains('id', $userId))
                                <span class="bg-green-600 text-white text-sm font-medium px-2 py-1 rounded">申込み済</span>
                            @else
                                <span class="bg-gray-500 text-white text-sm font-medium px-2 py-1 rounded">未申し込み</span>
                            @endif

                            {{-- イベント名 --}}
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ $event->event_name }}
                            </h2>

                            {{-- イベント説明（1行省略） --}}
                            <p class="text-gray-700 text-sm overflow-hidden text-ellipsis whitespace-nowrap w-full">
                                {{ $event->description }}
                            </p>

                            {{-- 詳細ボタン --}}
                            <a href="{{ route('events.show', $event->id) }}"
                                class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                詳細を見る
                            </a>

                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600"></p>
            @endif

            <div class="text-4xl font-semibold text-gray-800 mb-6">
                通知
            </div>
            @if ($notifications->count() > 0)
                <div class="max-w-7xl mx-auto mt-10 px-4">
                    @foreach ($notifications as $notification)
                        <div class="
                            w-full bg-white p-4
                            border-x border-t border-gray-300
                            @if ($loop->first) rounded-t-xl @endif
                            @if ($loop->last) border-b rounded-b-xl @else rounded-none @endif
                        ">
                            {{-- 通知タイトル --}}
                            <div class="text-base font-bold text-gray-900 mb-1">
                                @if ($notification->type == \App\Enums\Type::EventReminder->value)
                                    {{ $notification->event->event_name }} が間もなく始まります。
                                @elseif ($notification->type == \App\Enums\Type::NewEvent->value)
                                    新しい {{ $notification->event->event_name }} が登録されました。
                                @endif
                            </div>

                            {{-- 通知メッセージ --}}
                            <div class="text-sm text-gray-700">
                                @php
                                    $formattedDate = \Carbon\Carbon::parse($notification->event->event_date)->format('Y年m月d日 H:i');
                                @endphp

                                @if ($notification->type == \App\Enums\Type::EventReminder->value)
                                    {{ $notification->event->event_name }} が {{ $formattedDate }} に開催されます。お忘れなくご参加ください！
                                @elseif ($notification->type == \App\Enums\Type::NewEvent->value)
                                    {{ $notification->event->event_name }} が {{ $formattedDate }} に開催されます。ぜひご参加ください！
                                @endif
                            </div>

                            {{-- 開催場所 --}}
                            <div class="text-xs text-gray-600 mt-2">
                                開催場所：{{ $notification->event->location }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
