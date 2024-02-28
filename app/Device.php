<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{

    protected $table='devices';
    protected $fillable=['title','serial_number','department_id','remarks','status','lastActivity'];
    public $timestamps = false;

    public function client(){
        return $this->belongsTo('App\Client','department_id');
    }   
}
