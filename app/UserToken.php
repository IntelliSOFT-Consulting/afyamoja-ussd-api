<?php

namespace App;

use Validator;
use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
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
}
