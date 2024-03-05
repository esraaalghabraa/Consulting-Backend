<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Helpers\StatusCodeRequest;
use App\Http\Resources\ExpertInfoResource;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Date;
use App\Models\Day;
use App\Models\Experience;
use App\Models\Expert;
use App\Models\Favorite;
use App\Models\User;
use App\Models\WorkDays;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ExpertController extends Controller
{
    use Response;
    public function __construct(){
        $this->middleware('auth.experts');
    }

    function addInfo(Request $request){
        try{
            $validator=Validator::make($request->all(),[
                'expert_id'=>'required|numeric|exists:experts,id',
                'photo'=>'required|mimes:jpg,jpeg,png,jfif',
                'address'=>'required|string|min:3|max:30',
                'category_id'=>'required|numeric|exists:categories,id',
                'start_work'=>'required|string',
                'end_work'=>'required|string',
                'work_days'=>'required',
                'experiences'=>'required',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            DB::beginTransaction();
            $path=$request->file('photo')->store('experts','images');
            $path=explode('/',$path);
            $expert=Expert::find($request->expert_id);
            $expert->update([
                'photo'=>$path[1],
                'address'=>$request->address,
                'category_id'=>$request->category_id,
                'start_work'=>$request->start_work,
                'end_work'=>$request->end_work,
            ]);

            $experiences=json_decode($request->experiences);
            foreach ($experiences as $experience){
                Experience::create([
                    'name'=>$experience,
                    'expert_id'=>$request->expert_id,
                ]);
            }

            $times=divideTime($request->start_work,$request->end_work);
            $days=json_decode($request->work_days);
            foreach ($days as $day){
                WorkDays::create([
                    'expert_id'=>$request->expert_id,
                    'day_id'=>$day,
                ]);
                foreach ($times as $time){
                    Date::create([
                        'expert_id'=>$request->expert_id,
                        'day_id'=>$day,
                        'time'=>$time,
                    ]);
                }
            }
            DB::commit();
            return $this->handleResponse(null,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            DB::rollback();
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    function getInfo(Request $request){
        try{
            $validator=Validator::make($request->all(),[
                'expert_id'=>'required|numeric|exists:experts,id',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $expert=Expert::with('experiences')
                ->with(['category'=>function($q){
                return $q->select('id','name_ar','name_en');
            }])->with('workDays')->find($request->expert_id);
            return $this->handleResponse(new ExpertInfoResource($expert),'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    function getCategoriesAndDays(){
        try{
            $categories=Category::select('id','name_ar','name_en')->get();
            $days=Day::get();
            return $this->handleResponse(['categories'=>$categories,'days'=>$days],'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    function getExpertBookings(Request $request){
        try{
            $validator=Validator::make($request->all(),[
                'expert_id'=>'required|numeric|exists:experts,id',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $expert=Expert::with(['bookings'=>function($q){
                return $q->with(['user'=>function($q){
                    return $q->select('id','name','photo');
                }])->with(['date'=>function($q){
                    return $q->select('id','time','day_id')->with('day');
                }]);
            }])->find($request->expert_id);
            return $this->handleResponse($expert->bookings,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    function getExpertFollows(Request $request){
        try{
            $validator=Validator::make($request->all(),[
                'expert_id'=>'required|numeric|exists:experts,id',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $expert=Expert::with(['users'=>function($q){
                return $q->select('users.id','name','phone_number','photo');
            }])->find($request->expert_id);
            return $this->handleResponse($expert->users,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
}
