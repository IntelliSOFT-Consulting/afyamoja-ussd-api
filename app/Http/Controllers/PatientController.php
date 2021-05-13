<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Get Last Visit.
     *
     * @return \Illuminate\Http\Response
     */
    public function shareProfile(Request $request)
    {
        $rules = [
          'phonenumber' => 'regex:/^(\+254)[0-9]{9}$/',
          'provider' =>  'required',
          'scope' =>  'required|numeric|in:1,2'
        ];
        $response = User::shareProfile($request, $rules);

        return User::response($response->status, $response->message, $response->data);
    }


    /**
     * Update Profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $rules = [
          'phonenumber' => 'regex:/^(\+254)[0-9]{9}$/',
          'first_name' => 'required|max:25',
          'last_name' => 'required|max:25',
          'dob' => 'required|date_format:Y-m-d',
          'gender' => 'required|in:male,female'
        ];
        $response = User::updateProfile($request, $rules);

        return User::response($response->status, $response->message, $response->data);
    }


    /**
     * Start Visit.
     *
     * @return \Illuminate\Http\Response
     */
    public function lastVisit(Request $request)
    {
        $rules = ['phonenumber' => 'regex:/^(\+254)[0-9]{9}$/'];
        $response = User::lastVisit($request, $rules);

        return User::response($response->status, $response->message, $response->data);
    }


    /**
     * Get Full History.
     *
     * @return \Illuminate\Http\Response
     */
    public function fullHistory(Request $request)
    {
        $rules = ['phonenumber' => 'regex:/^(\+254)[0-9]{9}$/'];

        $response = User::fullHistory($request, $rules);

        return User::response($response->status, $response->message, $response->data);
    }


    /**
     * Get Full History.
     *
     * @return \Illuminate\Http\Response
     */
    public function patientProfile(Request $request)
    {
        $rules = ['phonenumber' => 'regex:/^(\+254)[0-9]{9}$/'];

        $response = User::patientProfile($request, $rules);

        return User::response($response->status, $response->message, $response->data);
    }


    /**
     * Delete Patient
     *
     * @return \Illuminate\Http\Response
     */
    public function forgetPatient(Request $request)
    {
        $rules = ['phonenumber' => 'regex:/^(\+254)[0-9]{9}$/'];

        $response = User::forgetPatient($request, $rules);

        return User::response($response->status, $response->message, $response->data);
    }
}
