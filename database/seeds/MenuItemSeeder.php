<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menu_item')->delete();

        DB::table('menu_item')->insert(array(
          0 =>
          array(
              'id' => 1,
              'level' => 0,
              'choice' => 0,
          'text' => 'CON Welcome to AfyaMoja. Please enter your first and last names (e.g John Doe) ',
              'created_at' => '2020-04-01 18:28:38',
          ),
          1 =>
          array(
              'id' => 2,
              'level' => 0,
              'choice' => 1,
          'text' => 'CON A first and last name are required to complete the registration. Please enter your first and last name to register e.g (John Doe)',
              'created_at' => '2020-04-20 16:17:49',
          ),
          2 =>
          array(
              'id' => 3,
              'level' => 0,
              'choice' => 2,
              'text' => 'CON Sorry, we are unable to process your request. _new Enter any key to go back ',
              'created_at' => '2020-05-03 12:14:17',
          ),
          3 =>
          array(
              'id' => 4,
              'level' => 1,
              'choice' => 0,
              'text' => 'CON Please enter your Kenyan national ID number',
              'created_at' => '2020-04-01 18:50:23',
          ),
          4 =>
          array(
              'id' => 5,
              'level' => 1,
              'choice' => 1,
              'text' => 'CON The national ID number you entered is not valid, please try again',
              'created_at' => '2020-04-01 19:04:45',
          ),
          5 =>
          array(
              'id' => 6,
              'level' => 2,
              'choice' => 0,
              'text' => 'CON Please enter date of birth in DDMMYYYY format e.g 28021992',
              'created_at' => '2020-04-01 19:42:31',
          ),
          6 =>
          array(
              'id' => 7,
              'level' => 2,
              'choice' => 1,
              'text' => 'CON The date of birth you entered is not valid, please try again',
              'created_at' => '2020-04-01 19:42:31',
          ),
          7 =>
          array(
              'id' => 8,
              'level' => 3,
              'choice' => 0,
              'text' => 'CON Select gender _new 1.Male _new 2.Female
',
              'created_at' => '2020-04-03 17:01:18',
          ),
          8 =>
          array(
              'id' => 9,
              'level' => 3,
              'choice' => 1,
              'text' => 'CON Please try again _new Select gender _new 1.Male _new 2.Female

',
              'created_at' => '2020-04-04 09:00:16',
          ),
          9 =>
          array(
              'id' => 10,
              'level' => 4,
              'choice' => 0,
              'text' => 'CON Please read the Terms and Conditions sent to you on SMS. _new 1.Agree to terms and conditions _new 2.Read terms and conditions
',
              'created_at' => '2020-04-04 09:06:14',
          ),
          10 =>
          array(
              'id' => 11,
              'level' => 5,
              'choice' => 0,
          'text' => 'CON Welcome to AfyaMoja. Please enter your PIN (enter 999 if you forgot your PIN)',
              'created_at' => '2020-04-04 09:15:06',
          ),
          11 =>
          array(
              'id' => 12,
              'level' => 5,
              'choice' => 1,
              'text' => 'CON Welcome to AfyaMoja _new 1.Share health record _new 2.My profile _new3.My last visit _new 4.My medical history _new 5.Ask a question  _new 6.Dependants and next of kin  _new 7.Change PIN  _new 8.Forget me  _new 9.Frequently asked questions  _new 10.Customer care _new 000.Exit ',
              'created_at' => '2020-04-04 09:24:52',
          ),
          12 =>
          array(
              'id' => 13,
              'level' => 5,
              'choice' => 2,
          'text' => 'CON You have entered the wrong PIN._new Please try again (or enter 999 if you forgot your PIN)',
              'created_at' => '2020-04-04 09:24:52',
          ),
          13 =>
          array(
              'id' => 14,
              'level' => 5,
              'choice' => 3,
              'text' => 'CON Please enter your ID number',
              'created_at' => '2020-04-04 11:12:57',
          ),
          14 =>
          array(
              'id' => 15,
              'level' => 5,
              'choice' => 4,
              'text' => 'END Welcome to AfyaMoja. We are processing your account, an SMS will be sent to you with your secret PIN.',
              'created_at' => '2020-04-17 20:19:41',
          ),
          15 =>
          array(
              'id' => 16,
              'level' => 5,
              'choice' => 5,
              'text' => 'END Thank you for Using AfyaMoja. We look forward to serving you next time.',
              'created_at' => '2020-04-17 21:23:58',
          ),
          16 =>
          array(
              'id' => 17,
              'level' => 5,
              'choice' => 6,
              'text' => 'END Sorry, there was an issue registering your account. Try and again, if you are unable to register contact customer care for assistance.',
              'created_at' => '2020-04-17 21:35:19',
          ),
          17 =>
          array(
              'id' => 18,
              'level' => 6,
              'choice' => 9990,
              'text' => 'CON Please enter your date of birth in DDMMYYYY format e.g 28021992 for 28th February 1992',
              'created_at' => '2020-04-04 13:21:04',
          ),
          18 =>
          array(
              'id' => 19,
              'level' => 6,
              'choice' => 9991,
              'text' => 'END The ID number does not match our records. Please call customer care on 0701 XXX XXX',
              'created_at' => '2020-04-04 13:21:04',
          ),
          19 =>
          array(
              'id' => 20,
              'level' => 6,
              'choice' => 0,
          'text' => 'CON Please enter the provider code (you can find it on the AfyaMoja poster) _new 0. Go back',
              'created_at' => '2020-04-04 13:40:54',
          ),
          20 =>
          array(
              'id' => 21,
              'level' => 6,
              'choice' => 1,
          'text' => 'CON _name (_age years old). _new Allergies: _allergies _new For more, please visit https://oh.ke/xxxxxx',
              'created_at' => '2020-04-04 13:40:54',
          ),
          21 =>
          array(
              'id' => 22,
              'level' => 6,
              'choice' => 2,
              'text' => 'CON _content.',
              'created_at' => '2020-04-04 13:49:22',
          ),
          22 =>
          array(
              'id' => 23,
              'level' => 6,
              'choice' => 3,
              'text' => 'CON We have sent you a link to your AfyaMoja medical history For more, please visit https://oh.ke/xxxxxx _new _new 0.Go Back',
              'created_at' => '2020-04-04 13:49:22',
          ),
          23 =>
          array(
              'id' => 24,
              'level' => 6,
              'choice' => 4,
              'text' => 'CON Type your question below _new _new 0.Go Back',
              'created_at' => '2020-04-04 13:49:22',
          ),
          24 =>
          array(
              'id' => 25,
              'level' => 6,
              'choice' => 5,
              'text' => 'CON Dependents and next of kin _new 1.Add new dependent / next of kin _new 2.List dependents / next of kin _new  0.Go Back',
              'created_at' => '2020-04-04 13:49:22',
          ),
          25 =>
          array(
              'id' => 26,
              'level' => 6,
              'choice' => 6,
              'text' => 'CON Enter your current PIN',
              'created_at' => '2020-04-04 13:49:22',
          ),
          26 =>
          array(
              'id' => 27,
              'level' => 6,
              'choice' => 7,
              'text' => 'CON Are you sure you want to leave AfyaMoja completely? _new 1.Yes _new 2.No ',
              'created_at' => '2020-04-04 13:49:22',
          ),
          27 =>
          array(
              'id' => 28,
              'level' => 6,
              'choice' => 8,
              'text' => 'CON Please visit https://oh.ke/faqs for Frequently Asked Questions _new 0.Go back to home menu _new 00.Exit',
              'created_at' => '2020-04-04 13:49:22',
          ),
          28 =>
          array(
              'id' => 29,
              'level' => 6,
              'choice' => 9,
              'text' => 'CON Please contact customer service on 072X XXX XXX or visit https://oh.ke/help _new _new 0.Go Back',
              'created_at' => '2020-04-04 13:49:22',
          ),
          29 =>
          array(
              'id' => 30,
              'level' => 7,
              'choice' => 9990,
              'text' => 'END _name, we have reset your PIN and sent you a message',
              'created_at' => '2020-04-04 14:09:50',
          ),
          30 =>
          array(
              'id' => 31,
              'level' => 7,
              'choice' => 9991,
              'text' => 'END The date of birth does not match our records. Please call customer care on 0701 XXX XXX',
              'created_at' => '2020-04-04 14:09:50',
          ),
          31 =>
          array(
              'id' => 32,
              'level' => 63,
              'choice' => 0,
              'text' => 'CON Sorry, we are unable to retrieve your last request. _new 0.Go Back',
              'created_at' => '2020-04-05 11:13:22',
          ),
          32 =>
          array(
              'id' => 33,
              'level' => 71,
              'choice' => 0,
              'text' => 'CON How much would you like to share with _provider? _new 1.Just my profile and recent visits _new 2.My full health record ',
              'created_at' => '2020-04-05 04:49:58',
          ),
          33 =>
          array(
              'id' => 34,
              'level' => 71,
              'choice' => 1,
              'text' => 'CON We cannot find a provider with that code. Please check and try again _new 0.Go Back',
              'created_at' => '2020-04-05 05:29:05',
          ),
          34 =>
          array(
              'id' => 35,
              'level' => 75,
              'choice' => 0,
              'text' => 'CON Your question has been sent and a response shall be sent on sms. _new 0.Home',
              'created_at' => '2020-04-05 16:46:19',
          ),
          35 =>
          array(
              'id' => 36,
              'level' => 76,
              'choice' => 0,
          'text' => 'CON Enter the name of the dependent. Please enter their first and last names (e.g John Doe) ',
              'created_at' => '2020-04-05 13:57:23',
          ),
          36 =>
          array(
              'id' => 37,
              'level' => 76,
              'choice' => 1,
              'text' => 'CON Select a dependent / next of kin to view their profile _new _dependents _new 00. Go back _new 000.Go Home',
              'created_at' => '2020-04-05 13:57:59',
          ),
          37 =>
          array(
              'id' => 38,
              'level' => 77,
              'choice' => 0,
              'text' => 'CON Enter the new PIN',
              'created_at' => '2020-04-05 08:34:44',
          ),
          38 =>
          array(
              'id' => 39,
              'level' => 77,
              'choice' => 1,
              'text' => 'CON That PIN is not valid. Please try again.',
              'created_at' => '2020-04-05 08:34:44',
          ),
          39 =>
          array(
              'id' => 40,
              'level' => 78,
              'choice' => 0,
              'text' => 'CON Please enter your PIN to confirm',
              'created_at' => '2020-04-05 09:34:46',
          ),
          40 =>
          array(
              'id' => 41,
              'level' => 81,
              'choice' => 0,
              'text' => 'CON AfyaMoja provider can now access your record for the purpose of treatment. To view your record, visit https://oh.ke/xxxxx _new _new 0.Go Back

',
              'created_at' => '2020-04-05 05:41:37',
          ),
          41 =>
          array(
              'id' => 42,
              'level' => 81,
              'choice' => 1,
              'text' => 'CON AfyaMoja is not able to share access with the provider for the purpose of treatment. To view your record, visit https://oh.ke/xxxxx _new _new 0.Go Back
',
              'created_at' => '2020-05-02 11:04:33',
          ),
          42 =>
          array(
              'id' => 43,
              'level' => 87,
              'choice' => 0,
              'text' => 'END Congratulations! You’ve successfully changed your PIN. Remember, your PIN is your secret',
              'created_at' => '2020-04-05 08:37:08',
          ),
          43 =>
          array(
              'id' => 44,
              'level' => 87,
              'choice' => 1,
          'text' => 'CON New PIN is not valid. Please try again. (Pin should be 4 digits)',
              'created_at' => '2020-04-05 08:44:19',
          ),
          44 =>
          array(
              'id' => 45,
              'level' => 88,
              'choice' => 0,
              'text' => 'END We are sorry to see you leave. We have delinked your record ',
              'created_at' => '2020-04-05 09:37:37',
          ),
          45 =>
          array(
              'id' => 46,
              'level' => 88,
              'choice' => 1,
              'text' => 'CON That PIN is not valid. Please try again. ',
              'created_at' => '2020-04-05 09:37:37',
          ),
          46 =>
          array(
              'id' => 47,
              'level' => 160,
              'choice' => 0,
              'text' => 'CON Enter the name of the dependant ',
              'created_at' => '2020-04-18 08:20:07',
          ),
          47 =>
          array(
              'id' => 48,
              'level' => 161,
              'choice' => 0,
              'text' => 'CON Select their relationship _new 1. Spouse _new 2. Child ',
              'created_at' => '2020-04-18 09:53:03',
          ),
          48 =>
          array(
              'id' => 49,
              'level' => 163,
              'choice' => 0,
          'text' => 'CON Enter their phone number e.g (0722111000)',
              'created_at' => '2020-04-18 10:33:36',
          ),
          49 =>
          array(
              'id' => 50,
              'level' => 163,
              'choice' => 1,
              'text' => 'CON _name has been added as your dependent. _new 0. Go back to dependents 00. _new Go back to home screen ',
              'created_at' => '2020-04-18 10:33:36',
          ),
          50 =>
          array(
              'id' => 51,
              'level' => 164,
              'choice' => 0,
              'text' => 'CON Would you like to mark them as a next of kin? A next of kin is notified in case of emergencies _new 1. Yes _new 2. No ',
              'created_at' => '2020-04-18 10:33:36',
          ),
          51 =>
          array(
              'id' => 52,
              'level' => 163,
              'choice' => 2,
              'text' => 'CON There was an issue adding your dependent. Please contact customer care on 072X XXX XXX or visit https://oh.ke/help _new _new 0.Go Back',
              'created_at' => '2020-04-27 10:32:04',
          ),
          52 =>
          array(
              'id' => 53,
              'level' => 260,
              'choice' => 0,
              'text' => 'CON _dependents _new 1. View profile _new 2. Remove from my profile _new 000.Go Back',
              'created_at' => '2020-04-05 15:24:40',
          ),
          53 =>
          array(
              'id' => 54,
              'level' => 260,
              'choice' => 1,
              'text' => 'CON Dependent does not exist, please try again. _new _dependents _new 00.Add dependent _new 000.Go Home',
              'created_at' => '2020-04-05 15:32:56',
          ),
          54 =>
          array(
              'id' => 55,
              'level' => 261,
              'choice' => 0,
              'text' => 'CON Remove _dependent from my dependents / next of kin? _new 1. Yes _new 2. No ',
              'created_at' => '2020-05-03 16:21:44',
          ),
          55 =>
          array(
              'id' => 56,
              'level' => 262,
              'choice' => 0,
              'text' => 'CON Please enter your PIN to confirm',
              'created_at' => '2020-05-03 17:13:49',
          ),
          56 =>
          array(
              'id' => 57,
              'level' => 262,
              'choice' => 1,
              'text' => 'CON We have not removed _dependent from your dependents / next of kin. _new 0. Go back to dependents _new 00. Home screen ',
              'created_at' => '2020-05-03 17:15:47',
          ),
          57 =>
          array(
              'id' => 58,
              'level' => 263,
              'choice' => 0,
              'text' => 'CON _dependent has been removed from your dependants / next of kin._new 0. Go back to dependants _new 00. Home screen ',
              'created_at' => '2020-05-03 17:54:09',
          ),
          58 =>
          array(
              'id' => 59,
              'level' => 4,
              'choice' => 1,
              'text' => 'CON Do you agree to the Terms and Conditions that were sent to you on SMS. _new
1.Agree _new 2.Disagree ',
              'created_at' => '2020-06-25 13:14:24',
          ),
          59 =>
          array(
              'id' => 60,
              'level' => 0,
              'choice' => 3,
              'text' => 'END Your account has been deactivated. Please contact customer care for assistance.',
              'created_at' => '2020-06-29 13:25:02',
          ),
          60 =>
          array(
              'id' => 61,
              'level' => 5,
              'choice' => 7,
              'text' => 'END Please read the Terms and Conditions sent to you on SMS. After dial *384*90# to accept or decline.',
              'created_at' => '2020-08-03 10:46:30',
          ),
          61 =>
          array(
              'id' => 62,
              'level' => 71,
              'choice' => 2,
              'text' => 'CON That choice is not recognized, please select another option _new _new 1.Just my profile and recent visits _new 2.My full health record ',
              'created_at' => '2020-08-03 21:54:11',
          ),
          62 =>
          array(
              'id' => 63,
              'level' => 5,
              'choice' => 8,
              'text' => 'CON Do you agree for us to contact you to get your feedback on Afya moja as we evaluate the service in order to make it better for you ?

1.Yes
2.No',
              'created_at' => '2020-10-14 00:18:28',
          ),
      ));
    }
}
