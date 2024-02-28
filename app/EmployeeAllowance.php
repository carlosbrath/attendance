<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeAllowance extends Model
{
    public $table='employees_allowances';
    protected $fillable=['allowance_id','employee_id','amount'];
    public $timestamps = false;
    
}
