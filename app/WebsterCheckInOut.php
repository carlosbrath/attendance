<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsterCheckInOut extends Model
{
    protected $connection = 'mysql2';
    protected $table='webster_checkinout';
    protected $fillable=['userid','checktime','checktype','verifycode','serialno','workcode','reserved','flag'];
    public $timestamps = false;
}
