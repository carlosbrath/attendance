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
    $rep_date="";
    $date= date('Y-m-d');
    $dep_id=Session::get('dep_id');
    if(Session::get('role_id')==1)
       {

        $department=Client::where('parent_id','!=',0)->get();

       }
     else
      {
       $department=Client::where('parent_id',Session::get('dep_id'))->get();  
       if($department->isEmpty()){
           $department=Client::where('id',Session::get('dep_id'))->get(); 
           $report_query_for_departmnent_adm ="select 
                                                u.id,
                                                u.name,
                                                u.dob,
                                                u.department_id,
                                                tcat.time_in,
                                               u.status,
                                                u.bps,u.file_number,
                                                clients.name as deparment_name,
                                                clients.parent_id,
                                                designations.title,tcat.title as timecat_title,
                                                ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15') as time_in,
                                                att.datetime as attendance_time,att.mark_attendance,
                                                leav_req.leave_date,
                                                attach_emp.attach_date,
                                                tcat.type,tcat.leave_type as leave_status,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                                    and date(att.datetime)='".$date."'
                                          LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
                                          LEFT JOIN designations on designations.id=u.designation_id
                                          LEFT JOIN attach_employees_details attach_emp 
                                               on attach_emp.user_id=u.id 
                                               and attach_emp.attach_date='".$date."'
                                          left JOIN leave_requests_details as leav_req 
                                             on leav_req.user_id = u.id 
                                             and leav_req.leave_date='".$date."'
                                          LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id
                                          LEFT JOIN clients on clients.id = u.department_id 
                                          WHERE flag=1 and u.status=1 and u.department_id='".Session::get('dep_id')."'
                                          
                                            
       order  by u.id";  

       }
     else{

            $report_query_for_departmnent_adm ="select 
                                                u.id,
                                                u.name,
                                                u.dob,
                                                u.department_id,
                                                tcat.time_in,
                                               u.status,
                                                u.bps,u.file_number,
                                                clients.name as deparment_name,
                                                clients.parent_id,
                                                designations.title,tcat.title as timecat_title,
                                                ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15') as time_in,
                                                att.datetime as attendance_time,att.mark_attendance,
                                                leav_req.leave_date,
                                                attach_emp.attach_date,
                                                tcat.type,tcat.leave_type as leave_status,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                                    and date(att.datetime)='".$date."'
                                          LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
                                          LEFT JOIN designations on designations.id=u.designation_id
                                          LEFT JOIN attach_employees_details attach_emp 
                                               on attach_emp.user_id=u.id 
                                               and attach_emp.attach_date='".$date."'
                                          left JOIN leave_requests_details as leav_req 
                                             on leav_req.user_id = u.id 
                                             and leav_req.leave_date='".$date."'
                                          LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id
                                          LEFT JOIN clients on clients.id = u.department_id 
                                          WHERE flag=1 and u.status=1 and clients.parent_id='".Session::get('dep_id')."'
                                          
                             order  by u.id";  

        }
     
      }
                                          
       $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
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
            dd($search);
           
			      $rep_date=$request->date;
			   if(Session::get('role_id')==5 ||  Session::get('role_id')==7){
                  if($search!='' && $rep_date!='' && $request->status=='absent' ){
                       $report_query_for_departmnent_adm  =  "select 
                                                                  u.id,
                                                                  u.name,
                                                                  u.bps,
                                                                  u.dob,
                                                                  u.file_number,  
                                                                  clients.name as deparment_name,
                                                                  designations.title,
                                                                  ADDTIME(CONCAT('".$rep_date."',' ',tcat.time_in),tcat.grace_time) as 
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
                                               and u.department_id='".$search."' order by u.id";

                        
                   $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 
    
                  }
                 if($search!=''  && $rep_date!='' && $request->status=='present' ){  
                       $report_query_for_departmnent_adm  =  "select  
                                                             u.id,
                                                             u.name,
                                                             u.bps,
                                                             u.dob,
                                                             u.file_number, 
                                                             clients.name as deparment_name,
                                                             designations.title,
                                                             ADDTIME(CONCAT('".$rep_date."',' ',tcat.time_in),tcat.grace_time) as  time_in,
                                                             att.datetime as attendance_time,att.mark_attendance  
                                          from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id 
                                          and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id 
                                          LEFT JOIN time_categories tcat 
                                             on tcat.id=u.tcat_id 
                                          LEFT JOIN clients on clients.id = u.department_id
                                          WHERE flag=1 and u.status=1  and att.datetime is not null  
                                          and u.department_id='".$search."' order by u.id";
                                      
                      $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)))
                      ;
                    
                 } 
               
               if($search!='' && $rep_date!='' && $request->status=='all' ){
                                         
                  $report_query_for_departmnent_adm  =  "select  u.id,
                                                                 u.name,
                                                                 u.bps,
                                                                 u.dob,
                                                                 u.file_number, 
                                                                 clients.name as 
                                                                 deparment_name,
                                                                 designations.title,
                                                                 ADDTIME(CONCAT('".$rep_date."' ,' ',tcat.time_in),'00:15') as 
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
                                              WHERE flag=1 and u.status=1 and clients.parent_id='".$search."' order by u.id";
                                      
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
          
               }


		    }
		}
      
        return view('Reports.attandance_report',compact('attandance_reports','department','rep_date','emp_total_leave','check_employee_leave'));     
   } //end report method
} 

