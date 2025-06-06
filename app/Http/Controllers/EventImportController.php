<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Event;
use App\Enums\UserType;
use App\Enums\Type;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\EventRequest;
use App\Http\Requests\ImportCsvRequest;

class EventImportController extends Controller
{
    public function showImportForm()
    {
        return view('events.import');
    }

    public function import(ImportCsvRequest $request)
    {
        $request->validated();

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $file = fopen($path, 'r');
        $csvheader = fgetcsv($file); // ヘッダー取得
        $imported = 0;

        try {
            DB::transaction(function () use (&$request, $file, $csvheader, &$imported) {
                $rowIndex = 1; // ヘッダー行

                while (($row = fgetcsv($file)) !== false) {
                    $rowIndex++;

                    // 空行はスキップ
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // 列数チェック
                    if (count($csvheader) !== count($row)) {
                        throw new \Exception("{$rowIndex}行目: CSVの列数が一致しません。");
                    }

                    $data = array_combine($csvheader, $row);

                    $dataForValidation = [
                        'category_id' => $data['category_id'],
                        'event_name'  => $data['event_name'],
                        'description' => $data['description'],
                        'date'        => $data['event_date'],
                        'time'        => $data['event_time'],
                        'location'    => $data['location'],
                    ];

                    // EventRequest のルール取得
                    $rules = (new EventRequest())->rules();

                    // バリデーション
                    $validator = Validator::make($dataForValidation, $rules);
                    if ($validator->fails()) {
                        $errorMessages = $validator->errors()->all();
                        throw new \Exception("{$rowIndex}行目のバリデーションエラー: " . implode(', ', $errorMessages));
                    }

                    // 日付結合部分の形式チェック
                    $eventDateTime = $data['event_date'] . ' ' . $data['event_time'];
                    if (!\DateTime::createFromFormat('Y-m-d H:i', $eventDateTime)) {
                        throw new \Exception("日時形式が不正です。");
                    }

                    // テーブル用のデータ構築、イベント登録
                    $event = \App\Models\Event::create([
                        'category_id' => $data['category_id'],
                        'event_name'  => $data['event_name'],
                        'description' => $data['description'],
                        'event_date'  => $eventDateTime,
                        'location'    => $data['location'],
                        'created_by'  => Auth::id() ?? 0, // CSVにない情報を補完
                        'updated_by'  => Auth::id() ?? 0, // CSVにない情報を補完
                    ]);
                    $imported++;

                    // 通知作成
                    $users = \App\Models\User::all();
                    foreach ($users as $user) {
                        \App\Models\Notification::create([
                            'event_id' => $event->id,
                            'type' => \App\Enums\Type::NewEvent,
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
