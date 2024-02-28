<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
	use SoftDeletes;
	protected $primaryKey = "region_id";
	protected $fillable = ['region_name','region_desc','address_line_1','address_line_2'];
	public $timestamps=false;

 
}
