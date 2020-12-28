<?php

use Illuminate\Database\Seeder;

class GameCategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => '00000000-0000-0000-0000-000000000000',
                'game_category_name' =>  'オリエンテーリング',
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000001',
                'game_category_name' =>  'その他',
            ],
        ];
        DB::table('game_categories')->insert($data);
    }
}
