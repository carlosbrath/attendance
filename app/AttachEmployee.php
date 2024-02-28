<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AttachEmployee extends Model
{
    use SoftDeletes;
    public $timestamps=false;
	protected $fillable = [
		'user_id','from_date','to_date','time_from','time_to','parent_table_id','attach_emp_id','attachment_status'
	];
	 public function user(){
        return $this->belongsTo('App\User');
    }
}
