<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    // Userとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Eventとのリレーション（必要であれば）
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
