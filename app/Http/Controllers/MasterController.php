<?php

namespace App\Http\Controllers;

require 'vendor/autoload.php';
use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Sms;
use DB;

class MasterController extends Controller
{
    public function index()
    {
        if (isset($_POST['phoneNumber'])) {
            $phonenumber = $_POST['phoneNumber'];
        } else {
            return "END The application is unavaible, our team is working to resolve the issue.";
        }

        $sessionId = $_POST['sessionId'];
        $servicecode = $_POST['serviceCode'];

        $textContent = (explode("*", $_POST['text']));
        $text = trim($textContent[(count($textContent) - 1)]);

        $userData = DB::table('customers')->where('phonenumber', '=', $phonenumber)->first();
        $userSession = DB::table('sessions')->where('sessionId', '=', $sessionId)->latest()->first();

        if (is_null($userSession)) {
            self::addSession(0);
        } else {
            $last_updated = new \DateTime($userSession->updated_at);
            $session_idle = $last_updated->diff(new \DateTime());

            if ($session_idle->i > 4) {
                self::level(1, 0);
            }
        }

        if (is_null($userData)) {
            self::level(0, '');
            return self::menuItem(0, 0);
        } else {
            $userSession = DB::table('sessions')->where('sessionId', '=', $sessionId)->latest()->first();
            $name = $userData->first_name." ".$userData->last_name;
            $age = (int)date("Y") - (int)substr($userData->dob, 4, 4);
            $level = $userSession->level;

            switch ($level) {
                case $level == 1 && $userData->terms_conditions == 1:
                    self::level(5, $text);
                    self::forgotPassword(0);
                    return self::menuItem(5, 0);
                    break;
                case $level == 1 && $userData->terms_conditions == 0:
                    if ($text) {
                        $names = explode(" ", $text);
                        $first_name = $names[0];
                        $last_name = count($names) > 1 ? $names[1]:null;

                        if ($last_name) {
                            self::level(2, $text);
                            DB::table('customers')->where('phonenumber', $_POST['phoneNumber'])->update(['first_name' => $first_name,'last_name'=>$last_name]);
                        } else {
                            return self::menuItem(0, 1);
                        }
                    }
                    return  self::menuItem($level, 0);
                    break;
                case $level == 2 && is_numeric($text) && strlen($text) > 5:
                    self::level(3, $text);
                    DB::table('customers')->where('phonenumber', $_POST['phoneNumber'])->update(['id_number' => $text]);
                    return self::menuItem($level, 0);
                    break;
                case $level == 2  && !is_numeric($text):
                    return self::menuItem(1, 1);
                    break;
                case $level == 3  && is_numeric($text) && strlen($text) == 8:
                    self::level(4, $text);
                    DB::table('customers')->where('phonenumber', $_POST['phoneNumber'])->update(['dob' => $text]);
                    return self::menuItem($level, 0);
                    break;
                case $level == 3 && (!is_numeric($text) || strlen($text) != 8):
                    return self::menuItem(2, 1);
                    break;
                case $level == 4 && ($text == 1 || $text == 2):
                    self::level(5, $text);
                    $gender = $text == 1 ? 'male':'female';
                    DB::table('customers')->where('phonenumber', $_POST['phoneNumber'])->update(['gender' => $gender]);
                    Sms::sendSMS($_POST['phoneNumber'], str_replace('_name', $name, self::smsItem('terms')));
                    return self::menuItem($level, 0);
                    break;
                case $level == 4 && $text != 1 && $text != 2:
                    return self::menuItem(3, 0);
                    break;
                case $level == 5 && $userData->terms_conditions == 0 && $text == 1:
                    $register = self::patientRegister($userData, $userSession->access_token);
                    if ($register) {
                        $placeHolders = ['_name', '_pin'];
                        $content = [$name,$register->data->pin];

                        DB::table('customers')->where('phonenumber', $_POST['phoneNumber'])->update(['pin' => $register->data->pin,'terms_conditions' => 1]);
                        Sms::sendSMS($_POST['phoneNumber'], str_replace($placeHolders, $content, self::smsItem('registration')));
                        return self::menuItem($level, 4);
                    }
                    return self::menuItem($level, 6);
                    break;
                case $level == 5 && $userData->terms_conditions == 0 && $text != 1:
                    return self::menuItem(5, 5);
                    break;
                case $level == 5 && $userData->terms_conditions == 1 && $text == 999:
                    self::forgotPassword(1);
                    self::level(6, $text);
                    return self::menuItem($level, 3);
                    break;
                case $level == 5 && $text == $userData->pin && $userData->terms_conditions == 1:
                    self::level(6, 1);
                    return self::menuItem($level, 1);
                    break;
                case $level == 5 && $text != $userData->pin && $userData->terms_conditions == 1:
                    self::level($level, 3);
                    return self::menuItem($level, 2);
                    break;
                case $level == 6 && strlen($text) > 5 && $userSession->forgot_password == 1:
                    self::level(7, $text);
                    if ($userData->id_number == $text) {
                        return self::menuItem($level, 9990);
                    } elseif ($userData->id_number != $text) {
                        self::forgotPassword(0);
                        return self::menuItem($level, 9991);
                    }
                    break;
                case $level == 6 && $text == 1:
                    self::level(71, 1);
                    return self::menuItem($level, 0);
                    break;
                case $level == 6 && $text == 2:
                    $request = self::request('patient_profile', $userData, $userSession->access_token);
                    if ($request) {
                        $placeHolders = ['_name', '_age','_allergies','_conditions'];
                        $content = [ $name, $age,$request->data->allergy, ''];
                        Sms::sendSMS($_POST['phoneNumber'], str_replace($placeHolders, $content, self::smsItem('profile')));
                        return str_replace($placeHolders, $content, self::menuItem($level, 1));
                    } else {
                        self::level(63, 0);
                        return self::menuItem(63, 0);
                    }
                    break;
                case $level == 6 && $text == 3:
                    $request = self::request('last_visit', $userData, $userSession->access_token);
                    if ($request) {
                        $placeHolders = ['_visit', '_url'];
                        $content = [$request->data->summary,$request->data->short_link];
                        return str_replace($placeHolders, $content, self::menuItem($level, 2));
                        Sms::sendSMS($_POST['phoneNumber'], str_replace($placeHolders, $content, self::smsItem('visit')));
                    } else {
                        self::level(63, 0);
                        return self::menuItem(63, 0);
                    }
                    break;
                case $level == 6 && $text == 4:
                    $request = self::request('full_medical_history', $userData, $userSession->access_token);
                    if ($request) {
                        Sms::sendSMS($_POST['phoneNumber'], str_replace('_url', $request->data->short_link, self::smsItem('medical_history')));
                    }
                    return self::menuItem($level, 3);
                    break;
                case $level == 6 && $text == 5:
                    self::level(75, 4);
                    return self::menuItem($level, 4);
                    break;
                case $level == 6 && $text == 6:
                    self::level(76, 5);
                    return self::menuItem($level, 5);
                    break;
                case $level == 6 && $text == 7:
                    self::level(77, 7);
                    return self::menuItem($level, 6);
                    break;
                case $level == 6 && $text == 8:
                    self::level(78, 8);
                    return self::menuItem($level, 7);
                    break;
                case $level == 6 && $text == 9:
                    self::level(79, 9);
                    Sms::sendSMS($_POST['phoneNumber'], self::smsItem('faq'));
                    return self::menuItem($level, 8);
                    break;
                case $level == 6 && $text == 10:
                    return self::menuItem($level, 9);
                    break;
                case $level == 6 && $text == 00:
                    return self::menuItem(4, 0);
                    break;
                case $level == 7 && is_numeric($text) && strlen($text) == 8 && $userSession->forgot_password == 1:
                    self::forgotPassword(0);
                    $request = self::request('forgot_pin', $userData, $userSession->access_token);
                    if ($request) {
                        $placeHolders = ['_name', '_pin'];
                        $content = [$name,$request->data->pin];
                        DB::table('customers')->where('phonenumber', $_POST['phoneNumber'])->update(['pin' => $request->data->pin]);
                        Sms::sendSMS($_POST['phoneNumber'], str_replace($placeHolders, $content, self::smsItem('reset_pin')));
                        return str_replace("_name", $name, self::menuItem($level, 9990));
                    }
                    break;
                case $level == 7 && $userSession->forgot_password == 1 && (!is_numeric($text) || strlen($text) != 8):
                    return self::menuItem($level, 9991);
                    break;
                case ($level == 7 && $text == 0) ||
                     ($level == 63 && $text == 0) ||
                     ($level == 75 && is_numeric($text) && $text == 0) ||
                     ($level == 78 && $text != 1) ||
                     ($level == 79 && $text == 0) ||
                     ($level == 71 && $text == 0) :
                    self::level(6, 1);
                    return self::menuItem(5, 1);
                    break;
                case $level == 71:
                    $provider = self::provider($userSession->access_token, $text);
                    if ($provider) {
                        self::level(81, $text);
                        DB::table('sessions')->where('sessionId', $userSession->access_token)->update(['provider' => $text]);
                        return str_replace("_provider", $provider, self::menuItem($level, 0));
                    } else {
                        return self::menuItem($level, 1);
                    }
                    break;
                case $level == 75 && strlen($text) > 1:
                    self::level(85, $text);
                    DB::table('questions')->insert(['msisdn' => $_POST['phoneNumber'],'question' => $text]);
                    return self::menuItem($level, 0);
                    break;
                case $level == 76 && $text == 1:
                    self::level(160, $text);
                    DB::insert('insert into dependents (parent_id,sessionId) values (?,?)', [$userData->id,$sessionId]);
                    return self::menuItem($level, 0);
                    break;
                case $level == 76 && $text == 2:
                    self::level(260, $text);
                    $dependents = self::dependents($userData, $userSession->access_token);
                    $dependentList = "";
                    if ($dependents) {
                        DB::table('sessions')->where('sessionId', $userSession->sessionId)->update(['kin' => json_encode($dependents)]);
                        for ($i = 0;$i < count($dependents);$i++) {
                            $number = $i+1;
                            $dependentList .= $number.".".$dependents[$i]->first_name." ".$dependents[$i]->last_name."\n";
                        }
                    }
                    return str_replace("_dependents", $dependentList, self::menuItem($level, 1));
                    break;
                case $level == 77 && $text == $userData->pin:
                    self::level(87, $text);
                    return self::menuItem($level, 0);
                    break;
                case $level == 77 && $text != $userData->pin:
                    return self::menuItem($level, 1);
                    break;
                case $level == 78 && $text == 1:
                    self::level(88, $text);
                    return self::menuItem($level, 0);
                    break;
                case $level == 79 && $text == 00:
                    return self::menuItem(4, 0);
                    break;
                case $level == 81:
                    if ($text == 1 || $text == 2) {
                        $share = self::shareProfile($userSession->provider, $text, $userSession->access_token);
                        return $share ? self::menuItem($level, 0) : self::menuItem($level, 1);
                    } else {
                        return self::menuItem(71, 2);
                    }
                    break;
                case $level == 85:
                    self::level(6, 1);
                    return self::menuItem(5, 1);
                    break;
                case $level == 87 && is_numeric($text) && strlen($text) == 4:
                    $resetPin = self::resetPin($userData, $userSession->access_token, $text);
                    if ($resetPin) {
                        DB::table('customers')->where('phonenumber', $_POST['phoneNumber'])->update(['pin' => $text]);
                        return self::menuItem($level, 0);
                    } else {
                        self::level(999, 1);
                        return self::menuItem(0, 2);
                    }
                    break;
                case $level == 87 && (!is_numeric($text) || strlen($text) == 4):
                    return self::menuItem($level, 1);
                    break;
                case $level == 88 && is_numeric($text) && strlen($text) == 4:
                    $request = self::request('forget_patient', $userData, $userSession->access_token);
                    if ($request) {
                        Sms::sendSMS($_POST['phoneNumber'], str_replace($placeHolders, $content, self::smsItem('reset_pin')));
                        return self::menuItem($level, 0);
                    } else {
                        self::level(999, 1);
                        return self::menuItem(0, 2);
                    }
                    break;
                case $level == 88 && (!is_numeric($text) || strlen($text) != 4):
                    return self::menuItem($level, 1);
                    break;
                case $level == 160:

                    if ($text) {
                        $names = explode(" ", $text);
                        $first_name = $names[0];
                        $last_name = count($names) > 1 ? $names[1]:'';

                        if ($last_name) {
                            self::level(161, 0);
                            return self::menuItem(3, 0);
                            DB::table('dependents')->where('sessionId', $sessionId)->update(['first_name' => $first_name, 'last_name' => $last_name]);
                        }
                    }
                    return self::menuItem(76, 0);
                    break;
                case $level == 161:
                    if ($text == 1 || $text == 2) {
                        $gender = $text == 1 ? 'male':'female';
                        DB::table('dependents')->where('sessionId', $sessionId)->update(['gender' => $gender]);
                        self::level(162, $text);
                        return self::menuItem($level, 0);
                    } else {
                        return self::menuItem(3, 1);
                    }
                    break;
                case $level == 162:
                    if ($text == 1 || $text == 2) {
                        $rltshp = $text == 1 ? 'spouse':'child';
                        DB::table('dependents')->where('sessionId', $sessionId)->update(['relationship' => $rltshp]);
                        self::level(163, $text);
                        return self::menuItem(2, 0);
                    } else {
                        return str_replace('CON', "CON Please try again \n", self::menuItem(161, 0));
                    }
                    break;
                case $level == 163:
                    if (is_numeric($text) && strlen($text) == 8) {
                        $date = substr($text, 4, 4)."-".substr($text, 2, 2)."-".substr($text, 0, 2);
                        DB::table('dependents')->where('sessionId', $sessionId)->update(['dob' => $date]);
                        if ((int)date("Y") - (int)substr($text, 4, 4) > 17) {
                            self::level(164, 1);
                            return self::menuItem($level, 0);
                        } else {
                            $userKin = DB::table('dependents')->where('sessionId', '=', $sessionId)->latest()->first();
                            $addKin = self::addKin($userKin, $userData, $userSession->access_token);
                            if ($addKin) {
                                self::level(165, 1);
                                $name_kin = $userKin->first_name." ".$userKin->last_name;
                                DB::table('dependents')->where('sessionId', $sessionId)->update(['status' => 1]);
                                Sms::sendSMS($phonenumber, str_replace("_name", $name_kin, self::smsItem('kin')));
                                return str_replace("_name", $name_kin, self::menuItem($level, 1));
                            }
                            return self::menuItem($level, 2);
                        }
                    } else {
                        return self::menuItem(2, 1);
                    }
                    break;
                case $level == 164:
                    if (is_numeric($text) && strlen($text) > 9) {
                        $msisdn = substr($text, 1);
                        $msisdn = '+254'.$msisdn;
                        DB::table('dependents')->where('sessionId', $sessionId)->update(['msisdn' => $msisdn]);
                        self::level(165, 0);
                        return self::menuItem($level, 0);
                    } else {
                        return str_replace('CON', "CON Invalid phone number \n Please try again \n", self::menuItem(163, 0));
                    }
                    break;
                case $level == 165:
                    $userKin = DB::table('dependents')->where('sessionId', '=', $sessionId)->latest()->first();
                    if ($text == 1 || $text == 2 && $userKin->next_of_kin == 0) {
                        DB::table('dependents')->where('sessionId', $sessionId)->update(['next_of_kin' => $text]);
                        $addKin = self::addKin($userKin, $userData, $userSession->access_token);
                        if ($addKin) {
                            $name_kin = $userKin->first_name." ".$userKin->last_name;
                            DB::table('dependents')->where('sessionId', $sessionId)->update(['status' => 1]);
                            Sms::sendSMS($phonenumber, str_replace("_name", $name_kin, self::smsItem('kin')));
                            return str_replace("_name", $name_kin, self::menuItem(163, 1));
                        }
                    } elseif (strlen($text) == 2 && $text == 00) {
                        self::level(76, 5);
                        return self::menuItem($level, 5);
                    } if (strlen($text) == 3 && $text == 000) {
                        self::level(6, 1);
                        return self::menuItem(5, 1);
                    } else {
                        self::level(160, 0);
                        return self::menuItem(76, 0);
                    }
                    break;
                case $level == 260:
                    if (strlen($text) == 3 && $text == 000) {
                        self::level(6, 1);
                        return self::menuItem(5, 1);
                    } elseif (is_numeric($text) && $text != 0) {
                        $dependents = json_decode($userSession->kin);
                        if ($dependents && $dependents[$text-1]->first_name) {
                            self::level(261, $text);
                            $dependantAge = date("Y", strtotime($dependents[$text-1]->date_of_birth));
                            $dependantName = $dependents[$text-1]->first_name." ".$dependents[$text-1]->last_name;
                            DB::table('sessions')->where('sessionId', $userSession->access_token)->update(['dependent' => $dependantName,'dependent_age' => $dependantAge]);
                            return str_replace("_dependent", $dependantName, self::menuItem($level, 0));
                        } else {
                            $dependentList = "";
                            if ($dependents) {
                                for ($i = 0;$i < count($dependents);$i++) {
                                    $number = $i+1;
                                    $dependentList .= $number.".".$dependents[$i]->first_name." ".$dependents[$i]->last_name."\n";
                                }
                            }
                            return str_replace("_dependents", $dependentList, self::menuItem($level, 1));
                        }
                    } else {
                        self::level(76, 5);
                        return self::menuItem(6, 5);
                    }
                    break;
                case $level == 261:
                    if (strlen($text) == 3 && $text == 000) {
                        self::level(6, 1);
                        return self::menuItem(5, 1);
                    } elseif (strlen($text) == 2 && $text == 00) {
                        self::level(161, 1);
                        return self::menuItem(160, 0);
                    } elseif ($text == 1) {
                        $request = self::request('next_of_kin_profile', $userData, $userSession->access_token);
                        if ($request) {
                            $placeHolders = ['_name', '_age','_allergies','_conditions'];
                            $content = [
                                        $userSession->dependent,
                                        $userSession->dependent_age,
                                        $request->data->allergies ? $request->data->allergies : '',
                                        $request->data->conditions ? $request->data->conditions : ''
                                    ];
                            Sms::sendSMS($_POST['phoneNumber'], str_replace($placeHolders, $content, self::smsItem('profile')));
                            return str_replace($placeHolders, $content, self::menuItem($level, 1));
                        } else {
                            self::level(262, 1);
                            return str_replace("_dependent", $dependantName, self::menuItem(260, 0));
                        }
                    } elseif ($text == 2) {
                        return str_replace("_dependent", $userSession->dependent, self::menuItem($level, 0));
                    } else {
                        self::level(999, $text);
                        return self::menuItem(0, 2);
                    }
                    break;
                case $level == 262:
                    if ($text == 1) {
                        self::level(263, $text);
                        return self::menuItem(262, 0);
                    } elseif ($text == 2) {
                        self::level(263, $text);
                        return self::menuItem(262, 1);
                    } else {
                        self::level(999, $text);
                        return self::menuItem(0, 2);
                    }
                    break;
                case $level == 263:
                    if ($text == 0) {
                        self::level(260, $text);
                        return str_replace("_dependents", $userSession->dependent, self::menuItem($level, 1));
                    } elseif (strlen($text) == 4 && $text == $userData->pin) {
                        $request = self::removeKin($userData, $userSession->access_token, $userSession->dependent);
                        if ($request) {
                            return str_replace("_dependent", $userSession->dependent, self::menuItem($level, 0));
                        } else {
                            self::level(999, $text);
                            return self::menuItem(0, 2) ;
                        }
                    } else {
                        self::level(6, 1);
                        return self::menuItem(5, 1);
                    }
                    break;
                case $level == 999:
                    self::level(6, 1);
                    return self::menuItem(5, 1);
                    break;
                default:
                    self::level(5, $text);
                    return self::menuItem($level-1, 0);
            }
        }
    }

