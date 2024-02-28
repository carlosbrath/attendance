<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Client;
use DB;

class DashboardDetailController extends Controller
{
   

	public function absent_employee(){
     $date= date('Y-m-d');
     
     $dep_id=Session::get('dep_id');
     $department=Client::where('id',$dep_id)->get();

         $client_name=Client::where('parent_id',Session::get('dep_id'))->first();
         if($client_name==null){
              $users =""; 
         }
         else{
              
              $client_check_sub_child=Client::where('parent_id',$client_name->id)->first();
              if($client_check_sub_child==null){

                      $report_query_for_departmnent_adm ="select 
                                                u.id,
                                                u.name,
                                                u.bps,u.file_number,
                                                clients.name as deparment_name,
                                                designations.title,tcat.title as timecat_title,
                                                ADDTIME(CONCAT('".$date."',' ',tcat.time_in),tcat.grace_time) as time_in,
                                                att.datetime as attendance_time,
                                                leav_req.leave_date,
                                                attach_emp.attach_date,
                                                tcat.type
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
                                          WHERE flag=1 and  u.status=1 and u.field_staff=0 and clients.parent_id='".Session::get('dep_id')."' 
                                          AND u.id  NOT IN (
                                          SELECT user_id FROM attach_employees_details d WHERE DATE(d.`attach_date`) = DATE(NOW()) AND d.`user_id` = u.id
                                          )
                                          AND u.id  NOT IN (
                                          SELECT user_id FROM leave_requests_details l WHERE DATE(l.`leave_date`) = DATE(NOW()) AND l.`user_id` = u.id
                                          )
                                          order  by u.id";  
              }
                else{

                      $fetch_user="SELECT  id FROM clients WHERE 
                      parent_id IN ( SELECT id FROM clients  WHERE parent_id='".Session::get('dep_id')."')";
                      $fetch_user_quer = collect(DB::select( DB::raw($fetch_user))); 
                      $ids=[];
                      foreach($fetch_user_quer as $d){
                      $ids[]=  $d->id ;    
                      }
                      $ids = join("','",$ids);
                     $report_query_for_departmnent_adm ="select 
                                                u.id,
                                                u.name,
                                                u.bps,u.file_number,
                                                clients.name as deparment_name,
                                                designations.title,tcat.title as timecat_title,
                                                ADDTIME(CONCAT('".$date."',' ',tcat.time_in),tcat.grace_time) as time_in,
                                                att.datetime as attendance_time,
                                                leav_req.leave_date,
                                                attach_emp.attach_date,
                                                tcat.type
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
                                          WHERE flag=1 and  u.status=1  and u.field_staff=0 and u.department_id in ('".$ids."') order  by u.id";                                      
             

                 }
                $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
               return view('dashboard_details.daily_absent',compact('attandance_reports'));
        }    
   }


