<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @auth
                @php $userId = auth()->id(); @endphp
            @endauth

            <div class="text-3xl font-bold text-gray-800 mb-6">
                イベントを探す
            </div>

            <form method="GET" action="{{ route('events.index') }}" class="mb-4" id="searchForm">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search events"
                    class="form-control mb-2"
                    id="searchInput"
                    autocomplete="off"
                >

                <label for="categorySelect" class="form-label">Filter by:</label>
                <select name="category_id" id="categorySelect" class="form-control mb-2">
                    <option value="">すべて</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>

            </form>

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
                                <span class="text-green-600 text-sm font-medium">申込み済</span>
                            @else
                                <span class="text-red-600 text-sm font-medium">未申し込み</span>
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
                            <span class="text-gray-500 text-sm font-semibold cursor-not-allowed" title="準備中です">
                                詳細を見る →
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600"></p>
            @endif
        </div>
    </div>
</x-app-layout>