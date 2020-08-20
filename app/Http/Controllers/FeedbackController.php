<?php

namespace App\Http\Controllers;

use App\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    //
    public static function index(){
      Log::info("point 1".json_encode($_POST));
      Feedback::patientFeedback();
    }
}
