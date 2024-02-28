<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class leaveRequest extends Model
{
	use SoftDeletes;
	protected $table = 'leave_requests';
	protected $fillable = ['user_id', 'from_date','tcat_id', 'to_date','time_from','time_to'];

	 public function user()
    {
        return $this->belongsTo('App\User');
    }
    
}
