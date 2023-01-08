<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Mail;
use Validator;

class AuthController extends Controller
{
    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'email' => ['required','string','email','max:255'],
                    'password'=>['required','confirmed','min:8']
                ]);
        // When validation fail
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // Create new user
        $user = User::create([
            'email' => $request->email,
            'verification_code' => rand(100000,999999),
            'password' => Hash::make($request->password)
        ]);
        // $token = Auth::guard('api')->login($user);
        // Send email to register user
        $email = $this->sendEmail($user); 
        return response()->json([
            "status" => "success",
            "statusCode" => 200,
            "message" => "User created successfully",
            "user" => $user
        ]);
    }
    /**
     * Login user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'email' => ['required','string','email','max:255'],
                    'password'=>['required','min:8']
                ]);
        // When validation fail
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // find user against email
        $user = User::where("email",$request->email)->first();
        if ($user) {
            if ($user->verified == 1) {
                // Attempt user credentials
                $token = Auth::guard('api')->attempt([
                    'email' => $request->email, 
                    'password' => $request->password
                ]);
                if ($token) {
                    // return token response
                    return $this->respondWithToken($token);
                }else{
                    // return error response wrong credentials
                      return response()->json([
                        'message' => 'Wrong email or password',
                        'status' => "error",
                        "statusCode" => 200
                      ]);
                }
            }else{
                // return error response not verified user
                  return response()->json([
                    'message' => 'User not verified',
                    'status' => "error",
                    "statusCode" => 200
                  ]);
            }
        }else{
            // return error response user not found
              return response()->json([
                'message' => 'User not found',
                'status' => "error",
                "statusCode" => 404
              ]);
        }
    }
    /**
     * Logout user
     */
    protected function logout(Request $request)
    {
        $headers = $request->header();
        // check request header has token or not
        if(!empty($headers) && isset($headers) && isset($headers['authorization'][0])){
            Auth::guard('api')->logout();
            return response()->json([
                'statusCode' => 200,
                'status' => 'success',
                'message' => 'Logout Successfully'
            ], 200);
        }else{
            // return error response token not found
              return response()->json([
                'message' => 'Bearer token not passed',
                'status' => "error",
                "statusCode" => 404
              ]);
        }
    }
    /**
     * Sending response of access token
     *
     * @return \Illuminate\Http\Response
     */
    protected function respondWithToken($token)
    {
      return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
      ]);
    }
    /**
     * Verification of code of user
     *
     * @return \Illuminate\Http\Response
     */
    protected function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'verification_code' => ['required','max:6','min:6']
                ]);
        // when validation fail
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
      // find user against verification code
      $user = User::where("verification_code",$request->verification_code)->first();
      if ($user) {
          //update user against verification code
          $user->update(['verified'=>1]);
          return response()->json([
            'user' => $user,
            'message' => 'User verified successfully',
            'status' => "success",
            "statusCode" => 200
          ]);
      }else{
        // return error response user not found
          return response()->json([
            'message' => 'User not found',
            'status' => "error",
            "statusCode" => 404
          ]);
      }
    }
    /**
     * Sending email to user
     *
     */
    protected function sendEmail($user)
    {
         $details = [
            'title' => 'Verification code',
            'body' => $user->verification_code
        ];
        // send email through blade
        return \Mail::to($user->email)->send(new \App\Mail\SendMail($details));
    }
}
