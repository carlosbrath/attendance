<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class AssignRight extends Model
{

    protected $table='assign_rights';
     
     protected $fillable = [
       'role_id','module_id', 'create_at', 'update_at'
    ];

  
    
     public function role()
    {
        return $this->belongsTo('App\Role');
    }
    public function module()
    {
        return $this->belongsTo('App\Module');
    }
        public $timestamps = false;

}
