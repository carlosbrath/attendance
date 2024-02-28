<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\User;
use App\Client;
use DB;

class PayrollController extends Controller
{
    public function report(Request $request){
    
          $dep_id=Session::get('dep_id');
          $data['department']=Client::where('parent_id',$dep_id)->get();
          $employees=User::whereIn('department_id',Client::where('parent_id',$dep_id)->pluck('id'));
          $data['department_users'] = $employees->get();
          if($request->has('user_id') && $request->has('from_date')  && $request->has('to_date') ){
           $data['empData'] = $employees->where('id',$request->user_id)->first();

           $data['from_date'] = $request->from_date;
           $data['to_date'] = $request->to_date;
          
           $individual_report_att = "SELECT 
                                        u.id as user_id,
                                        u.name as user_name,
                                        att.datetime as checktime,
                                        att.mark_attendance,
                                        att.dev_id,
                                        date(att.datetime) as att_date,
                                        tcat.id as tcat_id,
                                            tcat.time_in as tcat_time_in,
                                            tcat.is_night_shift as is_night_shift,
                                            tcat.tc_monday_in as monday_in,
                                            tcat.tc_monday_out as monday_out,
                                            tcat.tc_tuesday_in as tuesday_in,
                                            tcat.tc_tuesday_out as tuesday_out,
                                            tcat.tc_wednesday_in as wednesday_in,
                                            tcat.tc_wednesday_out as wednesday_out,
                                            tcat.tc_thursday_in as thursday_in,
                                            tcat.tc_thursday_out as thursday_out,
                                            tcat.tc_friday_in as friday_in,
                                            tcat.tc_friday_out as friday_out,
                                            tcat.tc_satureday_in as satureday_in,
                                            tcat.tc_satureday_out as satureday_out,
                                            tcat.tc_sunday_in as sunday_in,
                                            tcat.tc_sunday_out as sunday_out,
                                            tcat.time_out as tcat_time_out,
                                            tcat.grace_time as tcat_grace_time
                                        from users u inner join 
                                        attendance att on u.id= att.user_id

                                        INNER join time_categories tcat on tcat.id=u.tcat_id
                                    where  (date(att.datetime) 
                                BETWEEN '".date('Y-m-d', strtotime($request->from_date.' -1 day'))."' AND '".$request->to_date."') and u.id='".$request->user_id."' 
                                    group By att.datetime";
            $data['attendance'] = collect(DB::select( DB::raw($individual_report_att)));
           
           
          }else{
            $data['empData']="";
          }
       
          return view('Reports.payroll',$data);

    }
}
