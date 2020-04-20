<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Sms;
use DB;

class SMSController extends Controller
{
  public function index(){

    return Sms::sendSMS($_POST['receipients'],$_POST['message']);
  }
}
