<?php

namespace App\Http\Controllers;
use App\Helpers\Response;
use App\Helpers\StatusCodeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    use Response;
    public function __construct(){
        $this->middleware('auth.users', ['except' => ['login','register']]);
    }

    public function register(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:30',
                'phone_number' => 'required|string|min:10|max:30|unique:users',
                'password' => 'required|string|confirmed|min:6|max:255',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $user = User::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
            ]);
            $token = auth()->guard('api-users')->attempt($request->all());
            $user->setRememberToken($token);
            $user->save();
            $data['id']=$user->id;
            $data['remember_token']=$user->remember_token;
            return $this->handleResponse($data,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    public function login(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|min:10|max:30|exists:users',
                'password' => 'required|string|min:6|max:255',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $token = auth()->guard('api-users')->attempt($request->all());
            if (!$token)
                return $this->handleResponse(null,'Unauthorized',StatusCodeRequest::UNAUTHORISED);

            $user=User::where('phone_number',$request->phone_number)->first();
            $user->setRememberToken($token);
            $user->save();
            $data['id']=$user->id;
            $data['remember_token']=$user->remember_token;
            return $this->handleResponse($data,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    public function logout(){
        try{
            auth()->logout();
            return $this->handleResponse(null,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
}
