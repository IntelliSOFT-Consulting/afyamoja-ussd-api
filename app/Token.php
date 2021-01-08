<?php

namespace App;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    public static function token()
    {
        $token = Token::latest()->first();

        if ($token) {
            $last_updated = new \DateTime($token->created_at);
            $session_idle = $last_updated->diff(new \DateTime());

            return $session_idle->d < 1 && $session_idle->h < 1 && $session_idle->i < 30 ?  $token->access_token : self::generateToken();
        }

        return self::generateToken();
    }

    public static function generateToken()
    {
        $url =env("url_auth");
        $client_id=env("client_id");
        $client_secret=env("client_secret");

        $username = env("username");
        $password = env("password");

        if (!isset($client_id)||!isset($client_secret)) {
            die("Please declare the client id and client secret as defined in the documentation");
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/json','Content-Type:application/x-www-form-urlencoded'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "client_id=$client_id&client_secret=$client_secret&grant_type=password&username=$username&password=$password");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);

        $curl_response = curl_exec($curl);
        $curl_response = json_decode($curl_response);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $request = new Request();
        $request->replace([
          'url' => 'oauth2/token/',
          'http_code' => $httpcode ,
          'payload' => 'confidential',
          'response' => $curl_response ? 'valid' : 'invalid'  ,
          'system' => 'SIL' ]);
        SystemLog::store($request);

        if ($curl_response) {
            $token = new Token;
            $token->access_token = $curl_response ? $curl_response->access_token : '';
            $token->save();

            return $token->access_token;
        }
    }
}
