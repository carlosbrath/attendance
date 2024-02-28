<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HolidayCalendar extends Model
{
   use SoftDeletes;
    protected $fillable = [
    	
        'title','user_id','department_id','date','date_from','date_to','description','is_all_departments'
    ];
     public $timestamps = false;
     public function user()
     {
         return $this->belongsTo(User::class);
     }
     public function department()
     {
         return $this->belongsTo(Client::class);
     }
}
