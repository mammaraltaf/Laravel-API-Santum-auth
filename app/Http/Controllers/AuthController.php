<?php

namespace App\Http\Controllers;

use App\Mail\sendEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*Register User*/
    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return response()->json([
            'token' => $user->createToken('Register Token')->plainTextToken,
        ],200);
    }

    /*Login User*/
    public function login(Request $request){

      $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if(Auth::attempt(['email'=> $request->email, 'password'=> $request->password])){
            return response()->json([
                "Logged In Successfully",
                'token' => Auth::user()->createToken('Login Token')->plainTextToken,
            ],200);
        }
        else {
            return response()->json([
                "Unable to Login!",
            ], 200);
        }
    }

    /*Logout User*/
    public function logout(){
        auth()->user()->tokens()->delete();
        return ['msg'=> 'Logged out! Token has been deleted'];
    }

    /*Forget Password*/
    public function forgotPassword(Request $request){
        $otp = rand(1000,9999);
        $user = User::where('email',$request->email)->update(['otp' => $otp]);
        if($user){

            $mail_details = [
                'subject' => 'Forget Password OPT Code',
                'body' => 'Your OTP is : '. $otp
            ];

            \Mail::to($request->email)->send(new sendEmail($mail_details));

            return response(["status" => 200, "message" => "OTP sent successfully"]);
        }
        else{
            return response(["status" => 401, 'message' => 'Invalid Request']);
        }
    }

    /*Resend OTP*/
    public function resendOtp(Request $request){
        $otp = rand(1000,9999);
        $user = User::where('email',$request->email)->update(['otp' => $otp]);
        if($user){

            $mail_details = [
                'subject' => 'Resend OPT Code',
                'body' => 'Your resend OTP is : '. $otp
            ];

            \Mail::to($request->email)->send(new sendEmail($mail_details));

            return response(["status" => 200, "message" => "OTP sent successfully"]);
        }
        else{
            return response(["status" => 401, 'message' => 'Invalid Request']);
        }
    }

    /*Verify Otp*/
    public function verifyOtp(Request $request){

        $user  = User::where([['email',$request->email],['otp',$request->otp]])->first();
        if($user){
            auth()->login($user, true);
            User::where('email',$request->email)->update(['otp' => null]);
            $accessToken = auth()->user()->createToken('authToken')->accessToken;

            return response(["status" => 200, "message" => "Success", 'user' => auth()->user(), 'access_token' => $accessToken]);
        }
        else{
            return response(["status" => 401, 'message' => 'Invalid']);
        }
    }

    /*Reset Password*/
    public function resetPassword(Request $request){
        $user  = User::where('email',$request->email)->first();
        if($user){
            User::where('email',$user->email)->update(['password' => $request->password]);
            $accessToken = auth()->user()->createToken('ResetPasswordToken')->accessToken;
            return response(["status" => 200, "message" => "Success", 'Password Changed Successfully']);
        }
        else{
            return response(["status" => 401, 'message' => 'Invalid']);
        }

    }
}
