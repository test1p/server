<?php

use Illuminate\Database\Seeder;

class TimekeepingCardsTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => '00000000-0000-0000-0000-000000000000',
                'timekeeping_card_name' =>  'Eカード',
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000001',
                'timekeeping_card_name' =>  'SIカード',
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000002',
                'timekeeping_card_name' =>  'SIAC',
            ],
        ];
        DB::table('timekeeping_cards')->insert($data);
    }
}
