<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(EventClassesTableSeeder::class);
        $this->call(GameCategoriesTableSeeder::class);
        $this->call(GamePlansTableSeeder::class);
        $this->call(TimekeepingCardsTableSeeder::class);
        $this->call(TransferPlansTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}