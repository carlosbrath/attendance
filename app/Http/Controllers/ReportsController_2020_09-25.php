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
            
            // if($request->search == "" && $request->search == "" ){
            //     $this->validate($request, [  
            //                    'subdepartment_id'=>'required',
            //                     'search'=>'required',
            //                    ]); 
            // }                             
			      $date=date_create($request->date);
			      date_format($date,"d F, Y");                
			      $search=$request->search;
			      $rep_date=$request->date;
			   if(Session::get('role_id')==5 ||  Session::get('role_id')==7){
              
              $client=Client::where('parent_id',$search)->first();
                 if(date('l', strtotime($rep_date))=="Monday"){
                $check_day= date('l', strtotime($rep_date));
                
                
              if(!empty($request->subdepartment_id)){   
                // this is condition for main account having sub_account filter e.g main pbm like having pbm quetta,karachi,lahore,peshawar etc
                     if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' ){     
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                       $fetch_emp=collect(DB::select( DB::raw($emp)));
                      $report_query_for_departmnent_adm  =  
                      "SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_monday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND 
                                  c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                         $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                      }    
                      // end day monday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' 
                     
                   if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_monday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  monday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all'
                    if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_monday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } 

                  //  monday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent'


                  }  // monday end of !empty($request->sub_department_id)                
               if(!empty($request->search)){
                    
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                     
      
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";


                      $fetch_emp=collect(DB::select( DB::raw($emp)));
                                               
                      
                       $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_monday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                     
                    
                 }   //monday -> $search!=''  && $rep_date!='' && $request->status=='present' 
                   if($search!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_monday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  monday $search!=''  && $rep_date!='' && $request->status=='all'
                    if($search!=''  && $rep_date!='' && $request->status=='absent' ){
                      
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_monday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } // monday $search!=''  && $rep_date!='' && $request->status=='absent' 
                     if($search=='all'  && $rep_date!='' && $request->status=='absent' ){
                       
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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_monday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'  
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                         $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));  

                        $flag= "absent";       


                   }   // monday  searrch=all //  status= absent
                    if($search=='all'  && $rep_date!='' && $request->status=='present' ){  
                     
      
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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_monday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                          $holidays="";
                      $flag="present";
                     
                    
                 }   // monday  searrch=all //  status= present // rep_date !=""

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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_monday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."'
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen))); 

                        $flag= "all";       


                   }  //monday $serach==all // status==all // $rep_date!=''

                 
              } 
            

           } //end monday

 //start tuesday
         if(date('l', strtotime($rep_date))=="Tuesday"){
                 
               $check_day= date('l', strtotime($rep_date));
                if(!empty($request->subdepartment_id)){   
                // this is condition for main account having sub_account filter e.g main pbm like having pbm quetta,karachi,lahore,peshawar etc
                     if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' ){     
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
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
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND 
                                  c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                         $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                      }    
                      // end day tuesday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' 
                     
                   if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  tuesday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all'
                    if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
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
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } 

                  //  tuesday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent'


                  }  // tuesday end of !empty($request->sub_department_id) 
       


               if(!empty($request->search)){
                    
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                     
                  
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
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));

                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                     
                    
                 }   //tuesday -> $search!=''  && $rep_date!='' && $request->status=='present' 
                   if($search!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                     
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  tuesday $search!=''  && $rep_date!='' && $request->status=='all'
                    if($search!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_tuesday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } // tuesday $search!=''  && $rep_date!='' && $request->status=='absent' 
                     if($search=='all'  && $rep_date!='' && $request->status=='absent' ){
                       
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'  
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                         $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));  

                        $flag= "absent";       


                   }   // tuesday  searrch=all //  status= absent // $rep_date !=''
                    if($search=='all'  && $rep_date!='' && $request->status=='present' ){  
                     
      
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."'
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen))); 

                        $flag= "all";       


                   }  //tuesday $serach==all // status==all // $rep_date!=''

                 
              }  

           } // end tuesday


      //start Wednesday

           if(date('l', strtotime($rep_date))=="Wednesday"){
               
                $check_day= date('l', strtotime($rep_date));
                if(!empty($request->subdepartment_id)){   
                // this is condition for main account having sub_account filter e.g main pbm like having pbm quetta,karachi,lahore,peshawar etc
                     if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' ){     
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                       $fetch_emp=collect(DB::select( DB::raw($emp)));
                      $report_query_for_departmnent_adm  =  
                      "SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_wednesday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND 
                                  c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                         $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                      }    
                      // end day tuesday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' 
                     
                   if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_wednesday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  tuesday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all'
                    if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_wednesday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } 

                  //  wednesday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent'


                  }  // wednesday end of !empty($request->sub_department_id) 

                               
               if(!empty($request->search)){
                    
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                     
                  
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";

                      $fetch_emp=collect(DB::select( DB::raw($emp)));
                     
                      
                       $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_wednesday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                     
                    
                 }   //wednesday -> $search!=''  && $rep_date!='' && $request->status=='present' 
                   if($search!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_wednesday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  wednesday $search!=''  && $rep_date!='' && $request->status=='all'
                    if($search!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_wednesday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } // wednesday $search!=''  && $rep_date!='' && $request->status=='absent' 
                     if($search=='all'  && $rep_date!='' && $request->status=='absent' ){
                       
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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_wednesday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'  
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                         $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));  

                        $flag= "absent";       


                   }   // wednesday  searrch=all //  status= absent // $rep_date !=''
                    if($search=='all'  && $rep_date!='' && $request->status=='present' ){  
                     
      
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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_wednesday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                          $holidays="";
                      $flag="present";
                     
                    
                 }   // wednesday  searrch=all //  status= present // rep_date !=""

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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_wednesday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."'
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen))); 

                        $flag= "all";       


                   }  //Wednesday $serach==all // status==all // $rep_date!=''

                 
              }  

           } // end Wednesday

         //start Wednesday

           if(date('l', strtotime($rep_date))=="Thursday"){
             $check_day= date('l', strtotime($rep_date));
              if(!empty($request->subdepartment_id)){   
                // this is condition for main account having sub_account filter e.g main pbm like having pbm quetta,karachi,lahore,peshawar etc
                     if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' ){     
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                       $fetch_emp=collect(DB::select( DB::raw($emp)));
                      $report_query_for_departmnent_adm  =  
                      "SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_thursday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND 
                                  c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                         $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                      }    
                      // end day thursday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' 
                     
                   if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_thursday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  thursday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all'
                    if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_thursday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } 

                  //  thursday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent'


                  }  // thursday end of !empty($request->sub_department_id)    
                               
               if(!empty($request->search)){
                    
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                     
                  
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";

                      $fetch_emp=collect(DB::select( DB::raw($emp)));
                     
                      
                       $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_thursday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                     
                    
                 }   //Thursday -> $search!=''  && $rep_date!='' && $request->status=='present' 
                   if($search!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_thursday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  Thursday $search!=''  && $rep_date!='' && $request->status=='all'
                    if($search!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_thursday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } // Thursday $search!=''  && $rep_date!='' && $request->status=='absent' 
                     if($search=='all'  && $rep_date!='' && $request->status=='absent' ){
                       
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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_thursday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'  
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                         $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));  

                        $flag= "absent";       


                   }   // Thursday  searrch=all //  status= absent // $rep_date !=''
                    if($search=='all'  && $rep_date!='' && $request->status=='present' ){  
                     
      
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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_thursday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                          $holidays="";
                      $flag="present";
                     
                    
                 }   // wednesday  searrch=all //  status= present // rep_date !=""

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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_thursday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."'
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen))); 

                        $flag= "all";       


                   }  //Thursday $serach==all // status==all // $rep_date!=''

                 
              }  

           } // end Thursday


             //start Friday

           if(date('l', strtotime($rep_date))=="Friday"){
             $check_day= date('l', strtotime($rep_date));
               if(!empty($request->subdepartment_id)){   
                // this is condition for main account having sub_account filter e.g main pbm like having pbm quetta,karachi,lahore,peshawar etc
                     if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' ){     
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                       $fetch_emp=collect(DB::select( DB::raw($emp)));
                      $report_query_for_departmnent_adm  =  
                      "SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_friday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND 
                                  c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                         $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                      }    
                      // end day friday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' 
                     
                   if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_friday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  friday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all'
                    if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_friday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } 

                  //  friday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent'


                  }  // friday end of !empty($request->sub_department_id)    
                                  
                               
               if(!empty($request->search)){
                    
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                     
                  
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";

                      $fetch_emp=collect(DB::select( DB::raw($emp)));
                     
                      
                       $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_friday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                     
                    
                 }   //Friday -> $search!=''  && $rep_date!='' && $request->status=='present' 
                   if($search!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_friday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));

                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  Friday $search!=''  && $rep_date!='' && $request->status=='all'
                    if($search!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_friday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } // friday $search!=''  && $rep_date!='' && $request->status=='absent' 
                     if($search=='all'  && $rep_date!='' && $request->status=='absent' ){
                       
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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_friday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'  
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                         $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));  

                        $flag= "absent";       


                   }   // Friday  searrch=all //  status= absent // $rep_date !=''
                    if($search=='all'  && $rep_date!='' && $request->status=='present' ){  
                     
      
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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_friday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                          $holidays="";
                      $flag="present";
                     
                    
                 }   // friday  searrch=all //  status= present // rep_date !=""

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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_friday_in AS check_dayoff,
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
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."'
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen))); 

                        $flag= "all";       


                   }  //Friday $serach==all // status==all // $rep_date!=''

                 
              }  

           } // end Friday

         //start satureday

           if(date('l', strtotime($rep_date))=="Saturday"){
             $check_day= date('l', strtotime($rep_date));
               if(!empty($request->subdepartment_id)){   
                // this is condition for main account having sub_account filter e.g main pbm like having pbm quetta,karachi,lahore,peshawar etc
                     if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' ){     
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                       $fetch_emp=collect(DB::select( DB::raw($emp)));
                      $report_query_for_departmnent_adm  =  
                      "SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_satureday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_satureday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND 
                                  c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                         $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                      }    
                      // end day satureday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' 
                     
                   if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_satureday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_satureday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  satureday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all'
                    if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_satureday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_satureday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } 

                  //  satureday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent'


                  }  // satureday end of !empty($request->sub_department_id)       
                               
               if(!empty($request->search)){
                    
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                     
                  
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";

                      $fetch_emp=collect(DB::select( DB::raw($emp)));
                     
                      
                       $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_satureday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_satureday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                     
                    
                 }   //Saturday -> $search!=''  && $rep_date!='' && $request->status=='present' 
                   if($search!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_satureday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_satureday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  Saturday $search!=''  && $rep_date!='' && $request->status=='all'
                    if($search!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_satureday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_satureday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } // Saturday $search!=''  && $rep_date!='' && $request->status=='absent' 
                     if($search=='all'  && $rep_date!='' && $request->status=='absent' ){
                       
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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_satureday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_satureday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'  
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                         $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));  

                        $flag= "absent";       


                   }   // satureday  searrch=all //  status= absent // $rep_date !=''
                    if($search=='all'  && $rep_date!='' && $request->status=='present' ){  
                     
      
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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_satureday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_satureday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                          $holidays="";
                      $flag="present";
                     
                    
                 }   // Saturday  searrch=all //  status= present // rep_date !=""

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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_satureday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_satureday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."'
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen))); 

                        $flag= "all";       


                   }  //Saturday $serach==all // status==all // $rep_date!=''
                
                 
               }  

           } // end Saturday


          //start Sunday

           if(date('l', strtotime($rep_date))=="Sunday"){
             $check_day= date('l', strtotime($rep_date));
               if(!empty($request->subdepartment_id)){   
                // this is condition for main account having sub_account filter e.g main pbm like having pbm quetta,karachi,lahore,peshawar etc
                     if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' ){     
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                       $fetch_emp=collect(DB::select( DB::raw($emp)));
                      $report_query_for_departmnent_adm  =  
                      "SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_sunday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_sunday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND 
                                  c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id 
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                         $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                      }    
                      // end day sunday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='present' 
                     
                   if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_sunday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_sunday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  sunday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='all'
                    if($request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                          where clients.parent_id='".$request->subdepartment_id."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_sunday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_sunday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".$request->subdepartment_id."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".$request->subdepartment_id."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."' group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->subdepartment_id."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } 

                  //  sunday $request->subdepartment_id!=''  && $rep_date!='' && $request->status=='absent'


                  }  // sunday end of !empty($request->sub_department_id)       
                                
                               
               if(!empty($request->search)){
                    
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                     
                  
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";

                      $fetch_emp=collect(DB::select( DB::raw($emp)));
                     
                      
                       $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_sunday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_sunday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                      $flag="present";
                      $holidays="";
                     
                    
                 }   //Sunday -> $search!=''  && $rep_date!='' && $request->status=='present' 
                   if($search!=''  && $rep_date!='' && $request->status=='all' ){
                       
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_sunday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_sunday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));


                        $flag= "all";       


                   }  //  Sunday $search!=''  && $rep_date!='' && $request->status=='all'
                    if($search!=''  && $rep_date!='' && $request->status=='absent' ){
                       
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
                          where users.department_id='".$search."' and users.status=1 and flag=1";
                          $fetch_emp=collect(DB::select( DB::raw($emp)));
                         $report_query_for_departmnent_adm  =  "
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_sunday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_sunday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.id='".$search."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.id='".$search."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));

                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".$request->search."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays  = collect(DB::select( DB::raw($holi_calen)));   

                        $flag= "absent";       


                    } // Sunday $search!=''  && $rep_date!='' && $request->status=='absent' 
                     if($search=='all'  && $rep_date!='' && $request->status=='absent' ){
                       
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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_sunday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_sunday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'  
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                         $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen)));  

                        $flag= "absent";       


                   }   // sunday  searrch=all //  status= absent // $rep_date !=''
                    if($search=='all'  && $rep_date!='' && $request->status=='present' ){  
                     
      
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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_sunday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_sunday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."' 
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                          $holidays="";
                      $flag="present";
                     
                    
                 }   // Sunday  searrch=all //  status= present // rep_date !=""

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
                                  SELECT  u.id,
                                  u.name,
                                  u.bps,
                                  u.gender,
                                  u.dob,
                                  tcat.tc_sunday_in AS check_dayoff,
                                  u.file_number,
                                  tcat.is_night_shift, 
                                  clients.name AS 
                                  deparment_name,
                                  designations.title,
                                  ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.tc_sunday_in),tcat.grace_time) AS 
                                  time_in,
                                  att.datetime AS attendance_time,
                                  att.mark_attendance 
                                  FROM users u 
                                  LEFT JOIN attendance att ON u.id = att.user_id 
                                  LEFT JOIN time_categories tcat ON tcat.id=u.tcat_id
                                  LEFT JOIN designations ON designations.id=u.designation_id
                                  LEFT JOIN clients ON clients.id = u.department_id
                                  LEFT JOIN attach_employees_details attach_emp ON attach_emp.user_id=u.id 
                                  WHERE flag=1 AND u.status=1  and att.datetime is not null  AND clients.parent_id='".Session::get('dep_id')."'
                                  and date(att.datetime) between '".$rep_date."' and '".$rep_date."'
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
                                  WHERE flag=1 AND u.status=1 AND c.parent_id='".Session::get('dep_id')."'   
                                  AND DATE(lrd.leave_date)='".$rep_date."'  group by u.id
                                  ORDER BY u.id,lrd.leave_date";
                        $daily_leave_reports = collect(DB::select( DB::raw($daily_leave)));
                        
                        $attach_emp  =  "select users.id,users.name,users.file_number,users.gender,users.bps,clients.name as department_name, designations.title as designation
                        from users 
                        INNER join clients on clients.id=users.department_id
                        INNER join designations on designations.id=users.designation_id
                        INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                        where attach_employees_details.status=1 and attach_employees_details.attach_date='".$rep_date."'";
                          $attach_emp_detail = collect(DB::select( DB::raw($attach_emp)));
                        $holi_calen="SELECT id,title,user_id,date as  
                        holiday_date,department_id,date_from,date_to 
                        FROM holiday_calendars
                        WHERE department_id='".Session::get('dep_id')."' 
                        and  date BETWEEN '".$rep_date."' and '".$rep_date."'";
                        $holidays = collect(DB::select( DB::raw($holi_calen))); 

                        $flag= "all";       


                   }  //sunday $serach==all // status==all // $rep_date!=''
                
                 
               }  

           } // end Sunday

     


             
		    }
          
          return view('Reports.attandance_report',compact('attandance_reports','department','rep_date','emp_by_dep','daily_leave_reports','fetch_emp','attach_emp_detail','flag','holidays','sub_department','check_day'));  
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
          return view('Reports.attandance_report',compact('attandance_reports','flag','department','rep_date','emp_total_leave','check_employee_leave','emp_by_dep','daily_leave_reports','fetch_emp','attach_emp_detail','holidays','sub_department','check_day'));
   } //end report method
} 

