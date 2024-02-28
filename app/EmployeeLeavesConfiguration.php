<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeavesConfiguration extends Model
{
	public $timestamps = false;
    protected  $table = "emp_leave_configuration";
    protected $fillable = ['leave_type_id','emp_id','total_leaves'];
}
