<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class RoasterStaff extends Model
{
     use SoftDeletes;
    protected $fillable = [
          'user_id', 'tcat_id','month','date','roster_id','year'
    ];
     public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function tcat()
    {
        return $this->belongsTo('App\TimeCategory','tcat_id','id');
    }

    
     public $timestamps = false;
}
