<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use DB;
use Session;
use DateTime;

class MonthlyPayrollContoller extends Controller
{

	    public function reports(Request $request){
        $dep_id = Session::get('dep_id');
       if(Session::get('user')->role_id == 11 && $loged_in_department->parent_id!=0){
          $dep_id = $loged_in_department->parent_id;
       }
        $department= Client::where('parent_id',$dep_id)
        ->where('sub_account',1)
        ->pluck('name','id');
         $sub_department= Client::where('parent_id',$dep_id)
        ->where('sub_account',2)
        ->pluck('name','id');
        if($request->isMethod('post')){
            $this->validate($request, [
                'from_date'=>'required',
                'to_date'=>'required',
                'month'=>'required',
            ]);
            $department_id = $dep_id;
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


            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $first_day=$request->month;
            $yrdata= strtotime($request->month);
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
                    ->select('users.id','users.name','users.fixed_salary','users.hour_rate_salary','users.overtime_check','users.overtime_check','users.hour_rate_salary','designations.title','clients.parent_id as dep_id')
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
                                     date(datetime) BETWEEN '".$request->from_date."' AND '".$request->to_date."'
                                      
                                      order by users.id,att_date";



                 $emp_status_leave= "SELECT 
                                    users.id,time_categories.title,
                                    leave_requests_details.leave_date 
                                FROM  users INNER JOIN leave_requests on users.id=leave_requests.user_id
                                INNER JOIN time_categories on time_categories.id=leave_requests.leave_type_id
                                INNER JOIN clients on clients.id=users.department_id
                                INNER JOIN leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
                                WHERE clients.id in (".$users_department_clause.")
                                and date(leave_requests_details.leave_date) BETWEEN '".$request->from_date."' AND '".$request->to_date."'";
                                      
                 $leave_Status = DB::select(DB::raw($emp_status_leave));
                 $emp_status_leave_status = collect($leave_Status);

                 $emp_status_attach= "SELECT user_id as id,date(aed.attach_date) as attach_date,time_from,time_to FROM 
                                          `attach_employees_details`  aed
                                    join users u on u.id = aed.user_id
                                    where u.department_id in (".$users_department_clause.")
                                and date(aed.attach_date) BETWEEN '".$request->from_date."' AND '".$request->to_date."'";
                                      
                 $attach_Status = DB::select(DB::raw($emp_status_attach));
                 $emp_status_attach_status = collect($attach_Status);

                $for_dayoff="select users.id,users.name,users.tcat_id,
                time_categories.tc_monday_in,
                 time_categories.tc_tuesday_in,
                  time_categories.tc_wednesday_in,
                    time_categories.tc_thursday_in,
                      time_categories.tc_friday_in,
                       time_categories.tc_satureday_in,
                        time_categories.tc_sunday_in,
                        time_categories.time_in,
                        time_categories.time_out
                 from users INNER join clients
                on clients.id=users.department_id
                inner join time_categories 
                on time_categories.id=users.tcat_id
                WHERE clients.id='".$request->deparment_id."'";
                $day_off = collect(DB::select( DB::raw($for_dayoff)));
				$bonus="SELECT amount,date,user_id  FROM `employee_bonuses` WHERE date BETWEEN '".$request->from_date."' and '".$request->to_date."' and status=1";
				$emp_bonus=  collect(DB::select( DB::raw($bonus)));

				$deduction="SELECT amount,date,user_id  FROM `employee_bonuses` WHERE date BETWEEN '".$request->from_date."' and '".$request->to_date."' and status=2";
				$emp_deduction=  collect(DB::select( DB::raw($deduction)));
	


                $holi_calen="SELECT id,title,user_id,date as  
                holiday_date,department_id,date_from,date_to 
                FROM holiday_calendars
                WHERE department_id in(".$users_department_clause.") 
                and  date BETWEEN '".$request->from_date."' and '".$request->to_date."'";
                $holidays = collect(DB::select( DB::raw($holi_calen)));

                 if($request->deparment_id=="all"){
                     
                      $users = DB::table('users')
                    ->join('clients', 'users.department_id', '=', 'clients.id')
                    ->join('designations','designations.id','=','users.designation_id')
                    ->select('users.id','users.name','users.fixed_salary','users.hour_rate_salary','users.overtime_check','designations.title','clients.parent_id as dep_id')
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
                                     date(a.datetime) BETWEEN '".$request->from_date."' AND '".$request->to_date."'
                                 
                                    order by users.id,att_date";
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
                    FROM holiday_calendars  WHERE(date(date) BETWEEN '".$request->from_date."' AND '".$request->to_date."')";             
                    $holidays = collect(DB::select( DB::raw($holi_calen)));
                    $leave_Status = DB::select(DB::raw($emp_status_leave));
                    $emp_status_leave_status = collect($leave_Status);



                }
        
                $department_name=$request->deparment_id;
                
            }

                      
            if(!empty($request->subdeparment_id)){
                
              
                $users = DB::table('users')
                    ->join('clients', 'users.department_id', '=', 'clients.id')
                    ->join('designations','designations.id','=','users.designation_id')
                    ->select('users.id','users.name','users.fixed_salary','users.hour_rate_salary','users.overtime_check','designations.title','clients.parent_id as dep_id')
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
                    FROM holiday_calendars  WHERE(date(date) BETWEEN '".$request->from_date."' AND '".$request->to_date."')";             
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
          
            return view('Reports.monthly_detailed_payroll_report',compact(
                    'users',
                    'first_day',
                    'emp_status_time_in',
                    'emp_status_leave_status',
                    'emp_status_attach_status',
                    'department',
                    'department_name',
                    'from_date',
                    'to_date',
                    'holidays',
                    'month_year_name',
                    'sub_department',
                    'time_in',
                    'day_off',
                    'emp_bonus',
                    'emp_deduction'
                                                                 )
            );
        }
        $users='';
        $department_name="";
        $month_year_name="";
        $holidays="";
        $time_in="";
        $day_off="";
        $emp_bonus="";
        $emp_deduction="";

        return view('Reports.monthly_detailed_payroll_report',compact(
                                                               'users',
                                                               'department',
                                                               'department_name',
                                                               'month_year_name', 
                                                               'holidays',
                                                               'sub_department',
                                                               'time_in',
                                                               'day_off',
                                                               'emp_bonus',
                                                               'emp_deduction'
                                                           )
                    );

    }


