<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function Register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191',
            'phone'=>'required',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        }
        else{
            $user = User::create([
                'first_name'=>$request->first_name,
                'last_name'=>$request->last_name,
                'phone'=>$request->phone,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
            ]);
            $token = $user->createToken($user->email.'_Token')->plainTextToken;
            return response()->json([
                'status'=> 200,
                'userDetails'=>$user,
                'token'=>$token,
                'message'=>'user registered successfuly'
            ]);
        }
    }


    public function Login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:191',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        }
        else{
            $user = User::where('email',$request->email)->first();
            if (! $user || ! Hash::check($request->password,$user->password)){
               return response()->json([
                'status'=>401,
                'message'=>'Invalid Inputs'
               ]);
            }
            else{
                $token = $user->createToken($user->email.'_Token')->plainTextToken;
                $user = User::where('email',$request->email)->first();
                return response()->json([
                    'status'=> 200,
                    'userDetails'=>$user ,
                    'token'=>$token,
                    'message'=>'user Logged In successfuly'
                ]);
            }
        }
    }


    public function Upgrade(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191',
            'phone'=>'required',
            'email' => 'required|email|max:191|',
            'oldEmail' => 'required|email|max:191',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        }
        else{
            $user = User::where('email',$request->oldEmail)->first();
            $user->first_name=$request->first_name;
            $user->last_name=$request->last_name;
            $user->phone=$request->phone;
            $user->email=$request->email;
            $user->password=Hash::make($request->password);
            $user->save();
            $token = $user->createToken($user->email.'_Token')->plainTextToken;
            return response()->json([
                'status'=> 200,
                'userDetails'=>$user,
                'token'=>$token,
                'message'=>'user registered successfuly'
            ]);
        }
    }


}
