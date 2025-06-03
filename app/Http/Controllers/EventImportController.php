<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Event;
use App\Enums\UserType;
use App\Enums\Type;
use Illuminate\Support\Facades\DB;


class EventImportController extends Controller
{
    public function showImportForm()
    {
        if (Auth::user()->user_type !== UserType::Admin->value) {
            return redirect()->route('dashboard')->with('error', '管理者専用ページにアクセスしようとしました。');
        }

        return view('events.import');
    }

    public function import(Request $request)
    {
        if (Auth::user()->user_type !== UserType::Admin->value) {
            return redirect()->route('dashboard')->with('error', '不正な操作です。');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $file = fopen($path, 'r');
        $header = fgetcsv($file); // ヘッダー取得
        $imported = 0;

        try {
            DB::transaction(function () use ($file, $header, &$imported) {
                while (($row = fgetcsv($file)) !== false) {
                    // 空行はスキップ
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // 列数チェック
                    if (count($header) !== count($row)) {
                        throw new \Exception('CSVの列数が一致しません（不正な形式の行があります）。');
                    }

                    $data = array_combine($header, $row);

                    // バリデーション
                    $validator = Validator::make($data, [
                        'event_name' => 'required|string|max:50',
                        'category_id' => 'required|integer|exists:categories,id',
                        'description' => 'required|string',
                        'event_date' => 'required|date',
                        'location' => 'required|string|max:50',
                    ]);

                    if ($validator->fails()) {
                        throw new \Exception('CSVのバリデーションエラー: ' . json_encode($validator->errors()->all()));
                    }

                    // CSVにない情報を補完
                    $data['created_by'] = Auth::id() ?? 0;
                    $data['updated_by'] = Auth::id() ?? 0;

                    // イベント登録
                    $event = \App\Models\Event::create($data);
                    $imported++;

                    // 通知作成
                    $users = \App\Models\User::all();
                    foreach ($users as $user) {
                        \App\Models\Notification::create([
                            'event_id' => $event->id,
                            'type' => \App\Enums\Type::NewEvent->value,
                            'user_id' => $user->id,
                            'created_by' => Auth::id() ?? 0,
                            'updated_by' => Auth::id() ?? 0,
                        ]);
                    }
                }
            });

            return back()->with(['success' => "{$imported} 件のイベントをインポートしました。"]);

        } catch (\Exception $e) {
            \Log::error("インポートエラー: " . $e->getMessage());
            return back()->withErrors(['error' => "インポートに失敗しました: " . $e->getMessage()]);
        } finally {
            fclose($file);
        }
    }

    // CSVテンプレートをダウンロード
    public function downloadTemplate()
    {
        $filePath = storage_path('app/public/events_template.csv');

        if (!file_exists($filePath)) {
            abort(404, 'CSVファイルが見つかりません。');
        }

        return response()->download($filePath, 'event_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
