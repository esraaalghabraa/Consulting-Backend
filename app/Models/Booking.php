<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $guarded=[];
    public $timestamps=false;
    protected $hidden=['user_id','expert_id','date_id','created_at','updated_at'];

    public function user(){
        return $this -> belongsTo('App\Models\User');
    }
    public function expert(){
        return $this -> belongsTo('App\Models\Expert');
    }
    public function date(){
        return $this -> belongsTo('App\Models\Date','date_id');
    }
}
