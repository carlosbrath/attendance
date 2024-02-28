<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
	protected $table='attendance';
   protected $fillable = ['user_id','att_checktime','datetime','dev_id','att_checkinout_id','mark_attendance'];
   public $timestamps=false;
}
