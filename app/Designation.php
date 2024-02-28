<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
	use SoftDeletes;
    public $timestamps=false;
    protected $table = 'designations';
    protected $fillable = [
        'title','description', 'department_id'
    ];
      public function department()
    {
        return $this->belongsTo('App\Client');
    }

}
