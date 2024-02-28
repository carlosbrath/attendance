<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SyncHistory extends Model
{
    protected $table='sync_history';
    protected $fillable = ['sync_on','stats','extra'];
   public $timestamps=false;
   
}
