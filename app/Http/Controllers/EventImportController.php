<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Event;

class EventImportController extends Controller
{
    public function showImportForm()
    {
        return view('events.import');
    }

    public function import(Request $request)
    {
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

            // CSVにない情報を補完
            $data['created_by'] = Auth::id() ?? 0;
            $data['updated_by'] = Auth::id() ?? 0;

            // 保存
            \App\Models\Event::create($data);
            $imported++;
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
        $filePath = storage_path('app/public/event_template.csv');

        if (!file_exists($filePath)) {
            abort(404, 'CSVファイルが見つかりません。');
        }

        return response()->download($filePath, 'event_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
