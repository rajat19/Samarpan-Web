<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use App\Http\Requests;
use Illuminate\Support\Facades\Lang;
use Validator;

class MobileAuthController extends Controller
{
    public function register(Request $request) {
    	$validator = $this->validator($request->all());
    	$errors = array(); $createUser = array();
    	if($validator->fails())
    		$errors = $this->formatValidationErrors($validator);
        else {
        	$createUser = $this->create($request->all());
        	// $details = $this->login($request);
        }
        $data['details'] = $createUser;
        $data['errors'] = $errors;
        return response()->json($data);
    }

    public function login(Request $request) {
    	$errors = array(); $details = array(); $image;
    	$email_entered = $request->email;
    	$password_entered = bcrypt($request->password);
    	$user = User::where('email', $email_entered);
    	$count = $user->count();
    	$credentials = $this->getCredentials($request);
    	if (Auth::attempt($credentials, $request->has('remember'))) {
            if(Auth::user()->exists) {
            	$details = Auth::user();
                if(Auth::user()->detail()->get()[0]->photo != "") {
                    $image = Auth::user()->detail()->get()[0]->photo;
                }
                else $image = "user.jpg";
            }
            else {
            	$e = Lang::has('auth.failed')? Lang::get('auth.failed'): 'These credentials do not match our records.';
            	array_push($errors, $e);
            }
        }
        else $errors = Lang::has('auth.failed')? Lang::get('auth.failed'): 'These credentials do not match our records.';
    	$data['details'] = array($details);
        $data['photo'] = $image;
    	$data['errors'] = $errors;
    	return response()->json($data);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        return $request->only('email', 'password');
    }

	/**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'contact' => 'required|max:10',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'type' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'type' => $data['type'],
            'name' => $data['name'],
            'email' => $data['email'],
            'contact' => $data['contact'],
            'password' => bcrypt($data['password'])
        ]);
    }
}
