<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TransferHistory extends Model
{
    use SoftDeletes;
    protected $table = 'transfer_histories';
    public $timestamps=false;
    protected $fillable = [
        'user_id','from_dep_id','to_dep_id','date'

    ];


}
