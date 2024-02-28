<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ModuleRight extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'role_id', 'module_id'
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
