<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeDedicatedOffDay extends Model
{
   
    protected $guarded=[];
     public $timestamps = false;
     public function users(){
        return $this->belongsTo('App\User','user_id');
    }
}
