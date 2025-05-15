<x-app-layout>
    <div class="py-8 px-24">
        <!-- ステータスメッセージ -->
        @if (session('status'))
            <div class="mb-6 text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <div class="text-2xl font-bold text-gray-800 mb-6">
            プロフィール
        </div>

        @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
        @endif
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <!-- 名前 -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">名前</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="mt-1 w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <!-- メールアドレス -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">メールアドレス</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="mt-1 w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <!-- パスワード -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">パスワード</label>
                <input type="password" name="password" placeholder="new password"
                    class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <!-- 更新ボタンを右端に配置 -->
            <div class="mt-6 flex justify-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    更新
                </button>
            </div>
        </form>
    </div>
</x-app-layout>