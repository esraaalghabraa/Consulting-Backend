<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Expert  extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden=['category_id','pivot','password','created_at','updated_at','remember_token'];

    protected function Photo(): Attribute{
        return Attribute::make(
            get:fn ($value) => ($value != null) ? asset('assets/images/experts/'. $value) : asset('assets/images/experts/default.webp')
        );
    }

    public function category(){
        return $this -> belongsTo('App\Models\Category');
    }
    public function experiences(){
        return $this -> hasMany('App\Models\Experience');
    }
    public function bookings(){
        return $this -> hasMany('App\Models\Booking');
    }
    public function workDays(){
        return $this -> belongsToMany('App\Models\Day','experts_days');
    }
    public function dates(){
        return $this -> hasMany('App\Models\Date','expert_id');
    }
    public function users(){
        return $this -> belongsToMany('App\Models\User','users_experts');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
