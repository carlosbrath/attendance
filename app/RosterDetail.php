<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class RosterDetail extends Model
{
	 use SoftDeletes;
	  protected $fillable = [
          'user_id', 'tcat_id','roster_id','tcat_from_date','tcat_to_date'
    ]; 
    //
}
