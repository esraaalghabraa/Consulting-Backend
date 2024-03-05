<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded=[];
    public $timestamps=false;

    protected function Photo(): Attribute{
        return Attribute::make(
            get:fn ($value) => ($value !== null) ? asset('assets/images/categories/'. $value) :  asset('assets/images/categories/Medical.jfif')
        );
    }

    public function experts(){
        return $this -> hasMany('App\Models\Expert');
    }
}
