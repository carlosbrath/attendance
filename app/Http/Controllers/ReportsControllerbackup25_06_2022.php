<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Session;
use App\Roaster as roster;
use App\TimeCategory;
use App\User;
use DB;
use App\Client;
use Mail;
class ReportsController extends Controller
{
  public function reports(Request $request){
    $department_id = session::get('dep_id');
    $role_id = session::get('role_id');
    $report_date = date("Y-m-d");
    $user_attendance = [];
    $users = [];
    $choosen_departments = [];
    $leave_users =  [];
    $sleave_users =  [];
    $attach_users = [];
    $choosen_status = 'all';
    $holidays = [];
    if($request->has('generate_report')){
      
      $choosen_departments = $request->department;
      $choosen_status = $request->status;

      if($request->has('report_date')){
        $report_date = date("Y-m-d",strtotime($request->report_date));      
      }
      if(Session::get('role_id') == 7){
        //department head & we will show leave of its own department + sections
        $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$department_id." or c.id = ".$department_id;
      }else{
        $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$department_id." or c.id = ".$department_id;
        if($request->has('department')){
          $child_departments = Client::wherein('parent_id',$request->department)->pluck('id');
          $user_departments  = array_merge($child_departments->toArray(),$request->department);
          $users_department_clause = implode(",",$user_departments);
        }
      }
      $yesterday = date('Y-m-d',strtotime($report_date . "-1 days"));

      $hcal_sql ="SELECT hcal.id,hcal.title,hcal.user_id,
                   hcal.date as holiday_date,
                   hcal.department_id,
                   c.name as department_name,
                   hcal.date_from,
                   hcal.date_to 
            FROM holiday_calendars hcal JOIN clients c on hcal.department_id = c.id
               WHERE hcal.department_id in (".$users_department_clause.")
            and  
              hcal.`date` 
            = '".$report_date."'";  
      $holidays = collect(DB::select( DB::raw($hcal_sql)));
      
      $users_sql = "SELECT u.id as user_id,u.*,t.*,c.name as department_name,c.parent_id as d_parent_id,d.title as designation FROM users u 
                      JOIN time_categories t on t.id = u.tcat_id
                      JOIN clients c on c.id = u.department_id
                      JOIN designations d on d.id = u.designation_id
                      WHERE u.department_id IN (
                          ".$users_department_clause."
                      )
                    AND u.flag = 1 AND u.status = 1 ";
      $users = collect(DB::select($users_sql));
      $user_ids = implode(",", $users->pluck('user_id')->toArray());
      if($users->count()==0){ $user_ids = 0; }
      $leave_details_sql = "SELECT lrd.user_id FROM 
                                  `leave_requests_details` lrd
                            JOIN leave_requests lr on lr.id = lrd.parent_table_id
                            where lrd.user_id in (".$user_ids.") 
                            and lr.leave_type = 2
                            and date(lrd.leave_date) = '".$report_date."'";
      $leave_users = collect(DB::select($leave_details_sql));
      
      $sleave_details_sql = "SELECT lrd.user_id FROM 
                                  `leave_requests_details` lrd
                            JOIN leave_requests lr on lr.id = lrd.parent_table_id
                            where lrd.user_id in (".$user_ids.") 
                            and lr.leave_type = 1
                            and date(lrd.leave_date) = '".$report_date."'";
      $sleave_users = collect(DB::select($sleave_details_sql));
      $attach_details_sql = "SELECT distinct user_id FROM 
                                  `attach_employees_details` 
                            where user_id in (".$user_ids.") 
                            and date(attach_date) = '".$report_date."'";
      $attach_users = collect(DB::select($attach_details_sql));


      $users_attendance_sql = "SELECT 
                                a.`user_id`,
                                a.`datetime` AS attendance_time,
                                a.mark_attendance,
                                DATE(a.`datetime`) AS attendance_date
                              FROM attendance a WHERE a.`user_id` IN (
                                ".$user_ids."
                            )
                            AND DATE(a.`datetime`) 
                          BETWEEN DATE(DATE_SUB('".$report_date."', INTERVAL 1 DAY)) AND '".$report_date."'";
      $att_data_raw = collect(DB::select($users_attendance_sql));

      $att_today = collect($att_data_raw->where('attendance_date',$report_date));
      $att_yesterday = collect($att_data_raw->where('attendance_date',$yesterday));

      foreach ($users as $key => $user) {
        $attendance = array();
        $status = "absent";

        $tcat_data = TimeCategory::find($user->tcat_id);
        $tcat_day_check = "tc_".strtolower(date('l', strtotime($report_date)))."_in";
        if($tcat_day_check == 'tc_saturday_in'){
            $tcat_day_check = "tc_satureday_in";
        }
        if($tcat_day_check == 'tc_thursday_in'){
            $tcat_day_check = "tc_thursday_in";
        }

        if($user->tcat_id == ""){
            $status = "absent";
        }else if($tcat_data!=""){
            if($tcat_data->$tcat_day_check == ""){
              $status = "offday";
            }else{
              $status = "absent";
            }
        }

        $attendance['user_id'] = $user->user_id;
        $attendance['name']    = $user->name;
        $attendance['d_parent_id']    = $user->d_parent_id;
        $attendance['department_id']    = $user->department_id;
        $attendance['department_name']    = $user->department_name;
        $attendance['designation']    = $user->designation;
        $attendance['file_number']    = $user->file_number;
        $attendance['gender']    = $user->gender;
        $attendance['bps']    = $user->bps;

        $attendance['time_in'] = "--";
        $attendance['time_out'] = "--";
        /**
          here we are checking whether the employee is absent/present/late/leave/attach
        */
        if($user->time_cat_type == 2){ 
          //the user have 24 hours time category so lets check his attendance in previous date
          $att_records = $att_yesterday->where('user_id',$user->user_id);
          if($att_records->count()>0){
            $status = "present";
            $tcat_day_check = "tc_".strtolower(date('l', strtotime($yesterday)))."_in";
            $tcat_day_check_out = "tc_".strtolower(date('l', strtotime($yesterday)))."_out";
            if($tcat_day_check == 'tc_saturday_in'){
                $tcat_day_check = "tc_satureday_in";
            }
            if($tcat_day_check_out == 'tc_saturday_out'){
                $tcat_day_check_out = "tc_satureday_out";
            }
            $this_day_time_in = $yesterday." ".$user->$tcat_day_check;
            $this_day_time_out = $yesterday." ".$user->$tcat_day_check_out;
            $tcat_grace_time = "+".$user->grace_time." minutes";
            $this_day_time_in_with_grace_time =  date("Y-m-d H:i:s", strtotime($this->date2min($user->grace_time), strtotime($this_day_time_in)));
            $this_day_trxs = $att_records->pluck('attendance_time')->toArray();
            $this_day_in = $this_day_trxs[0];
            if(count($this_day_trxs)>1){
              $this_day_out = $this_day_trxs[count($this_day_trxs)-1];
            }else{
              $this_day_out = "Missing";
            }
            if($this_day_in>$this_day_time_in_with_grace_time){
              $status = "late";
            }

            if($this_day_out<$this_day_time_out){
              $status = "earlyout";
            }
            $attendance['time_in'] = $this_day_in;
            $attendance['time_out'] = $this_day_out;
            if($sleave_users->where('user_id',$user->user_id)->count()>0){
              $status = "s.leave";
            }
          }else{
            //check whether the employee is on leave or attach
            if($attach_users->where('user_id',$user->user_id)->count()>0){
              $status = "attach";
            }
            if($leave_users->where('user_id',$user->user_id)->count()>0){
              $status = "leave";
            }
            if($sleave_users->where('user_id',$user->user_id)->count()>0){
              $status = "s.leave";
            }
          }

        }else{
            if($leave_users->where('user_id',$user->user_id)->count()>0){
              $status = "leave";
            }
            if($sleave_users->where('user_id',$user->user_id)->count()>0){
              $status = "s.leave";
            }

          $att_records = $att_today->where('user_id',$user->user_id);
          if($att_records->count()>0){
            $status = "present";
            $tcat_day_check = "tc_".strtolower(date('l', strtotime($report_date)))."_in";
            $tcat_day_check_out = "tc_".strtolower(date('l', strtotime($report_date)))."_out";
            if($tcat_day_check == 'tc_saturday_in'){
                $tcat_day_check = "tc_satureday_in";
            }
            if($tcat_day_check_out == 'tc_saturday_out'){
                $tcat_day_check_out = "tc_satureday_out";
            }
            $this_day_time_in = $report_date." ".$user->$tcat_day_check;
            $this_day_time_out = $report_date." ".$user->$tcat_day_check_out;
            $tcat_grace_time = "+".$user->grace_time." minutes";
            $this_day_time_in_with_grace_time =  date("Y-m-d H:i:s", strtotime($this->date2min($user->grace_time), strtotime($this_day_time_in)));
            $this_day_trxs = $att_records->pluck('attendance_time')->toArray();
            $this_day_in = date("H:i:s A",strtotime($this_day_trxs[0]));

            if(count($this_day_trxs)>1){
              $this_day_out = date("H:i:s A",strtotime($this_day_trxs[count($this_day_trxs)-1]));
              $this_day_time_out_trx =  $this_day_trxs[count($this_day_trxs)-1];
            }else{
              $this_day_out = "Missing";
            }
            if($this_day_trxs[0]>$this_day_time_in_with_grace_time){
              $status = "late";
            }
            if($this_day_out !="Missing" && $this_day_time_out_trx<$this_day_time_out){
              $status = "earlyout";
            }
            if($sleave_users->where('user_id',$user->user_id)->count()>0){
              $status = "s.leave";
            }
            $attendance['time_in'] = $this_day_in;
            $attendance['time_out'] = $this_day_out;
          }else{
            //check whether the employee is on leave or attach
            if($attach_users->where('user_id',$user->user_id)->count()>0){
              $status = "attach";
            }
            if($leave_users->where('user_id',$user->user_id)->count()>0){
              $status = "leave";
            }
            if($sleave_users->where('user_id',$user->user_id)->count()>0){
              $status = "s.leave";
            }
          }
        }
        $attendance['status'] = $status;
        //dd($att_today->where('user_id',$user->id));
        $user_attendance[] = $attendance;
      };

      $user_attendance = collect($user_attendance);
      if($request->status == "present"){
        $user_attendance = $user_attendance->whereIn('status',['present','late']);
      }
      if($request->status == "absent"){
        $user_attendance = $user_attendance->where('status','absent');
      }
      if($request->status == "offday"){
        $user_attendance = $user_attendance->where('status','offday');
      }
      if($request->status == "late"){
        $user_attendance = $user_attendance->where('status','late');
      }
      if($request->status == "earlyout"){
        $user_attendance = $user_attendance->where('status','earlyout');
      }
      if($request->ordertype == 'desc'){
        $user_attendance = $user_attendance->sortByDesc($request->orderby);
      }else{
        $user_attendance = $user_attendance->sortBy($request->orderby);
      }
    }
    $departments = Client::where('parent_id',$department_id)->get();

    $data = [
      'users' => $users,
      'page_title' => 'Attendance Status Report',
      'departments' => $departments,
      'user_attendance' => $user_attendance,
      'leave_users' => $leave_users,
      'sleave_users' => $sleave_users,
      'attach_users' => $attach_users,
      'choosen_departments' => $choosen_departments,
      'status' => $choosen_status,
      'rep_date' => $report_date,
      'holidays' => $holidays,
      'role_id'  => $role_id,
      'orderby' => $request->orderby,
      'ordertype' => $request->ordertype
    ];
    return view('Reports.attendance_status_report',$data);
  }

