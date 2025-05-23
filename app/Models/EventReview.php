<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventReview extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'rating',
        'comment',
        'created_by',
        'updated_by',
    ];

    // リレーション：レビューしたユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // リレーション：レビュー対象のイベント
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}