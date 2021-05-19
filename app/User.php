<?php

namespace App;

use App\Sms;
use App\Token;
use App\UserToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\MasterController;

class User extends Model
{

  /**
 * The attributes that should be hidden for arrays.
 *
 * @var array
 */
    protected $hidden = ['id','status','pin','terms_conditions_sent','isSynced','updated_at','created_at','terms_conditions'];

    public static function addPatient($patient, $phonenumber, $id_number, $pin)
    {
        $addPatient =  new User;
        $addPatient->phonenumber = $phonenumber;
        $addPatient->id_number = $id_number;
        $addPatient->first_name = $patient->first_name;
        $addPatient->last_name = $patient->last_name;
        $addPatient->dob = date('dmY', strtotime($patient->date_of_birth));
        $addPatient->gender = $patient->gender;
        $addPatient->pin = Hash::make($pin);
        $addPatient->terms_conditions_sent = 1;
        $addPatient->terms_conditions = 1;
        $addPatient->status = 1;
        $addPatient->isSynced = 1;
        $addPatient->save();

        return $addPatient;
    }

    public static function login($request)
    {
        $login = $request->json()->all();
        $data = [];
        $status = "Failure";
        $message = "Login unsuccesful, please try again";

        $user = User::where('phonenumber', $login['phonenumber'])->where('status', 1)->first();

        if ($user && Hash::check($login['pin'], $user->pin)) {
            UserToken::where('user_id', $user->id)->update(['status' => 0]);

            $token= new UserToken;
            $token->user_id = $user->id;
            $token->user_agent = $request->server('HTTP_USER_AGENT');
            $token->ip = $request->ip();
            $token->firebase_id = '' ;
            $token->token = bin2hex(random_bytes(32));
            $token->expire =  date('Y-m-d', strtotime(now().' + 1 year'));
            $token->save();

            $status = "Success";
            $message =  "Login successful" ;

            $data = (object) ['token'=>$token->token,'expires'=>$token->expire];
        }

        return (object) ['status'=> $status,'message'=>$message,'data'=> $data];
    }

    public static function registration($request)
    {
        $user = $request->json()->all();
        $data = [];

        $userData = User::where('phonenumber', $user['phonenumber'])->first();

        if (!$userData) {
            $userId = User::insertGetId([
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'id_number' => $user['id_number'],
                'phonenumber' => $user['phonenumber'],
                'gender' => $user['gender'],
                'dob' => date('dmY', strtotime($user['dob']))  ,
                'terms_conditions_sent' => 1,
            ]);
        } else {
            $userId = $userData->id;
        }

        $new_user = (object) [
          'first_name' => $user['first_name'],
          'last_name' => $user['last_name'],
          'id_number' => $user['id_number'],
          'phonenumber' => $user['phonenumber'],
          'gender' => $user['gender'],
          'dob' => date('dmY', strtotime($user['dob']))
        ];

        $master = new MasterController();
        $master->processRegistration($new_user);

        if ($userId) {
            $token= new UserToken;
            $token->user_id = $userId;
            $token->user_agent = $request->server('HTTP_USER_AGENT');
            $token->ip = $request->ip();
            $token->firebase_id = '' ;
            $token->token = bin2hex(random_bytes(32));
            $token->expire =  date('Y-m-d', strtotime(now().' + 1 year'));
            $token->save();

            $data = (object) ['token'=>$token->token,'expires'=>$token->expire];
        }

        return (object) [
          'status'=> $userId ? "Success" : "Failure",
          'message'=>$userId ? "Registration successful, an SMS will be sent with your log in pin" : "Registration unsuccesful, please try again",
          'data'=> $data
        ];
        ;
    }

    /**
    *Get Reset User Pin
    **/
    public static function resetPin($request)
    {
        $status= "Failure";
        $message = "Sorry, unable to reset pin";

        $user = User::where('phonenumber', $request['phonenumber'])
                      ->where('id_number', $request['id_number'])
                      ->where('status', 1)
                      ->first();

        if ($user) {
            $master = new MasterController();
            $forgotPin = $master->request('forgot_pin', $user, Token::token());
            if ($forgotPin) {
                $phonenumber = $user->phonenumber;
                $first_name =  $user->first_name;
                $pin = $forgotPin->data->pin;
                $user = User::where('id', $user->id)->update(['pin' => Hash::make($forgotPin->data->pin)]);
                if ($user) {
                    Sms::sendSMS('normal', $phonenumber, $first_name.", we have reset your PIN to ".$pin);
                    $status = "Success";
                    $message = "Your pin has been reset, you should receive an Sms shortly";
                }
            }
        }


        return (object) ['status'=> $status,'message'=>$message,'data'=>[] ];
    }


