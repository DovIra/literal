<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('events.review.store', ['id' => $event->id]) }}">
                    @csrf

                    <h2 class="text-2xl font-semibold mb-4">イベントのレビュー</h2>

                    <!-- レーティング（リアルタイム更新） -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">評価（1～5）</label>
                        <input type="hidden" name="rating" id="ratingInput" value="{{ old('rating', 0) }}">

                        <div id="starDisplay" class="flex text-2xl text-yellow-500 cursor-pointer select-none">
                            @for ($i = 1; $i <= 5; $i++)
                                <span data-value="{{ $i }}">
                                    @if ($i <= old('rating', 0))
                                        ★
                                    @else
                                        ☆
                                    @endif
                                </span>
                            @endfor
                        </div>

                        @error('rating')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- コメント -->
                    <div class="mb-4">
                        <label for="comment" class="block text-sm font-medium text-gray-700">コメント</label>
                        <textarea name="comment" id="comment" rows="4" placeholder='ご意見や感想をどうぞ…' class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('comment') }}</textarea>
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
            <!-- スクリプト -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const starDisplay = document.getElementById('starDisplay');
                    const ratingInput = document.getElementById('ratingInput');

                    starDisplay.querySelectorAll('span').forEach(star => {
                        star.addEventListener('click', function () {
                            const val = Number(this.dataset.value);
                            ratingInput.value = val;
                            updateStars(val);
                        });
                    });

                    function updateStars(rating) {
                        starDisplay.querySelectorAll('span').forEach(star => {
                            const val = Number(star.dataset.value);
                            star.textContent = val <= rating ? '★' : '☆';
                        });
                    }

                    // 初期表示反映
                    updateStars(Number(ratingInput.value));
                });
            </script>
        </div>
    </div>
</x-app-layout>