  public function date_wise_attendance_compliance_report(Request $request){

    $choosen_departments = [];
    $choosen_designations = [];
    $holidays = [];
    $users = [];
    $report_type = "detailed";
    if($request->report_type == "summary"){
        $report_type = "summary";
    }
    $role_id  = Session::get('role_id');
    $dep_id  = Session::get('dep_id');
    if($request->has('search')){
        $date_form = $request->from_date;
        $date_to = $request->to_date;
        $date_from_min1 = date('Y-m-d',strtotime($date_form . "-1 days"));
        $date_to_plus1 = date('Y-m-d',strtotime($date_to . "+1 days"));

        $department_ids_sql = "SELECT id  FROM `clients` WHERE `parent_id` = ".$dep_id;
        $department_ids = collect(DB::select($department_ids_sql))->pluck('id');
        $users = User::where('status',1)->where('status',1)->whereIn('department_id',$department_ids);
        
        
        //lets apply filters
        if($request->has('department_id')  && $request->department_id != "all" ){
            //$users = $users->where('department_id',$request->department_id);    

            $department_ids_sql = "SELECT id  FROM `clients` WHERE `parent_id` = ".$request->department_id;
            $department_ids = collect(DB::select($department_ids_sql))->pluck('id');
            if(count($department_ids)==0){
                $department_ids[] = $request->department_id;
            }
            $users = User::with('TimeCategory')->where('status',1)->where('status',1)->whereIn('department_id',$department_ids);
        }

        $hcal_sql ="SELECT hcal.id,hcal.title,hcal.user_id,
               hcal.date as holiday_date,
               hcal.department_id,
               c.name as department_name,
               hcal.date_from,
               hcal.date_to 
        FROM holiday_calendars hcal JOIN clients c on hcal.department_id = c.id
           WHERE hcal.department_id IN (".implode(",",$department_ids->toArray()).")
        and  
          hcal.`date` 
        between '".$date_from_min1."' AND '".$date_to_plus1."'";

        $holidays = collect(DB::select( DB::raw($hcal_sql)));
        $users = $users->get();

        $attendance_sql = "SELECT 
                                u.id as user_id,
                                u.`name`,
                                u.`tcat_id`,
                                a.`datetime`,
                                date(a.datetime) as date_attendance,
                                t.*
                             FROM users u 
                            LEFT JOIN attendance a ON u.id = a.user_id
                            JOIN time_categories t ON t.id = u.`tcat_id`
                            WHERE u.`status` = 1 
                            AND u.`department_id` IN (".implode(",",$department_ids->toArray()).")
                            AND DATE(a.`datetime`) BETWEEN 
                            '".$date_from_min1."'
                            AND 
                            '".$date_to_plus1."'
                            order by a.datetime asc";
        $attendance = DB::select($attendance_sql);
        $attendance_by_user = [];
        
        foreach($users as $user){
            foreach($attendance as $att){
                
                if($att->user_id == $user->id){
                   $array_index = $user->id."-".date("ymd",strtotime($att->date_attendance));
                   $attendance_by_user[$array_index][] = $att;
                }
            }   
        }
        $leave_details_sql = "SELECT user_id,date(lad.leave_date) as leave_date FROM 
                              `leave_requests_details` lad
                             join users u on u.id = lad.user_id
                        where u.department_id in (".implode(",",$department_ids->toArray()).") 
                        and date(lad.leave_date) BETWEEN 
                            '".$date_from_min1."'
                            AND 
                            '".$date_to_plus1."'";
        $leave_users = collect(DB::select($leave_details_sql));

        $attach_details_sql = "SELECT user_id,date(aed.attach_date) as attach_date,time_from,time_to FROM 
                                      `attach_employees_details`  aed
                                join users u on u.id = aed.user_id
                                where u.department_id in (".implode(",",$department_ids->toArray()).") 
                            and date(aed.attach_date) BETWEEN 
                            '".$date_from_min1."'
                            AND 
                            '".$date_to_plus1."'";
        $attach_users = collect(DB::select($attach_details_sql));
        $startTime = strtotime($date_form);
        $endTime = strtotime($date_to);
        $user_attendance_cmb = array();
        // Loop between timestamps, 24 hours at a time
        for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
            foreach($users as $user){
                $in = "--";
                $out = "--";
                $status = "absent";
                $user_attendance = [];
                $thisDate = date( 'Y-m-d', $i );
                $yesterday = date('Y-m-d',strtotime($thisDate . "-1 days"));
                $user_date_index = $user->id."-".date("ymd",strtotime($thisDate));
                $user_pdate_index = $user->id."-".date("ymd",strtotime($yesterday));
                $this_day_attendance = [];
                $this_pday_attendance = [];
                if(array_key_exists($user_date_index,$attendance_by_user)){
                    $this_day_attendance = $attendance_by_user[$user_date_index];
                }
                if(array_key_exists($user_pdate_index,$attendance_by_user)){
                    $this_pday_attendance = $attendance_by_user[$user_pdate_index];
                }

                $attach_user_detail = $attach_users->where('attach_date',$thisDate)->where('user_id',$user->id);
                if(count($this_day_attendance) == 0){
                  $full_day = $this->dateToFullDay($thisDate);
                  $this_day_tin_pointer = "tc_".$full_day."_in";
                  $this_day_tout_pointer = "tc_".$full_day."_out";
                  $user_tcat = $user->TimeCategory;
                  $tcat_expected_hours = (strtotime($thisDate." ".$user_tcat->$this_day_tout_pointer) - strtotime($thisDate." ".$user_tcat->$this_day_tin_pointer))/3600; 
                    if($leave_users->where('leave_date',$thisDate)->where('user_id',$user->id)->count()>0){
                        $user_attendance['date'] = $thisDate;
                        $user_attendance['user_id'] = $user->id;
                        $user_attendance['status'] = 'leave';
                        $user_attendance['in'] = $in;
                        $user_attendance['out'] = $out;
                        $user_attendance['expected_hours'] = $tcat_expected_hours;
                        $user_attendance['hours_worked'] = $tcat_expected_hours;
                    }else if($attach_user_detail->count()>0){
                        $attach_user_detail = $attach_users->where('attach_date',$thisDate)->where('user_id',$user->id)->first();
                        $status = 'attach';
                        if($attach_user_detail->time_from != "00:00:00"){
                            $status = "sd-attach";
                        }

                        $user_attendance['date'] = $thisDate;
                        $user_attendance['user_id'] = $user->id;
                        $user_attendance['status'] = $status;
                        $user_attendance['in'] = $in;
                        $user_attendance['out'] = $out; 
                        $user_attendance['expected_hours'] = $tcat_expected_hours;
                        $user_attendance['hours_worked'] = $tcat_expected_hours;
                    }else{
                        $tcat_data = TimeCategory::find($user->tcat_id);
                        $tcat_day_check = "tc_".strtolower(date('l', strtotime($thisDate)))."_in";
                        if($tcat_day_check == 'tc_saturday_in'){
                            $tcat_day_check = "tc_satureday_in";
                        }
                        if($tcat_day_check == 'tc_thursday_in'){
                            $tcat_day_check = "tc_thursday_in";
                        }
                        $status = "absent";
                        if($tcat_data){
                        if($user->tcat_id == ""){
                            $status = "absent";
                        }else if($tcat_data->$tcat_day_check == ""){
                            $status = "offday";
                        }}else{
                            $status = "offday";
                        }
                    

                        $user_attendance['date'] = $thisDate;   
                        $user_attendance['user_id'] = $user->id;
                        $user_attendance['status'] = $status;
                        $user_attendance['in'] = $in;
                        $user_attendance['out'] = $out;

                        if($status == "absent"){
                          $user_attendance['expected_hours'] = $tcat_expected_hours;
                          $user_attendance['hours_worked'] = 0; 
                          $user_attendance['late_hours'] = 0; 
                        }else{
                          $user_attendance['expected_hours'] = 0;
                          $user_attendance['hours_worked'] = 0; 
                          $user_attendance['late_hours'] = 0; 
                        }   
                    }
                }else if(count($this_day_attendance)>0){

                    $is_night_shift = $this_day_attendance[0]->is_night_shift;
                    if($is_night_shift == 1){
                        $transaction = $this_day_attendance[0];

                        $full_day = $this->dateToFullDay($thisDate);
                        $this_day_tin_pointer = "tc_".$full_day."_in";
                        $this_day_tout_pointer = "tc_".$full_day."_out";
                        $user_tcat = $user->TimeCategory;
                        $tcat_expected_hours = (strtotime($thisDate." ".$user_tcat->$this_day_tout_pointer) - strtotime($thisDate." ".$user_tcat->$this_day_tin_pointer))/3600;

                        $tcat_day_check = "tc_".strtolower(date('l', strtotime($thisDate)))."_in";
                        if($tcat_day_check == 'tc_saturday_in'){
                            $tcat_day_check = "tc_satureday_in";
                        }

                        $tcat_out_check = "tc_".strtolower(date('l', strtotime($thisDate)))."_out";
                        if($tcat_out_check == 'tc_saturday_out'){
                            $tcat_out_check = "tc_satureday_out";
                        }
                        $tcat_timein = $yesterday." ".$transaction->$tcat_day_check;
                        $tcat_timeout = $thisDate." ".$transaction->$tcat_out_check;

                        //adjust 3 hours 
                        $tcat_timein = date('Y-m-d H:i:s', strtotime('-4 hours', strtotime($tcat_timein)));
                        $tcat_gracetime = date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime($tcat_timein)));
                        $tcat_timeout = date('Y-m-d H:i:s', strtotime('+4 hours', strtotime($tcat_timeout)));
                        foreach($this_pday_attendance as $ytda){
                            if($ytda->datetime >= $tcat_timein){
                                $in = $ytda->datetime;    
                            }
                        }
                        foreach($this_day_attendance as $tda){
                            if($tda->datetime <=$tcat_timeout){
                                $out = $tda->datetime;    
                            }
                        }

                        if($attach_users->where('attach_date',$thisDate)->where('user_id',$user->id)->count()>0){   
                           
                           $attach_user_detail = $attach_users->where('attach_date',$thisDate)->where('user_id',$user->id)->get();
                            $status = 'attach';
                            if($attach_user_detail->time_from != "00:00:00"){
                                $status = "sd-attach";
                            }
                        }else if($in != "--" || $out != "--"){
                            $status = "present";
                            if($in > $tcat_gracetime){
                                $status = "late";
                            }
                        }


                        $user_attendance['date'] = $thisDate;
                        $user_attendance['user_id'] = $transaction->user_id;
                        $user_attendance['status'] = $status;
                        $user_attendance['in'] = $in;
                        $user_attendance['out'] = $out;                        
                        if($status == "attach"){
                          $user_attendance['expected_hours'] = $tcat_expected_hours;
                          $user_attendance['hours_worked'] = (strtotime($tcat_timeout) - strtotime($tcat_timein))/3600;
                          $user_attendance['late_hours'] = 0;
                          $user_attendance['early_hours'] = 0;
                        }                 
                        if($status == "sd-attach"){
                          $user_attendance['expected_hours'] = ($tcat_expected_hours/2);
                          $user_attendance['hours_worked'] = (strtotime($out) - strtotime($in))/3600;
                          $user_attendance['late_hours'] = 0;
                          $user_attendance['early_hours'] = 0;
                        }                 
                        if($status == "present"){
                          $user_attendance['expected_hours'] = $tcat_expected_hours;
                          $user_attendance['hours_worked'] = (strtotime($out) - strtotime($in))/3600;
                          $user_attendance['late_hours'] = 0;
                          $user_attendance['early_hours'] = 0;
                        }                 
                        if($status == "late"){
                          $user_attendance['expected_hours'] = $tcat_expected_hours;
                          $user_attendance['hours_worked'] = (strtotime($out) - strtotime($in))/3600;
                          if(count($this_day_attendance)>1){
                            $user_attendance['late_hours'] = (strtotime($in) - strtotime($tcat_timein))/3600;
                            $early_hours = (strtotime($tcat_timeout) - strtotime($out))/3600;
                            if($early_hours<0){
                              $early_hours = 0;
                            }
                            $user_attendance['early_hours'] = $early_hours;
                          }else{
                            $user_attendance['late_hours'] = 0;  
                            $user_attendance['early_hours'] = 0;
                          }
                        }

                    }else{

                        $full_day = $this->dateToFullDay($thisDate);
                        $this_day_tin_pointer = "tc_".$full_day."_in";
                        $this_day_tout_pointer = "tc_".$full_day."_out";
                        $user_tcat = $user->TimeCategory;
                        $tcat_expected_hours = (strtotime($thisDate." ".$user_tcat->$this_day_tout_pointer) - strtotime($thisDate." ".$user_tcat->$this_day_tin_pointer))/3600;


                        $tcat_day_check = "tc_".strtolower(date('l', strtotime($thisDate)))."_in";
                        if($tcat_day_check == 'tc_saturday_in'){
                            $tcat_day_check = "tc_satureday_in";
                        }
                        $tcat_day_check_out = "tc_".strtolower(date('l', strtotime($thisDate)))."_out";
                        if($tcat_day_check_out == 'tc_saturday_out'){
                            $tcat_day_check_out = "tc_satureday_out";
                        }
                        $gracetime_arr = explode(':', $this_day_attendance[0]->grace_time);
                        $gracetime_hours = $gracetime_arr[0];
                        $gracetime_minutes = $gracetime_arr[1];
                        $gracetime_minutes = ($gracetime_hours*60)+$gracetime_minutes;
                        
                        $tcat_timein = $thisDate." ".$this_day_attendance[0]->$tcat_day_check;
                        $tcat_timeout = $thisDate." ".$this_day_attendance[0]->$tcat_day_check_out;

                        $in = $this_day_attendance[0]->datetime;
                        $out = $this_day_attendance[count($this_day_attendance)-1]->datetime;
                        $tcat_gracetime = date('Y-m-d H:i:s', strtotime('+'.$gracetime_minutes.' minutes', strtotime($tcat_timein)));
                        if($attach_users->where('attach_date',$thisDate)->where('user_id',$user->id)->count()>0){
                            $attach_user_detail = $attach_users->where('attach_date',$thisDate)->where('user_id',$user->id)->first();
                            $status = "attach";
                            if($attach_user_detail->time_from != "00:00:00"){
                                $status = "sd-attach";
                            }
                        }else if($in != "--" || $out != "--"){
                            $status = "present";
                            if($in > $tcat_gracetime && $this_day_attendance[0]->$tcat_day_check != null){
                                $status = "late";
                            }
                        }
                        $user_attendance['date'] = $thisDate;
                        $user_attendance['user_id'] = $user->id;
                        $user_attendance['status'] = $status;
                        $user_attendance['in'] = $in;
                        $user_attendance['out'] = $out;

                        if($status == "attach"){
                          $user_attendance['expected_hours'] = $tcat_expected_hours;
                          $user_attendance['hours_worked'] = $tcat_expected_hours;
                          $user_attendance['late_hours'] = 0;
                          $user_attendance['early_hours'] = 0;
                        }                 
                        if($status == "sd-attach"){
                          $user_attendance['expected_hours'] = ($tcat_expected_hours/2);
                          $user_attendance['hours_worked'] = (strtotime($out) - strtotime($in))/3600;
                          $user_attendance['late_hours'] = 0;
                          $user_attendance['early_hours'] = 0;
                        }                 
                        if($status == "present"){
                          $user_attendance['expected_hours'] = $tcat_expected_hours;
                          $user_attendance['hours_worked'] = (strtotime($out) - strtotime($in))/3600;
                          $user_attendance['late_hours'] = 0;
                          $user_attendance['early_hours'] = 0;
                        }                 
                        if($status == "late"){
                          $user_attendance['expected_hours'] = $tcat_expected_hours;
                          $user_attendance['hours_worked'] = (strtotime($out) - strtotime($in))/3600;
                          if(count($this_day_attendance)>1){
                            $user_attendance['late_hours'] = (strtotime($in) - strtotime($tcat_timein))/3600;  
                            $early_hours = (strtotime($tcat_timeout) - strtotime($out))/3600;
                            if($early_hours<0){
                              $early_hours = 0;
                            }
                            $user_attendance['early_hours'] = $early_hours;
                          }else{
                            $user_attendance['late_hours'] = 0;   
                            $user_attendance['early_hours'] = 0; 
                          }
                          
                        }
                    }
                }else{

                    $full_day = $this->dateToFullDay($thisDate);
                    $this_day_tin_pointer = "tc_".$full_day."_in";
                    $this_day_tout_pointer = "tc_".$full_day."_out";
                    $user_tcat = $user->TimeCategory;
                    $tcat_expected_hours = (strtotime($thisDate." ".$user_tcat->$this_day_tout_pointer) - strtotime($thisDate." ".$user_tcat->$this_day_tin_pointer))/3600;
                    $user_attendance['date'] = $thisDate;
                    $user_attendance['user_id'] = $user->id;
                    $user_attendance['status'] = $status;
                    $user_attendance['in'] = $in;
                    $user_attendance['out'] = $out;
                    $user_attendance['expected_hours'] = $tcat_expected_hours;
                    $user_attendance['hours_worked'] = 0;
                    $user_attendance['late_hours'] = 0;
                    $user_attendance['early_hours'] = 0;
                }
                $array_index = $user->id."-".date("ymd",strtotime($thisDate));
                $user_attendance_cmb[$array_index] = $user_attendance;
            }
        }
        $data['users'] = $users;
        $data['date_from'] = $request->from_date;
        $data['date_to'] = $request->to_date;
        $data['attendance'] = collect($user_attendance_cmb);
    }
    
    $data['page_title'] = "Attendance Compliance Report";
    $data['department'] =  Client::where('parent_id',Session::get('dep_id'))->pluck('name','id');

    //Client::where('parent_id',Session::get('dep_id'))->where('sub_account',1)->pluck('name','id');
    $data['sub_department'] = Client::where('parent_id',Session::get('dep_id'))->where('sub_account',2)->pluck('name','id');
    $data['report_type'] = $report_type;
    $data['holidays'] = $holidays;
    if($request->has('status')){
        $data['status'] = $request->status;            
    }else{
        $data['status'] = "all";
    }

    $data['choosen_department_id'] = $request->department_id;
    if($request->has('department_id') && $request->department_id!="all"){
        $data['choosen_department_name'] = Client::find($request->department_id)->name;
    }else{
        $data['choosen_department_name'] = "All Departments/Sub Sections";
    }
    return view('Reports.datewise_attendance_compliance_report',$data);
  }
  function dateToFullDay($date){
    $day = strtolower(date("D",strtotime($date)));
    switch($day){
      case 'sat':
        return 'satureday';
      break;
      case 'sun':
        return 'sunday';
      break;
      case 'mon':
        return 'monday';
      break;
      case 'tue':
        return 'tuesday';
      break;
      case 'wed':
        return 'wednesday';
      break;
      case 'thu':
        return 'thursday';
      break;
      case 'fri':
        return 'friday';
      break;
    }
  }
  function date2min ($gt) {
      $time=$gt.":00";
      $timesplit=explode(':',$time);
      $min=($timesplit[0]*60)+($timesplit[1])+($timesplit[2]>30?1:0);
      return "+ ".$min." minutes";
  }
    public function reports1(Request $request)
 {
  
    if($request->search=="all")  //when choosing all in reports dropdown
    {
       $emp_by_dep=DB::table('users')
            ->join('clients','users.department_id','clients.id')
            ->where('users.status',1)
            ->where('users.flag',1)
            ->where('clients.parent_id',session::get('dep_id'))
            ->count();
    }else{
        $emp_by_dep=User::where('department_id',$request->search)->where('flag',1)->where('status',1)->count();
    }
    
    $rep_date="";
    $date= date('Y-m-d');
    $day= date("l");
    $dep_id=Session::get('dep_id');
    if(Session::get('role_id')==1){
        $department=Client::where('parent_id','!=',0)->get();
    }else{
      
       $department=Client::where('parent_id',Session::get('dep_id'))->where('sub_account',1)->get(); 
       $sub_department= Client::where('parent_id',Session::get('dep_id'))
        ->where('sub_account',2)->get(); 
       if($department->isEmpty()){     
           $department=Client::where('id',Session::get('dep_id'))->where('status',1)->get();  
       }
      }                                     
       if($request->isMethod('post')){
                $date= date('Y-m-d');
                     $this->validate($request, [  
                               'date'=>'required'
                              ]);                           
            $date=date_create($request->date);
            date_format($date,"d F, Y");                
            $search=$request->search;
            $rep_date=$request->date;

            $weekday = date("l",strtotime($rep_date));
         if(Session::get('role_id')==5 ||  Session::get('role_id')==7){
              
            $client=Client::where('parent_id',$search)->first();
               $day_off=0;  
               $check_day= date('l', strtotime($rep_date));
            if(!empty($request->subdepartment_id)){

                     if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' ){     
                      $emp="select  users.id,
                                     users.name,
                                       users.bps,
                                        users.gender,
                                         users.dob,
                                           users.file_number,                     
                                            clients.name AS department_name,
                                             designations.title as designation 
                            from users
                            inner join clients on clients.id=users.department_id
                            inner join designations on designations.id= users.designation_id
                            where clients.parent_id='".$request->subdepartment_id."' 
                            and users.status=1 
                            and flag=1";
                       $fetch_emp=collect(DB::select( DB::raw($emp)));
                      $report_query_for_departmnent_adm  =  
                      "SELECT  u.id,
                                  u.name,
                                      u.bps,
                                       u.gender,
                                        u.dob,
                                         tcat.tc_tuesday_in AS check_dayoff,
                                          u.file_number,
                                           tcat.is_night_shift, 
                                             clients.name AS deparment_name,
                                              designations.title,
                                                tcat.tc_monday_in,
                                                  tcat.tc_tuesday_in,
                                                   tcat.tc_wednesday_in,
                                                    tcat.tc_thursday_in,
                                                      tcat.tc_friday_in,
                                                        tcat.tc_satureday_in,
                                                          tcat.tc_sunday_in,
                                                            tcat.grace_time,
                                                             date(att.datetime) as att_date,
                                                               att.datetime AS attendance_time,
                                                                 att.mark_attendance 
                        FROM users u 
                        LEFT JOIN attendance att ON u.id = att.user_id 
                        LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                        LEFT JOIN designations ON designations.id=u.designation_id
                        LEFT JOIN clients ON clients.id = u.department_id 
                        WHERE flag=1 
                        AND u.status=1  
                        and att.datetime is not null  
                        AND clients.parent_id='".$request->subdepartment_id."' 
                        and date(att.datetime) 
                        between '".$rep_date."' 
                        and '".$rep_date."'
                        ORDER BY u.id,att.datetime";              
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));


                      $daily_leave= "SELECT  u.id,
                                                lr.leave_type_id,
                                                 lr.leave_type,
                                                  lr.time_from,
                                                   lrd.leave_date,
                                                    lr.from_date,
                                                     lr.to_date,
                                                      lr.time_to,
                                                       u.name,
                                                        u.file_number,
                                                         u.bps,
                                                          u.gender,
                                                           u.dob,
                                                            d.title as desgnation,
                                                             c.name as deparment_name,
                                                              time_categories.title
                                      FROM users u 
                                      LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                      LEFT JOIN designations d ON d.id = u.designation_id 
                                      left join time_categories  on lr.leave_type_id=time_categories.id
                                      LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                      LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                      JOIN clients c ON c.id = u.`department_id` 
                                      WHERE flag=1 
                                      AND u.status=1 
                                      AND c.parent_id='".$request->subdepartment_id."'   
                                      AND DATE(lrd.leave_date)='".$rep_date."'
                                      group by u.id 
                                      ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                         $attach_emp  =  "select  
                                            users.id,
                                              users.name,
                                                users.file_number,
                                                  users.gender,
                                                    users.bps,
                                                       clients.name as department_name, 
                                                          designations.title as designation_id
                                          from users 
                                          INNER join clients on clients.id=users.department_id
                                          INNER join designations on designations.id=users.designation_id
                                          INNER JOIN attach_employees_details 
                                             on attach_employees_details.user_id=users.id
                                          where attach_employees_details.status=1 
                                          and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                      }    
                    
                     
                   if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all' ){
                       
                         $emp="select  users.id,
                                        users.name,
                                         users.bps,
                                          users.gender,
                                           users.dob,
                                            users.file_number,
                                             clients.name AS department_name,
                                               designations.title as designation 
                                from users
                                inner join clients on clients.id=users.department_id
                                inner join designations on designations.id= users.designation_id
                                where clients.parent_id='".$request->subdepartment_id."' 
                                and users.status=1 
                                and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                        $for_dayoff="select 
                                      users.id,
                                       users.name,
                                        users.tcat_id,
                                         time_categories.tc_monday_in,
                                           time_categories.tc_tuesday_in,
                                             time_categories.tc_wednesday_in,
                                               time_categories.tc_thursday_in,
                                                 time_categories.tc_friday_in,
                                                    time_categories.tc_satureday_in,
                                                     time_categories.tc_sunday_in
                                     from users INNER join clients
                                       on clients.id=users.department_id
                                     inner join time_categories 
                                      on time_categories.id=users.tcat_id
                                    WHERE clients.parent_id='".$request->subdepartment_id."'";
                       $day_off = collect(DB::select( DB::raw($for_dayoff)));  
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                           u.name,
                                            u.bps,
                                             u.gender,
                                              u.dob,
                                               tcat.tc_tuesday_in AS check_dayoff,
                                                u.file_number,
                                                 tcat.is_night_shift, 
                                                  clients.name AS deparment_name,
                                                   designations.title,
                                                    tcat.tc_monday_in,
                                                     tcat.tc_tuesday_in,
                                                      tcat.tc_wednesday_in,
                                                       tcat.tc_thursday_in,
                                                        tcat.tc_friday_in,
                                                         tcat.tc_satureday_in,
                                                          tcat.tc_sunday_in,
                                                           tcat.grace_time,
                                                             att.datetime AS attendance_time,
                                                               date(att.datetime) as att_date, 
                                                              att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  
                                  WHERE flag=1 
                                  AND u.status=1  
                                  and att.datetime is not null  
                                  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) 
                                  between '".$rep_date."' 
                                  and '".$rep_date."'
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      

                       $daily_leave= "SELECT  u.id,
                                              lr.leave_type_id,
                                               lr.leave_type,
                                                lr.time_from,
                                                 lrd.leave_date,
                                                  lr.from_date,
                                                   lr.to_date,
                                                    lr.time_to,
                                                     u.name,
                                                      u.file_number,
                                                       u.bps,
                                                        u.gender,
                                                         u.dob,
                                                          d.title as desgnation,
                                                           c.name as deparment_name,
                                                            time_categories.title
                                  FROM users u 
                                    LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                    LEFT JOIN designations d ON d.id = u.designation_id 
                                    left join time_categories  on lr.leave_type_id=time_categories.id
                                    LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                    LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                    JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 
                                  AND u.status=1 
                                  AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select 
                                            users.id,
                                              users.name,
                                                users.file_number,
                                                  users.gender,
                                                     users.bps,
                                                       clients.name as department_name,
                                                         designations.title as designation
                                          from users 
                                          INNER join clients on clients.id=users.department_id
                                          INNER join designations on designations.id=users.designation_id
                                          INNER JOIN attach_employees_details 
                                              on attach_employees_details.user_id=users.id
                                          where attach_employees_details.status=1 
                                          and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT 
                                        id,
                                          title,
                                            user_id,
                                              date as holiday_date,
                                                department_id,
                                                    date_from,
                                                        date_to 
                                      FROM holiday_calendars
                                         WHERE department_id='".$request->subdepartment_id."' 
                                      and  
                                         date 
                                      BETWEEN '".$rep_date."' 
                                      and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));
                        $flag= "all";       


                   }  

                    if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent' ){
                       
                         $emp="select  
                                   users.id,
                                        users.name,
                                          users.bps,
                                            users.gender,
                                              users.dob,
                                                users.file_number,
                                                  clients.name AS 
                                                   department_name,
                                                     designations.title as designation 
                                 from users
                                      inner join clients 
                                      on clients.id=users.department_id
                                inner join designations 
                                    on designations.id= users.designation_id
                                 where clients.parent_id='".$request->subdepartment_id."'
                                 and users.status=1 
                                 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT 
                                       u.id,
                                          u.name,
                                            u.bps,
                                              u.gender,
                                                u.dob,
                                                 tcat.tc_tuesday_in AS check_dayoff,
                                                   u.file_number,
                                                    tcat.is_night_shift, 
                                                      clients.name AS deparment_name,
                                                        designations.title,
                                                           tcat.tc_monday_in,
                                                              tcat.tc_tuesday_in,
                                                                 tcat.tc_wednesday_in,
                                                                   tcat.tc_thursday_in,
                                                                      tcat.tc_friday_in,
                                                                           tcat.tc_satureday_in,
                                                                             tcat.tc_sunday_in,
                                                                                  tcat.grace_time,
                                                                                    date(att.datetime) 
                                                                                        as att_date,
                                                                                      att.datetime 
                                                                                         AS attendance_time,
                                                                                          att.mark_attendance 
                                  FROM users u 
                                      LEFT JOIN attendance att ON u.id = att.user_id 
                                      LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                      LEFT JOIN designations ON designations.id=u.designation_id
                                      LEFT JOIN clients ON clients.id = u.department_id
                                      
                                  WHERE flag=1 
                                  AND u.status=1  
                                  and att.datetime is not null  
                                  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) 
                                  between '".$rep_date."' 
                                  and '".$rep_date."'
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                       $for_dayoff="select 
                                     users.id,
                                       users.name,
                                         users.tcat_id,
                                            time_categories.tc_monday_in,
                                              time_categories.tc_tuesday_in,
                                                 time_categories.tc_wednesday_in,
                                                    time_categories.tc_thursday_in,
                                                     time_categories.tc_friday_in,
                                                      time_categories.tc_satureday_in,
                                                       time_categories.tc_sunday_in
                                                        
                                    from users 
                                     INNER join clients on clients.id=users.department_id
                                     inner join time_categories on time_categories.id=users.tcat_id
                                     WHERE clients.parent_id='".$request->subdepartment_id."'";
                       $day_off = collect(DB::select( DB::raw($for_dayoff)));  

                       $daily_leave= "SELECT  u.id,
                                        lr.leave_type_id,
                                          lr.leave_type,
                                            lr.time_from,
                                              lrd.leave_date,
                                               lr.from_date,
                                                  lr.to_date,
                                                    lr.time_to,
                                                      u.name,
                                                       u.file_number,
                                                        u.bps,
                                                          u.gender,
                                                           u.dob,
                                                            d.title as desgnation,
                                                              c.name as deparment_name,
                                                               time_categories.title
                                        FROM users u 
                                            LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                            LEFT JOIN designations d ON d.id = u.designation_id 
                                            left join time_categories  on lr.leave_type_id=time_categories.id
                                            LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                            LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                            JOIN clients c ON c.id = u.`department_id` 
                                        WHERE 
                                         flag=1 
                                        AND
                                          u.status=1 
                                        AND 
                                          c.parent_id='".$request->subdepartment_id."'   
                                        AND 
                                          DATE(lrd.leave_date)='".$rep_date."' 
                                        group by u.id
                                        ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select 
                                            users.id,
                                              users.name,
                                                users.file_number,
                                                  users.gender,
                                                    users.bps,
                                                      clients.name as department_name, 
                                                         designations.title as designation_id
                                          from users 
                                            INNER join clients on clients.id=users.department_id
                                            INNER join designations on designations.id=users.designation_id
                                            INNER JOIN attach_employees_details 
                                                 on attach_employees_details.user_id=users.id
                                          where attach_employees_details.status=1 
                                          and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT 
                                       id,
                                         title,
                                           user_id,
                                            date as holiday_date,
                                              department_id,
                                                date_from,
                                                   date_to 
                                      FROM holiday_calendars
                                        WHERE department_id='".$request->subdepartment_id."' 
                                        and  date BETWEEN '".$rep_date."' 
                                        and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } 

                  }  
       
  

               if(!empty($request->search)){ //=> unit depeartment
                     
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  

                      $emp="select  
                                 users.id,
                                   users.name,
                                     users.bps,
                                       users.gender,
                                        users.dob,
                                         users.file_number,
                                           clients.name AS 
                                            department_name,
                                             designations.title as designation 
                            from users
                                inner join clients on clients.id=users.department_id
                                inner join designations on designations.id= users.designation_id
                            where users.department_id='".$search."' 
                            and users.status=1 
                            and users.flag=1";

                      $fetch_emp=collect(DB::select( DB::raw($emp)));
                     
                      
                       $report_query_for_departmnent_adm  =  "
                                  SELECT  
                                        u.id,
                                          u.name,
                                            u.bps,
                                             u.gender,
                                               u.dob,
                                                tcat.tc_tuesday_in AS check_dayoff,
                                                  u.file_number,
                                                   tcat.is_night_shift, 
                                                     clients.name AS deparment_name,
                                                       designations.title,
                                                        tcat.tc_monday_in,
                                                          tcat.tc_tuesday_in,
                                                            tcat.tc_wednesday_in,
                                                              tcat.tc_thursday_in,
                                                                tcat.tc_friday_in,
                                                                   tcat.tc_satureday_in,
                                                                      tcat.tc_sunday_in,
                                                                        tcat.grace_time,
                                                                           att.datetime AS attendance_time,
                                                                               date(att.datetime) as att_date,
                                                                                      att.mark_attendance 
                                      FROM users u 
                                          LEFT JOIN attendance att ON u.id = att.user_id 
                                          LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                          LEFT JOIN designations ON designations.id=u.designation_id
                                          LEFT JOIN clients ON clients.id = u.department_id 
                                      WHERE flag=1
                                      AND u.status=1  
                                      and att.datetime is not null  
                                      AND clients.id='".$search."' 
                                      and date(att.datetime) between '".$rep_date."' 
                                      and '".$rep_date."'
                                      ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));


                      $daily_leave= "SELECT  
                                     u.id,
                                        lr.leave_type_id,
                                          lr.leave_type,
                                           lr.time_from,
                                            lrd.leave_date,
                                             lr.from_date,
                                              lr.to_date,
                                               lr.time_to,
                                                u.name,
                                                  u.file_number,
                                                   u.bps,
                                                    u.gender,
                                                     u.dob,
                                                      d.title as desgnation,
                                                       c.name as deparment_name,
                                                         time_categories.title
                                      FROM users u 
                                          LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                          LEFT JOIN designations d ON d.id = u.designation_id 
                                          left join time_categories  on lr.leave_type_id=time_categories.id
                                          LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                          LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                      JOIN clients c ON c.id = u.`department_id` 
                                      WHERE flag=1 
                                      AND u.status=1 
                                      AND c.id='".$search."'   
                                      AND DATE(lrd.leave_date)='".$rep_date."' 
                                      group by u.id
                                      ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));

                        
                        $attach_emp  =  "select  
                                           users.id,
                                             users.name,
                                                users.file_number,
                                                   users.gender,
                                                      users.bps,
                                                        clients.name as department_name,
                                                           designations.title as designation
                                          from users 
                                            INNER join clients on clients.id=users.department_id
                                            INNER join designations on designations.id=users.designation_id
                                            INNER JOIN attach_employees_details 
                                              on attach_employees_details.user_id=users.id
                                           where attach_employees_details.status=1 
                                              and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                      $flag="present";
                      $holidays="";
                     
                    
                 }   //tuesday -> $search!=''  && $rep_date!='' && $request->status=='present' 
                   if($search!=''  && $rep_date!='' && $request->status=='all' ){
                       
                         $emp="select  
                                    users.id,
                                     users.name,
                                        users.bps,
                                          users.gender,
                                            users.dob,
                                              users.file_number,
                                                clients.name AS 
                                                   department_name,
                                       designations.title as designation 
                                from users
                                  inner join clients on clients.id=users.department_id
                                  inner join designations on designations.id= users.designation_id
                                where users.department_id='".$search."' 
                                and 
                                  users.status=1 
                                  and
                                flag=1";

                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  
                                       u.id,
                                        u.name,
                                         u.bps,
                                          u.gender,
                                            u.dob,
                                             tcat.tc_tuesday_in AS check_dayoff,
                                               u.file_number,
                                                tcat.is_night_shift, 
                                                  clients.name AS deparment_name,
                                                    designations.title,
                                                      tcat.tc_monday_in,
                                                         tcat.tc_tuesday_in,
                                                           tcat.tc_wednesday_in,
                                                             tcat.tc_thursday_in,
                                                               tcat.tc_friday_in,
                                                                 tcat.tc_satureday_in,
                                                                   tcat.tc_sunday_in,
                                                                     tcat.grace_time,
                                                                       att.datetime AS attendance_time,
                                                                         date(att.datetime) as att_date,
                                                                            att.mark_attendance 
                                  FROM users u 
                                        LEFT JOIN attendance att ON u.id = att.user_id 
                                        LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                        LEFT JOIN designations ON designations.id=u.designation_id
                                        LEFT JOIN clients ON clients.id = u.department_id
                                        LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                   WHERE 
                                     flag=1 
                                   AND
                                     u.status=1  
                                   and 
                                    att.datetime is not null  
                                   AND clients.id='".$search."' 
                                   and date(att.datetime) 
                                   between '".$rep_date."' and '".$rep_date."'
                                   ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                        $for_dayoff="select 
                                      users.id,
                                        users.name,
                                          users.tcat_id,
                                            time_categories.tc_monday_in,
                                              time_categories.tc_tuesday_in,
                                                 time_categories.tc_wednesday_in,
                                                    time_categories.tc_thursday_in,
                                                     time_categories.tc_friday_in,
                                                      time_categories.tc_satureday_in,
                                                       time_categories.tc_sunday_in
                                                        
                                      from users INNER join clients
                                         on clients.id=users.department_id
                                      inner join time_categories 
                                         on time_categories.id=users.tcat_id
                                      WHERE clients.id='".$search."' and users.status=1 and users.flag=1 ";
                       $day_off = collect(DB::select( DB::raw($for_dayoff)));



                       $daily_leave= "SELECT  
                                            u.id,
                                              lr.leave_type_id,
                                                lr.leave_type,
                                                 lr.time_from,
                                                  lrd.leave_date,
                                                   lr.from_date,
                                                     lr.to_date,
                                                       lr.time_to,
                                                          u.name,
                                                           u.file_number,
                                                             u.bps,
                                                              u.gender,
                                                               u.dob,
                                                                d.title as desgnation,
                                                                 c.name as deparment_name,
                                                                   time_categories.title
                                  FROM users u 
                                    LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                    LEFT JOIN designations d ON d.id = u.designation_id 
                                    left join time_categories  on lr.leave_type_id=time_categories.id
                                    LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                    LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 
                                  AND u.status=1 
                                  AND c.id='".$search."'   
                                  AND 
                                  DATE(lrd.leave_date)='".$rep_date."' 
                                  group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                     
                        $attach_emp  =  "select  
                                            users.id,
                                               users.name,
                                                 users.file_number,
                                                   users.gender,
                                                     users.bps,
                                                       clients.name as department_name,
                                                          designations.title as designation
                                          from users 
                                              INNER join clients 
                                                on clients.id=users.department_id
                                              INNER join designations 
                                                on designations.id=users.designation_id
                                              INNER JOIN attach_employees_details 
                                                on attach_employees_details.user_id=users.id
                                           where attach_employees_details.status=1 
                                           and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT 
                                       id,
                                         title,user_id,
                                           date as holiday_date,
                                             department_id,
                                               date_from,
                                                  date_to 
                                      FROM holiday_calendars
                                      WHERE department_id='".$request->search."' 
                                        and  
                                      date BETWEEN '".$rep_date."' 
                                      and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));
                        $flag= "all";       


                   }  //  tuesday $search!=''  && $rep_date!='' && $request->status=='all'
                    if($search!=''  && $rep_date!='' && $request->status=='absent' ){

                       
                         $emp="select  
                                   users.id,
                                     users.name,
                                      users.bps,
                                       users.gender,
                                         users.dob,
                                          users.file_number,
                                           clients.name AS 
                                             department_name,
                                              designations.title as designation 
                                  from users
                                   inner join clients on clients.id=users.department_id
                                   inner join designations on designations.id= users.designation_id
                                  where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));

                          $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                     u.name,
                                       u.bps,
                                         u.gender,
                                           u.dob,
                                             tcat.tc_tuesday_in AS check_dayoff,
                                               u.file_number,
                                                 tcat.is_night_shift, 
                                                   clients.name AS deparment_name,
                                                     designations.title,
                                                       tcat.tc_monday_in,
                                                         tcat.tc_tuesday_in,
                                                           tcat.tc_wednesday_in,
                                                             tcat.tc_thursday_in,
                                                               tcat.tc_friday_in,
                                                                 tcat.tc_satureday_in,
                                                                    tcat.tc_sunday_in,
                                                                      tcat.grace_time,
                                                                        att.datetime AS attendance_time,
                                                                          date(att.datetime) as att_date,
                                                                            att.mark_attendance 
                                    FROM users u 
                                      LEFT JOIN attendance att ON u.id = att.user_id 
                                      LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                      LEFT JOIN designations ON designations.id=u.designation_id
                                      LEFT JOIN clients ON clients.id = u.department_id
                                      LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                    WHERE 
                                       flag=1 
                                    AND 
                                       u.status=1  
                                    and att.datetime is not null  
                                    AND clients.id='".$search."' 
                                     and date(att.datetime) 
                                    between '".$rep_date."' 
                                    and '".$rep_date."'
                                    ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));

                      $for_dayoff="select 
                                     users.id,
                                       users.name,
                                         users.tcat_id,
                                          time_categories.tc_monday_in,
                                              time_categories.tc_tuesday_in,
                                                 time_categories.tc_wednesday_in,
                                                    time_categories.tc_thursday_in,
                                                     time_categories.tc_friday_in,
                                                      time_categories.tc_satureday_in,
                                                       time_categories.tc_sunday_in
                                                        
                                    from users 
                                    INNER join clients on clients.id=users.department_id
                                    inner join time_categories on time_categories.id=users.tcat_id
                                    WHERE clients.id='".$search."' ";
                       $day_off = collect(DB::select( DB::raw($for_dayoff)));
                     

                       $daily_leave= "SELECT  
                                       u.id,
                                        lr.leave_type_id,
                                         lr.leave_type,
                                          lr.time_from,
                                           lrd.leave_date,
                                            lr.from_date,
                                             lr.to_date,
                                              lr.time_to,
                                               u.name,
                                                u.file_number,
                                                 u.bps,
                                                  u.gender,
                                                   u.dob,
                                                    d.title as desgnation,
                                                     c.name as deparment_name,
                                                       time_categories.title
                                       FROM users u 
                                          LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                          LEFT JOIN designations d ON d.id = u.designation_id 
                                          left join time_categories  on lr.leave_type_id=time_categories.id
                                          LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                          LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                          JOIN clients c ON c.id = u.`department_id` 
                                        WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                       AND 
                                         DATE(lrd.leave_date)='".$rep_date."' 
                                       group by u.id
                                       ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select 
                                            users.id,
                                               users.name,
                                                  users.file_number,
                                                     users.gender,
                                                        users.bps,
                                                            clients.name as department_name, 
                                                               designations.title as designation
                                          from users 
                                          INNER join clients on clients.id=users.department_id
                                          INNER join designations on designations.id=users.designation_id
                                          INNER JOIN attach_employees_details 
                                               on attach_employees_details.user_id=users.id
                                          where attach_employees_details.status=1 
                                          and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT 
                                       id,
                                         title,
                                            user_id,
                                             date as holiday_date,
                                               department_id,
                                                  date_from,
                                                     date_to 
                                      FROM holiday_calendars
                                      WHERE department_id='".$request->search."' 
                                         and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   
                        $flag= "absent";       


                    } // tuesday $search!=''  && $rep_date!='' && $request->status=='absent' 
                     if($search=='all'  && $rep_date!='' && $request->status=='absent' ){
                       
                         $emp="select  
                                    users.id,
                                     users.name,
                                      users.bps,
                                       users.gender,
                                        users.dob,
                                         users.file_number,
                                          clients.name AS department_name,
                                            designations.title as designation 
                                from users
                                  inner join clients on clients.id=users.department_id
                                  inner join designations on designations.id= users.designation_id
                                where clients.parent_id='".Session::get('dep_id')."' 
                                and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                   SELECT  
                                         u.id,
                                          u.name,
                                           u.bps,
                                            u.gender,
                                             u.dob,
                                              tcat.tc_tuesday_in AS check_dayoff,
                                               u.file_number,
                                                tcat.is_night_shift, 
                                                 clients.name AS deparment_name,
                                                  designations.title,
                                                    tcat.tc_monday_in,
                                                      tcat.tc_tuesday_in,
                                                        tcat.tc_wednesday_in,
                                                          tcat.tc_thursday_in,
                                                           tcat.tc_friday_in,
                                                             tcat.tc_satureday_in,
                                                               tcat.tc_sunday_in,
                                                                 tcat.grace_time,
                                                                  att.datetime AS attendance_time,
                                                                      date(att.datetime) as att_date,
                                                                    att.mark_attendance 
                                   FROM users u 
                                      LEFT JOIN attendance att ON u.id = att.user_id 
                                      LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                      LEFT JOIN designations ON designations.id=u.designation_id
                                      LEFT JOIN clients ON clients.id = u.department_id
                                      LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                    WHERE flag=1 
                                    AND u.status=1  
                                    and att.datetime is not null  
                                    AND clients.parent_id='".Session::get('dep_id')."' 
                                    and date(att.datetime) between '".$rep_date."'
                                    and '".$rep_date."'
                                    ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      

                       $daily_leave= "SELECT 
                                       u.id,
                                        lr.leave_type_id,
                                         lr.leave_type,
                                           lr.time_from,
                                             lrd.leave_date,
                                               lr.from_date,
                                                 lr.to_date,
                                                   lr.time_to,
                                                     u.name,
                                                      u.file_number,
                                                       u.bps,
                                                        u.gender,
                                                         u.dob,
                                                           d.title as desgnation,
                                                             c.name as deparment_name,
                                                               time_categories.title
                                  FROM users u 
                                    LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                    LEFT JOIN designations d ON d.id = u.designation_id 
                                    left join time_categories  on lr.leave_type_id=time_categories.id
                                    LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                    LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                    WHERE flag=1 AND u.status=1 
                                  AND c.parent_id='".Session::get('dep_id')."'  
                                  AND
                                    DATE(lrd.leave_date)='".$rep_date."' 
                                  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        $for_dayoff="
                                     select 
                                        users.id,
                                           users.name,
                                             users.tcat_id,
                                              time_categories.tc_monday_in,
                                               time_categories.tc_tuesday_in,
                                                 time_categories.tc_wednesday_in,
                                                    time_categories.tc_thursday_in,
                                                     time_categories.tc_friday_in,
                                                      time_categories.tc_satureday_in,
                                                       time_categories.tc_sunday_in
                                                        
                                     from users INNER join clients
                                       on clients.id=users.department_id
                                     inner join time_categories 
                                       on time_categories.id=users.tcat_id
                                     WHERE clients.parent_id='".Session::get('dep_id')."' ";
                       $day_off = collect(DB::select( DB::raw($for_dayoff)));
                        $attach_emp  =  "select  
                                          users.id,
                                            users.name,
                                              users.file_number,
                                               users.gender,
                                                users.bps,
                                                 clients.name as department_name,
                                                   designations.title as designation
                                       from users 
                                         INNER join clients on clients.id=users.department_id
                                         INNER join designations on designations.id=users.designation_id
                                         INNER JOIN attach_employees_details 
                                            on attach_employees_details.user_id=users.id
                                       where attach_employees_details.status=1 
                                            and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,
                                             title,
                                               user_id,
                                                date as  holiday_date,department_id,
                                                  date_from,
                                                     date_to 
                                    FROM holiday_calendars
                                     WHERE department_id='".Session::get('dep_id')."' 
                                     and 
                                      date BETWEEN '".$rep_date."'
                                     and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));  
                        $flag= "absent";       


                   }   // tuesday  searrch=all //  status= absent // $rep_date !=''
                    if($search=='all'  && $rep_date!='' && $request->status=='present' ){ 
                  
                      $emp="select 
                              users.id,
                                  users.name,
                                   users.bps,
                                    users.gender,
                                     users.dob,
                                      users.file_number,
                                       clients.name AS 
                                        department_name,
                                         designations.title as designation from users
                                   inner join clients on clients.id=users.department_id
                                   inner join designations on designations.id= users.designation_id
                              where clients.parent_id='".Session::get('dep_id')."' 
                              and 
                               users.status=1 
                              and 
                               flag=1";

                      $fetch_emp=collect(DB::select( DB::raw($emp)));
                     
                      
                       $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                           u.name,
                                            u.bps,
                                             u.gender,
                                              u.dob,
                                              tcat.tc_tuesday_in AS check_dayoff,
                                               u.file_number,
                                                tcat.is_night_shift, 
                                                 clients.name AS 
                                                  deparment_name,
                                                   designations.title,
                                                    tcat.tc_monday_in,
                                                     tcat.tc_tuesday_in,
                                                      tcat.tc_wednesday_in,
                                                       tcat.tc_thursday_in,
                                                        tcat.tc_friday_in,
                                                          tcat.tc_satureday_in,
                                                            tcat.tc_sunday_in,
                                                             tcat.grace_time,
                                                               att.datetime AS attendance_time,
                                                                 date(att.datetime) as att_date,
                                                                   att.mark_attendance 
                                  FROM users u 
                                     LEFT JOIN attendance att ON u.id = att.user_id 
                                     LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                     LEFT JOIN designations ON designations.id=u.designation_id
                                     LEFT JOIN clients ON clients.id = u.department_id
                                     LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE 
                                    flag=1 
                                  AND 
                                    u.status=1  
                                  and 
                                   att.datetime is not null  
                                  AND 
                                   clients.parent_id='".Session::get('dep_id')."' 
                                  and 
                                   date(att.datetime) 
                                   between '".$rep_date."' and '".$rep_date."'
                                  ORDER BY 
                                   u.id,
                                   att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));

                      $daily_leave= "SELECT  
                                        u.id,
                                         lr.leave_type_id,
                                           lr.leave_type,
                                             lr.time_from,
                                               lrd.leave_date,
                                                lr.from_date,
                                                  lr.to_date,
                                                    lr.time_to,
                                                     u.name,
                                                      u.file_number,
                                                       u.bps,
                                                        u.gender,
                                                         u.dob,
                                                          d.title as desgnation,
                                                           c.name as deparment_name,
                                                            time_categories.title
                                        FROM users u 
                                          LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                           LEFT JOIN designations d ON d.id = u.designation_id 
                                            left join time_categories  on lr.leave_type_id=time_categories.id
                                             LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                              LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                              JOIN clients c ON c.id = u.`department_id` 
                                         WHERE flag=1 
                                           AND u.status=1
                                           AND c.parent_id='".Session::get('dep_id')."'   
                                           AND DATE(lrd.leave_date)='".$rep_date."' 
                                          group by u.id 
                                        ORDER BY u.id,
                                        lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select 
                                             users.id,
                                               users.name,
                                                  users.file_number,
                                                    users.gender,
                                                      users.bps,
                                                        clients.name as department_name,
                                                           designations.title as designation
                                        from users 
                                         INNER join clients on clients.id=users.department_id
                                          INNER join designations on designations.id=users.designation_id
                                            INNER JOIN attach_employees_details 
                                            on attach_employees_details.user_id=users.id
                                         where attach_employees_details.status=1 
                                           and 
                                           attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                          $holidays="";
                          $flag="present";
                     
                    
                 }   // tuesday  searrch=all //  status= present // rep_date !=""

                   if($search=='all'  && $rep_date!='' && $request->status=='all' ){
                       
                         $emp="select  users.id,
                                  users.name,
                                  users.bps,
                                  users.gender,
                                  users.dob,
                                
                                  users.file_number,
                                 
                                  clients.name AS 
                                  department_name,
                                  designations.title as designation from users
                               inner join clients on clients.id=users.department_id
                               inner join designations on designations.id= users.designation_id
                          where clients.parent_id='".Session::get('dep_id')."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  
                                       u.id,
                                          u.name,
                                              u.bps,
                                               u.gender,
                                                   u.dob,
                                                      tcat.tc_tuesday_in AS check_dayoff,
                                                        u.file_number,
                                                          tcat.is_night_shift, 
                                                            clients.name AS deparment_name,
                                                              designations.title,
                                                               tcat.tc_monday_in,
                                                                tcat.tc_tuesday_in,
                                                                 tcat.tc_wednesday_in,
                                                                  tcat.tc_thursday_in,
                                                                    tcat.tc_friday_in,
                                                                      tcat.tc_satureday_in,
                                                                       tcat.tc_sunday_in,
                                                                        tcat.grace_time,
                                                                         att.datetime AS attendance_time,
                                                                           date(att.datetime) as att_date,
                                                                            att.mark_attendance 
                                     FROM users u 
                                       LEFT JOIN attendance att ON u.id = att.user_id 
                                       LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                       LEFT JOIN designations ON designations.id=u.designation_id
                                       LEFT JOIN clients ON clients.id = u.department_id
                                       LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                      WHERE flag=1 
                                      AND u.status=1  
                                      and att.datetime is not null  
                                      AND clients.parent_id='".Session::get('dep_id')."'
                                      and date(att.datetime) between '".$rep_date."' 
                                      and '".$rep_date."'
                                      ORDER BY u.id,
                                      att.datetime";
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $for_dayoff="select 
                                     users.id,
                                         users.name,
                                            users.tcat_id,
                                             time_categories.tc_monday_in,
                                              time_categories.tc_tuesday_in,
                                                 time_categories.tc_wednesday_in,
                                                    time_categories.tc_thursday_in,
                                                     time_categories.tc_friday_in,
                                                      time_categories.tc_satureday_in,
                                                       time_categories.tc_sunday_in
                                                        
                                  from users
                                  INNER join clients
                                  on clients.id=users.department_id
                                  inner join time_categories 
                                  on time_categories.id=users.tcat_id
                                  WHERE clients.parent_id='".Session::get('dep_id')."'";

                       $day_off = collect(DB::select( DB::raw($for_dayoff)));
                       $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.file_number,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  d.title as desgnation,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  LEFT JOIN designations d ON d.id = u.designation_id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select  
                                               users.id,
                                               users.name,
                                               users.file_number,
                                               users.gender,
                                               users.bps,
                                               clients.name as department_name,
                                               designations.title as designation
                              from users 
                                   INNER join clients on clients.id=users.department_id
                                   INNER join designations on designations.id=users.designation_id
                                   INNER JOIN attach_employees_details on
                               attach_employees_details.user_id=users.id
                              where 
                                   attach_employees_details.status=1 
                                    and 
                                   attach_employees_details.attach_date='".$rep_date."'";
                        
                        $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT 
                                        id,
                                        title,
                                        user_id,
                                        date as holiday_date,
                                        department_id,
                                        date_from,
                                        date_to 
                                     FROM holiday_calendars
                                      WHERE department_id='".Session::get('dep_id')."' 
                                     and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen))); 
                        $flag= "all";
                   }  //tuesday $serach==all // status==all // $rep_date!=''
   
                }  
        }
       
        return view('Reports.attandance_report',compact(
                                                        'attandance_reports',
                                                        'department',
                                                        'rep_date',
                                                        'emp_by_dep',
                                                        'daily_leave_reports',
                                                        'fetch_emp',
                                                        'attach_emp_detail',
                                                        'flag',
                                                        'holidays',
                                                        'sub_department',
                                                        'check_day',
                                                        'day_off',
                                                        'weekday'
                                                       )
                    );  
         }   
         $attandance_reports='';
         $daily_leave_reports='';
         $emp_total_leave='';
         $check_employee_leave='';
         $fetch_emp='';
         $attach_emp_detail="";
         $flag="";
         $holidays="";
         $check_day="";
         $day_off="";
         $weekday="";
          return view('Reports.attandance_report',compact(
                                                          'attandance_reports',
                                                          'flag',
                                                          'department',
                                                          'rep_date',
                                                          'emp_total_leave',
                                                          'check_employee_leave',
                                                          'emp_by_dep',
                                                          'daily_leave_reports',
                                                          'fetch_emp',
                                                          'attach_emp_detail',
                                                          'holidays',
                                                          'sub_department',
                                                          'check_day',
                                                          'weekday'
                                                          )
                      );
      } 
  } 

