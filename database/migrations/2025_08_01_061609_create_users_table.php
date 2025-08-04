<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Support\MigrationColumns;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('user_type');
            //'admin', 'regular'は論理的な区別の説明であり、DBにenum型や製薬としては含まれていない
            //アプリケーション側で制御する前提
            $table->string('name', 10);
            $table->string('email')->unique();//UK
            $table-> string('password', 255);
            //Seederやコントローラーで使用　保存時にHash化 Hash::make()

            MigrationColumns::auditColumns($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};