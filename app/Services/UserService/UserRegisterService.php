<?php

namespace App\Services\UserService;

use Exception;
use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserRegisterService
{
    protected $model;
    function __construct()
    {
        $this->model = new User;
    }
    function validation($request)
    {
        $validator = Validator::make($request->all(), $request->rules());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        return $validator;
    }

    function store($data, $request)
    {
        $user =  $this->model->create(array_merge(
            $data->validated(),
            [
                'password' => bcrypt($request->password),
                // 'photo' => $request->file('photo')->store('users'),
            ]
        ));
        return $user;
    }



    function generateVerificationCode($mobile)
    {
        $verificationCode = mt_rand(1000, 9999);
        $user = $this->model->whereMobile($mobile)->first();
        $user->verification_code = $verificationCode;
        $user->save();
        return $verificationCode;
    }


    function sendMessage($message, $recipients)
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create(
            $recipients,
            ['from' => $twilio_number, 'body' => $message]
        );
    }

    function register($request)
    {
        try {
            DB::beginTransaction();
            $data =  $this->validation($request);
            $user = $this->store($data, $request);
            $verificationCode = $this->generateVerificationCode($user->mobile);
            $this->sendMessage($verificationCode, '+2' . $user->mobile);
            DB::commit();
            return response()->json([
                "message" => "account has been created please check your Mobile"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
