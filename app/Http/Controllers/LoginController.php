<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)){
            return response([
                "message" => "Usuario y/o contraseña es invalido",
                "success" => false,
                "request_code"=> 401
            ], 401);
        }

        $accessToken = Auth::user()->createToken('authTestToken')->accessToken;

        return response([
            "user" => Auth::user(),
            "acces_token" => $accessToken,
            "success" => true,
            "request_code"=> 401
        ], 200);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|numeric|unique:users',
            'document' => 'required|numeric|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'=>$validator->errors(),
                "success" => false,
                "request_code"=> 422
            ], 422);
        }

        try {
            $input = $request->all();
            $input['password'] = bcrypt($request->get('password'));
            $user = User::create($input);
            $token =  $user->createToken('MyApp')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
                'success' => true,
                "request_code"=> 200
            ], 200);
        } catch (\Throwable $th) {
            Log::debug($th->getMessage());
            return response()->json([
                'error'=>'Error al registrar al usuario',
                "success" => false,
                "request_code"=> 500
            ], 500);
        }

    }
}
