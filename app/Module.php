<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
   use SoftDeletes;
   public $timestamps = false;
   protected $table = 'modules';
   protected $fillable = [
    'name', 'route','parent_id'
    ];
    public function rights(){
    	return $this->hasMany("App\ModuleRight",'module_id','id');
    }

      public function role(){
    	return $this->belongsToMany("App\Role");
      }


     public function manyRoles(){
        
        return $this->belongsToMany('App\Role', 'assign_rights', 'module_id', 'role_id');
     
      }

   





}
