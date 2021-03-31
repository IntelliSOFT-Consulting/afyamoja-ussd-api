<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(FeedbackClassificationsTableSeeder::class);
        $this->call(SmsContentTableSeeder::class);
        $this->call(FeedbackTypesTableSeeder::class);
        $this->call(MenuItemTableSeeder::class);
    }
}
