<?php

use Illuminate\Database\Seeder;

class GamePlansTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => '00000000-0000-0000-0000-000000000000',
                'game_plan_name' =>  'スタンダード',
                'unit_price' =>  1800,
                'general_ticket_cost' =>  1,
                'student_ticket_cost' =>  1,
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000001',
                'game_plan_name' =>  'アドバンス',
                'unit_price' =>  1800,
                'general_ticket_cost' =>  2,
                'student_ticket_cost' =>  1,
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000002',
                'game_plan_name' =>  'フリー',
                'unit_price' =>  0,
                'general_ticket_cost' =>  0,
                'student_ticket_cost' =>  0,
            ],
        ];
        DB::table('game_plans')->insert($data);
    }
}
