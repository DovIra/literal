<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'category_name',
        'created_by',
        'updated_by',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

}