  public function present_employee(){
    //dd('right');
    $date= date('Y-m-d');
    $day= date("l");
    $dep_id=Session::get('dep_id');
    $client_name=Client::where('parent_id',$dep_id)->first();
    if($client_name==null){
      $users =""; 
    }
    else{   
      $client_check_sub_child=Client::where('parent_id',$client_name->id)->first();
      
      if($client_check_sub_child==null){
        //dd('no sub deps');
        if($day=="Monday"){
          $daily_present =  "select 
          u.id,
          u.name,
          u.bps,u.file_number,
          clients.name as deparment_name,
          designations.title,tcat.title as timecat_title,
          ADDTIME(CONCAT('".$date."',' ',tcat.tc_monday_in),tcat.grace_time) as time_in,
          att.datetime as attendance_time,
          tcat.type
          from users u 
          LEFT JOIN attendance att on u.id = att.user_id 
          and date(att.datetime)='".$date."'
          LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
          LEFT JOIN designations on designations.id=u.designation_id     
          LEFT JOIN clients on clients.id = u.department_id 
          WHERE  flag=1 and u.status=1 and u.field_staff=0   and u.field_staff=0 and att.datetime is not Null and clients.parent_id='".$dep_id."'
          order  by u.id";

        }else if($day=="Tuesday"){
          $daily_present =  "select 
          u.id,
          u.name,
          u.bps,u.file_number,
          clients.name as deparment_name,
          designations.title,tcat.title as timecat_title,
          ADDTIME(CONCAT('".$date."',' ',tcat.tc_tuesday_in),tcat.grace_time) as time_in,
          att.datetime as attendance_time,
          tcat.type
          from users u 
          LEFT JOIN attendance att on u.id = att.user_id 
          and date(att.datetime)='".$date."'
          LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
          LEFT JOIN designations on designations.id=u.designation_id     
          LEFT JOIN clients on clients.id = u.department_id 
          WHERE flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
          order  by u.id";

        }else if($day=="Wednesday"){
          $daily_present =  "select 
          u.id,
          u.name,
          u.bps,u.file_number,
          clients.name as deparment_name,
          designations.title,tcat.title as timecat_title,
          ADDTIME(CONCAT('".$date."',' ',tcat.tc_thursday_in),tcat.grace_time) as time_in,
          att.datetime as attendance_time,
          tcat.type
          from users u 
          LEFT JOIN attendance att on u.id = att.user_id 
          and date(att.datetime)='".$date."'
          LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
          LEFT JOIN designations on designations.id=u.designation_id     
          LEFT JOIN clients on clients.id = u.department_id 
          WHERE flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
          order  by u.id";

        }else if($day=="Thursday"){
          $daily_present =  "select 
          u.id,
          u.name,
          u.bps,u.file_number,
          clients.name as deparment_name,
          designations.title,tcat.title as timecat_title,
          ADDTIME(CONCAT('".$date."',' ',tcat.tc_thursday_in),tcat.grace_time) as time_in,
          att.datetime as attendance_time,
          tcat.type
          from users u 
          LEFT JOIN attendance att on u.id = att.user_id 
          and date(att.datetime)='".$date."'
          LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
          LEFT JOIN designations on designations.id=u.designation_id     
          LEFT JOIN clients on clients.id = u.department_id 
          WHERE flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
          order  by u.id";

        }else if($day=="Friday"){
          $daily_present =  "select 
          u.id,
          u.name,
          u.bps,u.file_number,
          clients.name as deparment_name,
          designations.title,tcat.title as timecat_title,
          ADDTIME(CONCAT('".$date."',' ',tcat.tc_friday_in),tcat.grace_time) as time_in,
          att.datetime as attendance_time,
          tcat.type
          from users u 
          LEFT JOIN attendance att on u.id = att.user_id 
          and date(att.datetime)='".$date."'
          LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
          LEFT JOIN designations on designations.id=u.designation_id     
          LEFT JOIN clients on clients.id = u.department_id 
          WHERE flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
          order  by u.id";

        }else if($day=="Saturday"){
          $daily_present =  "select 
          u.id,
          u.name,
          u.bps,u.file_number,
          clients.name as deparment_name,
          designations.title,tcat.title as timecat_title,
          ADDTIME(CONCAT('".$date."',' ',tcat.tc_saturday_in),tcat.grace_time) as time_in,
          att.datetime as attendance_time,
          tcat.type
          from users u 
          LEFT JOIN attendance att on u.id = att.user_id 
          and date(att.datetime)='".$date."'
          LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
          LEFT JOIN designations on designations.id=u.designation_id     
          LEFT JOIN clients on clients.id = u.department_id 
          WHERE flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
          order  by u.id";

        }else if($day=="Sunday"){
          $daily_present =  "select 
          u.id,
          u.name,
          u.bps,u.file_number,
          clients.name as deparment_name,
          designations.title,tcat.title as timecat_title,
          ADDTIME(CONCAT('".$date."',' ',tcat.tc_sunday_in),tcat.grace_time) as time_in,
          att.datetime as attendance_time,
          tcat.type
          from users u 
          LEFT JOIN attendance att on u.id = att.user_id 
          and date(att.datetime)='".$date."'
          LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
          LEFT JOIN designations on designations.id=u.designation_id     
          LEFT JOIN clients on clients.id = u.department_id 
          WHERE flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
          order  by u.id";

        }

      $attandance_reports = collect(DB::select( DB::raw($daily_present)));

    }
    else{

      $fetch_user="SELECT  id FROM clients WHERE 
      parent_id IN ( SELECT id FROM clients  WHERE parent_id='".$dep_id."')";
      $fetch_user_quer = collect(DB::select( DB::raw($fetch_user))); 
      $ids=[];
      foreach($fetch_user_quer as $d){
        $ids[]=  $d->id ;    
      }
      $ids = join("','",$ids);

      $daily_present =  "select 
        u.id,
        u.name,
        u.bps,u.file_number,
        clients.name as deparment_name,
        designations.title,tcat.title as timecat_title,
        ADDTIME(CONCAT('".$date."',' ',tcat.time_in),tcat.grace_time) as time_in,
        att.datetime as attendance_time,
        tcat.type
        from users u 
        LEFT JOIN attendance att on u.id = att.user_id 
        and date(att.datetime)='".$date."'
        LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
        LEFT JOIN designations on designations.id=u.designation_id     
        LEFT JOIN clients on clients.id = u.department_id 
        WHERE flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and u.department_id in ('".$ids."')
        order  by u.id";
      $attandance_reports = collect(DB::select( DB::raw($daily_present))); 

      }
    }

    return view('dashboard_details.daily_present',compact('attandance_reports'));
  }
  public function late_employee(){

     $date= date('Y-m-d');
    $day= date("l");
     $dep_id=Session::get('dep_id');
    $department=Client::where('parent_id',$dep_id)->get();
      $client_name=Client::where('parent_id',Session::get('dep_id'))->first();
         if($client_name==null){
              $users =""; 
         }
         else{
              
              $client_check_sub_child=Client::where('parent_id',$client_name->id)->first();
              if($client_check_sub_child==null){

                   if($day=="Monday"){
                      $daily_present =  "select 
                                          u.id,
                                          u.name,
                                          u.bps,u.file_number,
                                          clients.name as deparment_name,
                                          designations.title,tcat.title as timecat_title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.tc_monday_in),tcat.grace_time) as time_in,
                                          att.datetime as attendance_time,
                                          tcat.type
                                    from users u 
                                    LEFT JOIN attendance att on u.id = att.user_id 
                                              and date(att.datetime)='".$date."'
                                    LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
                                    LEFT JOIN designations on designations.id=u.designation_id     
                                    LEFT JOIN clients on clients.id = u.department_id 
                                    WHERE  flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
                                     order  by u.id";

                   }
                   if($day=="Tuesday"){
                      $daily_present =  "select 
                                          u.id,
                                          u.name,
                                          u.bps,u.file_number,
                                          clients.name as deparment_name,
                                          designations.title,tcat.title as timecat_title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.tc_tuesday_in),tcat.grace_time) as time_in,
                                          att.datetime as attendance_time,
                                          tcat.type
                                    from users u 
                                    LEFT JOIN attendance att on u.id = att.user_id 
                                              and date(att.datetime)='".$date."'
                                    LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
                                    LEFT JOIN designations on designations.id=u.designation_id     
                                    LEFT JOIN clients on clients.id = u.department_id 
                                    WHERE  flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
                                     order  by u.id";
                    
                   }
                   if($day=="Wednesday"){
                      $daily_present =  "select 
                                          u.id,
                                          u.name,
                                          u.bps,u.file_number,
                                          clients.name as deparment_name,
                                          designations.title,tcat.title as timecat_title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.tc_wednesday_in),tcat.grace_time) as time_in,
                                          att.datetime as attendance_time,
                                          tcat.type
                                    from users u 
                                    LEFT JOIN attendance att on u.id = att.user_id 
                                              and date(att.datetime)='".$date."'
                                    LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
                                    LEFT JOIN designations on designations.id=u.designation_id     
                                    LEFT JOIN clients on clients.id = u.department_id 
                                    WHERE  flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
                                     order  by u.id";
                    
                   }
                   if($day=="Thursday"){
                      $daily_present =  "select 
                                          u.id,
                                          u.name,
                                          u.bps,u.file_number,
                                          clients.name as deparment_name,
                                          designations.title,tcat.title as timecat_title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.tc_thursday_in),tcat.grace_time) as time_in,
                                          att.datetime as attendance_time,
                                          tcat.type
                                    from users u 
                                    LEFT JOIN attendance att on u.id = att.user_id 
                                              and date(att.datetime)='".$date."'
                                    LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
                                    LEFT JOIN designations on designations.id=u.designation_id     
                                    LEFT JOIN clients on clients.id = u.department_id 
                                    WHERE  flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
                                     order  by u.id";
                    
                   }
                   if($day=="Friday"){
                      $daily_present =  "select 
                                          u.id,
                                          u.name,
                                          u.bps,u.file_number,
                                          clients.name as deparment_name,
                                          designations.title,tcat.title as timecat_title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.tc_friday_in),tcat.grace_time) as time_in,
                                          att.datetime as attendance_time,
                                          tcat.type
                                    from users u 
                                    LEFT JOIN attendance att on u.id = att.user_id 
                                              and date(att.datetime)='".$date."'
                                    LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
                                    LEFT JOIN designations on designations.id=u.designation_id     
                                    LEFT JOIN clients on clients.id = u.department_id 
                                    WHERE  flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
                                     order  by u.id";
                    
                   } 
                    if($day=="Saturday"){
                        $daily_present =  "select 
                                          u.id,
                                          u.name,
                                          u.bps,u.file_number,
                                          clients.name as deparment_name,
                                          designations.title,tcat.title as timecat_title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.tc_saturday_in),tcat.grace_time) as time_in,
                                          att.datetime as attendance_time,
                                          tcat.type
                                    from users u 
                                    LEFT JOIN attendance att on u.id = att.user_id 
                                              and date(att.datetime)='".$date."'
                                    LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
                                    LEFT JOIN designations on designations.id=u.designation_id     
                                    LEFT JOIN clients on clients.id = u.department_id 
                                    WHERE  flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
                                     order  by u.id";
                    
                   } 
                    if($day=="Sunday"){
                        $daily_present =  "select 
                                          u.id,
                                          u.name,
                                          u.bps,u.file_number,
                                          clients.name as deparment_name,
                                          designations.title,tcat.title as timecat_title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.tc_sunday_in),tcat.grace_time) as time_in,
                                          att.datetime as attendance_time,
                                          tcat.type
                                    from users u 
                                    LEFT JOIN attendance att on u.id = att.user_id 
                                              and date(att.datetime)='".$date."'
                                    LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
                                    LEFT JOIN designations on designations.id=u.designation_id     
                                    LEFT JOIN clients on clients.id = u.department_id 
                                    WHERE  flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and clients.parent_id='".$dep_id."'
                                     order  by u.id";
                    
                   }   
                   
                  $attandance_reports = collect(DB::select( DB::raw($daily_present)));
              
              }
              else{
                  
                 $fetch_user="SELECT  id FROM clients WHERE 
                              parent_id IN ( SELECT id FROM clients  WHERE parent_id='".Session::get('dep_id')."')";
                     $fetch_user_quer = collect(DB::select( DB::raw($fetch_user))); 
                     $ids=[];
                    foreach($fetch_user_quer as $d){
                      $ids[]=  $d->id ;    
                     }
                     $ids = join("','",$ids);

                     $daily_present =  "select 
                                          u.id,
                                          u.name,
                                          u.bps,u.file_number,
                                          clients.name as deparment_name,
                                          designations.title,tcat.title as timecat_title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.time_in),tcat.grace_time) as time_in,
                                          att.datetime as attendance_time,
                                          tcat.type
                                    from users u 
                                    LEFT JOIN attendance att on u.id = att.user_id 
                                              and date(att.datetime)='".$date."'
                                    LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
                                    LEFT JOIN designations on designations.id=u.designation_id     
                                    LEFT JOIN clients on clients.id = u.department_id 
                                    WHERE  flag=1 and u.status=1 and u.field_staff=0  and att.datetime is not Null and u.department_id in ('".$ids."') 
                                     order  by u.id";

                
                  $attandance_reports = collect(DB::select( DB::raw($daily_present)));
     
                 }
               }

      return view('dashboard_details.daily_late',compact('attandance_reports'));
   }
  public function employee_leave(){
     $date= date('Y-m-d');
     $dep_id=Session::get('dep_id');        
     $client_name=Client::where('parent_id',Session::get('dep_id'))->first();
         if($client_name==null){
              $users =""; 
         }
         else{               
                    $daily_leave = "select 
                                            u.id,
                                             u.name,
                                              u.bps,u.file_number,
                                               clients.name as deparment_name,
                                                designations.title,tcat.title as timecat_title,  
                                                 leav_req.leave_date,
                                                   date(att.datetime) as attendance_time,
                                                    tcat.type,
                                                      leave_requests.leave_type_id,
                                                       leave_requests.leave_type,
                                                         leave_requests.time_from,
                                                          leave_requests.time_to
                      from users u 
                       LEFT JOIN attendance att on u.id = att.user_id 
                                and date(att.datetime)='".$date."'
                      LEFT JOIN time_categories tcat on u.tcat_id=tcat.id          
                      LEFT JOIN designations on designations.id=u.designation_id
                      left JOIN leave_requests_details as leav_req 
                         on leav_req.user_id = u.id 
                      LEFT join leave_requests ON leave_requests.id=leav_req.parent_table_id
                      LEFT JOIN clients on clients.id = u.department_id 
                      WHERE  flag=1 and u.status=1 and u.field_staff=0  and clients.parent_id='".Session::get('dep_id')."'  and leav_req.leave_date='".$date."'  and date(att.datetime) is null
                order  by u.id";
          
         }
     
       
      $attandance_reports = collect(DB::select( DB::raw($daily_leave)));                       
      return view('dashboard_details.daily_leave',compact('attandance_reports'));

                    
  }
  public function employee_attach(){
    
         $date= date('Y-m-d');
         $dep_id=Session::get('dep_id');
         $client_name=Client::where('parent_id',Session::get('dep_id'))->first();
         if($client_name==null){
              $users =""; 
         }
         else{
              
              $client_check_sub_child=Client::where('parent_id',$client_name->id)->first();
              if($client_check_sub_child==null){


                  $daily_attach = "SELECT users.id,users.name,clients.name as department_name,attach_employees_details.attach_date FROM attach_employees_details 
                           INNER JOIN users on users.id= attach_employees_details.user_id
                           INNER JOIN clients on clients.id= users.department_id
                           and attach_employees_details.attach_date='".$date."' 
                           AND clients.parent_id='".$dep_id."' ";
                  $attandance_reports = collect(DB::select( DB::raw($daily_attach)));    
              
              }
              else{


                   $fetch_user="SELECT  id FROM clients WHERE 
                      parent_id IN ( SELECT id FROM clients  WHERE parent_id='".Session::get('dep_id')."')";
                   $fetch_user_quer = collect(DB::select( DB::raw($fetch_user))); 
                   $ids=[];
                  foreach($fetch_user_quer as $d){
                    $ids[]=  $d->id ;    
                   }
                   $ids = join("','",$ids); 

                   $daily_attach = "SELECT users.id,users.name,clients.name as department_name,attach_employees_details.attach_date FROM attach_employees_details 
                           INNER JOIN users on users.id= attach_employees_details.user_id
                           INNER JOIN clients on clients.id= users.department_id
                           and attach_employees_details.attach_date='".$date."' 
                           AND  users.department_id in ('".$ids."')  ";
                  $attandance_reports = collect(DB::select( DB::raw($daily_attach)));
         
              }
            }
             
      return view('dashboard_details.daily_attach',compact('attandance_reports'));   
    }


  public function employee_computer_operator(){
       $job_title= "SELECT users.id,users.name,users.dob,designations.title, clients.name as 'department_name' FROM users 
                     INNER JOIN  clients on clients.id =users.department_id
                     INNER JOIN designations on designations.id =users.designation_id
                    where clients.parent_id='".Session('dep_id')."' and designations.title='computer operator' and users.status=1 ";
       $comp_oper = collect(DB::select( DB::raw($job_title)));  
        return view('dashboard_details.comp_opera',compact('comp_oper'));           
            }
      public function employee_director(){
         $job_title= "SELECT users.id,users.name,users.dob,designations.title, clients.name as 'department_name' FROM users 
                       INNER JOIN  clients on clients.id =users.department_id
                       INNER JOIN designations on designations.id =users.designation_id
                      where clients.parent_id='".Session('dep_id')."' and designations.title='director' and users.status=1 ";
         $director = collect(DB::select( DB::raw($job_title)));  
         return view('dashboard_details.director',compact('director'));

        }
         public function employee_dep_director(){
         $job_title= "SELECT users.id,users.name,users.dob,designations.title, clients.name as 'department_name' FROM users 
                       INNER JOIN  clients on clients.id =users.department_id
                       INNER JOIN designations on designations.id =users.designation_id
                      where clients.parent_id='".Session('dep_id')."' and designations.title='dupty director' and users.status=1 ";
         $deputy_director = collect(DB::select( DB::raw($job_title)));  
          return view('dashboard_details.deput_director',compact('deputy_director'));
        }

  public function short_leave(){
          $date= date('Y-m-d');
          $dep_id=Session::get('dep_id');
         $client_name=Client::where('parent_id',Session::get('dep_id'))->first();
         if($client_name==null){
              $users =""; 
          }
         else{
              
              $client_check_sub_child=Client::where('parent_id',$client_name->id)->first();
              if($client_check_sub_child==null){
        
                   $report_query_for_departmnent_adm ="select 
                                                u.id,
                                                u.name,
                                                u.bps,u.file_number,
                                                clients.name as deparment_name,
                                                designations.title,tcat.title as timecat_title,
                                                ADDTIME(CONCAT('".$date."',' ',tcat.time_in),tcat.grace_time) as time_in,
                                                att.datetime as attendance_time,
                                                leav_req.leave_date,
                                                attach_emp.attach_date,
                                                tcat.type,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to
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
                                          WHERE  flag=1 and u.status=1 and u.field_staff=0  and clients.parent_id='".Session::get('dep_id')."'
                                             order  by u.id";                                      
            
                         $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));    
              
              }
              else{

                   $fetch_user="SELECT  id FROM clients WHERE 
                      parent_id IN ( SELECT id FROM clients  WHERE parent_id='".Session::get('dep_id')."')";
                   $fetch_user_quer = collect(DB::select( DB::raw($fetch_user))); 
                   $ids=[];
                  foreach($fetch_user_quer as $d){
                    $ids[]=  $d->id ;    
                   }
                   $ids = join("','",$ids); 

             
                   $report_query_for_departmnent_adm ="select 
                                                u.id,
                                                u.name,
                                                
                                                u.bps,u.file_number,
                                                clients.name as deparment_name,
                                                designations.title,tcat.title as timecat_title,
                                                ADDTIME(CONCAT('".$date."',' ',tcat.time_in),tcat.grace_time) as time_in,
                                                att.datetime as attendance_time,
                                                leav_req.leave_date,
                                                attach_emp.attach_date,
                                                tcat.type,leave_requests.leave_type_id,leave_requests.leave_type,leave_requests.time_from,leave_requests.time_to
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
                                          WHERE  flag=1 and u.status=1 and u.field_staff=0  and u.department_id in ('".$ids."') 
                                             order  by u.id";                                      
            
                         $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm))); 
                    
                    }
                }  
         
         
            return view('dashboard_details.daily_short_leave',compact('attandance_reports'));
  }
  public function field_staff(){
     $dep_id=Session::get('dep_id');        
     $client_name=Client::where('parent_id',Session::get('dep_id'))->first();
         if($client_name==null){
              $users =""; 
         }
         else{               
                  $daily_leave = "select 
                              u.id,
                              u.name,
                              u.bps,u.file_number,u.remarks,
                              clients.name as deparment_name,
                              designations.title
                      from users u 
                        LEFT JOIN designations on designations.id=u.designation_id
                        LEFT JOIN clients on clients.id = u.department_id 
                      WHERE  flag=1 and u.status=1 and u.field_staff=1  and clients.parent_id='".Session::get('dep_id')."' 
                      order  by u.id";
          
         }
     
       
      $attandance_reports = collect(DB::select( DB::raw($daily_leave)));                       
      return view('dashboard_details.fieldstaff',compact('attandance_reports'));

  }

}