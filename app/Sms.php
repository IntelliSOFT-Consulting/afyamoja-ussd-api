<?php

namespace App;

require 'vendor/autoload.php';
use App\SystemLog;
use Illuminate\Http\Request;
use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SMS extends Model
{
    protected $table = 'sms';

    public static function sendSMS($recipients, $message)
    {
        $username   = env("usernameAT");
        $apiKey     = env("apiKey");

        $AT = new AfricasTalking($username, $apiKey);

        // Get the SMS service
        $sms = $AT->sms();

        try {
            $result = $sms->send(['to' => $recipients,'message' => $message,'from' => env("senderID")]);

            $request = new Request();
            $request->replace([
              'url' => 'https://account.africastalking.com/',
              'http_code' => 200 ,
              'payload' => "['to' => $recipients,'message' => $message]",
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
        $model = new SMS;
        $model->receipient = $receipient;
        $model->content = $message;
        $model->status = $status;
        $model->save();
    }

    public static function saveFeedback($payload){

      Log::info("Payload: ".$payload);
    }
}
