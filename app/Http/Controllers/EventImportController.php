<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Event;
use App\Enums\UserType;
use App\Enums\Type;

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
        $header = fgetcsv($file); // 1行目をヘッダーとして取得

        $skipped = 0;
        $skippedRows = [];
        $imported = 0;
        $rowIndex = 1;

        while (($row = fgetcsv($file)) !== false) {
            $rowIndex++;
            
            // 空行スキップ（全要素が空）
            if (count(array_filter($row, fn($v) => trim($v) !== '')) === 0) {
                continue;
            }

            // 列数チェック
            if (count($row) !== count($header)) {
                $skipped++;
                $skippedRows[] = [
                    'line' => $rowIndex,
                    'errors' => ['general' => ['列数がヘッダーと一致しません（' . count($row) . ' / ' . count($header) . '）'],],
                ];
                continue;
            }

            $data = array_combine($header, $row);

            // バリデーション
            $validator = Validator::make($data, [
                'event_name' => 'required|string|max:50',
                'category_id' => 'required|integer|exists:categories,id',
                'filename' => 'nullable|string',
                'description' => 'required|string',
                'event_date' => 'required|date',
                'location' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                $skipped++;
                $skippedRows[] = [
                    'line' => $rowIndex,
                    'errors' => $validator->errors()->toArray(),
                ];
                continue;
            }

            // filenameを配列に変換
            if (!empty($data['filename'])) {
                $data['filename'] = array_map('trim', explode(',', $data['filename']));
            } else {
                $data['filename'] = [];
            }

            // CSVにない情報を補完
            $data['created_by'] = Auth::id() ?? 0;
            $data['updated_by'] = Auth::id() ?? 0;

            // 保存
            $event = \App\Models\Event::create($data);
            $imported++;

            // 通知対象のユーザー一覧を取得（全ユーザーに通知する場合）
            $users = \App\Models\User::all(); // 必要に応じて条件を絞る

            foreach ($users as $user) {
                try {
                    \App\Models\Notification::create([
                        'event_id' => $event->id,
                        'type' => Type::NewEvent->value,
                        'user_id' => $user->id,
                        'created_by' => Auth::id() ?? 0,
                        'updated_by' => Auth::id() ?? 0,
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // 重複通知のユニーク制約に違反した場合はスキップ
                    continue;
                }
            }
        }

        fclose($file);

        return back()->with([
            'success' => "{$imported} 件のイベントをインポートしました。",
            'skipped' => $skipped,
            'skippedRows' => $skippedRows,
            'total' => $imported + $skipped,
        ]);
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
