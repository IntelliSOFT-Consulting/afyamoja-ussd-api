<?php

namespace App;

use App\User;
use App\FeedbackType;
use App\FeedbackClassification;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    public static function addFeedback($user)
    {
        $feedbacktypes = FeedbackType::where('status', 1)->first();

        $feedback = new Feedback;
        $feedback->phonenumber = $user->phonenumber;
        $feedback->feedback_type_id = $feedbacktypes->id;
        $feedback->save();

        User::where('phonenumber', $user->phonenumber)->update(['feedback_sent' => 1]);
    }

    public static function sendFeedback()
    {
        $userFeedbacks = Feedback::where('sms_sent', 0)->where('feedback_type_id', 1)->limit(5);->get();

        foreach ($userFeedbacks as $userFeedback) {
            $created_at = new \DateTime($userFeedback->created_at);
            $session_idle = $created_at->diff(new \DateTime());

            if ($session_idle->i > 10) {
              Log::info(json_encode($userFeedback).' => '.$session_idle->i);
                $feedbackType = FeedbackType::where('id', $userFeedback->feedback_type_id)->first();
                $sendSMS = SMS::sendSMS('feedback', $userFeedback->phonenumber, $feedbackType->feedback);

                Feedback::where('id', $userFeedback->id)->update(['sms_sent' => 1]);
            }
        }
    }

    public static function patientFeedback()
    {
        if (isset($_POST['from'])) {
            $feedback = Feedback::where('phonenumber', $_POST['from'])->where('sms_sent', 1)->whereNull('response')->first();

            if ($feedback) {
                if ($feedback->feedback_type_id == 4) {
                    $choice = (int)$_POST['text'];
                    $message;
                    switch ($choice) {
                      case $choice < 5:
                        $message = FeedbackClassification::where('classification', 'detractors')->pluck('feedback')->first();
                        break;
                      case $choice == 5:
                        $message = FeedbackClassification::where('classification', 'passive')->pluck('feedback')->first();
                          break;
                      case $choice > 5:
                        $message = FeedbackClassification::where('classification', 'promoters')->pluck('feedback')->first();
                        break;
                    }
                    if ($message) {
                        SMS::sendSMS('feedback', $_POST['from'], $message);
                    }
                }

                Feedback::where('phonenumber', $_POST['from'])->where('id', $feedback->id)->update(['response' => $_POST['text']]);

                if ($feedback->feedback_type_id == 1 && $_POST['text'] == "1") {
                    self::updateFeedback();
                } elseif ($feedback->feedback_type_id != 1) {
                    self::updateFeedback();
                }
            }
        }
    }



    public static function updateFeedback()
    {
        $sentFeedback = Feedback::where('phonenumber', $_POST['from'])->pluck('feedback_type_id')->toArray();
        $feedbackType = FeedbackType::where('status', 1)->whereNotIn('id', $sentFeedback)->first();

        if ($feedbackType) {
            $AddFeedback = new Feedback;
            $AddFeedback->phonenumber = $_POST['from'];
            $AddFeedback->feedback_type_id = $feedbackType->id;
            $AddFeedback->save();

            if ($AddFeedback) {
                $sendSMS = SMS::sendSMS('feedback', $_POST['from'], $feedbackType->feedback);
                if ($sendSMS['status'] == "success") {
                    Feedback::where('id', $AddFeedback->id)->update(['sms_sent' => 1]);
                }
            }
        }
    }
}
