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
class EmailAttendanceController extends Controller
{

  public function emailAttendanceStatusReport(){
    $clients = [
        '300' => 'smab26@gmail.com',
        '287' => 'sardarasimkhan@gmail.com',
        '287' => 'awaiskhattak555@gmail.com'
    ];  
    foreach($clients as $department_id => $email){
      $client_name = Client::find($department_id)->title;
      $att_table = $this->attendance_status_report_generate($department_id);
      $to_name = "TimeNex Alerts :: Daily Summary";
      $to_email = $email;
      $data = array(
                    "table"=>$att_table,
                    "client_name" => $client_name
                  );
      Mail::send('emails.attendance_status_report', $data, function($message) use ($to_name, $to_email) {
      $message->to($to_email, $to_name)
      ->subject('Attendance Status Report');
      $message->from('timenex712@gmail.com','TimeNex');
        $message->cc('waseemjustonline@gmail.com');
      });

      echo "Sent!".$department_id; 

    }
  }
  public function attendance_status_report_generate($department_id=NULL){
    $department_id = $department_id;
    $user_attendance = [];
    $users = [];
    $choosen_departments = [];
    $leave_users =  [];
    $sleave_users =  [];
    $attach_users = [];
    $choosen_status = 'all';
    $holidays = [];
    $emp_not_reg_prev_date=0;

      $report_date = date("Y-m-d");      
      

      $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$department_id." or c.id = ".$department_id;
      
      
      $yesterday = date('Y-m-d',strtotime($report_date . "-1 days"));
      $tomorrow = date('Y-m-d',strtotime($report_date . "+1 days"));

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
                    AND u.flag = 1 AND u.status = 1";
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
                          BETWEEN '".$report_date."' AND DATE(DATE_ADD('".$report_date."', INTERVAL 1 DAY))";
      $att_data_raw = collect(DB::select($users_attendance_sql));



      $att_today = collect($att_data_raw->where('attendance_date',$report_date));
     
      //$att_yesterday = collect($att_data_raw->where('attendance_date',$yesterday));
      $att_tomorrow = collect($att_data_raw->where('attendance_date',$tomorrow));
    
    
      foreach ($users as $key => $user) {
       
        $attendance = array();
        $status =checkAbsent($report_date,$user->reg_date);

        
        $time_cat_history = "select 
                                       date(tcat_history.join_date) as date, tcat.*
                                  from 
                                       time_category_histories tcat_history 
                                  INNER JOIN time_categories tcat 
                                  on tcat.id=tcat_history.tcat_id 
                                  where tcat_history.user_id='".$user->user_id."'";
        $time_cat_history = collect(DB::select($time_cat_history));
       
        $tcat_data = TimeCategory::find($user->tcat_id);
        $tcat_day_check = "tc_".strtolower(date('l', strtotime($report_date)))."_in";
       
        if($tcat_day_check == 'tc_saturday_in'){
            $tcat_day_check = "tc_satureday_in";
        }
        if($tcat_day_check == 'tc_thursday_in'){
            $tcat_day_check = "tc_thursday_in";
        }

        if($user->tcat_id == ""){
           
         
            $status =checkAbsent($report_date,$user->reg_date);
            if(checkAbsent($report_date,$user->reg_date)=='--'){
              $emp_not_reg_prev_date+=1; 
             }
        }else if($tcat_data!=""){
          
          $time_cat_for_offday= checkOffDayByTimeCategoryHistory($time_cat_history,$report_date,$tcat_data);
        
            if($time_cat_for_offday->$tcat_day_check == ""){
              $status = "offday";
            }else{
              $status = checkAbsent($report_date,$user->reg_date);
              if(checkAbsent($report_date,$user->reg_date)=='--'){
                $emp_not_reg_prev_date+=1; 
               }
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
     
        if($user->is_night_shift == 1){ 
          //the user have 24 hours time category so lets check his attendance in previous date
          $att_records = $att_today->where('user_id',$user->user_id);
          $att_tomorrow_records = $att_tomorrow->where('user_id',$user->user_id);
          if($att_records->count()>0){

            $emp_att_filter_date=$att_today->where('user_id',$user->user_id)->first()->attendance_date;
            $time_cat_his_info= checkTimeCategoryHistoryByDate($time_cat_history,$emp_att_filter_date,$user);

            $status = "present";
            $tcat_day_check = "tc_".strtolower(date('l', strtotime($report_date)))."_in";
            $tcat_day_check_out = "tc_".strtolower(date('l', strtotime($report_date)))."_out";
            
            if($tcat_day_check == 'tc_saturday_in'){
                $tcat_day_check = "tc_satureday_in";
            }
            if($tcat_day_check_out == 'tc_saturday_out'){
                $tcat_day_check_out = "tc_satureday_out";
            }
            
            
            
            $this_day_time_in = $report_date." ".$time_cat_his_info->$tcat_day_check;
            $this_day_time_out = $tomorrow." ".$time_cat_his_info->$tcat_day_check_out;
            
            $tcat_grace_time = "+".$time_cat_his_info->grace_time." minutes";
            $this_day_time_in_with_grace_time =  date("Y-m-d H:i:s", strtotime($this->date2min($time_cat_his_info->grace_time), strtotime($this_day_time_in)));

            $this_day_trxs = $att_records->pluck('attendance_time')->toArray();
            $tomorrow_trxs = $att_tomorrow_records->pluck('attendance_time')->toArray();
            $this_day_in = $this_day_trxs[0];

            if(count($tomorrow_trxs)>0){
              $this_day_out = $tomorrow_trxs[count($tomorrow_trxs)-1];
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

           $emp_att_filter_date=$att_today->where('user_id',$user->user_id)->first()->attendance_date;
           $time_cat_his_info= checkTimeCategoryHistoryByDate($time_cat_history,$emp_att_filter_date,$user);
           $status = "present";
            $tcat_day_check = "tc_".strtolower(date('l', strtotime($report_date)))."_in";
          
            $tcat_day_check_out = "tc_".strtolower(date('l', strtotime($report_date)))."_out";
            if($tcat_day_check == 'tc_saturday_in'){
                $tcat_day_check = "tc_satureday_in";
            }
            if($tcat_day_check_out == 'tc_saturday_out'){
                $tcat_day_check_out = "tc_satureday_out";
            }
           
            $this_day_time_in = $report_date." ".$time_cat_his_info->$tcat_day_check;
            $this_day_time_out = $report_date." ".$time_cat_his_info->$tcat_day_check_out;
            $tcat_grace_time = "+".$time_cat_his_info->grace_time." minutes";
            $this_day_time_in_with_grace_time =  date("Y-m-d H:i:s", strtotime($this->date2min($time_cat_his_info->grace_time), strtotime($this_day_time_in)));
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
      }; //end users

      $user_attendance = collect($user_attendance);

      $user_attendance = $user_attendance->sortBy("user_id");
    
    
    $departments = Client::where('parent_id',$department_id)->get();
    $table = '<div class="table-responsive" >';
    $table.='<table id="dataexample" border="1" class="table dataTable display nowrap" aria-describedby="example3_info" >';
    $table.='<tr>';
    $table.='<th>ID</th>';
    $table.='<th>NAME</th>';
    $table.='<th>Department</th>';
    $table.='<th>Designation</th>';
    $table.='<th>File#</th>';
    $table.='<th>Gender</th>';
    $table.='<th>BPS</th>';
    $table.='<th>IN</th>';
    $table.='<th>OUT</th>';
    $table.='<th>Status</th>';
    $table.='<th>Remarks</th>';
    $table.='</tr>';

    foreach($user_attendance as $key=>$attendance){
        $table.="<tr>";
            $table.='<td>'.$attendance['user_id'].'</td>';
            $table.='<td>'.$attendance['name'].'</td>';
            $table.='<td>'.$attendance['department_name'].'</td>';
            $table.='<td>'.$attendance['designation'].'</td>';
            $table.='<td>'.$attendance['file_number'].'</td>';
            $table.='<td>'.$attendance['gender'].'</td>';
            $table.='<td>'.$attendance['bps'].'</td>';
            $table.='<td>'.$attendance['time_in'].'</td>';
            $table.='<td>'.$attendance['time_out'].'</td>';
            
            if($holidays->where('department_id',$attendance['department_id'])->count() >0){
                $table.='<td>Holiday :: '.$holidays->where('department_id',$attendance['department_id'])->first()->title.'</td>';
            }else{
                $table.='<td>'.$attendance['status'].'</td>';
            }
                
           
            $table.='<td></td>';
        $table.='</tr>';
    }


    $table.='</table>';
    $table.='</div>';   
    return $table;
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
  } 

