<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Sms;
use App\User;
use DB;

class SMSController extends Controller
{
    public function index()
    {
        //User::getBearerToken();
        return Sms::sendSMS('normal', $_POST['receipients'], $_POST['message']);
    }

    public function store()
    {
        return Sms::receiveSMS($_POST);
    }
}
