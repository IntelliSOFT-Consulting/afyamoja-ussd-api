<?php

namespace App;

use App\SystemLog;
use Illuminate\Http\Request;
use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Sms extends Model
{
    protected $table = 'sms';

    public static function sendSMS($sender, $recipients, $message)
    {
        $username   = env("usernameAT");
        $apiKey     = env("apiKey");

        $AT = new AfricasTalking($username, $apiKey);

        // Get the SMS service
        $sms = $AT->sms();

        $senderID = $sender == 'normal' ? env("senderID") : 20552;

        try {
            $result = $sms->send(['to' => $recipients,'message' => $message,'from' => $senderID ]);

            $request = new Request();
            $request->replace([
              'url' => 'https://account.africastalking.com/',
              'http_code' => 200 ,
              'payload' => "['to' => $recipients,'message' => $message, 'from' => $senderID]",
              'response' => json_encode($result) ,
              'system' => 'AT' ]);
            SystemLog::store($request);

            return $result;
        } catch (Exception $e) {
            Log::info("Error: ".$e->getMessage());
        }
    }


    /**
    * Save sms
    */
    public static function saveSMS($receipient, $message, $status)
    {
        $model = new Sms;
        $model->receipient = $receipient;
        $model->content = $message;
        $model->status = $status;
        $model->save();
    }
}
