<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkDays extends Model
{
    use HasFactory;
    protected $table='experts_days';
    protected $guarded=[];
    public $timestamps=false;
}
