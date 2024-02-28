<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExternalDepartmentsStat extends Model
{
    
    public $timestamps = false;
    protected  $table = "external_departments_stats";
    protected $fillable = ['dep_id','total','present','absent','late','onleave','ontour','onschedule','onattach','shift_not_started','early_exit','off_days','updated_date'];
}
