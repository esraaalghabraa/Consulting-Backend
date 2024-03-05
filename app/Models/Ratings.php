<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratings extends Model
{
    use HasFactory;
    protected $guarded=[];
    public $timestamps=false;

    public function user(){
        return $this -> belongsTo('App\Models\User');
    }
    public function expert(){
        return $this -> belongsTo('App\Models\Expert');
    }
}
