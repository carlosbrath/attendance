<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class DepartmentLevel extends Model
{

   use SoftDeletes;	 
   public $timestamps = false;	
   protected $table = 'department_levels';
   protected $fillable = [
        'title'
    ];    
}
