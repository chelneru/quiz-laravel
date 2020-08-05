<?php

namespace App\Http\Controllers\Auth;

use App\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(Request $request)
    {
        $quiz_id = $request->has('quiz_id') ? $request->input('quiz_id') : null;

        return view('auth.register', ['quiz_id' => $quiz_id]);
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        $validators_array = [
            'email' => 'required|string|email|max:190|unique:users',
            'password' => 'required|string|min:6|max:50|confirmed',
            'first_name' =>'required|string|max:255',
            'last_name' =>'required|string|max:255',
            'privacy_policy'=>'accepted'
        ];
        if(!env("APP_DEBUG", false)) {
            $validators_array['g-recaptcha-response'] = 'required|recaptcha';
        }
        return Validator::make($data,$validators_array );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $result = UserService::CreateNewNormalUser(
            'username',
            $data['password'],
            $data['email'],
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $data['department'],$data['position'],
            $data['user_role'],
            $data['class_token'] ?? null,
            $data['quiz_id'] ?? null

        );
        if($result !== null && isset($result['redirect']) && $result['redirect'] != '') {
            $this->redirectTo = $result['redirect'];
        }
        return $result;
    }
}
