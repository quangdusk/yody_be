<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Jobs\RegisterAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AuthController extends Controller
{
    public function register(UserRequest $request)
    {
        $userCheck = User::where('email', $request['email'])->orWhere('phone', $request['email'])->orWhere('username', $request['email'])->first();
        if ($userCheck) {
            return response(['message' => 'Tài khoản đã được sử dụng'], 400);
        }
        $user = User::create([
            'code' => $request['code'] ? $request['code'] : null,
            'type' => $request['type'] ? $request['type'] : null,
            'name' => $request['name'] ? $request['name'] : null,
            'phone' => $request['phone'] ? $request['phone'] : null,
            'email' => $request['email'] ? $request['email'] : null,
            'address' => $request['address'] ? $request['address'] : null,
            'username' => $request['username'] ? $request['username'] : null,
            'password' => $request['password'] ? Hash::make($request['password']) : null,
            'cityId' => $request['cityId'] ? $request['cityId'] : null,
            'status' => 1,
            'cmnd' => $request['cmnd'] ? $request['cmnd'] : null,
            'birthday' => $request['birthday'] ? $request['birthday'] : null,
            'images' => $request['images'] ? $request['images'] : null,
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        \Amqp::publish('send-mail-register-account', $user->toArray()['email'], ['queue' => 'send-mail-register-account', 'exchange' => 'exchange_' . config('config.merchant_name')]);

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response(['message' => 'Đăng xuất thành công']);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])->orWhere('phone', $data['email'])->orWhere('username', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response(['message' => 'Invalid Credentials'], 401);
        } else {
            $token = $user->createToken('authToken')->plainTextToken;
            $response = [
                'user' => $user,
                'token' => $token
            ];
            return response($response, 200);
        }
    }
}
