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

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants', 'event_id', 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
