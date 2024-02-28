<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use SoftDeletes;   
    protected $table='zones';
    protected $primaryKey='zone_id';
    public $timestamps = false;

}