    /**
    *Change User Pin
    **/
    public static function changePin($request)
    {
        $status= "Failure";
        $message = "Sorry, unable to change pin";
        $response = $request->json()->all();
        $user = User::where('phonenumber', $response['phonenumber'])->where('status', 1)->first();

        if ($user && Hash::check($response['current_pin'], $user->pin)) {
            $master = new MasterController();
            $changePin = $master->resetPin($user, $response['current_pin'], Token::token(), $response['new_pin']);
            if ($changePin) {
                $status = "Success";
                $message = "Your pin has been changed successfully.";
                User::where('phonenumber', $response['phonenumber'])->update(['pin' => Hash::make($response['new_pin'])]);
            }
        }
        return (object) ['status'=> $status,'message'=>$message,'data'=>[] ];
    }

    /**
    *Patient Profile
    **/
    public static function patientProfile($request, $rules)
    {
        $status= "Failure";
        $message = "Sorry, unable to retrieve your profile";
        $data = [];

        $user = UserToken::getBearerToken($request, $rules);
        if ($user->status == "Success") {
            $master = new MasterController();
            $profile = $master->request('patient_profile', $user->response, Token::token());
            if ($profile) {
                $status = "Success";
                $message = "Patient profile";
                if (isset($profile->data->patient_profile)) {
                    $data = $profile->data->patient_profile;
                }
            }
        } else {
            $message = $user->response;
        }

        return (object) ['status'=> $status,'message'=>$message,'data'=>$data ];
    }

    /**
    *Get Last Visit
    **/
    public static function lastVisit($request, $rules)
    {
        $status= "Failure";
        $message = "Sorry, unable to retrieve your last visits";
        $data = array();

        $user = UserToken::getBearerToken($request, $rules);
        if ($user->status == "Success") {
            $master = new MasterController();
            $last_visit = $master->request('last_visit', $user->response, Token::token());
            if ($last_visit) {
                $status = "Success";
                $message = "Patient's last visit";
                if (isset($last_visit->data->visitSummary)) {
                    $data = $last_visit->data->visitSummary;
                }
            } else {
                $message = "You currently have no last visit";
            }
        } else {
            $message = $user->response;
        }

        return (object) ['status'=> $status,'message'=>$message,'data'=>$data ];
    }


    /**
    *Get Full History
    **/
    public static function fullHistory($request, $rules)
    {
        $status= "Failure";
        $message = "Sorry, unable to retrieve your full history";
        $data = array();

        $user = UserToken::getBearerToken($request, $rules);
        if ($user->status == "Success") {
            $master = new MasterController();
            $history = $master->request('full_medical_history', $user->response, Token::token());
            if ($history) {
                $status = "Success";
                $message = "Patient's Full History";
                if (isset($history->data->fullHistory)) {
                    if (count($history->data->fullHistory->fullHistory) > 0) {
                        $data = $history->data->fullHistory->fullHistory[0];
                    }
                }
            } else {
                $message = "You currently have no history";
            }
        } else {
            $message = $user->response;
        }

        return (object) ['status'=> $status,'message'=>$message,'data'=>$data ];
    }


    public static function patientData($patientData)
    {
        $data = array();

        foreach ($patientData->AllergyIntolerance as $visit) {
            $data['allergies'][] =[
            'criticality'=> $visit->criticality,
            'type'=>$visit->type,
            'allergy'=>$visit->code->text,
            'date'=>$visit->recordedDate,
            'doctor'=>$visit->asserter->display
          ];
        }
        foreach ($patientData->Composition as $visit) {
            $data['consultant-note'][] =[
            'title'=>$visit->title,
            'date'=>$visit->date,
            'tests'=>$visit->section,
            'doctor'=> $visit->author[0]->display
          ];
        }
        foreach ($patientData->Condition as $visit) {
            $data['condition'][] =[
            'condition'=>$visit->code->text,
            'date'=>$visit->recordedDate,
            'verificationStatus'=>$visit->verificationStatus->text,
            'doctor'=> $visit->recorder->display
          ];
        }
        foreach ($patientData->MedicationRequest as $visit) {
            $data['medication'][] =[
            'medicine'=> $visit->medicationCodeableConcept->text,
            'condition'=>$visit->supportingInformation[0]->display,
            'date'=>$visit->authoredOn,
            'dosageInstruction'=>$visit->dosageInstruction[0]->text,
            'note'=>$visit->note[0]->text,
            'doctor'=> $visit->requester->display
          ];
        }
        foreach ($patientData->Observation as $visit) {
            $data['observation'][] =[
            'observation'=> $visit->category[0]->text,
            'condition'=>$visit->code->text,
            'interpretation'=>$visit->interpretation[0]->text,
            'date'=>$visit->issued,
            'valueQuantity'=>$visit->valueQuantity->value,
          ];
        }

        return $data;
    }

    /**
    *Update Profile
    **/
    public static function updateProfile($request, $rules)
    {
        $status= "Failure";
        $message = "Sorry, we are unable to update your profile.";

        $userUpdate = json_decode(json_encode($request->json()->all()));

        $user = UserToken::getBearerToken($request, $rules);
        if ($user->status == "Success") {
            $master = new MasterController();
            $updateProfile = $master->updateProfile($userUpdate, $user->response, Token::token());
            if ($updateProfile) {
                $status = "Success";
                $message = "Your profile has been successfully updated";

                User::where('phonenumber', $user->response->phonenumber)
                      ->update([
                        'first_name' => $userUpdate->first_name,
                        'last_name' => $userUpdate->last_name ,
                        'dob' =>  date('dmY', strtotime($userUpdate->dob))  ,
                        'gender' => $userUpdate->gender
                       ]);
            } else {
                $message = "We are currently not able to update your profile";
            }
        } else {
            $message = $user->response;
        }

        return (object) ['status'=> $status,'message'=>$message,'data'=>[] ];
    }

