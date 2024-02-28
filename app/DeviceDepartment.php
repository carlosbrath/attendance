<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceDepartment extends Model
{

    protected $table='devices_other_departments';
    protected $fillable=['device_id','dep_id'];
    public $timestamps = false;

    public function device(){
        return $this->belongsTo('App\Device','device_id');
    }

    // In your DeviceDepartment model
    public function client()
    {
        return $this->belongsTo(Client::class, 'dep_id', 'id');
    }
}
