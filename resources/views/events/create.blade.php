<x-app-layout>
<div class="py-12">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-4xl font-bold text-gray-800 mb-6">
            イベント新規登録
        </div>

        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- カテゴリ -->
            <div class="mb-3">
                <label for="category" class="block text-sm font-bold text-gray-700 mb-1">カテゴリ</label>
                <select name="category_id" id="category" class="w-full border-gray-300 rounded-md  focus:ring-blue-500 focus:border-blue-500" required>
                    @if($categories->isNotEmpty())
                        <option value="">選択してください</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>
                                {{ $category->category_name }}
                            </option>

                        @endforeach
                    @else
                        <option value="0" selected>カテゴリなし</option>
                    @endif
                </select>
                @error('category_id')<div class="text-danger">{{ $message }}</div>@enderror
            </div>

            <!-- イベント名称 -->
            <div class="mb-3">
                <label for="name" class="block text-sm font-bold text-gray-700 mb-1">名称</label>
                <input type="text" name="event_name" id="name" class="w-full border-gray-300 rounded-md  focus:ring-blue-500 focus:border-blue-500" maxlength="50" required>
            </div>

            <!-- 画像選択 -->
            <div class="mb-3">
                <label class="block text-sm font-bold text-gray-700 mb-1">画像の選択</label>
                    <div id="image-fields">
                        <div class="flex items-center mb-2">
                            <input type="file" name="images[]" class="w-full border border-gray-300 rounded-md " />
                            <button type="button" class="add-image px-2 py-1 border rounded">＋</button>
                        </div>
                    </div>
            </div>

            <!-- イベント説明 -->
            <div class="mb-3">
                <label for="description" class="block text-sm font-bold text-gray-700 mb-1">説明</label>
                <textarea name="description" id="description" class="w-full border-gray-300 rounded-md  focus:ring-blue-500 focus:border-blue-500" rows="4" required></textarea>
            </div>

            <!-- 開催日 -->
            <div class="mb-3">
                <label for="date" class="block text-sm font-bold text-gray-700 mb-1">開催日</label>
                <input type="date" name="date" id="date" class="w-full border-gray-300 rounded-md  focus:ring-blue-500 focus:border-blue-500" min="{{ date('Y-m-d') }}" required>
            </div>

            <!-- 開催時刻 -->
            <div class="mb-3">
                <label for="time" class="block text-sm font-bold text-gray-700 mb-1">開催時刻</label>
                <input type="time" name="time" id="time" class="w-full border-gray-300 rounded-md  focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- 開催場所 -->
            <div class="mb-3">
                <label for="location" class="block text-sm font-bold text-gray-700 mb-1">場所</label>
                <input type="text" name="location" id="location" class="w-full border-gray-300 rounded-md  focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- 登録ボタン -->
            <div class="text-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md shadow">
                    登録
                </button>
            </div>
        </form>
    </div>
    
    <!-- JavaScript for dynamic image fields -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const imageFields = document.getElementById('image-fields');

            imageFields.addEventListener('click', function (e) {
                if (e.target.classList.contains('add-image')) {
                // 新しい行を作成
                const newGroup = document.createElement('div');
                newGroup.classList.add('flex', 'items-center', 'mb-2');

                const input = document.createElement('input');
                input.type = 'file';
                input.name = 'images[]';
                input.classList.add('w-full', 'border', 'border-gray-300', 'rounded-md');

                // 追加ボタン（＋）
                const addBtn = document.createElement('button');
                addBtn.type = 'button';
                addBtn.classList.add('add-image', 'px-2', 'py-1', 'border', 'rounded');
                addBtn.textContent = '＋';

                // 削除ボタン（－）
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.classList.add('remove-image', 'px-2', 'py-1', 'border', 'rounded');
                removeBtn.textContent = '－';

                newGroup.appendChild(input);
                newGroup.appendChild(addBtn);
                newGroup.appendChild(removeBtn);

                imageFields.appendChild(newGroup);
                }

                if (e.target.classList.contains('remove-image')) {
                // クリックされた行だけ削除
                const group = e.target.closest('div.flex.items-center');
                if (group) {
                    group.remove();
                }
                }
            });
        });
    </script>
</div>
</x-app-layout>