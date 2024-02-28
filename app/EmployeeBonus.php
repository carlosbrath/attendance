<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeBonus extends Model
{
	 protected $table="employee_bonuses";
	 protected $fillable=['user_id','amount','date','status','remarks'];
     public function users(){
        return $this->belongsTo('App\User','user_id');
    }
}
