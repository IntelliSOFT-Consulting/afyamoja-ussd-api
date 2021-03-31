<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmsContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sms_content')->delete();

        DB::table('sms_content')->insert(array(
          0 =>
          array(
              'id' => 1,
              'title' => 'registration',
              'messageContent' => 'Dear _name, welcome to AfyaMoja.Your PIN is _pin.To access your healthrecord, dial *384*90# or visit https://oh.ke/xxxxxx',
              'created_at' => '2020-04-17 23:47:49',
          ),
          1 =>
          array(
              'id' => 2,
              'title' => 'terms',
              'messageContent' => 'Dear _name, welcome to AfyaMoja. Please read our Privacy Policy & Terms of Use by visiting https://oh.ke/xxxxxx ',
              'created_at' => '2020-04-18 01:15:38',
          ),
          2 =>
          array(
              'id' => 3,
              'title' => 'reset_pin',
              'messageContent' => 'Dear _name. Your Pin has been reset. Your new pin is _pin. ',
              'created_at' => '2020-04-18 01:49:30',
          ),
          3 =>
          array(
              'id' => 4,
              'title' => 'kin',
              'messageContent' => '_name has been added as your dependant. For more information, please visit https://oh.ke/xxxxxx ',
              'created_at' => '2020-04-18 14:38:28',
          ),
          4 =>
          array(
              'id' => 5,
              'title' => 'profile',
              'messageContent' => 'Profile Summary:
Allergies: _allergies
Conditions: _conditions
For more, please visit _url',
              'created_at' => '2020-05-02 15:00:27',
          ),
          5 =>
          array(
              'id' => 6,
              'title' => 'visit',
              'messageContent' => 'Profile summary:
_content
For more information, visit _url',
              'created_at' => '2020-05-02 16:31:18',
          ),
          6 =>
          array(
              'id' => 7,
              'title' => 'medical_history',
              'messageContent' => 'To view your OneHealth medical history, please click on _url',
              'created_at' => '2020-05-02 16:45:21',
          ),
          7 =>
          array(
              'id' => 8,
              'title' => 'forget',
          'messageContent' => 'We’ll miss you. Thank you for trying AfyaMoja. (Optional) You could tell us why you left at https://ok.ke/xxxxxx',
              'created_at' => '2020-05-07 13:54:18',
          ),
          8 =>
          array(
              'id' => 9,
              'title' => 'remove_kin',
              'messageContent' => '_name has been removed as your dependant. For more information, please visit https://oh.ke/xxxxxx ',
              'created_at' => '2020-05-13 13:46:17',
          ),
          9 =>
          array(
              'id' => 10,
              'title' => 'share',
              'messageContent' => 'AfyaMoja provider can now access your record for the purpose of treatment.',
              'created_at' => '2020-05-13 13:46:52',
          ),
      ));
    }
}
