<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
   use SoftDeletes;
   public $timestamps = false;
   protected $table = 'leaves';
   protected $fillable = [
    'type', 'description'
    ];
   
}
