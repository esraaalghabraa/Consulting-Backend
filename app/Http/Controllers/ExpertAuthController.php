<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Helpers\StatusCodeRequest;
use App\Http\Resources\ExpertInfoResource;
use App\Models\Category;
use App\Models\Day;
use App\Models\Expert;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ExpertAuthController extends Controller
{
    use Response;
    public function __construct(){
        $this->middleware('auth.experts', ['except' => ['login','register']]);
    }
    public function register(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:30',
                'phone_number' => 'required|string|min:10|max:30|unique:experts',
                'password' => 'required|string|confirmed|min:6|max:255',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $expert = Expert::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
            ]);
            $token = auth()->guard('api-experts')->attempt($request->all());
            $expert->setRememberToken($token);
            $expert->save();
            $data['id']=$expert->id;
            $data['remember_token']=$expert->remember_token;
            return $this->handleResponse($data,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    public function login(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|min:10|max:30|exists:experts',
                'password' => 'required|string|min:6|max:255',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $token = auth()->guard('api-experts')->attempt($request->all());
            if (!$token)
                return $this->handleResponse(null,'Unauthorized',StatusCodeRequest::UNAUTHORISED);

            $expert=Expert::where('phone_number',$request->phone_number)->first();
            $expert->setRememberToken($token);
            $expert->save();
            $expert=Expert::with('experiences')->with('category')->with('workDays')->find($expert->id);
            $data['id']=$expert->id;
            $data['remember_token']=$expert->remember_token;
            return $this->handleResponse($data,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    public function logout(){
        try{
            Auth::logout();
            return $this->handleResponse(null,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }

}
