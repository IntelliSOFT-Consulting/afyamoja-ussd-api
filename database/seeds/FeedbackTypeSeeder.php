<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedbackTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('feedback_types')->delete();

        DB::table('feedback_types')->insert(array(
          0 =>
          array(
              'id' => 1,
              'type' => 'closed',
              'feedback' => 'Thank you for choosing Afya Moja. We would like to contact you and get your feedback. Continue? 1. YES 2. NO. 3. STOP',
              'status' => 1,
              'created_at' => '2020-08-21 13:34:57',
              'updated_at' => '2020-08-21 00:31:42',
          ),
          1 =>
          array(
              'id' => 2,
              'type' => 'closed',
              'feedback' => 'On a scale of 0 to 10, how likely are you to recommend Afya Moja to your family and friends?',
              'status' => 1,
              'created_at' => '2020-08-21 00:31:42',
              'updated_at' => '2020-08-21 00:31:42',
          ),
          2 =>
          array(
              'id' => 3,
              'type' => 'open',
              'feedback' => 'What was missing or disappointing in your experience with us?',
              'status' => 1,
              'created_at' => '2020-08-21 00:33:00',
              'updated_at' => '2020-08-21 00:33:00',
          ),
          3 =>
          array(
              'id' => 4,
              'type' => 'open',
              'feedback' => 'What is the one thing we could do to make you happier?',
              'status' => 1,
              'created_at' => '2020-08-21 00:33:00',
              'updated_at' => '2020-08-21 00:33:00',
          ),
      ));
    }
}
