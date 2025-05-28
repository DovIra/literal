<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
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
                カテゴリーを追加
            </div>

            <!-- カテゴリ追加フォーム -->
            <div class="bg-white p-6 sm:rounded-lg border border-gray-300 mb-6">
                <div class="text-xl font-bold text-gray-800 mb-4">
                    カテゴリー名
                </div>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <input type="text" name="name" placeholder='カテゴリー名' class="border rounded px-3 py-1 w-full block mb-4">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">
                        追加
                    </button>
                </form>
            </div>

            <!-- カテゴリ一覧 -->
            @if ($categories->isNotEmpty())
                @foreach ($categories as $category)
                    <div class="
                        flex flex-row w-full bg-white p-4 space-x-2 
                        border-x border-t border-gray-300 
                        @if ($loop->first) rounded-t-lg 
                        @elseif ($loop->last) border-b rounded-b-lg 
                        @else rounded-none 
                        @endif">
                        
                        <span class="text-gray-600 whitespace-nowrap">
                            ID: {{ $category->id }}
                        </span>

                        <!-- 編集フォーム -->
                        <form action="{{ route('categories.update', $category->id) }}"
                                method="POST" class="flex space-x-2 w-full">
                            @csrf
                            @method('PUT')
                            <input
                                id="input-{{ $category->id }}"
                                type="text"
                                name="name"
                                value="{{ old('name_'.$category->id, $category->category_name) }}"
                                onblur="handleBlur(this, '{{ $category->category_name }}')"
                                class="border rounded px-3 py-1 flex-1"
                                required>

                            <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 whitespace-nowrap">
                                編集
                            </button>
                        </form>

                        <!-- 削除フォーム -->
                        <form action="{{ route('categories.destroy', $category->id) }}"
                                method="POST"
                                onsubmit="return confirm('本当に削除しますか？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 whitespace-nowrap">
                                削除
                            </button>
                        </form>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <script>
        // フォーカスが外れたときのみチェック
        function handleBlur(input, originalValue) {
            if (input.value.trim() === '') {
                input.value = originalValue;
            }
        }
    </script>
</x-app-layout>
