<?php

namespace App\Http\Controllers;

use App\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    //
    public static function index(){
      Feedback::patientFeedback();
    }

    public static function send(){
      Feedback::sendFeedback();
    }
}
