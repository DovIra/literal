<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\UserType;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('users')->insert([
            [
                'user_type' => UserType::Admin -> value,
                'name' => 'Admin',
                'email' => 'admin@test.test',
                'password' => bcrypt('administrator'),
                'created_at' => $now,
                'created_by' => 0,
                'updated_at' => $now,
                'updated_by' => 0,
            ],
        ]);                                                
    }
}
