<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Designation;
use App\Client;
use App\Module;
use App\ModuleRight;
use App\leaveRequest;
use App\Leave;
use App\AttachEmployee;
use Hash;
use Mail;
use DB;
use DateTime;
use DatePeriod;
use DateInterval;
use Session;
class DashboardController extends Controller
{
  public function dashboard(){
    $date= date('Y-m-d');
    $day= date("l");
    $dep_id=Session::get('dep_id');
    $user=DB::table('users')
          ->join('clients','users.department_id','clients.id')
          ->where('parent_id',$dep_id)->where('users.status',1)->count();
   // User::where('department_id',$dep_id)->count();
   // User::count();
    $comp_oper=DB::table('users')
                ->join('designations','users.designation_id','designations.id')
                ->join('clients','users.department_id','clients.id')
                ->where('users.status',1)
                ->where('title','computer operator')
                ->where('clients.parent_id',$dep_id)
                ->count(); 
     $director=DB::table('users')
                ->join('designations','users.designation_id','designations.id')
                ->join('clients','users.department_id','clients.id')
                ->where('users.status',1)
                ->where('title','director')
                ->where('clients.parent_id',$dep_id)
                ->count(); 
      $dupty_director=DB::table('users')
                ->join('designations','users.designation_id','designations.id')
                ->join('clients','users.department_id','clients.id')
                ->where('users.status',1)
                ->where('title','duptydirector')
                ->where('clients.parent_id',$dep_id)
                ->count();  
      $short_leave="SELECT count(1) as short_leave
              from(
                SELECT leave_requests.user_id 
                from leave_requests 
                INNER JOIN leave_requests_details 
                on leave_requests.id = leave_requests_details.parent_table_id
                WHERE leave_requests.leave_type=1 and leave_requests_details.leave_date='".date('Y-m-d')."' 
                GROUP by leave_requests_details.user_id) as temp";  
    $short_leave_count = DB::select( DB::raw($short_leave)); 

    $designation=Designation::where('department_id',$dep_id)->count();
    $department=Client::where('parent_id',$dep_id)->count();
    $attach_employee = AttachEmployee::count();
    $device_data = DB::select("SELECT * FROM `device_info`");

    $date= date('Y-m-d');

     if(Session::get('role_id')==1){
       $piegraph='';
        return view('Dashboards.super_admin',compact('device_data','user','designation','department','piegraph'));
        }

    elseif(Session::get('role_id')==5){
        
         if($day=='Saturday'){
               $daily_present="SELECT  sum(IF(temp.attendance_time < temp.time_in,1,0)) as 'present'
                        from
                          (SELECT u.id, ADDTIME(CONCAT('".$date."' ,' ',tcat.tc_satureday_in),tcat.grace_time) as time_in,att.datetime as attendance_time
                           from users u INNER JOIN clients c on u.department_id= c.id 
                           inner join time_categories tcat on tcat.id  =u.tcat_id
                           inner join attendance att on att.user_id=u.id 
                           where c.parent_id = '".Session::get('dep_id')."' and date(datetime)='".$date."' and u.flag=1 and u.status=1 GROUP by u.id order by u.id,datetime) as temp";

            $daily_late="SELECT  sum(IF(temp.attendance_time > temp.time_in,1,0)) as 'late'
                        from
                          (SELECT u.id, ADDTIME(CONCAT('".$date."' ,' ',tcat.tcat.tc_satureday_in),tcat.grace_time) as time_in,att.datetime as attendance_time
                           from users u INNER JOIN clients c on u.department_id= c.id 
                            inner join time_categories tcat on tcat.id  =u.tcat_id
                           inner join attendance att on att.user_id=u.id 
                           where c.parent_id = '".Session::get('dep_id')."' and date(datetime)='".$date."' and u.flag=1 and u.status=1 GROUP by u.id order by u.id,datetime) as temp";
          $daily_late_count = DB::select( DB::raw($daily_late));  
          $first_day_query =   "SELECT  
                              sum(IF(temp.attendance_time > temp.time_in,1,0)) as 'late',
                              sum(IF(temp.attendance_time < temp.time_in,1,0)) as 'present'
                           from
                             (SELECT u.id, ADDTIME(CONCAT('".$first_day_of_week."' ,' ',tcat.tc_satureday_in),tcat.grace_time) as time_in,att.datetime as attendance_time
                              from users u INNER JOIN clients c on u.department_id= c.id 
                              inner join attendance att on att.user_id=u.id 
                              inner join time_categories tcat on tcat.id=u.tcat_id 
                              where c.parent_id = '".Session::get('dep_id')."' 
                                and date(datetime)='".$first_day_of_week."' and u.flag=1 
                                  and u.status=1 
                              GROUP by u.id order by u.id,datetime) as temp"; 
          $second_day_query =   "SELECT  sum(IF(temp.attendance_time > temp.time_in,1,0)) as 'late',
                                  sum(IF(temp.attendance_time < temp.time_in,1,0)) as 'present'
                           from
                             (SELECT u.id, ADDTIME(CONCAT('".$second_day_of_week."' ,' ',tcat.tc_satureday_in),tcat.grace_time) as time_in,att.datetime as attendance_time
                              from users u INNER JOIN clients c on u.department_id= c.id 
                              inner join attendance att on att.user_id=u.id 
                              inner join time_categories tcat on tcat.id=u.tcat_id 
                              where c.parent_id = '".Session::get('dep_id')."' 
                                and date(datetime)='".$second_day_of_week."' and u.flag=1 
                                  and u.status=1 
                              GROUP by u.id order by u.id,datetime) as temp"; 
                                    
         
         }

      
          $daily_present_count = DB::select( DB::raw($daily_present));
          
          if($daily_late_count[0]->late==''){
              $daily_late_count[0]->late=0;

          }else{
                  $daily_late_count[0]->late;
          }
       
           if($daily_present_count[0]->present==''){
              $daily_present_count[0]->present=0;

          }else{
                  $daily_present_count[0]->present;

          }
          $daily_leave="SELECT count(1) as leave_title
                          FROM(
                             SELECT u.id,u.name,u.flag,lev_re.leave_date,att.datetime from users u 
                           INNER JOIN clients c on c.id = u.department_id
                           INNER JOIN leave_requests_details lev_re on lev_re.user_id=u.id
                           LEFT join attendance att on att.user_id=u.id and date(att.datetime)='".$date."'
                           WHERE c.parent_id='".Session::get('dep_id')."' and u.flag=1 and u.status=1 AND lev_re.leave_date='".$date."' and att.datetime is null group by lev_re.user_id) as temp";
          $daily_leave_count = DB::select( DB::raw($daily_leave));
         $daily_attach= "SELECT count(1) as attach_count FROM attach_employees_details 
                       INNER JOIN users on users.id= attach_employees_details.user_id
                       INNER JOIN clients on clients.id= users.department_id
                        and attach_employees_details.attach_date='".$date."' 
                        AND clients.parent_id='".Session::get('dep_id')."'";
          $daily_attach_count = DB::select( DB::raw($daily_attach));
          $total_late_and_present_leave =  $daily_late_count[0]->late + $daily_present_count[0]->present +  $daily_leave_count[0]->leave_title + $daily_attach_count[0]->attach_count;

           $users="select count(users.id) as user_id from users INNER JOIN clients on users.department_id =clients.id WHERE clients.parent_id='".$dep_id."' and users.flag=1 and users.status=1";
           $all_users = DB::select( DB::raw($users)); 
           $daily_absent_count =  $all_users[0]->user_id -   $total_late_and_present_leave;                 
               $gender_data= DB::table('clients as c')
                  ->join('users as u','u.department_id','c.id')
                  ->where('c.parent_id',$dep_id)
                  ->selectRaw(
                    'distinct c.id, c.name as dept_names'
                  )
                  ->selectRaw('(select count(1) from users where gender="male" and department_id=c.id) as male')
                  ->selectRaw('(select count(1) from users where gender="female" and department_id=c.id) as female')
                  ->where('c.parent_id',Session::get('dep_id'))
                  ->where('u.status',1)
                  ->groupBy('u.gender','c.name')
                  ->orderBy('c.name')->get();
                  
      $first_day_of_week = date('Y-m-d', strtotime("this week"));
      $second_day_of_week = date_create('this week +1 days')->format('Y-m-d');
      $third_day_of_week  = date_create('this week +2 days')->format('Y-m-d');
      $fourth_day_of_week = date_create('this week +3 days')->format('Y-m-d');
      $fifth_day_of_week  = date_create('this week +4 days')->format('Y-m-d');
      $sixth_day_of_week  = date_create('this week +5 days')->format('Y-m-d');
       $i=date('d', strtotime($first_day_of_week));
       $l=date('d', strtotime($sixth_day_of_week));

    $month = date('m', strtotime("this week"));
    
    $Last_date = date_create('this week +5 days')->format('Y-m-d');

    $Last_day = date('d', strtotime($Last_date));
    $first_day_of_weekly_graph  =  DB::select(DB::raw($first_day_query));          
    $first_day_leave=   "SELECT count(1) as leave_title
                          FROM(
                             SELECT u.id,u.name,u.flag,lev_re.leave_date,att.datetime from users u 
                           INNER JOIN clients c on c.id = u.department_id
                           INNER JOIN leave_requests_details lev_re on lev_re.user_id=u.id
                           LEFT join attendance att on att.user_id=u.id and date(att.datetime)='".$first_day_of_week."'
                           WHERE c.parent_id='".$dep_id."' and u.flag=1 and u.status=1 AND lev_re.leave_date='".$first_day_of_week."' and att.datetime is null group by lev_re.user_id) as temp";
    $first_day_of_weekly_graph_leave  =  DB::select(DB::raw($first_day_leave));  
    $first_day_attach= "SELECT count(1) as attach_count FROM attach_employees_details 
                       INNER JOIN users on users.id= attach_employees_details.user_id
                       INNER JOIN clients on clients.id= users.department_id
                        and attach_employees_details.attach_date='".$first_day_of_week."' 
                        AND clients.parent_id='".Session::get('dep_id')."'";
    $first_day_of_weekly_graph_attach  =  DB::select(DB::raw($first_day_attach));

                                                                                
    if($first_day_of_weekly_graph[0]->present==''){
      $first_day_of_weekly_graph[0]->present=0;
      }else{
           $first_day_of_weekly_graph[0]->present;      
      }
    if($first_day_of_weekly_graph[0]->late==''){
       $first_day_of_weekly_graph[0]->late=0;
      }else{

        $first_day_of_weekly_graph[0]->late;
      }   
    $first_day_of_weekly_graph_present_late=$first_day_of_weekly_graph[0]->present+$first_day_of_weekly_graph[0]->late+$first_day_of_weekly_graph_leave[0]->leave_title+$first_day_of_weekly_graph_attach[0]->attach_count;
    $first_day_of_weekly_graph_absent=$all_users[0]->user_id-$first_day_of_weekly_graph_present_late;  
     $second_day_of_weekly_graph  =  DB::select(DB::raw($second_day_query));
      $second_day_leave=   "SELECT count(1) as leave_title
                              FROM(
                           SELECT u.id,u.name,u.flag,lev_re.leave_date,att.datetime from users u 
                           INNER JOIN clients c on c.id = u.department_id
                           INNER JOIN leave_requests_details lev_re on lev_re.user_id=u.id
                           LEFT join attendance att on att.user_id=u.id and date(att.datetime)='".$second_day_of_week."'
                           WHERE c.parent_id='".Session::get('dep_id')."' and u.flag=1 and u.status=1 AND lev_re.leave_date='".$second_day_of_week."' and att.datetime is null group by lev_re.user_id) as temp";
    $second_day_of_weekly_graph_leave  =  DB::select(DB::raw($second_day_leave)); 
    $second_day_attach= "SELECT count(1) as attach_count FROM attach_employees_details 
                       INNER JOIN users on users.id= attach_employees_details.user_id
                       INNER JOIN clients on clients.id= users.department_id
                        and attach_employees_details.attach_date='".$second_day_of_week."' 
                        AND clients.parent_id='".Session::get('dep_id')."'";
    $second_day_of_weekly_graph_attach  =  DB::select(DB::raw($second_day_attach));

     
    if($second_day_of_weekly_graph[0]->present==''){
      $second_day_of_weekly_graph[0]->present=0;

     }
    else{
           $second_day_of_weekly_graph[0]->present;      
        }
    if($second_day_of_weekly_graph[0]->late==''){
       $second_day_of_weekly_graph[0]->late=0;
      }
     else{

           $second_day_of_weekly_graph[0]->late;
         
         } 
    $second_day_of_weekly_graph_present_late=$second_day_of_weekly_graph[0]->present+$second_day_of_weekly_graph[0]->late+$second_day_of_weekly_graph_leave[0]->leave_title+$second_day_of_weekly_graph_attach[0]->attach_count;
    $second_day_of_weekly_graph_absent=$all_users[0]->user_id-$second_day_of_weekly_graph_present_late;
     $third_day_leave=   "SELECT count(1) as leave_title
                              FROM(
                           SELECT u.id,u.name,u.flag,lev_re.leave_date,att.datetime from users u 
                           INNER JOIN clients c on c.id = u.department_id
                           INNER JOIN leave_requests_details lev_re on lev_re.user_id=u.id
                           LEFT join attendance att on att.user_id=u.id and date(att.datetime)='".$third_day_of_week."'
                           WHERE c.parent_id='".Session::get('dep_id')."' and u.flag=1 and u.status=1 AND lev_re.leave_date='".$third_day_of_week."' and att.datetime is null group by lev_re.user_id) as temp";
    $third_day_of_weekly_graph_leave  =  DB::select(DB::raw($third_day_leave)); 
    $third_day_attach= "SELECT count(1) as attach_count FROM attach_employees_details 
                       INNER JOIN users on users.id= attach_employees_details.user_id
                       INNER JOIN clients on clients.id= users.department_id
                        and attach_employees_details.attach_date='".$third_day_of_week."' 
                        AND clients.parent_id='".Session::get('dep_id')."'";
    $third_day_of_weekly_graph_attach  =  DB::select(DB::raw($third_day_attach));
      if($third_day_of_weekly_graph[0]->present==''){
      $third_day_of_weekly_graph[0]->present=0;

     }
    else{
           $third_day_of_weekly_graph[0]->present;      
        }
    if($third_day_of_weekly_graph[0]->late==''){
       $third_day_of_weekly_graph[0]->late=0;
      }
     else{

        $third_day_of_weekly_graph[0]->late;
       
        } 
    $third_day_of_weekly_graph_present_late=$third_day_of_weekly_graph[0]->present+$third_day_of_weekly_graph[0]->late+ $third_day_of_weekly_graph_leave[0]->leave_title+$third_day_of_weekly_graph_attach[0]->attach_count;
    $third_day_of_weekly_graph_absent=$all_users[0]->user_id-$third_day_of_weekly_graph_present_late;

      $fourth_day_query =   "SELECT  sum(IF(temp.attendance_time > temp.time_in,1,0)) as 'late',
                                  sum(IF(temp.attendance_time < temp.time_in,1,0)) as 'present'
                           from
                             (SELECT u.id, ADDTIME(CONCAT('".$fourth_day_of_week."' ,' ',tcat.time_in),tcat.grace_time) as time_in,att.datetime as attendance_time
                              from users u INNER JOIN clients c on u.department_id= c.id 
                              inner join attendance att on att.user_id=u.id 
                              inner join time_categories tcat on tcat.id=u.tcat_id 
                              where c.parent_id = '".Session::get('dep_id')."' 
                                and date(datetime)='".$fourth_day_of_week."' and u.flag=1 
                                  and u.status=1 
                              GROUP by u.id order by u.id,datetime) as temp"; 

     $fourth_day_of_weekly_graph  =  DB::select(DB::raw($fourth_day_query));
      $fourth_day_leave=   "SELECT count(1) as leave_title
                              FROM(
                            SELECT u.id,u.name,u.flag,lev_re.leave_date,att.datetime from users u 
                            INNER JOIN clients c on c.id = u.department_id
                            INNER JOIN leave_requests_details lev_re on lev_re.user_id=u.id
                            LEFT join attendance att on att.user_id=u.id and date(att.datetime)='".$fourth_day_of_week."'
                            WHERE c.parent_id='".Session::get('dep_id')."' and u.flag=1 and u.status=1 AND lev_re.leave_date='".$fourth_day_of_week."' and att.datetime is null group by lev_re.user_id) as temp";
    $fourth_day_of_weekly_graph_leave  =  DB::select(DB::raw($fourth_day_leave)); 
    $fourth_day_attach= "SELECT count(1) as attach_count FROM attach_employees_details 
                       INNER JOIN users on users.id= attach_employees_details.user_id
                       INNER JOIN clients on clients.id= users.department_id
                        and attach_employees_details.attach_date='".$fourth_day_of_week."' 
                        AND clients.parent_id='".Session::get('dep_id')."'";
    $fourth_day_of_weekly_graph_attach  =  DB::select(DB::raw($fourth_day_attach));

     if($fourth_day_of_weekly_graph[0]->present==''){
      $fourth_day_of_weekly_graph[0]->present=0;

     }
    else{
           $fourth_day_of_weekly_graph[0]->present;      
        }
    if($fourth_day_of_weekly_graph[0]->late==''){
       $fourth_day_of_weekly_graph[0]->late=0;
      }
     else{

        $fourth_day_of_weekly_graph[0]->late;
       
        } 
    $fourth_day_of_weekly_graph_present_late=$fourth_day_of_weekly_graph[0]->present+$fourth_day_of_weekly_graph[0]->late+$fourth_day_of_weekly_graph_leave[0]->leave_title+$fourth_day_of_weekly_graph_attach[0]->attach_count;;
    $fourth_day_of_weekly_graph_absent=$all_users[0]->user_id-$fourth_day_of_weekly_graph_present_late;

    $fifth_day_query =   "SELECT  sum(IF(temp.attendance_time > temp.time_in,1,0)) as 'present',
                                  sum(IF(temp.attendance_time < temp.time_in,1,0)) as 'late'
                           from
                             (SELECT u.id, ADDTIME(CONCAT('".$fifth_day_of_week."' ,' ',tcat.time_in),tcat.grace_time) as time_in,att.datetime as attendance_time
                              from users u INNER JOIN clients c on u.department_id= c.id 
                              inner join attendance att on att.user_id=u.id 
                              inner join time_categories tcat on tcat.id=u.tcat_id 
                              where c.parent_id = '".Session::get('dep_id')."' 
                                and date(datetime)='".$fifth_day_of_week."' and u.flag=1 
                                  and u.status=1 
                              GROUP by u.id order by u.id,datetime) as temp";
    $fifth_day_of_weekly_graph  =  DB::select(DB::raw($fifth_day_query));
    $fifth_day_leave=   "SELECT count(1) as leave_title
                              FROM(
                            SELECT u.id,u.name,u.flag,lev_re.leave_date,att.datetime from users u 
                            INNER JOIN clients c on c.id = u.department_id
                            INNER JOIN leave_requests_details lev_re on lev_re.user_id=u.id
                            LEFT join attendance att on att.user_id=u.id and date(att.datetime)='".$fifth_day_of_week."'
                            WHERE c.parent_id='".Session::get('dep_id')."' and u.flag=1 and u.status=1 AND lev_re.leave_date='".$fifth_day_of_week."' and att.datetime is null group by lev_re.user_id) as temp";
    $fifth_day_of_weekly_graph_leave  =  DB::select(DB::raw($fifth_day_leave)); 

    $fifth_day_attach= "SELECT count(1) as attach_count FROM attach_employees_details 
                       INNER JOIN users on users.id= attach_employees_details.user_id
                       INNER JOIN clients on clients.id= users.department_id
                        and attach_employees_details.attach_date='".$fifth_day_of_week."' 
                        AND clients.parent_id='".Session::get('dep_id')."'";
    $fifth_day_of_weekly_graph_attach  =  DB::select(DB::raw($fifth_day_attach));

       if($fifth_day_of_weekly_graph[0]->present==''){
      $fifth_day_of_weekly_graph[0]->present=0;

     }
    else{
           $fifth_day_of_weekly_graph[0]->present;      
        }
    if($fifth_day_of_weekly_graph[0]->late==''){
       $fifth_day_of_weekly_graph[0]->late=0;
      }
     else{

          $fifth_day_of_weekly_graph[0]->late;

        }
     $fifth_day_of_weekly_graph_present_late=$fifth_day_of_weekly_graph[0]->present+$fifth_day_of_weekly_graph[0]->late+$fifth_day_of_weekly_graph_leave[0]->leave_title+$fifth_day_of_weekly_graph_attach[0]->attach_count;
     $fifth_day_of_weekly_graph_absent=$all_users[0]->user_id-$fifth_day_of_weekly_graph_present_late;

   
     $sixth_day_query =   "SELECT  sum(IF(temp.attendance_time > temp.time_in,1,0)) as 'present',
                                  sum(IF(temp.attendance_time < temp.time_in,1,0)) as 'late'
                           from
                             (SELECT u.id, ADDTIME(CONCAT('".$sixth_day_of_week."' ,' ',tcat.time_in),tcat.grace_time) as time_in,att.datetime as attendance_time
                              from users u INNER JOIN clients c on u.department_id= c.id 
                              inner join attendance att on att.user_id=u.id 
                              inner join time_categories tcat on tcat.id=u.tcat_id 
                              where c.parent_id = '".Session::get('dep_id')."' 
                                and date(datetime)='".$sixth_day_of_week."' and u.flag=1 
                                  and u.status=1 
                              GROUP by u.id order by u.id,datetime) as temp"; 
     $sixth_day_of_weekly_graph  =  DB::select(DB::raw($sixth_day_query));

     $sixth_day_leave=   "SELECT count(1) as leave_title
                              FROM(
                            SELECT u.id,u.name,u.flag,lev_re.leave_date,att.datetime from users u 
                            INNER JOIN clients c on c.id = u.department_id
                            INNER JOIN leave_requests_details lev_re on lev_re.user_id=u.id
                            LEFT join attendance att on att.user_id=u.id and date(att.datetime)='".$sixth_day_of_week."'
                            WHERE c.parent_id='".Session::get('dep_id')."' and u.flag=1 and u.status=1 AND lev_re.leave_date='".$sixth_day_of_week."' and att.datetime is null group by lev_re.user_id) as temp";
    $sixth_day_of_weekly_graph_leave  =  DB::select(DB::raw($sixth_day_leave));
    $sixth_day_attach= "SELECT count(1) as attach_count FROM attach_employees_details 
                       INNER JOIN users on users.id= attach_employees_details.user_id
                       INNER JOIN clients on clients.id= users.department_id
                        and attach_employees_details.attach_date='".$sixth_day_of_week."' 
                        AND clients.parent_id='".Session::get('dep_id')."'";
    $sixth_day_of_weekly_graph_attach  =  DB::select(DB::raw($sixth_day_attach)); 

    if($sixth_day_of_weekly_graph[0]->present==''){
      $sixth_day_of_weekly_graph[0]->present=0;

     }
    else{
           $sixth_day_of_weekly_graph[0]->present;      
        }
    if($sixth_day_of_weekly_graph[0]->late==''){
       $sixth_day_of_weekly_graph[0]->late=0;
      }
     else{

          $sixth_day_of_weekly_graph[0]->late;

        }
     $sixth_day_of_weekly_graph_present_late=$sixth_day_of_weekly_graph[0]->present+$sixth_day_of_weekly_graph[0]->late+$sixth_day_of_weekly_graph_leave[0]->leave_title+$sixth_day_of_weekly_graph_attach[0]->attach_count;
     $sixth_day_of_weekly_graph_absent=$all_users[0]->user_id-$sixth_day_of_weekly_graph_present_late;



    $total_late_in_week = $first_day_of_weekly_graph[0]->late+ $second_day_of_weekly_graph[0]->late +$third_day_of_weekly_graph[0]->late + $fourth_day_of_weekly_graph[0]->late + $fifth_day_of_weekly_graph[0]->late + $sixth_day_of_weekly_graph[0]->late;
   
   $total_absent_in_week = $first_day_of_weekly_graph_absent+$second_day_of_weekly_graph_absent +$third_day_of_weekly_graph_absent+ $fourth_day_of_weekly_graph_absent+$fifth_day_of_weekly_graph_absent + $sixth_day_of_weekly_graph_absent;
 
   $total_present_in_week = $first_day_of_weekly_graph[0]->present+ $second_day_of_weekly_graph[0]->present +$third_day_of_weekly_graph[0]->present + $fourth_day_of_weekly_graph[0]->present + $fifth_day_of_weekly_graph[0]->present + $sixth_day_of_weekly_graph[0]->present;

        return view('Dashboards.department_admin',
                      compact('user',
                              'designation',                    
                              'department',
                              'device_data',
                              'attach_employee',
                              'daily_absent_count',
                              'daily_leave_count',
                              'daily_present_count',
                              'first_day_of_weekly_graph_absent',
                              'second_day_of_weekly_graph_absent',
                              'third_day_of_weekly_graph_absent',
                              'fourth_day_of_weekly_graph_absent',
                              'fifth_day_of_weekly_graph_absent',
                              'sixth_day_of_weekly_graph_absent',
                              'first_day_of_weekly_graph_leave',
                              'first_day_of_weekly_graph_attach',
                              'second_day_of_weekly_graph_attach',
                              'third_day_of_weekly_graph_attach',
                              'fourth_day_of_weekly_graph_attach',
                              'fifth_day_of_weekly_graph_attach',
                              'sixth_day_of_weekly_graph_attach',
                              'second_day_of_weekly_graph_leave',
                              'third_day_of_weekly_graph_leave',
                              'fourth_day_of_weekly_graph_leave',
                              'fifth_day_of_weekly_graph_leave',
                              'sixth_day_of_weekly_graph_leave',
                              'daily_late_count',
                              'short_leave_count',
                              'total_late_in_week',
                              'total_absent_in_week',
                              'total_present_in_week',
                              'first_day_of_weekly_graph',
                              'second_day_of_weekly_graph',
                              'third_day_of_weekly_graph',
                              'fourth_day_of_weekly_graph',
                              'fifth_day_of_weekly_graph',
                              'sixth_day_of_weekly_graph',
                              'daily_attach_count',
                              'gender_data',
                              'comp_oper',
                              'dupty_director',
                              'director'                      
                            ));
      
     }


  else{

    return view('Dashboards.head_of_dep_dashboard');

    }

 }
 
 }