    public function menuItem($level, $choice)
    {
        $content =  DB::table('menuItem')->where('level', '=', $level)->where('choice', '=', $choice)->first();
        if ($content) {
            return str_replace('_new', "\n", $content->text);
        } else {
            self::level(6, 1);
            return "CON Sorry, we are unable to process your request. \nEnter any key to go back ";
        }
    }

    public function smsItem($title)
    {
        $content =  DB::table('sms_content')->where('title', '=', $title)->first();
        return $content ? $content->messageContent: '';
    }

    public function level($level, $choice)
    {
        if ($level == 0) {
            DB::insert('insert into customers (phonenumber) values (?)', [$_POST['phoneNumber']]);
            self::addSession($choice);
        } else {
            DB::table('sessions')->where('sessionId', $_POST['sessionId'])
            ->update(['text' => $_POST['text'],'level' => $level,'choice' => $choice,'updated_at' => date("Y-m-d H:i:s", time())]);
        }
    }

    public function addSession($choice)
    {
        $access_token = self::accessToken();
        DB::insert('insert into sessions (sessionId,access_token,phonenumber,level,text,choice)
                            values (?,?,?,?,?,?)', [$_POST['sessionId'],$access_token,$_POST['phoneNumber'],1,$_POST['text'],$choice]);
    }

    public function forgotPassword($status)
    {
        DB::table('sessions')->where('sessionId', $_POST['sessionId'])->update(['forgot_password' => $status ,'updated_at' => date("Y-m-d H:i:s", time())]);
    }

    public function patientRegister($userData, $token)
    {
        $date = substr($userData->dob, 4, 4)."-".substr($userData->dob, 2, 2)."-".substr($userData->dob, 0, 2);
        $curl_post_data = array(
             'first_name'=> $userData->first_name,
             'last_name'=> $userData->last_name,
             'date_of_birth'=> $date,
             'gender'=> $userData->gender,
             'msisdn'=> [$userData->phonenumber],
             'id_number'=>$userData->id_number ,
             'passport_number'=> ''
        );
        $data_string = json_encode($curl_post_data);
        $register = json_decode(self::generalAPI($data_string, $token, 'patients/register_patient/'));
        if ($register && $register->status == "Success") {
            return $register;
        } else {
            return null;
        }
    }

    public function addKin($userKin, $userData, $token)
    {
        $patient_data = (object) [
            'msisdn'=> [$userData->phonenumber],
            'id_number'=> $userData->id_number ,
            'passport_number'=> ''
        ];

        $next_of_kin = (object) [
             'first_name'=> $userKin->first_name,
             'last_name'=> $userKin->last_name,
             'date_of_birth'=> $userKin->dob,
             'gender'=> $userKin->gender,
             'relationship'=>$userKin->relationship,

        ];

        if ($userKin->relationship != 'child') {
            $next_of_kin = (object) array_merge((array)$next_of_kin, array(  'msisdn'=> [$userKin->msisdn],'id_number'=>$userKin->id_number ));
        }

        $curl_post_data = array('patient'=> $patient_data, 'next_of_kin' => [$next_of_kin]);
        $data_string = json_encode($curl_post_data);
        $add_kin = json_decode(self::generalAPI($data_string, $token, 'patients/add_next_of_kin/'));
        if ($add_kin && $add_kin->status == "Success") {
            return $add_kin;
        } else {
            return null;
        }
    }

    public function removeKin($userData, $token, $full_name)
    {
        $names = explode(" ", $full_name);
        $first_name = $names[0];
        $last_name = count($names) > 1 ? $names[1]:'';

        $patient_data = (object) [
            'msisdn'=> [$userData->phonenumber],
            'id_number'=> $userData->id_number ,
            'passport_number'=> ''
        ];

        $next_of_kin = (object) [
             'first_name'=> $first_name,
             'last_name'=> $last_name
        ];

        $curl_post_data = array('patient'=> $patient_data, 'next_of_kin' => [$next_of_kin]);
        $data_string = json_encode($curl_post_data);
        $add_kin = json_decode(self::generalAPI($data_string, $token, 'patients/remove_next_of_kin/'));
        if ($add_kin && $add_kin->status == "Success") {
            return $add_kin;
        } else {
            return null;
        }
    }

    public function shareProfile($provider_code, $scope, $token)
    {
        $curl_post_data = array(
                'identifier'=> $_POST['phoneNumber'],
                'provider_code'=> $provider_code ,
                'scope'=> $scope
        );
        $data_string = json_encode($curl_post_data);
        $share = json_decode(self::generalAPI($data_string, $token, 'patients/start_visit/'));
        if ($share && $share->status == "Success") {
            return $share;
        } else {
            return null;
        }
    }

    public function resetPin($userData, $token, $new_pin)
    {
        $objectPatient = (object) [
            'msisdn'=> [$userData->phonenumber],
            'id_number'=> $userData->id_number ,
            'passport_number'=> ''
        ];

        $curl_post_data = array('patient'=> $objectPatient, 'current_pin' => $userData->pin,'new_pin' =>$new_pin);
        $data_string = json_encode($curl_post_data);
        $pinReset = json_decode(self::generalAPI($data_string, $token, 'patients/reset_pin/'));
        if ($pinReset && $pinReset->status == "Success") {
            return $pinReset;
        } else {
            return null;
        }
    }

    public function dependents($userData, $token)
    {
        $objectPatient = (object) [
            'msisdn'=> [$userData->phonenumber],
            'id_number'=> $userData->id_number ,
            'passport_number'=> ''
        ];

        $curl_post_data = array('patient'=> $objectPatient);
        $data_string = json_encode($curl_post_data);
        $dependents = json_decode(self::generalAPI($data_string, $token, 'patients/get_next_of_kin/'));
        if ($dependents && count($dependents) > 0) {
            return $dependents;
        } else {
            return null;
        }
    }

    public function request($url, $userData, $token)
    {
        $objectPatient = (object) [
            'msisdn'=> [$userData->phonenumber],
            'id_number'=> $userData->id_number ,
            'passport_number'=> ''
        ];

        $curl_post_data = array('patient'=> $objectPatient);
        $data_string = json_encode($curl_post_data);
        $response = json_decode(self::generalAPI($data_string, $token, 'patients/'.$url.'/'));
        if ($response && $response->status == "Success") {
            Log::info("api response --- ".json_encode($response));
            return $response;
        } else {
            return null;
        }
    }

    public function provider($token, $text)
    {
        $provider = json_decode(self::generalAPI(null, $token, "common/organisations/get_provider_code/$text/"));
        if ($provider && $provider->provider_name) {
            return $provider->provider_name;
        } else {
            return false;
        }
    }


    public function generalAPI($curl_post_data, $token, $path)
    {
        $url = env("url");
        $url = $url.$path;

        Log::info("api path --- ".$url);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/json','Content-Type:application/json','Authorization:Bearer '.$token));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if ($curl_post_data) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
        }
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        $curl_response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        Log::info("api response --- ".json_encode($curl_response));
        if ($httpcode != 500 && $httpcode != 401) {
            return $curl_response;
        } else {
            return null;
        }
    }


    public function accessToken()
    {
        $url =env("url_auth");
        $client_id=env("client_id");
        $client_secret=env("client_secret");

        $username = env("username");
        $password = env("password");

        if (!isset($client_id)||!isset($client_secret)) {
            die("Please declare the client id and client secret as defined in the documentation");
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/json','Content-Type:application/x-www-form-urlencoded'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "client_id=$client_id&client_secret=$client_secret&grant_type=password&username=$username&password=$password");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $curl_response = curl_exec($curl);
        $curl_response = json_decode($curl_response);

        if ($curl_response) {
            return $curl_response->access_token ;
        }

        return '';
    }
}
