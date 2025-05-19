<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('categories')->insert([
            [
                'category_name' => '博覧会',
                'created_at' => $now,
                'created_by' => 0,
                'updated_at' => $now,
                'updated_by' => 0,
            ],
            [
                'category_name' => 'フェスティバル',
                'created_at' => $now,
                'created_by' => 0,
                'updated_at' => $now,
                'updated_by' => 0,
            ],
            [
                'category_name' => '見本市・展示会',
                'created_at' => $now,
                'created_by' => 0,
                'updated_at' => $now,
                'updated_by' => 0,
            ],
            [
                'category_name' => '会議イベント',
                'created_at' => $now,
                'created_by' => 0,
                'updated_at' => $now,
                'updated_by' => 0,
            ],
            [
                'category_name' => '文化イベント',
                'created_at' => $now,
                'created_by' => 0,
                'updated_at' => $now,
                'updated_by' => 0,
            ],
            [
                'category_name' => 'スポーツイベント',
                'created_at' => $now,
                'created_by' => 0,
                'updated_at' => $now,
                'updated_by' => 0,
            ],
            [
                'category_name' => '販促イベント',
                'created_at' => $now,
                'created_by' => 0,
                'updated_at' => $now,
                'updated_by' => 0,
            ],
            [
                'category_name' => 'その他',
                'created_at' => $now,
                'created_by' => 0,
                'updated_at' => $now,
                'updated_by' => 0,
            ],
        ]);                                                
    }
}
