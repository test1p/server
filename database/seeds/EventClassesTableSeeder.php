<?php

use Illuminate\Database\Seeder;

class EventClassesTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => '00000000-0000-0000-0000-000000000000',
                'class_name' =>  'M21E',
                'min_age' =>  0,
                'max_age' =>  100,
                'min_member_num' =>  1,
                'max_member_num' =>  1,
                'difficulty' =>  0,
                'distance' =>  0,
                'women_only' =>  0,
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000001',
                'class_name' =>  'W21E',
                'min_age' =>  0,
                'max_age' =>  100,
                'min_member_num' =>  1,
                'max_member_num' =>  1,
                'difficulty' =>  0,
                'distance' =>  0,
                'women_only' =>  1,
            ],
        ];
        DB::table('event_classes')->insert($data);
    }
}
