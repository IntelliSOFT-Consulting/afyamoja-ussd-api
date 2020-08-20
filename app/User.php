<?php

namespace App;

use Validator;
use App\SMS;
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

    public static function addPatient($patient,$phonenumber,$id_number,$pin){

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

        $userId = User::insertGetId([
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'id_number' => $user['id_number'],
                'phonenumber' => $user['phonenumber'],
                'gender' => $user['gender'],
                'dob' => date('dmY', strtotime($user['dob']))  ,
                'terms_conditions_sent' => 1,
            ]);

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
    public static function resetPin($request, $rules)
    {
        $status= "Failure";
        $message = "Sorry, unable to reset pin";

        $bearerToken = User::getBearerToken($request, $rules);
        if ($bearerToken->status == "Success") {
            $master = new MasterController();
            $forgotPin = $master->request('forgot_pin', $bearerToken->response, Token::token());
            if ($forgotPin) {
                $user = User::where('phonenumber', $bearerToken->response->phonenumber)->update(['pin' => Hash::make($forgotPin->data->pin)]);
                if ($user) {
                    SMS::sendSMS($bearerToken->response->phonenumber, $bearerToken->response->first_name.", we have reset your PIN to ".$forgotPin->data->pin);
                    $status = "Success";
                    $message = "Your pin has been reset, you should receive an SMS shortly";
                }
            }
        } else {
            $message = $bearerToken->response;
        }

        return (object) ['status'=> $status,'message'=>$message,'data'=>[] ];
    }


    /**
    *Get header Authorization
    **/
    public static function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
    * Get access token from header
    * */
    public static function getBearerToken($request, $rules)
    {
        $headers = self::getAuthorizationHeader();
        $status = "Failure";
        $response = "Invalid token";
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $request_json = $request->json()->all();
                $token = UserToken::where('token', $matches[1])->where('status', 1)->first();
                $validator = Validator::make($request_json, $rules);
                if ($validator->fails()) {
                    $response = $validator->errors()->first();
                } elseif ($token) {
                    $status = "Success";
                    $response = User::where('id', $token->user_id)->first();
                }
            }
        }
        return (object)['status' => $status,'response' => $response];
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
