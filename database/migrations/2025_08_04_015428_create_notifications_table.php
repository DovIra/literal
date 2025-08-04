<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Support\MigrationColumns;

return new class extends Migration
//Laravel8以降で使える「匿名クラスによるマイグレーション」の書き方。クラス名を明示的に書かなくてよく、シンプル
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
        //notificationsというテーブルを作成する命令
            $table->bigIncrements('id');
            //主キー primary key bigint型、自動増分 autoincrement
            $table->unsignedBigInteger('user_id');
            $table->string('message', 255);
            $table->boolean('is_read')->default(false);

            MigrationColumns::auditColumns($table);

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
