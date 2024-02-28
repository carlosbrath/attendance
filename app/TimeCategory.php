<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TimeCategory extends Model
{
    use SoftDeletes;
    protected $table = 'time_categories';
	public $timestamps=false;
	protected $fillable = [
		'title','department_id','type','time_in', 
		 'time_out',
		 'tc_monday_in',
		 'tc_monday_out',
		 'tc_tuesday_in',
		 'tc_tuesday_out',
		 'tc_wednesday_in',
		 'tc_wednesday_out',
		 'tc_thursday_in',
		 'tc_thursday_out',
		 'tc_friday_in',
		 'tc_friday_out',
		 'tc_saturday_in',
		 'tc_saturday_out',
		 'tc_sunday_in',
		 'tc_sunday_out',
		 'grace_time',
		 'is_night_shift',
         'hours',
         'color',
         'time_cat_type',
         'leave_type'
	];
	 public function departments(){
        return $this->belongsTo('App\Client','department_id','id');
    }  

}
