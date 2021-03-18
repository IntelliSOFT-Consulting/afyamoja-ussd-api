<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

  /**
 * Login user
 *
 */
    public function login(Request $request)
    {
        $rules = [
          'phonenumber' => 'required|regex:/^(\+254)[0-9]{9}$/',
          'pin' => 'required|digits_between:3,5',
        ];
        $status = "Failure";
        $data = [];

        $validator = Validator::make($request->json()->all(), $rules);
        if ($validator->fails()) {
            $message = $validator->errors()->first();
        } else {
            $response = User::login($request);

            $status = $response->status;
            $message = $response->message;
            $data = $response->data;
        }

        return User::response($status, $message, $data);
    }


    /**
     * Register user
     *
     */
    public function registration(Request $request)
    {
        $rules = [
        'first_name' =>  'required|max:25',
        'last_name' =>  'required|max:25',
        'id_number' => 'required|numeric',
        'phonenumber' => 'required|regex:/^(\+254)[0-9]{9}$/',
        'gender' => 'in:male,female',
        'dob' =>  'date_format:Y-m-d|before:-18 years',
    ];
        $data = [];
        $status = "Failure";

        $validator = Validator::make($request->json()->all(), $rules);
        if ($validator->fails()) {
            $status = "Failure";
            $message = $validator->errors()->first();
        } else {
            $response = User::registration($request);

            $status = $response->status;
            $message = $response->message;
            $data = $response->data;
        }

        return User::response($status, $message, $data);
    }

    /**
     * Reset Pin.
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPin(Request $request)
    {
        $rules = [
          'id_number' =>  'required|numeric',
          'phonenumber' => 'required|regex:/^(\+254)[0-9]{9}$/'
        ];

        $data = [];
        $status = "Failure";

        $validator = Validator::make($request->json()->all(), $rules);
        if ($validator->fails()) {
            $status = "Failure";
            $message = $validator->errors()->first();
        } else {
            $response = User::resetPin($request);

            $status = $response->status;
            $message = $response->message;
            $data = $response->data;
        }

        return User::response($status, $message, $data);
    }


    /**
     * Change Pin.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePin(Request $request)
    {
        $rules = [
          'id_number' =>  'required|numeric',
          'new_pin' =>  'required|digits_between:3,5',
          'current_pin' =>  'required|digits_between:3,5',
          'phonenumber' => 'required|regex:/^(\+254)[0-9]{9}$/'
        ];

        $data = [];
        $status = "Failure";

        $validator = Validator::make($request->json()->all(), $rules);
        if ($validator->fails()) {
            $status = "Failure";
            $message = $validator->errors()->first();
        } else {
            $response = User::changePin($request);

            $status = $response->status;
            $message = $response->message;
            $data = $response->data;
        }

        return User::response($status, $message, $data);
    }


    /**
     * Get Dependents.
     *
     * @return \Illuminate\Http\Response
     */
    public function dependents(Request $request)
    {
        $rules = [];

        $response = User::dependents($request, $rules);

        return User::response($response->status, $response->message, $response->data);
    }


    /**
     * Add dependent
     *
     */
    public function addDependent(Request $request)
    {
        $rules = [
          'first_name' =>  'required|max:25',
          'last_name' =>  'required|max:25',
          'gender' => 'required|in:male,female',
          'relationship' => 'required|in:spouse,child',
          'dob' =>  'required|date_format:Y-m-d',
          'msisdn' =>  'nullable|regex:/^(\+254)[0-9]{9}$/|required_if:relationship,=,spouse',
        ];

        $response = User::addDependent($request, $rules);

        return User::response($response->status, $response->message, $response->data);
    }

    /**
     * Delete dependent
     *
     */
    public function deleteDependent(Request $request)
    {
        $rules = [
          'first_name' =>  'required|max:25',
          'last_name' =>  'required:max:25'
        ];

        $response = User::deleteDependent($request, $rules);

        return User::response($response->status, $response->message, $response->data);
    }
}
