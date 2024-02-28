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
       
     
       $attandance_reports="";
      

       $emp_total_leave= DB::table('emp_leave_configuration')
                         ->join('time_categories','time_categories.id','emp_leave_configuration.leave_type_id')
                         ->get();
    
       $emp_leave="select *
                            FROM
                       (SELECT leave_requests_details.user_id,leave_requests_details.leave_date,leave_requests_details.parent_table_id,leave_requests.leave_type_id,time_categories.title 
                       FROM `leave_requests_details`
                         INNER join leave_requests on leave_requests_details.parent_table_id=leave_requests.id 
                         INNER JOIN time_categories  on leave_requests.leave_type_id=time_categories.id
                        ) as temp";                 
       $check_employee_leave= collect(DB::select( DB::raw($emp_leave)));  
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
			   if(Session::get('role_id')==5 ||  Session::get('role_id')==7){
              $client=Client::where('parent_id',$search)->first();

              if(date('l', strtotime($rep_date))=="Monday"){
                               
               if($client==null){
                     
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){

                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                  u.gender,
                                                                   tcat.tc_monday_in as check_dayoff,
                                                                  u.dob,
                                                                  u.file_number, 
                                                                  tcat.is_night_shift, 
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_monday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and u.department_id='".$search."' order by u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                   
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                            u.gender,
                                                             tcat.tc_monday_in as check_dayoff,
                                                             u.bps,
                                                             u.dob,
                                                             u.file_number, 
                                                             tcat.is_night_shift,
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_monday_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and u.department_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                    
                 }

               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                  u.gender,
                                                                 u.dob,
                                                                 tcat.tc_tuesday_in as check_dayoff,
                                                                 u.file_number,
                                                                 tcat.is_night_shift, 
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_tuesday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and              attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.id='".$search."' order by u.id,att.datetime";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
               }     

                 
               
                
              }  //client == null
              else{
                     

                  
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                   u.gender,
                                                                    tcat.tc_monday_in as check_dayoff,
                                                                  u.dob,
                                                                  u.file_number, 
                                                                  tcat.is_night_shift, 
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_monday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and clients.parent_id='".$search."' order by u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                   if($search=='all' && $rep_date!='' && $request->status=='absent' ){

                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                   u.gender,
                                                                    tcat.tc_monday_in as check_dayoff,
                                                                  u.dob,
                                                                  u.file_number, 
                                                                  tcat.is_night_shift, 
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_monday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and clients.parent_id='".$search."' order by u.id,att.datetime";      
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                  
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                             u.bps,
                                                              u.gender,
                                                               tcat.tc_monday_in as check_dayoff,
                                                             u.dob,
                                                             u.file_number, 
                                                             tcat.is_night_shift,
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_monday_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;
                    
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
                                                                 ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_monday_in),tcat.grace_time) as  time_in,
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
                                   
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                  u.gender,
                                                                 u.dob,
                                                                  tcat.tc_monday_in as check_dayoff,
                                                                 u.file_number, 
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 tcat.is_night_shift,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_monday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and              attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
            
                  } 
               } 
             }
              if(date('l', strtotime($rep_date))=="Tuesday"){

               if($client==null){

                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                   u.gender,
                                                                  u.dob,
                                                                   tcat.tc_tuesday_in as check_dayoff,
                                                                  u.file_number,
                                                                  tcat.is_night_shift,  
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_tuesday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and u.department_id='".$search."' order by u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                             u.bps,
                                                              tcat.tc_tuesday_in as check_dayoff,
                                                              u.gender,
                                                             u.dob,
                                                             u.file_number, 
                                                             tcat.is_night_shift,
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_tuesday_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and u.department_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;
                    
                 } 
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                  
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                  u.gender,
                                                                 u.dob,
                                                                 tcat.tc_tuesday_in as check_dayoff,
                                                                 u.file_number,
                                                                 tcat.is_night_shift, 
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_tuesday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and              attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.id='".$search."' order by u.id,att.datetime";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
               }        
              }  //client == null
              else{

                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                   u.gender,
                                                                  u.dob,
                                                                   tcat.tc_tuesday_in as check_dayoff,
                                                                  tcat.is_night_shift,
                                                                  u.file_number,  
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_tuesday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and clients.parent_id='".$search."' order by u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                             u.bps,
                                                              u.gender,
                                                               tcat.tc_tuesday_in as check_dayoff,
                                                             u.dob,
                                                             tcat.is_night_shift,
                                                             u.file_number, 
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_tuesday_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;
                    
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
                                                                 ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_monday_in),tcat.grace_time) as  time_in,
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
                                     
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                  u.gender,
                                                                 u.dob,
                                                                 u.file_number,
                                                                 tcat.tc_tuesday_in as check_dayoff,
                                                                 tcat.is_night_shift, 
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_tuesday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and              attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
            
          
                 } 
              } 
              
                
              }
              if(date('l', strtotime($rep_date))=="Wednesday"){
               if($client==null){
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                   u.gender,
                                                                  u.dob,
                                                                  tcat.tc_wednesday_in as check_dayoff,
                                                                  u.file_number,
                                                                  tcat.is_night_shift,  
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_wednesday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and u.department_id='".$search."' order by u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                              u.gender,
                                                             u.bps,
                                                             tcat.is_night_shift,
                                                             tcat.tc_wednesday_in as check_dayoff,
                                                             u.dob,
                                                             u.file_number, 
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_wednesday_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and u.department_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;
                    
                 } 
                 
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                         
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                  u.gender,
                                                                 u.dob,
                                                                 tcat.tc_wednesday_in as check_dayoff,
                                                                 u.file_number, 
                                                                 tcat.is_night_shift,
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_thursday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.id='".$search."' order by u.id,att.datetime";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
               }        
              }  //client == null
              else{


                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                   u.gender,
                                                                  u.dob,
                                                                  u.file_number,
                                                                  tcat.tc_wednesday_in as check_dayoff, 
                                                                  tcat.is_night_shift, 
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_wednesday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and clients.parent_id='".$search."' order by u.id,att.datetime";

                        
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
                                                                 ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_monday_in),tcat.grace_time) as  time_in,
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
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                              u.gender,
                                                             u.bps,
                                                             u.dob,
                                                             u.file_number,
                                                             tcat.tc_wednesday_in as check_dayoff, 
                                                             tcat.is_night_shift,
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_wednesday_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;
                    
                 } 
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                         
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                  u.gender,
                                                                 u.dob,
                                                                 u.file_number, 
                                                                 tcat.is_night_shift,
                                                                 clients.name as 
                                                                 deparment_name,
                                                                  tcat.tc_wednesday_in as check_dayoff,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_wednesday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
            
          
                 } 
              } 
              
                 

              }
               if(date('l', strtotime($rep_date))=="Thursday"){
               
               if($client==null){
                 
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                   u.gender,
                                                                  u.dob,
                                                                  tcat.tc_thursday_in as check_dayoff,
                                                                  u.file_number,
                                                                  tcat.is_night_shift,  
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_thursday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and u.department_id='".$search."' order by u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                              u.gender,
                                                             u.bps,
                                                             u.dob,
                                                             tcat.is_night_shift,
                                                             tcat.tc_thursday_in as check_dayoff,
                                                             u.file_number, 
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
                                          and u.department_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;
                    
                 } 
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                       
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                  u.gender,
                                                                 u.dob,
                                                                 u.file_number,
                                                                 tcat.is_night_shift,
                                                                 tcat.tc_thursday_in as check_dayoff, 
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_thursday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.id='".$search."' order by u.id,att.datetime";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
               }        
              }  //client == null
              else{

                   
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                   u.gender,
                                                                  u.dob,
                                                                  tcat.is_night_shift,
                                                                  tcat.tc_thursday_in as check_dayoff,
                                                                  u.file_number,  
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_thursday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and clients.parent_id='".$search."' order by u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                             u.bps,
                                                              u.gender,
                                                             u.dob,
                                                             tcat.is_night_shift,
                                                             tcat.tc_thursday_in as check_dayoff,
                                                             u.file_number, 
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
                                          and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;
                    
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
                                                                 ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_monday_in),tcat.grace_time) as  time_in,
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
                                       
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                  u.gender,
                                                                 u.dob,
                                                                 tcat.is_night_shift,
                                                                 u.file_number, 
                                                                 tcat.tc_thursday_in as check_dayoff,
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_thursday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
            
          
                 } 
              } 
            }
              if(date('l', strtotime($rep_date))=="Friday"){
                 
            
               $client=Client::where('parent_id',$search)->first();
               if($client==null){

                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                  u.dob,
                                                                   u.gender,
                                                                  tcat.is_night_shift,
                                                                  u.file_number,  
                                                                  tcat.tc_friday_in as check_dayoff,
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_friday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  date(time_in) as date,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and u.department_id='".$search."' order by u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));

                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                      
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                              u.gender,
                                                             u.bps,
                                                             tcat.is_night_shift,
                                                             u.dob,
                                                             u.file_number, 
                                                             tcat.tc_friday_in as check_dayoff,
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_friday_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and u.department_id='".$search."' order by u.id,att.datetime";
                                      
              $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
             
                    
                 } 

              
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                         
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                 u.dob,
                                                                  u.gender,
                                                                 u.file_number, 
                                                                 tcat.is_night_shift,
                                                                 tcat.tc_friday_in as check_dayoff,
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_friday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.id='".$search."' order by u.id,att.datetime";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
               }        
              }  //client == null
              else{
               
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                   u.gender,
                                                                  tcat.is_night_shift,
                                                                  tcat.tc_friday_in as check_dayoff,
                                                                  u.dob,
                                                                  u.file_number,  
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_friday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and clients.parent_id='".$search."' order by u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                             u.bps,
                                                              u.gender,
                                                             u.dob,
                                                             tcat.is_night_shift,
                                                             tcat.tc_friday_in as check_dayoff,
                                                             u.file_number, 
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_friday_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;

                    
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
                                                                 ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_monday_in),tcat.grace_time) as  time_in,
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
                                         
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                 u.dob,
                                                                  u.gender,
                                                                 tcat.is_night_shift,
                                                                 tcat.tc_friday_in as check_dayoff,
                                                                 u.file_number, 
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_friday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and              attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
            
                  } 
               } 
            }
              if(date('l', strtotime($rep_date))=="Saturday"){
               
               if($client==null){
                        //$client ==null check  parent id of department  in normal case 

                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                   u.gender,
                                                                  u.dob,
                                                                  tcat.is_night_shift,
                                                                  u.file_number,
                                                                  tcat.tc_satureday_in as check_dayoff,  
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_satureday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and u.department_id='".$search."' order by u.id,att.datetime";

                      
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));

                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                              u.gender,
                                                             u.bps,
                                                             u.dob,
                                                             tcat.is_night_shift,
                                                             u.file_number, 
                                                             tcat.tc_satureday_in as check_dayoff,
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_satureday_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and u.department_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;
                    
                 } 
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                         
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                  u.gender,
                                                                 u.dob,
                                                                 tcat.is_night_shift,
                                                                 u.file_number, 
                                                                 tcat.tc_satureday_in as check_dayoff,
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_satureday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and              attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.id='".$search."' order by u.id,att.datetime";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));

               }        
              }  //client == null
              else{
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                   u.gender,
                                                                  u.bps,
                                                                  u.dob,
                                                                  tcat.is_night_shift,
                                                                  tcat.tc_satureday_in as check_dayoff,
                                                                  u.file_number,  
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_satureday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=leav_req.leave_type_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and clients.parent_id='".$search."' order by u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));

                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                              u.gender,
                                                             u.bps,
                                                             tcat.is_night_shift,
                                                             tcat.tc_satureday_in as check_dayoff,
                                                             u.dob,
                                                             u.file_number, 
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_satureday_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;
                    
                 } 
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                         
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                  u.gender,
                                                                 u.dob,
                                                                 tcat.is_night_shift,
                                                                 u.file_number, 
                                                                 tcat.tc_satureday_in as check_dayoff,
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_satureday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and              attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
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
                                                                 ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_monday_in),tcat.grace_time) as  time_in,
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
               } 
            }
              if(date('l', strtotime($rep_date))=="Sunday"){
              
               if($client==null){
                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                   u.gender,
                                                                  u.bps,
                                                                  tcat.tc_sunday_in as check_dayoff,
                                                                  tcat.is_night_shift,
                                                                  u.dob,
                                                                  u.file_number,  
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_sunday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=u.tcat_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and u.department_id='".$search."' order by u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                              u.gender,
                                                             u.bps,
                                                             tcat.tc_sunday_in as check_dayoff,
                                                             tcat.is_night_shift,
                                                             u.dob,
                                                             u.file_number, 
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_sunday_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and u.department_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;
                    
                 } 
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                         
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                  u.gender,
                                                                 u.dob,
                                                                 tcat.is_night_shift,
                                                                 tcat.tc_sunday_in as check_dayoff,
                                                                 u.file_number, 
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_sunday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and              attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.id='".$search."' order by u.id,att.datetime";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
               }        
              }  //client == null
              else{

                      if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                   u.gender,
                                                                  u.bps,
                                                                  u.dob,
                                                                  tcat.is_night_shift,
                                                                  tcat.tc_sunday_in as check_dayoff,
                                                                  u.file_number,  
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_sunday_in),tcat.grace_time) as 
                                                                  time_in,
                                                                  att.datetime as attendance_time,
                                                                  leav_req.leave_date,
                                                                  tcat.type as leave_type,
                                                                  tcat.title as leave_of_title,
                                                                  attach_emp.attach_date,
                                                                  leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to     
                                        from users u 
                                        LEFT JOIN attendance att on u.id = att.user_id
                                                  and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN attach_employees_details attach_emp
                                              on attach_emp.user_id=u.id
                                                and attach_emp.attach_date='".$rep_date."'
                                        left JOIN leave_requests_details as leav_req 
                                              on leav_req.user_id = u.id 
                                                and leav_req.leave_date ='".$rep_date."'
                                        LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                        left JOIN time_categories as tcat on tcat.id=leav_req.leave_type_id
                                           LEFT JOIN clients on clients.id = u.department_id 
                                        WHERE flag=1 and u.status=1  and att.datetime is null  
                                               and clients.parent_id='".$search."' order by u.id,att.datetime";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                             u.bps,
                                                              u.gender,
                                                             tcat.is_night_shift,
                                                             u.dob,
                                                             u.file_number, 
                                                             tcat.tc_sunday_in as check_dayoff,
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_sunday_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;
                    
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
                                                                 ADDTIME(CONCAT('".$rep_date."',' ',tcat.  tc_monday_in),tcat.grace_time) as  time_in,
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
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                  u.gender,
                                                                 u.dob,
                                                                 tcat.is_night_shift,
                                                                 tcat.tc_sunday_in as check_dayoff,
                                                                 u.file_number, 
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.  tc_sunday_in),tcat.grace_time) as 
                                                                 time_in,
                                                                 att.datetime as attendance_time,
                                                                 att.mark_attendance,
                                                                 leav_req.leave_date,
                                                                 tcat.title as leave_of_title, 
                                                                 attach_emp.attach_date,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to 
                                              from users u 
                                              LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                              left JOIN leave_requests_details as leav_req
                                               on leav_req.user_id = u.id 
                                               and leav_req.leave_date ='".$rep_date."'
                                             LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id 
                                             left join time_categories tcat on tcat.id=u.tcat_id
                                              LEFT JOIN designations on designations.id=u.designation_id
                                              LEFT JOIN clients on clients.id = u.department_id
                                              LEFT JOIN attach_employees_details attach_emp on attach_emp.user_id=u.id and              attach_emp.attach_date='".$rep_date."'
                                              WHERE flag=1 and u.status=1 and clients.parent_id='".$search."' order by u.id,att.datetime";
                                      
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));  
                  } 
               } 
              
            }              
		    }
		}     
        return view('Reports.attandance_report',compact('attandance_reports','department','rep_date','emp_total_leave','check_employee_leave','emp_by_dep'));     
   } //end report method
} 

