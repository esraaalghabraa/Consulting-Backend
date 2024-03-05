<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Helpers\StatusCodeRequest;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Date;
use App\Models\Experience;
use App\Models\Expert;
use App\Models\Favorite;
use App\Models\Ratings;
use App\Models\User;
use App\Models\WorkDays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;

class UserController extends Controller
{
    use Response;
    public function __construct(){
        $this->middleware('auth.users');
    }

    function getUserInfo(Request $request){
        try{
            $validator=Validator::make($request->all(),[
                'user_id'=>'required|numeric|exists:users,id',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);
            $user=User::where('id',$request->user_id)->first();
            return $this->handleResponse($user,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    function getCategories(){
        try{
            $categories=Category::get();
            return $this->handleResponse($categories,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    function getCategoryExperts(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|numeric|exists:categories,id',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $category=Category::with(['experts'=>function($q){
                return $q->whereNot('address',null)->select('id','name','photo','rating','phone_number','address','category_id');
            }])->find($request->category_id);
            return $this->handleResponse($category->experts,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    function getExpert(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'expert_id' => 'required|numeric|exists:experts,id',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $expert=Expert::select('id','name','phone_number','address','photo','rating','rating_number',
                'start_work','end_work','category_id')->with(['category'=>function($q){
                    return  $q->select('id','name_ar','name_en');
                }])->with('experiences')->with('workDays')->find($request->expert_id);
            return $this->handleResponse($expert,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
   }
    function addBooking(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'date_id' => 'required|numeric|exists:dates,id',
                'expert_id' => 'required|numeric|exists:experts,id',
                'user_id' => 'required|numeric|exists:users,id',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            DB::beginTransaction();
            $user=User::where('id',$request->user_id)->first();
            if($user->money<1000){
                return $this->handleResponse(null,"you don't has enough money",StatusCodeRequest::BAD_REQUEST);
            }
            $user->money=$user->money-1000;
            $user->save();
            $expert=Expert::where('id',$request->expert_id)->first();
            $expert->money=$expert->money+1000;
            $expert->save();
            $date=Date::find($request->date_id);
            $date->update(['available'=>0]);
            $date->save();
            Booking::create([
                'user_id'=>$request->user_id,
                'expert_id'=>$request->expert_id,
                'date_id'=>$request->date_id,
            ]);
            DB::commit();
            return $this->handleResponse($date,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            DB::rollback();
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    function getAvailableDate(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'expert_id' => 'required|numeric|exists:experts,id',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $expert=Expert::with(['dates'=>function($q){
                    return $q->where('available',1);
                }])->find($request->expert_id);
            return $this->handleResponse($expert->dates,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    function changeFavorite(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'expert_id' => 'required|numeric|exists:experts,id',
                'user_id' => 'required|numeric|exists:users,id',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $favorite = Favorite::where('expert_id',$request->expert_id)->where('user_id',$request->user_id)->get();
            if($favorite->isEmpty()) {
                Favorite::create([
                    'expert_id' => $request->expert_id,
                    'user_id' => $request->user_id
                ]);
                return $this->handleResponse(['isFavorite'=>1], 'success', StatusCodeRequest::OK);
            }
            Favorite::where('expert_id',$request->expert_id)->where('user_id',$request->user_id)->delete();
            return $this->handleResponse(['isFavorite'=>0], 'success', StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    function getFavorites(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric|exists:users,id',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $favorites = User::with(['experts'=>function($q){
                return $q->select('experts.id','name','photo','rating','phone_number','address');
            }])->find($request->user_id);
            return $this->handleResponse($favorites->experts,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    function getBookings(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric|exists:users,id',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            $user = User::with(['bookings'=>function($q){
                return $q->with(['expert'=>function($q){
                    return $q->select('id','name','photo');
                }])->with(['date'=>function($q){
                    return $q->select('id','time','day_id')->with('day');
                }]);
            }])->find($request->user_id);
            return $this->handleResponse($user->bookings,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            DB::rollback();
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }
    function addRating(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'expert_id' => 'required|numeric|exists:experts,id',
                'user_id' => 'required|numeric|exists:users,id',
                'rating' => 'required|numeric|min:0|max:5',
            ]);
            if($validator->fails())
                return $this->handleResponse(null,$validator->errors()->first(),StatusCodeRequest::BAD_REQUEST);

            DB::beginTransaction();
            $rating=Ratings::where('expert_id',$request->expert_id)->where('user_id',$request->user_id)->first();
            if(!$rating){
                Ratings::create([
                    'expert_id' => $request->expert_id,
                    'user_id' => $request->user_id,
                    'rating' => $request->rating,
                ]);
            }else{
                $rating->update([
                    'rating' => $request->rating,
                ]);
                $rating->save();
            }
            $ratings=Ratings::where('expert_id',$request->expert_id)->get();
            $sum=0;
            foreach ($ratings as $rating){
                $sum+=$rating->rating;
            }
            $expert = Expert::find($request->expert_id);
            $expert->rating = $sum/count($ratings);
            $expert->rating_number = count($ratings);
            $expert->save();
            DB::commit();
            return $this->handleResponse(null,'success',StatusCodeRequest::OK);
        }catch (\Exception $e){
            DB::rollback();
            return $this->handleResponse(null,'Server failure : '.$e,StatusCodeRequest::SERVER_ERROR);
        }
    }

}
