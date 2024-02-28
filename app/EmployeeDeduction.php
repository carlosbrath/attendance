<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    public $table='employees_deductions';
    protected $fillable=['deduction_id','employee_id','amount'];
    public $timestamps = false;
}
