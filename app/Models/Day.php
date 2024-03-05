<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;
    protected $guarded=[];
    public $timestamps=false;
    protected $hidden=['pivot'];

    public function experts(){
        return $this -> belongsToMany('App\Models\Expert','experts_days');
    }
}
