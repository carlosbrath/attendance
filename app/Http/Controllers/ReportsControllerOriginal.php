<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use App\Client;
use Session;
use App\Roaster as roster;
use App\TimeCategory;
use App\User;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
     
public function excel(){

      return Excel::download(new ReportExport, 'Report.xlsx');            
    }


 public function reports(Request $request)
 {
   
    $dep_id=Session::get('dep_id');

    if(Session::get('role_id')==1)
       {

        $department=Client::where('parent_id','!=',0)->get();

       }

     else
      {
     
       $department=Client::where('parent_id',Session::get('dep_id'))->get();  
     
      }
  
       $date= date('Y-m-d');
       $dep_id=Session::get('dep_id');
       $department_id_unit =Client::select('id')->where('parent_id',$dep_id)->get();
       $ids=[];
       foreach($department_id_unit as $d){
        $ids[]=  $d->id ;    
       }
    
   $ids = join("','",$ids); 

   $query_for_absent="select SUM(IF(temp.attendance_time IS NULL,1,0)) as Absent from 
                                (select rs.date,tcat.type,tcat.title as leave_title, u.id,u.name,clients.name as deparment_name,designations.title,
                                        ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as 
                                        time_in,att.datetime as attendance_time  from users u 
                                         LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                         LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                        LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN clients on clients.id = u.department_id 
                                        where u.department_id in ('$ids') 
                                        order by att.datetime) as temp where temp.attendance_time IS NULL and temp.time_in IS NULL";

   $absent_count=  DB::select(DB::raw($query_for_absent));

   $query_for_present =   "select SUM(if(level1.attendance_time < level1.time_in,1,0)) as Present from
                                  (select rs.date, u.id,u.name,clients.name as deparment_name,designations.title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as 
                                          time_in,att.datetime as attendance_time  from users u 
                                          LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                          LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                          LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                          LEFT JOIN designations on designations.id=u.designation_id
                                          LEFT JOIN clients on clients.id = u.department_id 
                                          WHERE u.department_id in ('$ids') and  att.datetime IS NOT NULL
                                          order by att.datetime) as level1 group by level1.id";
   $present_count =  DB::select(DB::raw($query_for_present));
   $query_for_late=   "select if(level1.attendance_time > level1.time_in,1,0) as Late from
                                          (select rs.date, u.id,u.name,clients.name as deparment_name,designations.title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as 
                                          time_in,att.datetime as attendance_time  from users u 
                                          LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                          LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                          LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                          LEFT JOIN designations on designations.id=u.designation_id
                                          LEFT JOIN clients on clients.id = u.department_id 
                                          WHERE u.department_id in ('$ids') and  att.datetime IS NOT NULL
                                          order by att.datetime) as level1 group by level1.id";
   $late_count=  DB::select(DB::raw($query_for_late));


   $query_for_leave ="select SUM(case when level1.type = 2 then 1 else 0 end) as leave_status from
                       (select rs.date,tcat.title as leave_title,tcat.type,u.id,u.name,clients.name as 
                        deparment_name,designations.title,
                        ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as 
                        time_in,att.datetime as attendance_time  from users u 
                         LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                         LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                        LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                        LEFT JOIN designations on designations.id=u.designation_id
                        LEFT JOIN clients on clients.id = u.department_id where u.department_id in ('".$ids."')
                        and tcat.type='2'
                        order by att.datetime) as level1 ";
    $leave_count=  DB::select(DB::raw($query_for_leave));

   $query_for_report_summary="select SUM(IF(level2.attendance_time>level2.time_in,1,0)) as late,
                                     SUM(IF(level2.attendance_time<level2.time_in,1,0))  as present,
                                     SUM(IF(level2.attendance_time IS NULL,1,0)) as absent,
                                     
                                     (SELECT count(*) as leave_approve FROM
                                     (SELECT COUNT(r.user_id) from time_categories t 
                                     INNER JOIN roaster_staffs r on t.id = r.tcat_id 
                                     WHERE t.type='2' and r.date='".$date."' 
                                     GROUP BY r.user_id) as temp) as total_leaves,

                                     (select count(1) from attach_employees where from_date = '".$date."') as total_attach
                                     from (SELECT
                                     temp.*
                                     FROM
                                     (SELECT u.id,u.name,u.designation_id,ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as time_in,att.datetime as attendance_time from users u 
                                     LEFT JOIN roaster_staffs rs on u.id = rs.user_id and rs.date = '".$date."'
                                     LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                     LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime) = '".$date."'
                                     LEFT JOIN clients c on c.id =u.department_id
                                      WHERE c.parent_id='".$dep_id."'
                                     order by u.id,datetime) as temp group by id) as level2";

    $report_query_all  =        "select level2.* , CASE WHEN level2.attendance_time IS NULL THEN 'Absent' 
                                      ELSE IF(level2.attendance_time>level2.time_in,'Late','Present') END as Emp_Status 
                                      from (SELECT
                                      temp.*
                                      FROM
                                      (SELECT u.id,u.name,clients.name as deparment_name,designations.title ,ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as time_in,att.datetime as attendance_time from users u 
                                      LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                      LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                      LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                      LEFT JOIN designations on designations.id=u.designation_id
                                      LEFT JOIN clients on clients.id=designations.department_id where u.department_id='".$dep_id."'                            
                                      order by u.id,datetime) as temp group by id) as level2"; 

    // $report_query_for_departmnent_adm  = "select rs.date,tcat.title as leave_title,tcat.type,u.id,u.name,clients.name as 
    //                                       deparment_name,designations.title,
    //                                       ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as 
    //                                       time_in,att.datetime as attendance_time  from users u 
    //                                        LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
    //                                        LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
    //                                       LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
    //                                       LEFT JOIN designations on designations.id=u.designation_id
    //                                       LEFT JOIN clients on clients.id = u.department_id where u.department_id in ('".$ids."') and u.status=1 order by u.id
    //                                     ";
                                          //  order by att.datetime
         $report_query_for_departmnent_adm  =      "select  u.id,u.name,clients.name as 
                                          deparment_name,designations.title,
                                          ADDTIME(CONCAT('2019-12-18',' ',u.time_in),'00:15:00') as 
                                          time_in,att.datetime as attendance_time  from users u 
                                          LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='2019-12-18'
                                          LEFT JOIN designations on designations.id=u.designation_id
                                          LEFT JOIN clients on clients.id = u.department_id WHERE flag=1 order by u.id";

    $search_all_for_hod = "select level2.* , CASE WHEN level2.attendance_time IS NULL THEN 'Absent' 
                                      ELSE IF(level2.attendance_time>level2.time_in,'Late','Present') END as Emp_Status 
                                      from (SELECT
                                      temp.*
                                      FROM
                                      (SELECT u.id,u.name,clients.name as deparment_name,designations.title ,ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as time_in,att.datetime as attendance_time from users u 
                                      LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                      LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                      LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                      LEFT JOIN designations on designations.id=u.designation_id
                                      LEFT JOIN clients on clients.id = u.department_id
                                      where u.department_id = '".$dep_id."'
                                      order by u.id,datetime) as temp group by id) as level2";                                 


 
    if($request->isMethod('post')){
         $date=date_create($request->date);
         date_format($date,"d F, Y");                
         $search=$request->search;

         $rep_date=$request->date;
     if(Session::get('role_id')==5){

        if($search!='' && $rep_date!='dd/mm/yyyy' && $request->status=='absent' ){
              $report_query = "select temp.* from 
                                (select rs.date,tcat.type,tcat.title as leave_title, u.id,u.name,clients.name as deparment_name,designations.title,
                                        ADDTIME(CONCAT('".$rep_date."',' ',tcat.time_in),'00:15:00') as 
                                        time_in,att.datetime as attendance_time  from users u 
                                         LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$rep_date."'
                                         LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                        LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN clients on clients.id = u.department_id 
                                         WHERE u.department_id ='".$search."' and att.datetime is null
                                        ) as temp"; 
 
                                      $attandance_reports=collect(DB::select(DB::raw($report_query)));


             return view('Reports.attandance_report',compact('attandance_reports','department'));
       
       
       }
        if($search=='' && $rep_date!='dd/mm/yyyy' && $request->status=='absent' ){
             
              $report_query = "select temp.* from 
                                (select rs.date,tcat.type,tcat.title as leave_title, u.id,u.name,clients.name as deparment_name,designations.title,
                                        ADDTIME(CONCAT('".$rep_date."',' ',tcat.time_in),'00:15:00') as 
                                        time_in,att.datetime as attendance_time  from users u 
                                         LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$rep_date."'
                                         LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                        LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN clients on clients.id = u.department_id 
                                         WHERE u.department_id in ('".$ids."') and att.datetime is null
                                        ) as temp"; 
 
                                      $attandance_reports=collect(DB::select(DB::raw($report_query)));
                                      

              return view('Reports.attandance_report',compact('attandance_reports','department'));
       
        }
      if($search!=''  && $rep_date=='dd/mm/yyyy' && $request->status=='present' ){ 
               $report_query  =  "select *,  if(level1.attendance_time < level1.time_in,1,0)  as Emp_Status 
                                      from
                                          (select rs.date, u.id,u.name,clients.name as deparment_name,designations.title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as 
                                          time_in,att.datetime as attendance_time  from users u 
                                          LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                          LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                          LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                          LEFT JOIN designations on designations.id=u.designation_id
                                          LEFT JOIN clients on clients.id = u.department_id 
                                          WHERE u.department_id ='".$search."' and  att.datetime IS NOT NULL
                                           order by att.datetime) as level1"; 
      $attandance_reports=collect(DB::select(DB::raw( $report_query)));
      return view('Reports.attandance_report',compact('attandance_reports','department'));
       
       }
      if($search=='' && $rep_date!='dd/mm/yyyy' && $request->status=='present' ){
       $report_query  =     "select *,  if(level1.attendance_time < level1.time_in,1,0)  as Emp_Status from
                                          (select rs.date, u.id,u.name,clients.name as deparment_name,designations.title,
                                          ADDTIME(CONCAT('".$rep_date."',' ',tcat.time_in),'00:15:00') as 
                                          time_in,att.datetime as attendance_time  from users u 
                                          LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$rep_date."'
                                          LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                          LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id
                                          LEFT JOIN clients on clients.id = u.department_id 
                                          WHERE date(att.datetime) ='".$rep_date."' and   u.department_id in ('$ids') 
                                           order by att.datetime) as level1";
      
     $attandance_reports=collect(DB::select(DB::raw( $report_query)));
    return view('Reports.attandance_report',compact('attandance_reports',
                                                    'department'
                                                     ));
       
       }
        if($search!='' && $rep_date!='dd/mm/yyyy' && $request->status=='present' ){   
                   $report_query  =     "select *,  if(level1.attendance_time < level1.time_in,1,0)  as Emp_Status from
                                          (select rs.date, u.id,u.name,clients.name as deparment_name,designations.title,
                                          ADDTIME(CONCAT('".$rep_date."',' ',tcat.time_in),'00:15:00') as 
                                          time_in,att.datetime as attendance_time  from users u 
                                          LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$rep_date."'
                                          LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                          LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$rep_date."'
                                          LEFT JOIN designations on designations.id=u.designation_id
                                          LEFT JOIN clients on clients.id = u.department_id 
                                          WHERE date(att.datetime) ='".$rep_date."' and (u.department_id ='".$search."' ) and  att.datetime IS NOT NULL
                                           order by att.datetime) as level1";  
        $attandance_reports=collect(DB::select(DB::raw( $report_query)));
        return view('Reports.attandance_report',compact('attandance_reports','department'));
                                                    
       
       }
       if($search=='' && $rep_date=='dd/mm/yyyy' && $request->status=='present' ){
        
                $report_query  =     "select *,  if(level1.attendance_time < level1.time_in,1,0)  as Emp_Status from
                                          (select rs.date, u.id,u.name,clients.name as deparment_name,designations.title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as 
                                          time_in,att.datetime as attendance_time  from users u 
                                          LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                          LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                          LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                          LEFT JOIN designations on designations.id=u.designation_id
                                          LEFT JOIN clients on clients.id = u.department_id 
                                          WHERE u.department_id in ('$ids') and  att.datetime IS NOT NULL
                                           order by att.datetime) as level1";
               $attandance_reports=collect(DB::select(DB::raw( $report_query)));

            return view('Reports.attandance_report',compact('attandance_reports','department'));
                             

                       
            }
           
        if($search=='' && $rep_date=='dd/mm/yyyy' && $request->status=='absent' ){
            
          $report_query = "select temp.* from 
                                (select rs.date,tcat.type,tcat.title as leave_title, u.id,u.name,clients.name as deparment_name,designations.title,
                                        ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as 
                                        time_in,att.datetime as attendance_time  from users u 
                                         LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                         LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                        LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN clients on clients.id = u.department_id 
                                         WHERE u.department_id in ('$ids') and att.datetime is null
                                        ) as temp ";   
               $attandance_reports=collect(DB::select(DB::raw( $report_query)));
              return view('Reports.attandance_report',compact('attandance_reports','department'));  


                                  }

    
   if($search=='' && $rep_date!='dd/mm/yyyy')
     {
   
          $report_query = "select rs.date,tcat.title as leave_title,tcat.type,u.id,u.name,clients.name as 
                                          deparment_name,designations.title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as 
                                          time_in,att.datetime as attendance_time  from users u 
                                           LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                           LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                          LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                          LEFT JOIN designations on designations.id=u.designation_id
                                          LEFT JOIN clients on clients.id = u.department_id 
                                           WHERE clients.parent_id='".$dep_id."' OR ((date(rs.date) LIKE '".$rep_date."')  and (att.datetime= '')) 
                                          order by att.datetime "; 
          $attandance_reports=collect(DB::select(DB::raw($report_query)));
   
   
   
           return view('Reports.attandance_report',compact('attandance_reports','department'));
                   
      }


    if($search!='' && $rep_date=='dd/mm/yyyy' )
                     {
        
         $report_query = "select tcat.type,tcat.title as leave_title,rs.date, u.id,u.name,clients.name as deparment_name,designations.title,
                                          ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as 
                                          time_in,att.datetime as attendance_time  from users u 
                                           LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                           LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                          LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                          LEFT JOIN designations on designations.id=u.designation_id
                                          LEFT JOIN clients on clients.id = u.department_id 
                                          where u.department_id = '".$search."' 
                                           ";

      

            $attandance_reports=collect(DB::select(DB::raw($report_query)));
             return view('Reports.attandance_report',compact('attandance_reports','department'));                  
          }
           if($search=='' && $rep_date=='dd/mm/yyyy' &&  $request->absent=='' && $request->present==''  )
              {   
                   $attandance_reports = DB::select( DB::raw($report_query_for_departmnent_adm));
                    return view('Reports.attandance_report',compact('attandance_reports','department'));

              }
              if($search!='' && $rep_date!='dd/mm/yyyy')
                 {
                    
                 $report_query = "select rs.date,tcat.type,tcat.title as leave_title,u.id,u.name,clients.name as 
                                  deparment_name,designations.title,
                                  ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as 
                                  time_in,att.datetime as attendance_time  from users u 
                                   LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                   LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                  LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                  LEFT JOIN designations on designations.id=u.designation_id
                                  LEFT JOIN clients on clients.id = u.department_id 
                                  WHERE u.department_id= '".$search."'
                                  order by att.datetime ";

               $attandance_reports = collect(DB::select( DB::raw($report_query)));
              
                return view('Reports.attandance_report',compact('attandance_reports','department'));

                      }
              

         }
       
         
         


      if(Session::get('role_id')==7){

       if($search=='' && $rep_date=='dd/mm/yyyy' && $request->status=='absent' ){
        
              $report_query = "select rs.date, u.id,u.name,clients.name as deparment_name,designations.title,
                                        ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as 
                                        time_in,att.datetime as attendance_time  from users u 
                                         LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                         LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                        LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                        LEFT JOIN designations on designations.id=u.designation_id
                                        LEFT JOIN clients on clients.id = u.department_id 
                                        where u.department_id = '".$dep_id."' and datetime is Null
                                        order by att.datetime";  
                   $attandance_reports=collect(DB::select(DB::raw( $report_query)));
              return view('Reports.attandance_report',compact('attandance_reports','department'));                            
                                         
            }
            if($search=='' && $rep_date=='dd/mm/yyyy' &&  $request->absent=='' && $request->present==''  )
              {   
                   $attandance_reports = DB::select( DB::raw($search_all_for_hod));
                    return view('Reports.attandance_report',compact('attandance_reports','department'));

              }
                $attandance_reports = DB::select( DB::raw($search_all_for_hod)); 
                 return view('Reports.attandance_report',compact('attandance_reports','department'));


       }
     


       

          if(Session::get('role_id')==1){


                  $report_query  =   "SELECT level3.* from
                                      (select level2.* , CASE WHEN level2.attendance_time IS NULL THEN 'Absent'
                                       ELSE IF(level2.attendance_time>level2.time_in,'Late','Present') END as Emp_Status
                                       from (SELECT
                                       temp.*
                                       FROM
                                       (SELECT u.id,u.name,clients.name as deparment_name,designations.title ,ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as time_in,att.datetime as attendance_time 
                                       from users u LEFT JOIN roaster_staffs rs on u.id = rs.user_id and rs.date = '".$date."'
                                       LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                       LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime) ='".$date."'
                                       LEFT JOIN designations on designations.id=u.designation_id
                                       LEFT JOIN clients on clients.id=u.department_id 
                                       order by u.id,datetime) as temp group by id) as level2) as level3 
                                       WHERE Emp_Status='Present'";
                                        $attandance_reports=collect(DB::select(DB::raw( $report_query)));
                         return view('Reports.attandance_report',compact('attandance_reports','department'));


                   $superadmin_users="select level2.* , CASE WHEN level2.attendance_time IS NULL THEN 'Absent' 
                                      ELSE IF(level2.attendance_time>level2.time_in,'Late','Present') END as Emp_Status 
                                      from (SELECT
                                      temp.*
                                      FROM
                                      (SELECT u.id,u.name,clients.name as deparment_name,designations.title ,ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as time_in,att.datetime as attendance_time from users u 
                                      LEFT JOIN roaster_staffs rs on u.id = rs.user_id and date(rs.date)='".$date."'
                                      LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
                                      LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime)='".$date."'
                                      LEFT JOIN designations on designations.id=u.designation_id
                                      LEFT JOIN clients on clients.id = u.department_id
                                      order by u.id,datetime) as temp group by id) as level2";

                    $attandance_reports = DB::select( DB::raw($superadmin_users)); 
                    return view('Reports.attandance_report',compact('attandance_reports','department'));     



             }
          }
            $attandance_reports = collect(DB::select( DB::raw($report_query_for_departmnent_adm)));
                 return view('Reports.attandance_report',compact('attandance_reports','department'));

}         
        

  

   public function monthly_report($id){
     
    
     $roster = roster::findOrFail($id);
     $leaves_query="SELECT * FROM time_categories t JOIN roaster_staffs rs on t.id =rs.tcat_id WHERE t.type=2";
     $leavess = DB::select( DB::raw($leaves_query));
     $leaves = collect($leavess);
    $emp_status_query = "SELECT rs.user_id,rs.tcat_id,t.time_in,a.datetime,t.type,rs.date 
                          FROM `attendance` a join roaster_staffs rs on  a.user_id = rs.user_id
                           and date(a.datetime) = rs.date
                            and rs.roster_id ='".$id."' JOIN time_categories t on t.id = rs.tcat_id and t.type = '1' "; 
    $time_in = DB::select( DB::raw($emp_status_query));
    $emp_status_time_in = collect($time_in); 
    $day_off="SELECT r.date,u.id,r.tcat_id,t.type,t.title from roaster_staffs r 
                        inner JOIN time_categories t on t.id=r.tcat_id
                        inner join users u ON u.id=r.user_id where t.type='3'";
    $day_off_query = DB::select( DB::raw($day_off));
    $day_off_query_collection = collect($day_off_query);
    $emp_status_leave="SELECT r.date,u.id,r.tcat_id,t.type,t.title from roaster_staffs r 
                        inner JOIN time_categories t on t.id=r.tcat_id
                        inner join users u ON u.id=r.user_id where t.type='2'" ;
      $leave_Status = DB::select( DB::raw($emp_status_leave));
      $emp_status_leave_status = collect($leave_Status); 
      $tcats = TimeCategory::all();
      $time_category = collect($tcats);
      $users=User::where('department_id',$roster->department_id)->where('status',1)->get();
      return view('Reports.monthly_report',compact('users','emp_status_time_in','emp_status_leave_status','leaves','roster','time_category','day_off_query_collection'));

   }
  
}
