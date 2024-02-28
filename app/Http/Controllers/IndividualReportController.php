<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;
use App\TimeCategory;
use DB;
use Session;
use DateTime;
use DateInterval;
use DatePeriod;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class IndividualReportController extends Controller
{
     
    public function fetch_user_for_dep(Request $request){
       
         $client=Client::where('parent_id',$request->id)->first();
               if($client==null){ 
                     $user=client::with(['users' => function ($query) {
                         $query->where('status',1)->where('flag',1); 
                         }])->where('id',$request->id)->get(); 
                          $arr = [];
                          foreach ($user as $u){
                          foreach ($u['users'] as $m) {
                           $arr[] = $m;
                           }  
                       }
                     
               }
               else{

                   $user=client::with(['users' => function ($query) {
                         $query->where('status',1)->where('flag',1); 
                         }])->where('parent_id',$request->id)->get(); 
                          $arr = [];
                          foreach ($user as $u){
                          foreach ($u['users'] as $m) {
                           $arr[] = $m;
                           }  
                       }
                   
               }

                return json_encode(['employee' => $arr]);   
    }
    public function reports(Request $request)
    {       
      
        $time1='4:00';
        $time2='5:00';
      $result=strtotime($time1)+strtotime($time2);
      //dd($request);

      $dep_id=Session::get('dep_id');
      $date_from = ""; $date_to = "";
      if($request->has('from_date') && $request->has('to_date')){
          $date_from = $request->from_date;
          $date_to = $request->to_date;
      }
      if($request->has('linked')){
          //get user_id, department etc
          $user_id = $request->user_id;
          $user = User::find($user_id);
          $dep_id = $user->department_id;

          $date_from = date("Y-m-01");
          $date_to = date("Y-m-d");
      }
      if(Session::get('user')->role_id == 1){
            $dep_id = 278;
      }

      $loged_in_department = Client::find($dep_id);
      if(!$request->has('linked')){
        if(Session::get('user')->role_id == 11 && $loged_in_department->parent_id!=0){
            $dep_id = $loged_in_department->parent_id;
        }
      }
      $data['department']=Client::where('parent_id',$dep_id)->get();
      $data['department_users'] = User::whereIn('department_id',Client::where('parent_id',$dep_id)->pluck('id'))->get();

      if(Session::get('user')->role_id == 1){
            $data['department_users'] = User::get();
      }
      if($data['department']->isEmpty()){
          $data['department']=Client::where('id',$dep_id)->where('status',1)->get(); 
      }

        if($date_from && $date_to){
        
          $timecategory=TimeCategory::where('department_id',$request->deparment_id)->first();
          if(!$request->has('linked')){
            $this->validate($request, [
                'user_id' => 'required',
                'from_date' => 'required',
                      'to_date'=>'required',
                    ]); 
          }
          $data['from_date'] = $date_from;
          $data['to_date'] = $date_to;
          

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
                                                tcat.tc_saturday_in as satureday_in,
                                                tcat.tc_saturday_out as satureday_out,
                                                tcat.tc_sunday_in as sunday_in,
                                                tcat.tc_sunday_out as sunday_out,
                                                tcat.time_out as tcat_time_out,
                                                tcat.grace_time as tcat_grace_time
                                                from users u inner join 
                                                timenex_new.attendance att on u.id= att.user_id

                                                INNER join time_categories tcat on tcat.id=u.tcat_id
                                        where  (date(att.datetime) 
                                        BETWEEN '".date('Y-m-d', strtotime($date_from.' -1 day'))."' AND '".$date_to."') and u.id='".$request->user_id."' 
                                            group By att.datetime";

      $data['attendance'] = collect(DB::select( DB::raw($individual_report_att)));
  

    
    

      $timcat_history="SELECT date(time_category_histories.join_date) as date, 
                          users.id,
                          time_categories.time_in as tcat_time_in,
                          time_categories.is_night_shift as is_night_shift,
                          time_categories.time_out as tcat_time_out,
                          time_category_histories.is_current,
                          time_categories.id as cat_id,
                          time_categories.tc_monday_in as monday_in,
                          time_categories.tc_monday_out as monday_out,
                          time_categories.tc_tuesday_in as tuesday_in,
                          time_categories.tc_tuesday_out as tuesday_out,
                          time_categories.tc_wednesday_in as wednesday_in,
                          time_categories.tc_wednesday_out as wednesday_out,
                          time_categories.tc_thursday_in as thursday_in,
                          time_categories.tc_thursday_out as thursday_out,
                          time_categories.tc_friday_in as friday_in,
                          time_categories.tc_friday_out as friday_out,
                          time_categories.tc_saturday_in as satureday_in,
                          time_categories.tc_saturday_out as satureday_out,
                          time_categories.tc_sunday_in as sunday_in,
                          time_categories.tc_sunday_out as sunday_out,
                        
                          time_categories.grace_time as tcat_grace_time
                          FROM `time_category_histories` 
                            INNER JOIN users 
                            on users.id=time_category_histories.user_id
                            INNER JOIN time_categories 
                            on time_categories.id=time_category_histories.tcat_id 
                            where users.id='".$request->user_id."' order by date asc";
      $data['timcat_his'] = collect(DB::select( DB::raw($timcat_history))); 
      
      
                          
        // $for_dayoff= "select users.*,clients.*,time_categories.*,time_category_histories
        //                   INNER JOIN time_category_histories on time_category_histories.user_id=users.id 
        //                   INNER JOIN time_categories on time_categories.id=time_category_histories.tcat_id
        //                    WHERE clients.id='".$request->deparment_id."' and users.id=".$request->user_id."'";
        
      
      
        $for_dayoff="select * from users INNER join clients
                      on clients.id=users.department_id
                      INNER JOIN time_category_histories on time_category_histories.user_id=users.id 
                      INNER JOIN time_categories on time_categories.id=time_category_histories.tcat_id
                      WHERE clients.id='".$request->deparment_id."' and users.id='".$request->user_id."'";
        $data['day_off'] = collect(DB::select( DB::raw($for_dayoff))); 
        
        
      
        $indivi_leav="SELECT tcat.title,tcat.type, leave_requests_details.leave_date as leave_date,leave_requests.leave_type
                      FROM users  
                        INNER JOIN leave_requests on users.id=leave_requests.user_id 
                        INNER JOIN  time_categories tcat on tcat.id = leave_requests.leave_type_id
                        INNER JOIN leave_requests_details ON leave_requests_details.parent_table_id=leave_requests.id
                          WHERE users.id='".$request->user_id."'";
        $data['leaves'] = collect(DB::select( DB::raw($indivi_leav)));

        $indivi_attach="SELECT user_id,attach_date  FROM attach_employees_details
                          WHERE user_id='".$request->user_id."'";             
        $data['attachs'] = collect(DB::select( DB::raw($indivi_attach)));

        if(Session::get('role_id') == 7){
          //department head & we will show leave of its own department + sections
          $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$loged_in_department->id." or c.id = ".$loged_in_department->id;
        }else{

          $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$loged_in_department->id." or c.id = ".$loged_in_department->id;
          if($request->has('department')){
            $child_departments = Client::wherein('parent_id',$request->deparment_id)->pluck('id');
            $user_departments  = array_merge($child_departments->toArray(),$request->department);
            $users_department_clause = implode(",",$user_departments);
          }
        }


        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id in (".$users_department_clause.") 
                        and deleted_at is null and  date BETWEEN '".$request->from_date."' and '".$date_from."'";
        if($loged_in_department->sub_account==2){
        
          $holi_calen="SELECT id,title,user_id,date as  
                          holiday_date,department_id,date_from,date_to 
                          FROM holiday_calendars
                          WHERE department_id in (".$users_department_clause.") 
                          and deleted_at is null  and  date BETWEEN '".$request->from_date."' and '".$date_to."'";
                        
        }
        $dedicate_off_day="select * FROM employee_dedicated_off_days where user_id= '".$request->user_id."'";
        $data['dedicate_off_day'] = collect(DB::select( DB::raw($dedicate_off_day)));
      
        $data['holidays'] = collect(DB::select( DB::raw($holi_calen)));
      
        //SELECT min(date(datetime)) FROM `attendance` WHERE user_id=22211;

        $data['emp_min_transaction']=DB::table('attendance')->selectRaw('min(date(datetime)) as date')->where('user_id',$request->user_id)->first();
      
        $data['user_info_query'] = DB::table('users as u')
                                          ->join('clients as c','c.id','u.department_id','left')
                                          ->leftjoin('designations as d','d.id','u.designation_id','left')         
                                          ->where('u.id',$request->user_id)
                                          ->selectRaw('c.name,d.title,u.id,u.name as user_name,u.image,u.reg_date,u.cnic_issue_date,cnic_expiry_date')
                                          ->groupBy('u.name')   
                                          ->get();
        }

      else{

      $data['to_date'] = "";
      $data['emp_min_transaction']="";
      $data['from_date'] = "";
      $data['dedicate_off_day']="";
      $data['user_info_query']="";
      $data['dates'] = "";
      $data['individual_report_query'] = "";
      // $data['user'] = $user;
      $data['leaves'] = "";
      $data['attachs'] = "";
      $data['holidays'] = "";
      $data['day_off'] ="";

      }
      $data['page_title'] = "Individual Report";
      $data['linked'] = $request->linked;
    return view('Reports.individual_report',$data);
      } 

  public function individual_report_api(Request $request){
    $auth_user = JWTAuth::user();
    $date_from = $request->date_from;//'2021-02-01';
    $date_to   = $request->date_to;//'2021-03-01';
    $department_id = $auth_user->department_id;
    $user_id = $auth_user->id;
    $tcat_id = $auth_user->tcat_id;
    $parent_department_id = Client::find($department_id)->parent_id;
    $att_sql = "SELECT  DATE(a.`datetime`) AS att_date,
                       
                        u.id,
                        GROUP_CONCAT(TIME(a.`datetime`)) AS checkinout,
                        tc.`tc_monday_in`,
                        tc.`tc_monday_out`,
                        tc.`tc_tuesday_in`,
                        tc.`tc_tuesday_out`,
                        tc.`tc_wednesday_in`,
                        tc.`tc_wednesday_out`,
                        tc.`tc_thursday_in`,
                        tc.`tc_thursday_out`,
                        tc.`tc_friday_in`,
                        tc.`tc_friday_out`,
                        tc.`tc_saturday_in` as tc_saturday_in,
                        tc.`tc_saturday_out` as tc_saturday_out,
                        tc.`tc_sunday_in`,
                        tc.`tc_sunday_out`,
                        tc.`grace_time`
                FROM users u 
                  JOIN attendance a ON a.user_id = u.id
                  LEFT JOIN time_categories tc ON tc.id = u.`tcat_id`
                WHERE u.`department_id` = ".$department_id."
                AND u.id = ".$user_id."
                AND DATE(a.`datetime`) BETWEEN '".$date_from."' AND '".$date_to."'
                GROUP BY att_date
                ORDER BY a.`datetime` ASC";

      $att_transactions = collect(DB::select($att_sql));
    

      $for_dayoff="select * from users INNER join clients
                  on clients.id=users.department_id
                  inner join time_categories 
                  on time_categories.id=users.tcat_id
                  WHERE clients.id='".$parent_department_id."' and users.id='".$user_id."'";
      $dayoffs = collect(DB::select( DB::raw($for_dayoff))); 
      $indivi_leav="SELECT 
                          tcat.title,
                          tcat.type, 
                          leave_requests_details.leave_date as leave_date,
                          leave_requests.leave_type
                     FROM users  
                       INNER JOIN leave_requests on users.id=leave_requests.user_id 
                       INNER JOIN  time_categories tcat on tcat.id = leave_requests.leave_type_id
                       INNER JOIN leave_requests_details ON leave_requests_details.parent_table_id=leave_requests.id
                    WHERE users.id='".$user_id."'";
      $leaves = collect(DB::select( DB::raw($indivi_leav)));
      $indivi_attach="SELECT user_id,attach_date  FROM attach_employees_details
                        WHERE user_id='".$user_id."'";             
      $attach = collect(DB::select( DB::raw($indivi_attach)));

      $TimeCategory = TimeCategory::find($tcat_id);
      $holi_calen="SELECT title,
                          date_from,
                          date_to 
                  FROM holiday_calendars c WHERE c.department_id IN 
                  (SELECT GROUP_CONCAT(id) FROM clients WHERE parent_id = 276)
                  AND c.deleted_at IS NULL";
      $holidays = collect(DB::select( DB::raw($holi_calen)));

      $date_from_obj = new DateTime($date_from);
      $date_to_obj   = new DateTime($date_to); 
      $attendance = [];

      for($i = $date_from_obj; $i <= $date_to_obj; $i->modify('+1 day')){
        $today_record = [];
        $today_transaction = $att_transactions->where('att_date',$date_from_obj->format('Y-m-d'));
        if($today_transaction->count()>0){
          //the attendance have records 
          $today_transaction = $today_transaction->first();
          $today_record['date'] = $today_transaction->att_date;
          //get & sort transactions
          $trx_arr = explode(",",$today_transaction->checkinout);
          $trx_count = count($trx_arr);
          $todays_tcat_timein_index = "tc_".strtolower($date_from_obj->format('l')."_in");
          $todays_tcat_timein       = $today_transaction->$todays_tcat_timein_index;
          $todays_tcat_grace_time   = $today_transaction->grace_time;
          $todays_tcat_timein_with_gt = date("H:i:s",strtotime($todays_tcat_timein)
                                        +strtotime($todays_tcat_grace_time));
          $today_record['status'] = "Present";
          $hoursworked = "00:00:00";
          if($trx_arr[0]>$todays_tcat_timein_with_gt){
            $today_record['status'] = "Late";
          }

          $today_record['timein'] = $trx_arr[0];
          if($trx_count == 1){
             $today_record['timeout'] = "Missing Checkout";
             $hoursworked = "00:00:00";
          }else{
            $today_record['timeout'] = $trx_arr[$trx_count-1];
            $hoursworked = date("h:i:s",(strtotime($trx_arr[$trx_count-1]) - strtotime($trx_arr[0])));
            $hoursworked = $hoursworked;
          }
          $today_record['hoursworked'] = $hoursworked;
          $attendance[] = $today_record;
        }else{
          //check leave/attach etc
          $this_day_leaves = $leaves->where('leave_date',">=",$date_from_obj->format('Y-m-d'))
                 ->where('leave_date',"<=",$date_from_obj->format('Y-m-d'));
          $todays_tcat_timein_index = "tc_".strtolower($date_from_obj->format('l')."_in");
          $todays_tcat_timein       = $TimeCategory->$todays_tcat_timein_index;
          if(count($this_day_leaves)>0){
              $today_record['date'] = $this_day_leaves->first()->leave_date;
              $today_record['status'] = "Leave";
              $today_record['timein'] = "--";
              $today_record['timeout'] = "--";
              $today_record['hoursworked'] = "--";
              $attendance[] = $today_record;
          }else if(count($attach)>0){
              $today_record['date'] = $i->format('Y-m-d');
              $today_record['status'] = "Attach";
              $today_record['timein'] = "--";
              $today_record['timeout'] = "--";
              $today_record['hoursworked'] = "--";
              $attendance[] = $today_record;
          }else if($todays_tcat_timein == ""){
              $today_record['date'] = $i->format('Y-m-d');
              $today_record['status'] = "Off Day";
              $today_record['timein'] = "--";
              $today_record['timeout'] = "--";
              $today_record['hoursworked'] = "--";
              $attendance[] = $today_record;
          }else{
              $today_record['date'] = $i->format('Y-m-d');
              $today_record['status'] = "Absent";
              $today_record['timein'] = "--";
              $today_record['timeout'] = "--";
              $today_record['hoursworked'] = "--";
              $attendance[] = $today_record;  
          }
        }
      }
      $attendance = collect($attendance);
      $summary = [
        'total' => $attendance->count(),
        'present' => $attendance->where('status','Present')->count(),
        'absent' => $attendance->where('status','Absent')->count(),
        'offday' => $attendance->where('status','Off Day')->count(),
        'late' => $attendance->where('status','Late')->count(),
        'leave' => $attendance->where('status','Leave')->count(),
        'attach' => $attendance->where('status','Attach')->count(),
      ];
      $data = [
        'summary' => $summary,
        'attendance' => $attendance
      ];
      return json_encode($data);

  }

  
}
