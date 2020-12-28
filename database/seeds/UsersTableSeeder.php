<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => '00000000-0000-0000-0000-000000000000',
                'email' => 'info@create-o-life.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('toga5807'),
                'name' => '戸上直哉',
                'role' => 0,
            ],
        ];
        DB::table('users')->insert($data);
    }
}