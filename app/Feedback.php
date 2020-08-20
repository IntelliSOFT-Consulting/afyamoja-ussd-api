<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    //
    public static function patientFeedback()
    {
        Log::info("point 2".json_encode($_POST));
        if (isset($_POST['phonenumber'])) {
            $feedback = Feedback::where('phonenumber', $_POST['phonenumber']);
        }
    }
}
