<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @auth
                @php $userId = auth()->id(); @endphp
            @endauth

            @if(session('error'))
                <div class="bg-red-200 text-red-800 px-4 py-2 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-200 text-green-800 px-4 py-2 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="text-4xl font-bold text-gray-800 mb-6">
                イベント詳細
            </div>

            {{-- ラベル --}}
            @if ($event->participants->contains('id', $userId))
                <span class="bg-green-600 text-white text-sm font-medium px-2 py-1 rounded">申込み済</span>
            @else
                <span class="bg-gray-500 text-white text-sm font-medium px-2 py-1 rounded">未申し込み</span>
            @endif

            <div class="bg-white border border-gray-300 rounded-lg overflow-hidden mt-4 mb-4">
                <!-- ヘッダー部分（グレー背景） -->
                <div class="bg-gray-100 px-4 py-2 border-b border-gray-300">
                    概要
                </div>

                <!-- 内容部分（白背景） -->
                <div class="bg-white px-4 py-4 space-y-2">
                    {{-- イベント名 --}}
                    <h2 class="text-lg font-semibold text-gray-900">
                        名称：{{ $event->event_name }}
                    </h2>

                    {{-- カテゴリ --}}
                    <p class="text-gray-900">
                        カテゴリ：{{ $event->category->category_name }}
                    </p>

                    {{-- 開催日時 --}}
                    <p class="text-gray-900">
                        開催日：{{ $eventDate->format('Y年m月d日') }}
                    </p>
                    <p class="text-gray-900">
                        開催時刻：{{ $eventDate->format('H:i') }}
                    </p>

                    {{-- 開催場所 --}}
                    <p class="text-gray-900">
                        開催場所：{{ $event->location }}
                    </p>

                    {{-- 主催者 --}}
                    <p class="text-gray-900">
                        主催者：{{ $event->creator->name }}
                    </p>
                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded-lg overflow-hidden mb-4">
                <!-- ヘッダー部分（グレー背景） -->
                <div class="bg-gray-100 px-4 py-2 border-b border-gray-300">
                    画像
                </div>

                <!-- 内容部分（白背景） -->
                <div class="bg-white px-4 py-4 space-y-2">
                    @if (!empty($event->filename))
                        <div class="flex flex-wrap gap-4">
                            @foreach ($event->filename as $image)
                                <div class="w-1/2 md:w-1/4">
                                    <img src="{{ asset('storage/' . $image) }}" alt="画像" class="w-full rounded shadow">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p>画像はありません。</p>
                    @endif
                </div>
            </div> 

            <div class="bg-white border border-gray-300 rounded-lg overflow-hidden mb-4">
                <!-- ヘッダー部分（グレー背景） -->
                <div class="bg-gray-100 px-4 py-2 border-b border-gray-300">
                    詳細
                </div>

                <!-- 内容部分（白背景） -->
                <div class="bg-white px-4 py-4 space-y-2">
                    {{-- イベント説明 --}}
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $event->description }}
                    </p>
                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded-lg overflow-hidden mb-4">
                <!-- ヘッダー部分（グレー背景） -->
                <div class="bg-gray-100 px-4 py-2 border-b border-gray-300">
                    参加者
                </div>

                <!-- 内容部分（白背景） -->
                <div class="bg-white px-4 py-4 space-y-2">
                    @if ($sortedParticipants->isEmpty())
                        <p class="text-gray-500">参加者はいません。</p>
                    @else
                        <table class="w-full border border-gray-300 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border px-2 py-1 text-left w-16">連番</th>
                                    <th class="border px-2 py-1 text-left">名前</th>
                                    <th class="border px-2 py-1 text-left">メールアドレス</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sortedParticipants as $index => $participant)
                                    <tr class="border-t">
                                        <td class="border px-2 py-1">{{ $index + 1 }}</td>
                                        <td class="border px-2 py-1">
                                            {{ $participant->user->name }}
                                            @if ($participant->user_id === $currentUserId)
                                                <span class="text-green-500 text-xs font-semibold ml-1">(あなた)</span>
                                            @endif
                                        </td>
                                        <td class="border px-2 py-1">{{ $participant->user->email }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
            {{-- 編集・削除ボタン（開催日以前、イベント作成者のみ） --}}
            <div class="flex justify-end space-x-2 mt-4">
                @if (!$isTodayOrAfter && auth()->id() === $event->created_by)
                    <a href="{{ route('events.edit', ['id' => $event->id]) }}"
                    class="inline-block bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded cursor-pointer select-none">
                    編集
                    </a>
                @endif
                @if (auth()->id() === $event->created_by)
                    <form action="{{ route('events.destroy', ['id' => $event->id]) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded"
                            onclick="return confirm('本当に削除しますか？');">
                            削除
                        </button>
                    </form>
                @endif
            </div>
            @if (!$isTodayOrAfter)
                {{-- 参加ボタン(開催日以前) --}}
                <div class="flex justify-end mt-2">
                    @auth
                        @if ($isParticipant)
                            {{-- 参加キャンセル（参加者のみ）--}}
                            <form method="POST" action="{{ route('events.cancel', ['id' => $event->id]) }}">
                                @csrf
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded"
                                    onclick="return confirm('参加をキャンセルしてもよろしいですか？');">
                                    参加をキャンセルする
                                </button>
                            </form>
                        @else
                            {{-- 参加する（非参加者のみ）--}}
                            <form method="POST" action="{{ route('events.join', ['id' => $event->id]) }}">
                                @csrf    
                                <button type="submit"
                                    class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded">
                                    参加する
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            @else
                {{-- レビューボタン（開催日以降、参加者のみ、未レビューのみ） --}}
                <div class="flex justify-end mt-2">
                    @auth
                        @if ($isParticipant && $hasReviewed)
                            <a href="{{ route('events.review.form', ['id' => $event->id]) }}"
                                class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded">
                                レビューする
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>        
    </div>
</x-app-layout>