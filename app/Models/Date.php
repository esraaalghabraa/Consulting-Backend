<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden=['available','expert_id'];
    public $timestamps=false;

    public function expert(){
        return $this -> belongsTo('App\Models\Expert','expert_id');
    }
    public function day(){
        return $this -> belongsTo('App\Models\Day','day_id');
    }
}
