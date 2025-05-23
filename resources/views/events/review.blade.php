<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('events.review.store', ['id' => $event->id]) }}">
                    @csrf

                    <!-- レーティング -->
                    <div class="mb-4">
                        <label for="rating" class="block text-sm font-medium text-gray-700">レーティング (1〜5)</label>
                        <select name="rating" id="rating" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        @error('rating')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- コメント -->
                    <div class="mb-4">
                        <label for="comment" class="block text-sm font-medium text-gray-700">コメント</label>
                        <textarea name="comment" id="comment" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 送信ボタン -->
                    <div class="text-right">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            送信
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>