<?php

namespace App\Services\UserService;

use Exception;
use App\Models\User;
// use Aloha\Twilio\Twilio;
use Twilio\Rest\Client;
use Aloha\Twilio\Twilio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserVerifyService
{
    protected $model;
    function __construct()
    {
        $this->model = new User;
    }
    function validation($request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        return $validator;
    }


    function chekUser($request)
    {
        $user = User::where('mobile', $request->input('mobile'))->first();

        if (!$user) {
            return response()->json(['error' => 'Mobile number not found'], 404);
        }

        if ($user->verification_code != $request->input('code')) {
            return response()->json(['error' => 'Verification code is incorrect'], 400);
        }
        $user->verified_at = now();
        $user->verification_code = null;
        $user->save();
        return $user;
    }



    function verify($request)
    {
        $this->validation($request);
        $this->chekUser($request);
        return response()->json(['message' => 'Verification successful']);
    }
}
