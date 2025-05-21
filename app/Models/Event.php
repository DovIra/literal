<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'event_name',
        'category_id',
        'filename',
        'description',
        'event_date',
        'location',
        'created_by',
        'updated_by',
    ];

    // User モデルとの belongsToMany リレーション（ユーザー一覧取得用）
    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants', 'event_id', 'user_id');
    }

    // EventParticipant モデルとのリレーション（中間テーブルそのもの）
    public function eventParticipants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected $casts = [
        'filename' => 'array',
    ];




}
