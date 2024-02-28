<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class RoleJurisdiction extends Model
{

   use SoftDeletes;	 
   public $timestamps = false;	
   protected $table = 'role_jurisdiction';
   protected $fillable = [
        'role_id', 'departments','parent_departments','regions','zones','branches'
   ];
   
}
