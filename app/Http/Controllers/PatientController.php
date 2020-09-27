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
}
