<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="text-4xl font-bold text-gray-800 mb-6">
                イベントをまとめて作成
            </div>

            <a href="{{ route('events.downloadTemplate') }}" 
                class="inline-block bg-gray-500 hover:bg-gray-600 text-white text-sm px-4 py-2 rounded cursor-pointer select-none">
                CSVテンプレートをダウンロード
            </a>

            <div class="mt-4 px-4 pb-4 border rounded">
                <form action="{{ route('events.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">CSVファイルを選択</label>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>

                    @if ($errors->any())
                        <ul class="mt-2 list-disc list-inside text-sm px-3 py-3 bg-red-100 text-red-700 rounded">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-md transition">
                        アップロード
                    </button>
                </form>

                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-3 rounded mt-4 mb-4">
                        {{ session('success') }}
                        （全 {{ session('total') }} 件中、成功: {{ session('total') - session('skipped') }} 件 / 失敗: {{ session('skipped') }} 件）
                    </div>
                @endif

                @if(session('skipped') && session('skipped') > 0)
                    <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-4 text-sm">
                        <strong>{{ session('skipped') }} 件の行がバリデーションエラーでスキップされました。</strong>
                        <ul class="mt-3 list-disc list-inside space-y-2">
                            @foreach(session('skippedRows') as $row)
                                <li>
                                    <div class="font-semibold">行 {{ $row['line'] }} のエラー:</div>
                                    <ul class="ml-4 list-disc list-inside">
                                        @foreach($row['errors'] as $field => $messages)
                                            @foreach($messages as $msg)
                                                <li>{{ $field }}: {{ $msg }}</li>
                                            @endforeach
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>