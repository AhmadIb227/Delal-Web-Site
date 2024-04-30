<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;




class AuthController extends Controller
{
    //

    public function auth(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ], 400);
            }
    
            $user = User::where('phone', $request->phone)->first();
    
            if (!$user) {
                $otp = $this->sendOtp($request->phone);
                if (!$otp) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to send OTP.',
                    ], 500);
                }
    
                $user = User::create([
                    'phone' => $request->phone,
                    'otp' => $otp,
                    'otp_attempts' => 1,
                ]);
    
                return response()->json([
                    'status' => true,
                    'message' => 'You have logged in successfully.',
                    'token' => $this->generateToken($user),
                ], 200);
            }
    
            // Check if the user is blocked
            if ($user->blocked_at && $user->blocked_at->addMinutes(60)->isPast()) {
                $user->update([
                    'blocked_at' => null,
                    'otp_attempts' => 1,
                ]);
            }
    
            if ($user->blocked_at && $user->blocked_at->addMinutes(60)->isFuture()) {
                $remainingTime = now()->diffInMinutes($user->blocked_at->addMinutes(60));
                return response()->json([
                    'status' => false,
                    'message' => 'You are blocked. Please try again later.',
                    'remaining_time' => $remainingTime
                ], 403);
            }
    
            if ($user->otp_attempts >= 3) {
                $user->update([
                    'blocked_at' => now(),
                ]);
    
                return response()->json([
                    'status' => false,
                    'message' => 'You have exceeded the maximum number of OTP attempts. You are blocked for 60 minutes.'
                ], 403);
            }
    
            $user->otp_attempts++;
            $user->save();
    
            $otp = $this->sendOtp($request->phone);
    
            $user->update([
                'otp' => $otp
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'You have logged in successfully.',
                'token' => $this->generateToken($user),
            ], 200);
    
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }
    
    private function sendOtp($phone)
    {
        try {
            $otp = random_int(1000, 9999);
            $otpString = (string) $otp;
    
            $response = Http::withHeaders([
                'Authorization' => 'App ' . env('API_KEY'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post(env('URL_BASE_PATH'), [
                'messages' => [[
                    'from' => 'ServiceSMS',
                    'destinations' => [['to' => $phone]],
                    'text' => 'Your OTP is: ' . $otpString
                ]]
            ]);
            
    
            if ($response->successful()) {
                return $otpString;
            } else {
                throw new \Exception('Failed to send OTP. Response code: ' . $response->status() . ' ' . $response->body());
            }
    
        } catch (\Throwable $th) {
            \Log::error('Error sending OTP: ' . $th->getMessage());
            return false;
        }
    }
            
    private function generateToken($user){
        $random = Str::random(10);
        $token = $user->createToken($random)->accessToken;

        return $token;
    }


    public function updateName(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ]);

            if ($validator->fails()) {
                # code...
                return response()->json([
                    'status' => false,
                    'message' =>  $validator->errors()->first(),
                ], 201);
            }
            
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'No User!',
                ], 404);
                # code...
            }

            $user->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'the name is successfully updated',
                'data' => $user
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function verfyOtp(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' =>  $validator->errors()->first(),
                ], 201);
            }
            $user = auth()->user();

            if (!$user) {
                # code...
                return response()->json([
                    'status' => false,
                    'message' => 'No User!',
                ], 404);
            }

            if ($request->otp == $user->otp) {
                # code...

                $user->update([
                    'otp_attempts' => 0
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'You have logged in successfully.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'wronge otp'
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function delete(){
        try {
            $user = auth()->user();
            

            if(!$user){
                return response()->json([
                    'status' => false,
                    'message' => 'No User!'
                ], 404);
            }

            $user->delete();


            return response()->json([
                'status' => true,
                'message' => 'The User successfully deleted'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    
}
