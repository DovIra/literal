<x-app-layout>
    <div class="py-12">
        @auth
            @php $userId = auth()->id(); @endphp
        @endauth

        @if(session('success'))
            <div class="bg-green-200 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-200 text-red-800 px-4 py-2 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="text-4xl font-bold text-gray-800 mb-6">
                    イベントを探す
                </div>
                <form method="GET" action="{{ route('events.index') }}" id="searchForm" class="flex flex-col space-y-4">
                    
                    {{-- 検索バー --}}
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search events"
                        class="w-full border border-gray-300 rounded px-4 py-2"
                        id="searchInput"
                        autocomplete="off"
                    >

                    {{-- カテゴリ選択 --}}
                    <div>
                        <label for="categorySelect" class="block text-sm font-medium text-gray-700 mb-1">Filter by:</label>
                        <select
                            name="category_id"
                            id="categorySelect"
                            class="w-full border border-gray-300 rounded px-4 py-2"
                        >
                            <option value="">すべて</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <script>
                document.getElementById('searchInput').addEventListener('blur', function() {
                    document.getElementById('searchForm').submit();
                });
                document.getElementById('categorySelect').addEventListener('change', function() {
                    document.getElementById('searchForm').submit();
                });
            </script>
            
            @if ($events->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($events as $event)
                        <div class="bg-white rounded-lg shadow-md p-4 flex flex-col items-start space-y-2">
                            
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
        </div>        
    </div>
</x-app-layout>