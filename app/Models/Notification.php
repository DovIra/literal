<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Type;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'event_id',
        'type',
        'user_id',
        'created_by',
        'updated_by',
    ];

    // Enumのキャスト設定
    protected $casts = [
        'type' => Type::class,
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            get: function () {
                $eventName = $this->event->event_name ?? 'イベント';
                return match ($this->type) {
                    Type::EventReminder => "{$eventName} が間もなく始まります。",
                    Type::NewEvent      => "新しい {$eventName} が登録されました。",
                    default             => '',
                };
            }
        );
    }

    protected function message(): Attribute
    {
        return Attribute::make(
            get: function () {
                $eventName = $this->event->event_name ?? 'イベント';
                $formattedDate = $this->event->event_date
                    ? Carbon::parse($this->event->event_date)->format('Y年m月d日H:i')
                    : '未定';

                return match ($this->type) {
                    Type::EventReminder => "{$eventName} が {$formattedDate} に開催されます。お忘れなくご参加ください！",
                    Type::NewEvent      => "{$eventName} が {$formattedDate} に開催されます。ぜひご参加ください！",
                    default             => '',
                };
            }
        );
    }

}