    public function monthly_payroll(Request $request){
        DB::statement('SET GLOBAL group_concat_max_len = 100000000');

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
        $emp_bonus = [];
        $emp_deduction = [];
        $choosen_department_name = "";
        $daytime_attendance = [];
        if($request->has('search')){
            $this->validate($request, [       
                'from_date'=>'required',
                'to_date'=>'required'
            ]);
            $yesterday= date('Y-m-d',strtotime($request->from_date . "-1 days"));
            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $first_day=$request->month;
            $report_type=$request->report_type;
            $status=$request->status;
            $department_id = Session::get('dep_id');
            $loged_in_department = Client::find($department_id);
            if($request->has('department_id') && $request->department_id!="all"){
                $department_id = $request->department_id;
            }
            $department_ids = Client::where('parent_id',$department_id)->pluck('id');
            $department_ids[] = $department_id;
            $department_ids = $department_ids->toArray();
            $department_ids = implode(",",$department_ids);
            $attendance_daytime_sql = "SELECT 
                                        u.id as user_id,
                                        u.name,
                                        u.fixed_salary,
                                        u.hour_rate_salary,
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
                                                 GROUP_CONCAT('\"',a.datetime,'\"' ORDER BY a.datetime ASC SEPARATOR ', '),
                                                 ']'
                                                ) AS att_transactions,
                                        c.* 
                                    FROM timenex_new.`users` u
                                    JOIN timenex_new.`attendance` a ON u.id = a.`user_id`
                                    JOIN timenex_new.`time_categories` c ON c.id = u.`tcat_id`
                                    WHERE DATE(a.datetime) BETWEEN '".$yesterday."' AND '".$to_date."'
                                    AND u.`department_id` IN (".$department_ids.")
                                    GROUP BY u.id,DATE(a.`datetime`)
                                    ORDER BY u.id
                                    ) att ON att.user_id = u.id
                                    JOIN timenex_new.`time_categories` c ON c.id = u.`tcat_id`
                                    JOIN timenex_new.`designations` ds ON u.designation_id = ds.`id`
                                    WHERE u.`department_id` IN (".$department_ids.")
                                    and u.status = 1
                                    and u.flag=1
                                    GROUP BY u.id";
            $leave_sql = "SELECT 
                                u.id as user_id,l.`leave_date`,l.time_from,l.time_to 
                          FROM timenex_new.`users` u 
                                JOIN timenex_new.`leave_requests_details` l ON u.id = l.`user_id`
                          WHERE u.`department_id` IN ($department_ids)
                          AND DATE(l.`leave_date`) BETWEEN '".$yesterday."' AND '".$to_date."'";
            $attach_sql = "SELECT 
                                u.id as user_id,l.`attach_date`,l.time_from,l.time_to
                          FROM timenex_new.`users` u 
                                JOIN timenex_new.`attach_employees_details` l ON u.id = l.`user_id`
                          WHERE u.`department_id` IN ($department_ids)
                          AND DATE(l.`attach_date`) BETWEEN '".$yesterday."' AND '".$to_date."'";
            
