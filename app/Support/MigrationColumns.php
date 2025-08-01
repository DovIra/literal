<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;

class MigrationColumns
{
    public static function auditColumns(Blueprint $table)
    {
        $table->timestamp('created_at')->useCurrent();
        $table->unsignedBigInteger('created_by');
        $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        $table->unsignedBigInteger('updated_by')->nullable();
    }
}

/*
use App\Support\MigrationColumns;

Schema::create('your_table_name', function (Blueprint $table) {
    $table->id();
    $table->string('name');

    // 共通項目を追加
    MigrationColumns::auditColumns($table);
});
*/