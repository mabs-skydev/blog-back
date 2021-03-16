<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Validator;
use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'errors'   => $validator->errors()
            ], 400);     
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
   
        return response()->json([
            'success'   => true,
            'token'     => $success['token'],
            'user'      => $user,
            'message'   => 'User register successfully.'
        ]);
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['name'] =  $user->name;
   
            return response()->json([
                'success'   => true,
                'message'   => 'User login successfully.',
                'token'     =>  $success['token'],
                'user'      =>  $user,
            ]);  
        } 
        else{ 
            return response()->json([
                'success'   => false,
                'message'   => 'Unauthorised.',
                'errors'    => 'Unauthorised',
            ], 400);
        } 
    }
}
