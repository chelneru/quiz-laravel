<?php


namespace App\Http\Controllers;


use Auth;
use Mail;

class TestController  extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function TestEmail()
    {
        if (Auth::user()->u_id == 1) {
            Mail::send("emails.test", [], function ($message) {
                $message->from('sagaprojectmail@uni.au.dk');
                $message->to('alin.panainte95@gmail.com')
                    ->subject('test 2');
            });
        }
    }
}
