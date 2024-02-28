<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HolidayCalendar extends Model
{
   use SoftDeletes;
    protected $fillable = [
    	
        'title','department_id','date_from','date_to','description'
    ];
     public $timestamps = false;
}
