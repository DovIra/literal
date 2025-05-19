<x-app-layout>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container">
        <h2>イベント新規登録</h2>
        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- カテゴリ -->
            <div class="mb-3">
                <label for="category" class="form-label">カテゴリ</label>
                <select name="category_id" id="category" class="form-select" required>
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
                <label for="name" class="form-label">名称</label>
                <input type="text" name="event_name" id="name" class="form-control" maxlength="50" required>
            </div>

            <!-- 画像選択 -->
            <div class="mb-3">
                <label class="form-label">画像の選択</label>
                <div id="image-fields">
                    <div class="input-group mb-2">
                        <input type="file" name="images[]" class="form-control">
                        <button type="button" class="btn btn-outline-secondary add-image">＋</button>
                    </div>
                </div>
            </div>

            <!-- イベント説明 -->
            <div class="mb-3">
                <label for="description" class="form-label">説明</label>
                <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
            </div>

            <!-- 開催日 -->
            <div class="mb-3">
                <label for="date" class="form-label">開催日</label>
                <input type="date" name="date" id="date" class="form-control" min="{{ date('Y-m-d') }}" required>
            </div>

            <!-- 開催時刻 -->
            <div class="mb-3">
                <label for="time" class="form-label">開催時刻</label>
                <input type="time" name="time" id="time" class="form-control" required>
            </div>

            <!-- 開催場所 -->
            <div class="mb-3">
                <label for="location" class="form-label">場所</label>
                <input type="text" name="location" id="location" class="form-control" required>
            </div>

            <!-- 登録ボタン -->
            <div class="text-end">
                <button type="submit" class="btn btn-primary">登録</button>
            </div>
        </form>
    </div>
</x-app-layout>