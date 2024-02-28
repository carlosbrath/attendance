<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Session;
use App\Roaster as roster;
use App\TimeCategory;
use App\User;
use DB;
use App\Client;

class ReportsController extends Controller
{
    public function reports(Request $request)
 {
  
    if($request->search=="all")  //when choosing all in reports dropdown
    {
       $emp_by_dep=DB::table('users')
            ->join('clients','users.department_id','clients.id')
            ->where('users.status',1)
            ->where('users.flag',1)
            ->where('clients.parent_id',session::get('dep_id'))
            ->count();
    }
    else{
    $emp_by_dep=User::where('department_id',$request->search)->where('flag',1)->where('status',1)->count();
     }
     // employee fetch by department
    $rep_date="";
    $date= date('Y-m-d');
    $day= date("l");

    // echo date("l");
    // $weekday = date("l",$date);
    // $unixTimestamp = strtotime($date);
    // dd(date("l", $unixTimestamp));
     $dep_id=Session::get('dep_id');
     if(Session::get('role_id')==1)
       {
        $department=Client::where('parent_id','!=',0)->get();
       }
     else
      {
      
       $department=Client::where('parent_id',Session::get('dep_id'))->get();  
       if($department->isEmpty()){     
           $department=Client::where('id',Session::get('dep_id'))->where('status',1)->get();  
       }
      }                                     
       if($request->isMethod('post')){
		   	        $date= date('Y-m-d');
                     $this->validate($request, [  
                               'search' => 'required',
                               'date'=>'required'
                              ]);            
			      $date=date_create($request->date);
			      date_format($date,"d F, Y");                
			      $search=$request->search;
			      $rep_date=$request->date;
            $day=date('l', strtotime($rep_date));
            dd($day);
			   if(Session::get('role_id')==5 ||  Session::get('role_id')==7){
              $client=Client::where('parent_id',$search)->first();
                 if(date('l', strtotime($rep_date))=="Monday"){
                               
               if($client==null){
                     
                     $holi_calen="SELECT id,title,user_id,date as  
                                      holiday_date,department_id,date_from,date_to 
                                      FROM holiday_calendars
                                      WHERE department_id='".$request->search."' 
                                      and  date='".$request->date."'";

                      $holidays = collect(DB::select( DB::raw($holi_calen)));

                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       
                      
                       $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_monday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.id='".$search."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                  }

                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                   
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_monday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";

                     
                    
                 }

               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                
                   $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_monday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."'
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.id='".$search."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 }      
              }  //client == null
              else{
                     

                    $holi_calen="SELECT id,title,user_id,date as  
                                      holiday_date,department_id,date_from,date_to 
                                      FROM holiday_calendars
                                      WHERE department_id='".$request->search."' 
                                      and  date='".$request->date."'";

                      $holidays = collect(DB::select( DB::raw($holi_calen)));
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){

                       $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_monday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.parent_id='".$search."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                  
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_monday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$search."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";
                 }
                 if($search=='all'  && $rep_date!='' && $request->status=='present' ){  
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_monday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";
                    
                 }
                if($search=='all'  && $rep_date!='' && $request->status=='absent' ){

                      $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_monday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.parent_id='".Session::get('dep_id')."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                     
                }
  
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                   
                   $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$search."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_monday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."'
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.parent_id='".$search."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
            
                  }
            if($search=='all'  && $rep_date!='' && $request->status=='all' ){


                    $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND
                                   c.parent_id='".Session::get('dep_id')."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."'  ,' ',tcat.tc_monday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."' 
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.parent_id='".Session::get('dep_id')."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                  
                 }        
               } 
             }
              if(date('l', strtotime($rep_date))=="Tuesday"){

               if($client==null){
                       $holi_calen="SELECT id,title,user_id,date as  
                                      holiday_date,department_id,date_from,date_to 
                                      FROM holiday_calendars
                                      WHERE department_id='".$request->search."' 
                                      and  date='".$request->date."'";

                      $holidays = collect(DB::select( DB::raw($holi_calen)));
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                        $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));


                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.id='".$search."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";

                      
                    
                 } 
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                  
                  $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_wednesday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."'
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.id='".$search."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
               }        
            }  //client == null
              else{
                      
                       $holi_calen="SELECT id,title,user_id,date as  
                                      holiday_date,department_id,date_from,date_to 
                                      FROM holiday_calendars
                                      WHERE department_id='".$request->search."' 
                                      and  date='".$request->date."'";

                      $holidays = collect(DB::select( DB::raw($holi_calen)));
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                      $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));


                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.parent_id='".$search."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$search."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";
                    
                 } 
              if($search=='all'  && $rep_date!='' && $request->status=='present' ){  
                      

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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";
                    
                 }
                if($search=='all'  && $rep_date!='' && $request->status=='absent' ){

                      $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.parent_id='".Session::get('dep_id')."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                     
                }
                 
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                     
                   $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$search."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_wednesday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."'
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.parent_id='".$search."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
            
          
                 }
                   if($search=='all'  && $rep_date!='' && $request->status=='all' ){


                    $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND
                                   c.parent_id='".Session::get('dep_id')."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."'  ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."' 
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.parent_id='".Session::get('dep_id')."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));

                 }  
              } 
              
                
              }
              if(date('l', strtotime($rep_date))=="Wednesday"){
               if($client==null){
                       $holi_calen="SELECT id,title,user_id,date as  
                                      holiday_date,department_id,date_from,date_to 
                                      FROM holiday_calendars
                                      WHERE department_id='".$request->search."' 
                                      and  date='".$request->date."'";

                      $holidays = collect(DB::select( DB::raw($holi_calen)));
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                        $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));


                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_wednesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.id='".$search."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_wednesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";
                    
                 } 
                 
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                         
                  $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_wednesday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."'
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.id='".$search."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
               }        
              }  //client == null
              else{
                       $holi_calen="SELECT id,title,user_id,date as  
                                      holiday_date,department_id,date_from,date_to 
                                      FROM holiday_calendars
                                      WHERE department_id='".$request->search."' 
                                      and  date='".$request->date."'";

                      $holidays = collect(DB::select( DB::raw($holi_calen)));

                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                      $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));


                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_wednesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.parent_id='".$search."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                   if($search=='all'  && $rep_date!='' && $request->status=='present' ){  
                      

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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_wednesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";
                    
                 }
                if($search=='all'  && $rep_date!='' && $request->status=='absent' ){

                      $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_wednesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.parent_id='".Session::get('dep_id')."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                     
                } 
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_wednesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$search."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";
                    
                 }
                  if($search!='' && $rep_date!='' && $request->status=='all' ){
                                         
                $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$search."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_wednesday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."'
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.parent_id='".$search."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
            
          
                 } 
               
                  if($search=='all'  && $rep_date!='' && $request->status=='all' ){


                    $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND
                                   c.parent_id='".Session::get('dep_id')."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."'  ,' ',tcat.tc_wednesday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."' 
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.parent_id='".Session::get('dep_id')."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));

                 

                 } 
               } 
                
              }

               if(date('l', strtotime($rep_date))=="Thursday"){
               
               if($client==null){
                     $holi_calen="SELECT id,title,user_id,date as  
                                    holiday_date,department_id,date_from,date_to 
                                    FROM holiday_calendars
                                    WHERE department_id='".$request->search."' 
                                    and  date='".$request->date."'";

                    $holidays = collect(DB::select( DB::raw($holi_calen)));
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));


                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_thursday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.id='".$search."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));


                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_thursday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";
                 } 
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                  

                   $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_thursday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."'
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.id='".$search."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));

                 }        
              }  //client == null
              else{

                      $holi_calen="SELECT id,title,user_id,date as  
                                      holiday_date,department_id,date_from,date_to 
                                      FROM holiday_calendars
                                      WHERE department_id='".$request->search."' 
                                      and  date='".$request->date."'";

                      $holidays = collect(DB::select( DB::raw($holi_calen)));
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                      $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));


                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_thursday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.parent_id='".$search."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_thursday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$search."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";
                    
                 }

                if($search=='all'  && $rep_date!='' && $request->status=='absent' ){

                      $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_thursday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.parent_id='".Session::get('dep_id')."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                     
                }
                  if($search=='all'  && $rep_date!='' && $request->status=='present' ){  
                     
                       $report_query_for_departmnent_adm  =  "select  
                                                                 u.id,
                                                                 u.name,
                                                                u.gender,
                                                                 tcat.tc_monday_in as check_dayoff,
                                                                 u.bps,
                                                                 u.dob,
                                                                 u.file_number, 
                                                                 tcat.is_night_shift,
                                                                 clients.parent_id,
                                                                 clients.name as deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_thursday_in),tcat.grace_time) as  time_in,
                                                                 att.datetime as attendance_time,att.mark_attendance  
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id 
                                              and date(att.datetime)='".$rep_date."'
                                              LEFT JOIN designations on designations.id=u.designation_id 
                                              LEFT JOIN time_categories tcat 
                                                 on tcat.id=u.tcat_id 
                                              LEFT JOIN clients on clients.id = u.department_id
                                              WHERE flag=1 and u.status=1  and att.datetime is not null  
                                              and clients.parent_id='".Session::get('dep_id')."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                    
                 }  
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                       
                    $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$search."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_thursday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."'
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.parent_id='".$search."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
            
          
                 } 
                  if($search=='all'  && $rep_date!='' && $request->status=='all' ){


                    $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND
                                   c.parent_id='".Session::get('dep_id')."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."'  ,' ',tcat.tc_thursday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."' 
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.parent_id='".Session::get('dep_id')."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));

                 

                 }  
              } 
            }
              if(date('l', strtotime($rep_date))=="Friday"){
                 
          
               $client=Client::where('parent_id',$search)->first();
               if($client==null){
                     $holi_calen="SELECT id,title,user_id,date as  
                                    holiday_date,department_id,date_from,date_to 
                                    FROM holiday_calendars
                                    WHERE department_id='".$request->search."' 
                                    and  date='".$request->date."'";

                      $holidays = collect(DB::select( DB::raw($holi_calen)));
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       
                        $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_friday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.id='".$search."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm))); 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  

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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_friday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";
                    
                 } 
     
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                         
                  $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_friday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."'
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.id='".$search."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
               }        
              }  //client == null
              else{
                       $holi_calen="SELECT id,title,user_id,date as  
                                      holiday_date,department_id,date_from,date_to 
                                      FROM holiday_calendars
                                      WHERE department_id='".$request->search."' 
                                      and  date='".$request->date."'";

                      $holidays = collect(DB::select( DB::raw($holi_calen)));
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_friday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.parent_id='".$search."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_friday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$search."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";
                    
                 }
                  if($search=='all'  && $rep_date!='' && $request->status=='present' ){  
                      

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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_friday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  ORDER BY u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                      $daily_leave_reports="";
                    
                 }
                if($search=='all'  && $rep_date!='' && $request->status=='absent' ){

                      $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                      $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                       $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_friday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  and date(att.datetime)='".$rep_date."'
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1 AND clients.parent_id='".Session::get('dep_id')."'  and att.datetime is null  
                                  ORDER BY u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                     
                }
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                         
                   $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$search."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_friday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."'
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.parent_id='".$search."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
            
                  }
                  if($search=='all'  && $rep_date!='' && $request->status=='all' ){


                    $daily_leave= "SELECT  u.id,
                                  lr.leave_type_id,
                                  lr.leave_type,
                                  lr.time_from,
                                  lrd.leave_date,
                                  lr.from_date,
                                  lr.to_date,
                                  lr.time_to,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  c.name as deparment_name,
                                  time_categories.title
                                  FROM users u 
                                  LEFT JOIN leave_requests lr ON lr.`user_id` = u.id 
                                  left join time_categories  on lr.leave_type_id=time_categories.id
                                  LEFT JOIN `leaves` t ON t.id = lr.leave_type_id
                                  LEFT JOIN leave_requests_details lrd ON lrd.`parent_table_id` = lr.id 
                                  JOIN clients c ON c.id = u.`department_id` 
                                  WHERE flag=1 AND u.status=1 AND
                                   c.parent_id='".Session::get('dep_id')."'     
                                  AND DATE(lrd.leave_date)='".$rep_date."' 
                                  ORDER BY u.id,lrd.leave_date";
                  $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));                     
                  $report_query_for_departmnent_adm  =  "SELECT  u.id,
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
                            ADDTIME(CONCAT('".$rep_date."'  ,' ',tcat.tc_friday_in),tcat.grace_time) AS 
                            time_in,
                            att.datetime AS attendance_time,
                            att.mark_attendance 
                            FROM users u 
                            LEFT JOIN attendance att ON u.id = att.user_id 
                            and date(att.datetime)='".$rep_date."' 
                            LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                            LEFT JOIN designations ON designations.id=u.designation_id
                            LEFT JOIN clients ON clients.id = u.department_id
                            LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                            WHERE flag=1 AND u.status=1 AND clients.parent_id='".Session::get('dep_id')."'    
                            ORDER BY u.id,att.datetime";          
                  $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));

                 

                 }  
                
               } 
                  
               //checking
            } 
           
		    }
         
          return view('Reports.attandance_report',compact('attandance_reports','department','rep_date','emp_by_dep','daily_leave_reports','holidays','day'));  
		}   
         $attandance_reports='';
         $daily_leave_reports='';
         $emp_total_leave='';
         $check_employee_leave='';
         $holidays="";
         $day="";

          return view('Reports.attandance_report',compact('attandance_reports','department','rep_date','emp_total_leave','check_employee_leave','emp_by_dep','daily_leave_reports','day'));
   } //end report method
} 

