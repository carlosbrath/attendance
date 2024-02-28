<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Branch extends Model
{   
    protected $table='branches';
    protected $primaryKey='branch_id';
    public $timestamps = false;

}
