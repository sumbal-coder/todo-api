<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use App\Models\Code;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use DB;

class ApiController extends Controller
{
    /********************************** Registration Method *********************************/
    public function register(Request $request)
    {
    	//Validate data
        $data = $request->only('email', 'password', 'confirm_password');
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password', 
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $email = $request->email;
        $password = $request->password;

            //Request is valid, create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'code_verified' => 'no'
            ]);

        //Generate Verification Code
        $str = substr(md5(uniqid(rand(), true)), 16, 16);
        // Send email
            $messageData = ['email' => $request->email, 'password' => $password, 'str'=>$str];
                Mail::send('emails.register-api',$messageData,function ($message) use ($email){
                    $message->to($email)->subject('TodoList Account Verification Code');
                    });

                    DB::table('codes')->insert([
                        'verification_code' => $str,
                    ]);

        //Return success response
            return response()->json([
                'success' => true,
                'message' => 'Verification Code is sent to your email address.',
                //   'data' => $sesionemail
            ], Response::HTTP_OK);
    }

    /********************************** Verification Method *********************************/ 
    public function verification(Request $request)
    {
        $this->validate($request, [
            'verification_code' => 'required'
        ]);
        $code = $request->verification_code;
        $tbl_code = DB::table('codes')->pluck('verification_code')->first();
        if($code == $tbl_code) 
        {
            DB::table('codes')->truncate();
            return response()->json([
            'success' => true,
            'message' => 'Your Account is verified and created successfully.',
            ], Response::HTTP_OK);
        
        }
    else{
        return response()->json([
            'success' => false,
            'message' => 'Invalid verification code',
        ],400);
    }
       
}

    /********************************** Login Method *********************************/
    public function authenticate(Request $request)
    {
        $codes = DB::table('codes')->first();
        if(empty($codes)){
            DB::table('users')->update([
                'code_verified' => 'yes',
            ]);
        
        $credentials = $request->only('email', 'password');
        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                	'success' => false,
                	'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'success' => false,
                	'message' => 'Could not create token.',
                ], 500);
        }
 	
 		//Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'message' =>'Logged in Successfully.',
            'token' => $token,
        ]);
    }
    else{
        return response()->json([
            'success' => false,
            'message' => 'Please Verify your account.',
        ],400);
    }
    }
 
    /********************************** Logout Method *********************************/
    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

		//Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 
    /********************************** User Profile Method *********************************/
    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
        $user = JWTAuth::authenticate($request->token);
        return response()->json(['user' => $user]);
    }
}