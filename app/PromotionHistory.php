<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PromotionHistory extends Model
{
    use SoftDeletes;
    protected $table = 'promotion_histories';
    public $timestamps=false;
    protected $fillable = [
        'user_id','old_designation','new_designation','old_scale', 'new_scale', 'date'

    ];


}
