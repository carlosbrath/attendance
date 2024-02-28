<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\TimeCategory;
use DB;
use Session;
use DateTime;

class Monthly_Detailed_Report extends Controller
{

    public function reports(Request $request){
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

            //lets apply filters
            if($request->has('department_id')  && $request->department_id != "all" ){
                //$users = $users->where('department_id',$request->department_id);    

                $department_ids_sql = "SELECT id  FROM `clients` WHERE `parent_id` = ".$request->department_id;
                $department_ids = collect(DB::select($department_ids_sql))->pluck('id');

                if(count($department_ids)==0){
                    $department_ids[] = $request->department_id;
                }
                if($role_id == 7){
                    $department_ids[] = $dep_id;   
                }
                $users = User::where('status',1)->where('flag',1)->whereIn('department_id',$department_ids);
            }else if($request->has('id_from')  && $request->id_from != "" ){
                $users = User::where('status',1)->where('flag',1)->whereIn('department_id',$department_ids)->whereBetween('id',[$request->id_from,$request->id_to]);
            }else{                
                $users = User::where('status',1)->where('flag',1)->whereIn('department_id',$department_ids);
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
                                    a.mark_attendance,
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
                        if($leave_users->where('leave_date',$thisDate)->where('user_id',$user->id)->count()>0){
                            $user_attendance['date'] = $thisDate;
                            $user_attendance['user_id'] = $user->id;
                            $user_attendance['status'] = 'leave';
                            $user_attendance['in'] = $in;
                            $user_attendance['out'] = $out;
                            $user_attendance['manual'] = 0;
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
                            $user_attendance['manual'] = 0;
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
                            $user_attendance['manual'] = 0;                          
                        }
                    }else if(count($this_day_attendance)>0){
                        $is_night_shift = $this_day_attendance[0]->is_night_shift;
                        if($is_night_shift == 1){
                            $transaction = $this_day_attendance[0];
                            
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
                            $user_attendance['manual'] = $transaction->mark_attendance;
                        }else{
                            
                            $tcat_day_check = "tc_".strtolower(date('l', strtotime($thisDate)))."_in";
                            if($tcat_day_check == 'tc_saturday_in'){
                                $tcat_day_check = "tc_satureday_in";
                            }
                            $gracetime_arr = explode(':', $this_day_attendance[0]->grace_time);
                            $gracetime_hours = $gracetime_arr[0];
                            $gracetime_minutes = $gracetime_arr[1];
                            $gracetime_minutes = ($gracetime_hours*60)+$gracetime_minutes;
                            
                            $tcat_timein = $thisDate." ".$this_day_attendance[0]->$tcat_day_check;
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
                            $user_attendance['manual'] = $this_day_attendance[0]->mark_attendance;
                        }
                    }else{
                        $user_attendance['date'] = $thisDate;
                        $user_attendance['user_id'] = $user->id;
                        $user_attendance['status'] = $status;
                        $user_attendance['in'] = $in;
                        $user_attendance['out'] = $out;
                        $user_attendance['manual'] = 0;
                    }
                    $array_index = $user->id."-".date("ymd",strtotime($thisDate));
                    $user_attendance_cmb[$array_index] = $user_attendance;
                }
            }
            $data['users'] = $users;
            $data['date_from'] = $request->from_date;
            $data['date_to'] = $request->to_date;
            $data['attendance'] = $user_attendance_cmb;
            
        }

        $data['page_title'] = "Monthly Detailed Attendance";
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

        return view('Reports.monthly_detailed_report',$data);
    }
    public function monthly_details_attendance(Request $request){
       
       
        DB::statement('SET GLOBAL group_concat_max_len = 1000000');
        $dep_id = Session::get('dep_id');
        $loged_in_department = Client::find($dep_id);
        if(Session::get('user')->role_id == 11 && $loged_in_department->parent_id!=0){
          $dep_id = $loged_in_department->parent_id;
        }

        $department= Client::where('parent_id',$dep_id)->pluck('name','id');
        $from_date = "";
        $to_date = "";
        $leaves = "";
        $attachs = "";
        $holidays = [];
        $report_type = "detailed";
        $status = "all";
        $choosen_department_name = "";
        $time_cat_history='';
        $daytime_attendance = [];
       
        if($request->has('search')){
           
            $this->validate($request, [       
                'from_date'=>'required',
                'to_date'=>'required'
            ]);
            $from_date = date('Y-m-d',strtotime($request->from_date . "-1 days"));
            $to_date  = date('Y-m-d',strtotime($request->to_date . "+1 days"));
            $from_date = $request->from_date;
            $to_date   = $to_date;
            $first_day = $request->month;
            $report_type=$request->report_type;
            $status    = $request->status;
            $department_id = Session::get('dep_id');
          
            $loged_in_department = Client::find($department_id);
            if($request->has('department_id') && $request->department_id!="all"){
                $department_id = $request->department_id;
               
            }
          
            $department_ids = Client::where('parent_id',$department_id)->pluck('id');
          
            $department_ids[] = $department_id;
            $department_ids = $department_ids->toArray();
            $department_ids = implode(",",$department_ids);
           
            $q="select 
                      tcat_history.user_id, 
                      date(tcat_history.join_date) as date,
                       time_categories.* 
                from users 
                        INNER JOIN time_category_histories as tcat_history on users.id=tcat_history.user_id
                        INNER JOIN time_categories on time_categories.id=tcat_history.tcat_id 
                where users.department_id in (".$department_ids.")";
           
            $time_cat_history = collect(DB::select($q)); 
            $attendance_daytime_sql = "SELECT 
                                        u.id as user_id,
                                        u.reg_date,
                                        u.name,
                                        ds.title as designation,
                                        u.department_id as user_department_id,
                                        c.is_night_shift,
                                        CONCAT('{',GROUP_CONCAT(att.att_transactions),'}') AS att_transactions,
                                        c.*
                                     FROM timenex_new.users u  LEFT JOIN (

                                    SELECT 
                                        u.id AS user_id,
                                        u.`name`,
                                        DATE(a.datetime) AS att_date,
                                        CONCAT('\"',DATE(a.datetime),'\"',
                                                 ': [',
                                                 GROUP_CONCAT('\"',concat(a.datetime,'-',a.mark_attendance),'\"' ORDER BY a.datetime ASC SEPARATOR ', '),
                                                 ']'
                                                ) AS att_transactions,
                                        c.* 
                                    FROM timenex_new.`users` u
                                    JOIN timenex_new.`attendance` a ON u.id = a.`user_id`
                                    JOIN timenex_new.`time_categories` c ON c.id = u.`tcat_id`
                                    WHERE DATE(a.datetime) BETWEEN '".$from_date."' AND '".$to_date."'
                                    AND u.`department_id` IN (".$department_ids.")
                                    GROUP BY u.id,DATE(a.`datetime`)
                                    ORDER BY u.id
                                    ) att ON att.user_id = u.id
                                    JOIN timenex_new.`time_categories` c ON c.id = u.`tcat_id`
                                    LEFT JOIN timenex_new.`designations` ds ON u.designation_id = ds.`id`
                                    WHERE u.`department_id` IN (".$department_ids.")
                                    and u.status = 1
                                    
                                    and u.flag=1  GROUP BY u.id";
        
                                     
                          
                                
            $leave_sql = "SELECT 
                                u.id as user_id,l.`leave_date` 
                          FROM timenex_new.`users` u 
                                JOIN timenex_new.`leave_requests_details` l ON u.id = l.`user_id`
                          WHERE u.`department_id` IN ($department_ids)
                          AND DATE(l.`leave_date`) BETWEEN '".$from_date."' AND '".$to_date."'";
            $attach_sql = "SELECT 
                                u.id as user_id,l.`attach_date` 
                          FROM timenex_new.`users` u 
                                JOIN timenex_new.`attach_employees_details` l ON u.id = l.`user_id`
                          WHERE u.`department_id` IN ($department_ids)
                          AND DATE(l.`attach_date`) BETWEEN '".$from_date."' AND '".$to_date."'";
            
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
                            and  date BETWEEN '".$from_date."' and '".$to_date."'";

            if($loged_in_department->sub_account==2){
              $holi_calen="SELECT id,title,user_id,date as  
                              holiday_date,department_id,date_from,date_to 
                              FROM holiday_calendars
                              WHERE department_id in (".$users_department_clause.") 
                              and  date BETWEEN '".$from_date."' and '".$to_date."'";
            }

            $holidays = collect(DB::select( DB::raw($holi_calen)));
            $daytime_attendance = collect(DB::select($attendance_daytime_sql));
            $leaves = collect(DB::select($leave_sql));
            $attachs = collect(DB::select($attach_sql));   
        
        }
        $data = [
            'department'=>$department,
            'choosen_department_id' => 1,
            'date_from' => $from_date,
            'date_to' => $request->to_date,
            'daytime_attendance' => $daytime_attendance,
            'leaves' => $leaves,
            'attachs' => $attachs,
            'report_type' => $report_type,
            'holidays' => $holidays,
            'time_cat_history_test'=>$time_cat_history,
            'status' => $status
        ];
        if($request->has('department_id') && $request->department_id!="all"){
            $data['choosen_department_name'] = Client::find($request->department_id)->name;
        }else{
            $data['choosen_department_name'] = "All Departments/Sub Sections";
        }        
        return view('Reports.monthly_detailed_attendance_report',$data);

    }
    public function reports1(Request $request){

        $department= Client::where('parent_id',Session::get('dep_id'))
        ->where('sub_account',1)
        ->pluck('name','id');
         $sub_department= Client::where('parent_id',Session::get('dep_id'))
        ->where('sub_account',2)
        ->pluck('name','id');
        if($request->isMethod('post')){
            $this->validate($request, [       
                'from_date'=>'required',
                'to_date'=>'required',
                'month'=>'required',
            ]);
            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $first_day=$request->month;
            $yrdata= strtotime($request->month);
            $emp_attach_data = collect([]);
            $month_year_name= date('M-Y', $yrdata);

            if(empty($request->subdeparment_id) && empty($request->deparment_id)){
                  
                    $this->validate($request, [
                            'deparment_id'=>'required',
                            'subdeparment_id'=>'required',
                    ]);
            }
            if(!empty($request->deparment_id)){
               
                //Means the department is parent , we are running a simple query
                $users = DB::table('users')
                    ->join('clients', 'users.department_id', '=', 'clients.id')
                    ->join('designations','designations.id','=','users.designation_id')
                    ->select('users.id','users.name','designations.title','clients.parent_id as dep_id')
                    ->where('clients.id',$request->deparment_id)
                    ->where('users.status',1)
                    ->where('flag',1)
                    ->get();

                $emp_status_query = "SELECT users.id,users.name,t.tc_monday_in,t.tc_monday_out,t.grace_time,t.tc_tuesday_in,t.tc_tuesday_out,tc_wednesday_in,t.tc_wednesday_out,t.tc_thursday_in,t.tc_thursday_out,t.tc_friday_in,t.tc_friday_out,t.tc_satureday_in,t.tc_satureday_out,t.tc_sunday_in,t.tc_sunday_out,t.time_in,t.time_out,date(a.datetime) as date_time,a.datetime as att_date,t.type 
                                FROM users INNER JOIN attendance a on users.id=a.user_id
                                INNER JOIN time_categories t on t.id=users.tcat_id
                                inner join clients on users.department_id = clients.id
                                where                  
                                users.status=1

                                     and users.flag=1 
                                     and t.time_cat_type=1 
                                     and clients.id='".$request->deparment_id."'
                                     and
                                     date(datetime) BETWEEN '".date('Y-m-d', strtotime($request->from_date.' -1 day'))."' AND '".$request->to_date."'
                                      
                                      order by users.id,att_date";

                 $emp_status_leave= "SELECT 
                                    users.id,time_categories.title,
                                    leave_requests_details.leave_date 
                                FROM  users INNER JOIN leave_requests on users.id=leave_requests.user_id
                                INNER JOIN time_categories on time_categories.id=leave_requests.leave_type_id
                                INNER JOIN clients on clients.id=users.department_id
                                INNER JOIN leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
                                WHERE clients.id='".$request->deparment_id."'
                                and date(leave_requests_details.leave_date) BETWEEN '".$request->from_date."' AND '".$request->to_date."'";
                $emp_attach_sql= "select 
                        ad.* from attach_employees_details ad 
                        join users u on u.id = ad.user_id
                        join clients c on u.department_id = c.id
                  where c.id = '".$request->deparment_id."'
                  and date(ad.attach_date) BETWEEN '".$request->from_date."' AND '".$request->to_date."'";                
                $leave_Status = DB::select(DB::raw($emp_status_leave));
                $emp_status_leave_status = collect($leave_Status);
                $emp_attach_data = collect(DB::select($emp_attach_sql));

                $for_dayoff="select users.id,users.name,users.tcat_id,
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
                WHERE clients.id='".$request->deparment_id."'";
                $day_off = collect(DB::select( DB::raw($for_dayoff)));


                 $holi_calen="SELECT id,title,user_id,date as  
                holiday_date,department_id,date_from,date_to 
                FROM holiday_calendars
                WHERE department_id='".$request->deparment_id."' 
                and  date BETWEEN '".$request->from_date."' and '".$request->to_date."'";
                $holidays = collect(DB::select( DB::raw($holi_calen)));

                if($request->deparment_id=="all"){
                $child_departments = Client::where('parent_id',Session::get('dep_id'))->pluck('id');
                $for_dayoff="select users.id,users.name,users.tcat_id,
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
                WHERE clients.parent_id='".Session::get('dep_id')."'";
                $day_off = collect(DB::select( DB::raw($for_dayoff)));
                     
                      $users = DB::table('users')
                    ->join('clients', 'users.department_id', '=', 'clients.id')
                    ->join('designations','designations.id','=','users.designation_id')
                    ->select('users.id','users.name','designations.title','clients.parent_id as dep_id')
                    ->where('clients.parent_id',Session::get('dep_id'))
                    ->where('users.status',1)
                    ->where('flag',1)
                    ->get();                  
                $emp_status_query = "SELECT 
                                            users.id,
                                            users.name,
                                            t.tc_monday_in,
                                            t.tc_monday_out,
                                            t.grace_time,
                                            t.tc_tuesday_in,
                                            t.tc_tuesday_out,
                                            tc_wednesday_in,
                                            t.tc_wednesday_out,
                                            t.tc_thursday_in,t.tc_thursday_out,
                                            t.tc_friday_in,t.tc_friday_out,
                                            t.tc_satureday_in,t.tc_satureday_out,
                                            t.tc_sunday_in,t.tc_sunday_out,
                                            t.time_in,t.time_out,date(a.datetime) as date_time,
                                            a.datetime as att_date,t.type 
                                    FROM users INNER JOIN attendance a on users.id=a.user_id
                                    INNER JOIN time_categories t on t.id=users.tcat_id
                                    inner join clients on users.department_id = clients.id
                                    where                  
                                    users.status=1 
                                     and users.flag=1 
                                     and t.time_cat_type=1 
                                     and clients.parent_id='".Session::get('dep_id')."'
                                     and
                                     date(a.datetime) BETWEEN '".date('Y-m-d', strtotime($request->from_date.' -1 day'))."' AND '".$request->to_date."'
                                 
                                    order by users.id,att_date";
                    $emp_attach_sql= "select 
                                            ad.* from attach_employees_details ad 
                                            join users u on u.id = ad.user_id
                                            join clients c on u.department_id = c.id
                                      where c.parent_id = '".Session::get('dep_id')."'
                                      and date(ad.attach_date) BETWEEN '".$request->from_date."' AND '".$request->to_date."'";

                    $emp_status_leave= "SELECT 
                                    users.id,time_categories.title,
                                    leave_requests_details.leave_date 
                                FROM  users INNER JOIN leave_requests on users.id=leave_requests.user_id
                                INNER JOIN time_categories on time_categories.id=leave_requests.leave_type_id
                                INNER JOIN clients on clients.id=users.department_id
                                INNER JOIN leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
                                WHERE clients.parent_id='".Session::get('dep_id')."'
                                and date(leave_requests_details.leave_date) BETWEEN '".$request->from_date."' AND '".$request->to_date."'";

                    $holi_calen="SELECT id,title,user_id,date(date) as  
                                                 holiday_date,department_id,date_from,date_to 
                                     FROM holiday_calendars  WHERE date(date) BETWEEN '".$request->from_date."' AND '".$request->to_date."' 
                                     and department_id in (".implode(',',$child_departments->toArray()).")"; 
                    $holidays = collect(DB::select( DB::raw($holi_calen)));

                    $leave_Status = DB::select(DB::raw($emp_status_leave));
                    $emp_status_leave_status = collect($leave_Status);
                    $emp_attach_data = collect(DB::select($emp_attach_sql));



                }
        
                $department_name=$request->deparment_id;
                
            }

                      
            if(!empty($request->subdeparment_id)){
                
              
                $users = DB::table('users')
                    ->join('clients', 'users.department_id', '=', 'clients.id')
                    ->join('designations','designations.id','=','users.designation_id')
                    ->select('users.id','users.name','designations.title','clients.parent_id as dep_id')
                    ->where('clients.parent_id',$request->subdeparment_id)
                    ->where('users.status',1)
                    ->where('flag',1)
                    ->get();
                                   
                $emp_status_query = "SELECT 
                                            users.id,
                                            users.name,
                                            t.tc_monday_in,
                                            t.tc_monday_out,
                                            t.grace_time,
                                            t.tc_tuesday_in,
                                            t.tc_tuesday_out,
                                            tc_wednesday_in,
                                            t.tc_wednesday_out,
                                            t.tc_thursday_in,t.tc_thursday_out,
                                            t.tc_friday_in,t.tc_friday_out,
                                            t.tc_satureday_in,t.tc_satureday_out,
                                            t.tc_sunday_in,t.tc_sunday_out,
                                            t.time_in,t.time_out,date(a.datetime) as date_time,
                                            a.datetime as att_date,t.type 
                                    FROM users INNER JOIN attendance a on users.id=a.user_id
                                    INNER JOIN time_categories t on t.id=users.tcat_id
                                    inner join clients on users.department_id = clients.id
                                    where                  
                                    users.status=1 
                                     and users.flag=1 
                                     and t.time_cat_type=1 
                                     and clients.parent_id='".$request->subdeparment_id."'
                                     and
                                     date(a.datetime) BETWEEN '".$request->from_date."' AND '".$request->to_date."'
                                 
                                    order by users.id,att_date";
                    $emp_status_leave= "SELECT 
                                    users.id,time_categories.title,
                                    leave_requests_details.leave_date 
                                FROM  users INNER JOIN leave_requests on users.id=leave_requests.user_id
                                INNER JOIN time_categories on time_categories.id=leave_requests.leave_type_id
                                INNER JOIN clients on clients.id=users.department_id
                                INNER JOIN leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
                                WHERE clients.parent_id='".$request->subdeparment_id."'
                                and date(leave_requests_details.leave_date) BETWEEN '".$request->from_date."' AND '".$request->to_date."'";
                    $holi_calen="SELECT id,title,user_id,date(date) as  
                    holiday_date,department_id,date_from,date_to 
                    FROM holiday_calendars  WHERE date(date) BETWEEN '".date('Y-m-d', strtotime($request->from_date.' -1 day'))."' AND '".$request->to_date."' and department_id '".$request->subdeparment_id."'";             
                    $holidays = collect(DB::select( DB::raw($holi_calen)));
                    $leave_Status = DB::select(DB::raw($emp_status_leave));
                    $emp_status_leave_status = collect($leave_Status);  
             
                  $department_name=$request->subdeparment_id;
            }
            
          
           
            $time_in = DB::select( DB::raw($emp_status_query));

           
            $emp_att_data = [];
            foreach ($time_in as $tin_data){
                $att_unique_idex = $tin_data->id."--".$tin_data->date_time;
                $emp_att_data[$att_unique_idex][] = $tin_data;
            }
          
            $emp_status_time_in = $emp_att_data;
            return view('Reports.monthly_detailed_report',compact(
                    'users',
                    'first_day',
                    'emp_status_time_in',
                    'emp_status_leave_status',
                    'emp_attach_data',
                    'department',
                    'department_name',
                    'from_date',
                    'to_date',
                    'holidays',
                    'month_year_name',
                    'sub_department',
                    'day_off'
                                                                 )
            );
            
        }
        $users='';
        $department_name="";
        $month_year_name="";
        $holidays="";
        $day_off="";
        $emp_attach_data = [];
        return view('Reports.monthly_detailed_report',compact(
                                                               'users',
                                                               'department',
                                                               'emp_attach_data',
                                                               'department_name',
                                                               'month_year_name', 
                                                               'holidays',
                                                               'sub_department',
                                                               'day_off'
                                                           )
                    );

    }
}