<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeCategoryHistory extends Model
{
    protected $table = 'time_category_histories';
    public $timestamps = false;

    public function timeCategory(){
        return $this->belongsTo(timeCategory::class,'tcat_id');
    }
    
}
