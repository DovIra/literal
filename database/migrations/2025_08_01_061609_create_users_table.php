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
    
            $table->bigIncrements('id');//AI
            $table->enum('user_type',['admin', 'regular']);
            $table->string('name', 10);// varchar(10)
            $table->string('email', 255)->unique(); // UK
            $table->string('password', 255);
            
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
