<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class Role extends Model
{

   use SoftDeletes;	 
   public $timestamps = false;	
   protected $table = 'roles';
   protected $fillable = [
        'title', 'description'
    ];
  

   public function rights(){
    
   	  return $this->hasMany(ModuleRight::class,'module_id','id');
    }
    
      public function modules(){
      
        return $this->belongsToMany("App\Module");
      
      }
      public function jurisdiction(){
      
        return $this->belongsTo("App\RoleJurisdiction");
      
      }



    public function module_name(){
        
        return $this->belongsToMany('App\Module', 'assign_rights', 'role_id', 'module_id');
     
     }
  
 
   
}