            if(Session::get('role_id') == 7){
              //department head & we will show leave of its own department + sections
              $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$loged_in_department->id." or c.id = ".$loged_in_department->id;
            }else if(Session::get('role_id') == 11 && $loged_in_department->parent_id!=0){
              //department head & we will show leave of its own department + sections
              $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$loged_in_department->parent_id." or c.id = ".$loged_in_department->id;
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
                            and  date BETWEEN '".$yesterday."' and '".$to_date."'";

            if($loged_in_department->sub_account==2){
              $holi_calen="SELECT id,title,user_id,date as  
                              holiday_date,department_id,date_from,date_to 
                              FROM holiday_calendars
                              WHERE department_id in (".$users_department_clause.") 
                              and  date BETWEEN '".$yesterday."' and '".$to_date."'";
            }


            $bonus="SELECT amount,date,user_id  FROM `employee_bonuses` WHERE date BETWEEN '".$request->from_date."' and '".$request->to_date."' and status=1";
            $deduction="SELECT amount,date,user_id  FROM `employee_bonuses` WHERE date BETWEEN '".$request->from_date."' and '".$request->to_date."' and status=2";

            $emp_deduction=  collect(DB::select( DB::raw($deduction)));            
            $emp_bonus=  collect(DB::select( DB::raw($bonus)));
            $holidays = collect(DB::select( DB::raw($holi_calen)));
            $daytime_attendance = collect(DB::select($attendance_daytime_sql));
            $leaves = collect(DB::select($leave_sql));
            $attachs = collect(DB::select($attach_sql));

        }
        $data = [
            'department'=>$department,
            'choosen_department_id' => 1,
            'choosen_department_name' => "",
            'date_from' => $from_date,
            'date_to' => $to_date,
            'daytime_attendance' => $daytime_attendance,
            'leaves' => $leaves,
            'attachs' => $attachs,
            'report_type' => $report_type,
            'holidays' => $holidays,
            'emp_bonus' => $emp_bonus,
            'emp_deduction' => $emp_deduction,
            'status' => $status
        ];
        return view('Reports.monthly_payroll',$data);

    }    
    
}
