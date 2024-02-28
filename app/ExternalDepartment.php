<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExternalDepartment extends Model
{
    public $timestamps = false;
    protected  $table = "external_departments";
    protected $fillable = ['department_name','total_employees','login_link','stats_api','last_updated'];
    //


}
