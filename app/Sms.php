<?php

namespace App;

require 'vendor/autoload.php';
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
            $result = $sms->send([
            'to'      => $recipients,
            'message' => $message,
            'from'    => env("senderID")
        ]);

        Log::info("AT response --- ".json_encode($result));
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
        $model->receipient   = $receipient;
        $model->content  = $message;
        $model->status   = $status;
        $model->save();
    }
}
