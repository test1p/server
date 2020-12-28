<?php

use Illuminate\Database\Seeder;

class TransferPlansTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => '00000000-0000-0000-0000-000000000000',
                'transfer_plan_name' =>  'アドバンス(24歳以下限定)',
                'price' =>  15000,
                'add_ticket_num' =>  8,
                'plan_disclosure' =>  true,
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000001',
                'transfer_plan_name' =>  'スタンダード(24歳以下限定)',
                'price' =>  8000,
                'add_ticket_num' =>  4,
                'plan_disclosure' =>  true,
            ],
        ];
        DB::table('transfer_plans')->insert($data);
    }
}