    /**
    *Share Profile
    **/
    public static function shareProfile($request, $rules)
    {
        $status= "Failure";
        $message = "We cannot find a provider with that code. Please check and try again";

        $response = json_decode(json_encode($request->json()->all()));

        $user = UserToken::getBearerToken($request, $rules);
        if ($user->status == "Success") {
            $master = new MasterController();
            $provider = $master->provider(Token::token(), $response->provider);

            if ($provider) {
                $share = $master->shareProfile($response, $user->response, $response->scope);
                if ($share) {
                    $status = "Success";
                    $message = "You have successfully shared your profile.";
                }
            }
        } else {
            $message = $user->response;
        }

        return (object) ['status'=> $status,'message'=>$message,'data'=>[] ];
    }

    /**
    *Forget Patient
    **/
    public static function forgetPatient($request, $rules)
    {
        $status= "Failure";
        $message = "Patient successfully forgotten";

        $user = UserToken::getBearerToken($request, $rules);

        if ($user->status == "Success") {
            $master = new MasterController();
            $forget_patient = $master->request('forget_patient', $user->response, Token::token());
            $status = "Success";
            User::where('phonenumber', $user->response->phonenumber)
                  ->update(['status' => 0, 'feedback_sent' => 0 ,'terms_conditions' => 0 , 'terms_conditions_sent' => 0,'isSynced' => 0]);
        } else {
            $message = $user->response;
        }

        return (object) ['status'=> $status,'message'=>$message,'data'=>[] ];
    }


    /**
    *Add Dependents
    **/
    public static function addDependent($request, $rules)
    {
        $status= "Failure";
        $message = "Sorry, unable to add your dependents";

        $kin = json_decode(json_encode($request->json()->all()));

        $user = UserToken::getBearerToken($request, $rules);
        if ($user->status == "Success") {
            $master = new MasterController();
            $addDependent = $master->addKin($kin, $user->response, Token::token());
            if ($addDependent) {
                $status = "Success";
                $message = "Your dependent has been successfully added";
            } else {
                $message = "We are currently not able to add dependent";
            }
        } else {
            $message = $user->response;
        }
        return (object) ['status'=> $status,'message'=>$message,'data'=>[] ];
    }


    /**
    *Update Dependents
    **/
    public static function updateDependent($request, $rules)
    {
        $status= "Failure";
        $message = "Sorry, unable to update your dependent";

        $kin = json_decode(json_encode($request->json()->all()));

        $user = UserToken::getBearerToken($request, $rules);
        if ($user->status == "Success") {
            $master = new MasterController();
            $addDependent = $master->updateKin($kin, $user->response, Token::token());
            if ($addDependent) {
                $status = "Success";
                $message = "Your dependent has been successfully updated";
            } else {
                $message = "We are currently not able to update your dependent";
            }
        } else {
            $message = $user->response;
        }
        return (object) ['status'=> $status,'message'=>$message,'data'=>[] ];
    }

    /**
    *Delete Dependents
    **/
    public static function deleteDependent($request, $rules)
    {
        $status= "Failure";
        $message = "Sorry, unable to delete your dependent";

        $kin = json_decode(json_encode($request->json()->all()));

        $user = UserToken::getBearerToken($request, $rules);
        if ($user->status == "Success") {
            $master = new MasterController();
            $removeDependent = $master->removeKin($user->response, Token::token(), $kin->first_name." ".$kin->last_name);
            if ($removeDependent) {
                $status = "Success";
                $message = "Your dependent has been successfully removed";
            } else {
                $message = "We are currently not able to remove your dependent";
            }
        } else {
            $message = $user->response;
        }
        return (object) ['status'=> $status,'message'=>$message,'data'=>[] ];
    }

    /**
    *Get Dependents
    **/
    public static function dependents($request, $rules)
    {
        $status= "Failure";
        $message = "Sorry, unable to retrieve your dependents";
        $data = array();

        $user = UserToken::getBearerToken($request, $rules);
        if ($user->status == "Success") {
            $master = new MasterController();
            $dependents = $master->dependents($user->response, Token::token());
            if ($dependents) {
                $status = "Success";
                $message = "Dependents successfully retrieved.";
                $data = $dependents;
            } else {
                $message = "You currently have no dependents";
            }
        } else {
            $message = $user->response;
        }
        return (object) ['status'=> $status,'message'=>$message,'data'=>$data ];
    }



    public static function response($status, $message, $data)
    {
        $responseUser = (object) [
            'status'=> $status,
            'message'=> $message,
            'data'=> $data
        ];

        return response()->json($responseUser);
    }
}
