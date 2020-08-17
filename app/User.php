<?php

namespace App;

use Validator;
use App\UserToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public static function login($request)
    {
        $login = $request->json()->all();
        $user = User::where('phonenumber', $login['phone_number'])->where('status', 1)->first();

        if ($user && Hash::check($login['pin'], $user->pin)) {
            UserToken::where('user_id', $user->id)->update(['status' => 0]);

            $token= new UserToken;
            $token->user_id = $user->id;
            $token->user_agent = $request->server('HTTP_USER_AGENT');
            $token->ip = $request->ip();
            $token->firebase_id = $user['firebase_id'] ? $user['firebase_id'] : '' ;
            $token->token = bin2hex(random_bytes(32));
            $token->save();

            return (object) ['user' => $user,'token' => $token->token];
        }

        return null;
    }

    public static function registration($request)
    {
        $user = $request->json()->all();

        $userId = User::insertGetId(
                      [
                          'name' => $user['name'],
                          'country_code' => $user['country_code'],
                          'phone_number' => $user['phone_number'],
                          'gender' => $user['gender'],
                          'dob' => $user['dob'],
                          'pin' => Hash::make($user['pin']),
                          'created_at' => now(),
                          'updated_at' => now()
                      ]
                  );


        return (object) [
                                  'token' => $token->token ,
                                  'user' => User::where('id', $userId)->first(),
                                  'home' => $home,
                                  'residence' => $residence
                              ];
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
* get access token from header
* */
    public static function getBearerToken($request, $rules)
    {
        $headers = self::getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $request_json = $request->json()->all();
                $token = UserToken::where('user_id', $request_json['user_id'])->where('token', $matches[1])->first();
                $validator = Validator::make($request_json, $rules);
                if ($validator->fails()) {
                    return $validator->errors()->first();
                } elseif ($token) {
                    return "Success";
                }
                return "Invalid token";
            }
        }
        return null;
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
