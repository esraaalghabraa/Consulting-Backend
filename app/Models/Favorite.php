<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $table="users_experts";
    protected $guarded=[];
    public $timestamps=false;
    public function user(){
        return $this -> belongsTo('App\Models\User');
    }
    public function expert(){
        return $this -> belongsTo('App\Models\Expert');
    }

}
