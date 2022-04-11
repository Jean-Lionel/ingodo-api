<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
       public function register(Request $request)
    {
        try{
            $attributes= $request->validate(
                [
                    'name'=>'required|min:3|max:30',
                    'email'=>'required|email|unique:users',
                    'password'=>'required|min:6|max:30',
                ]
            );

            $attributes['password']= bcrypt($attributes['password']);

            $user = User::create($attributes);

            $message= "Account created successfully, welcome aboard.";
            $success= true;
        } catch(Exception $e){
            $success= false;
            $message= $e->getMessage();
        }


        $response= [
            'success'=>$success,
            'message'=>$message,
        ];

        return response()->json($response);
    }

    public function login(Request $request)
    {
        $attributes= $request->validate(
            [
                'email'=>'required|email',
                'password'=>'required',
            ]
        );

        if(!auth()->attempt($attributes)){
            return response()->json(
                ['message'=>'These credentials do not match our records'], 401
            );
        }

        $user= auth()->user();
        $token= $user->createToken($user->id)->plainTextToken;

        $message= "User logged in successfully";
        $success= true;

        $response= [
            'success'=>$success,
            'message'=>$message,
            'data'=>[
                'user'=>$user,
                'token'=>$token,
            ],
        ];     

        return response()->json($response, 200);
    }


    public function logout()
    {
        Session::flush();
        $success= true;
        $message= 'User logged out successfully';

        $response= [
            'success'=>$success,
            'message'=>$message,
        ];

        return response()->json($response);
    }
}
