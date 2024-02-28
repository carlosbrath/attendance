<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject {
    use Notifiable;
    public $timestamps = false;
    protected $fillable = [
      'id','name','father_name','password','fixed_salary','hour_rate_salary','email','designation_id','department_id','role_id',
      'father_name','cnic','bps','mobile_number','file_number','status','dob','cnic','gender','land_line_number','mob_number_2',
      'region_id','zone_id','branch_id','tcat_id','flag','contract_type','app_attendance','reg_date','cnic_issue_date',
      'cnic_expiry_date','is_gazetted','jurisdiction_id','tags','field_staff','emp_type','remarks'
    ];

    public function role(){
        return $this->belongsTo('App\Role');
    }
    public function designations(){
        return $this->belongsTo('App\Designation','designation_id');
    }
    public function time_catgories(){
        return $this->belongsToMany('App\TimeCategory', 'time_category_histories','user_id','tcat_id');
    }
    public function departments(){
        return $this->belongsTo('App\Client','department_id');
    } 

        /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }   
    public function timeCategory(){
        return $this->hasOne(TimeCategory::class,'id','tcat_id');
    }         
}
