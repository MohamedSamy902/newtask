<?php

namespace App\Services\UserService;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserLoginService
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

    function isValidData($data)
    {
        if (!$token = auth()->attempt($data->validated())) {
            return response()->json(['error' => 'invalid data'], 401);
        }
        return $token;
    }

    function getStatus($mobile)
    {
        $user =  $this->model->whereMobile($mobile)->first();
        $status = $user->status;
        return $status;
    }

    function isVerified($mobile)
    {
        $user =  $this->model->where('mobile', $mobile)->first();
        $verified = $user->verified_at;
        return $verified;
    }
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,

            'user' => auth()->user()
        ]);
    }
    function login($request)
    {
        $data =  $this->validation($request);
        $token = $this->isValidData($data);
        if ($this->isVerified($request->mobile) == null) {
            return response()->json(["message" => "your account is not verified"], 422);
        } elseif ($this->getStatus($request->mobile) == 'inactive') {
            return response()->json(["message" => "your account is Bloked"], 422);
        }

        return $this->createNewToken($token);
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }
}
