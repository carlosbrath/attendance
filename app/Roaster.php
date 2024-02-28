<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Roaster extends Model
{
	use SoftDeletes;	  
    protected $table = 'roasters';
    public $timestamps=false;
    protected $fillable = [
        'title', 'month', 'year','department_id'
    ];
   

         
        
}
