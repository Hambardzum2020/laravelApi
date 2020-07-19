<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UserModel;
use Validator;
use Hash;



class UserController extends Controller
{
    public function register(Request $r){
        $v = Validator::make($r->all(),
        [
            "name" => "required",
            "email" => "required|email",
            "password" => "required|min:6|max:18",
            "comfirm_password" => "required|same:password"
        ]);
        if($v->fails()){
            return response()->json(['error' => $v->errors()]);
        }
        $users = new UserModel();
            $users->name = $r->name;
            $users->email = $r->email;
            $users->password = Hash::make($r->password);
            $users->save();
        $success['token'] = $users->createToken('MyApp')->accessToken;
        return response()->json(['success' => $success]);
    }





    public function login(Request $r){
        $v = Validator::make($r->all(),
        [
            "email" => "required|email",
            "password" => "required|min:6|max:18",
        ]);
        $data = UserModel::where('email', $r->email)->first();
        $v->after(function($v) use ($data, $r){
            if(!$data){
                $v->errors()->add('email', 'chka tenc email');
            }
            else if(!Hash::check($r->password, $data['password'])){
                $v->errors()->add('password', 'sxal parol');
            }
        });
        if($v->fails()){
            return response()->json(['error' => $v->errors()]);
        }
        $success['token'] = $data->createToken('MyApp')->accessToken;
        UserModel::where('email', $r->email)->update(['token' => $success['token']]);
        return response()->json(['success' => $success]);

    }


    public function edit(Request $r){
        $token = UserModel::where('token', $r->token)->first();
        if($r->token == '' || $r->token != $token->token){
            return response()->json(['error' => "grancvac che"]);
        }
        $v = Validator::make($r->all(),
            [
                "name" => "required",
                "email" => "required|email",
            ],
        );
        $email_valid = UserModel::where('email', $r->email)->where('email', '!=', $token->email)->first();
        $v->after(function($v) use ($r, $email_valid){
            if($email_valid){
                $v->errors()->add('email', 'nman mail arden ka');
            }
        });
        if($v->fails()){
            return response()->json(['error' => "error"]);
        }
        else{
            UserModel::where('token', $r->token)->update([
                'name' => $r->name,
                'email' => $r->email
            ]);
            return UserModel::all();
        }
    }

    public function logout(){

    }
}
