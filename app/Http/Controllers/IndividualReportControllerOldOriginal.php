<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\TimeCategory;
use DB;
use Session;
use DateTime;
use DateInterval;
use DatePeriod;

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


        // $data['department']=Client::where('id',$dep_id)->first();
          // if($data['department']->parent_id==0){
          //          $user=client::with(['users' => function ($query) {
          //               $query->where('status',1)->where('flag',1); 
          //               }])->where('parent_id',$request->id)->get(); 
          //               $arr = [];
          //                 foreach ($user as $u){
          //                 foreach ($u['users'] as $m) {
          //                 $arr[] = $m;
          //                 }  
          //             }
          //      return json_encode(['employee' => $arr]);
          //    }else{
                     
          //             $user=client::with(['users' => function ($query) {
          //               $query->where('status',1)->where('flag',1); 
          //               }])->where('id',$request->id)->get(); 
          //                $arr = [];
          //                 foreach ($user as $u){
          //                 foreach ($u['users'] as $m) {
          //                 $arr[] = $m;
          //                 }  
          //             }
          //           return json_encode(['employee' => $arr]);   
          //      }
    }
     public function reports(Request $request)
     {        	
         $time1='4:00';
         $time2='5:00';
       $result=strtotime($time1)+strtotime($time2);
       $dep_id=Session::get('dep_id');
       $data['department']=Client::where('parent_id',Session::get('dep_id'))->get(); 
       if($data['department']->isEmpty()){
           $data['department']=Client::where('id',Session::get('dep_id'))->where('status',1)->get(); 
       }
        //$weekday = date("l",$date);
   
       // $user=client::with(['users' => function ($query) {
       //    $query->where('status',1)->where('flag',1); 
       // }])->where('parent_id',$dep_id)->get(); 
       
        if($request->has('from_date') && $request->has('to_date')){

          $timecategory=TimeCategory::where('department_id',$request->deparment_id)->first();

          $this->validate($request, [
         			'user_id' => 'required',
         			'from_date' => 'required|before:to_date',
                    'to_date'=>'required|after:from_date',
       		 				]); 
           $data['from_date'] = $request->from_date;
           $data['to_date'] = $request->to_date;
          
           $individual_report_att = "SELECT 
                                             u.id as user_id,
                                                u.name as user_name,
                                                att.datetime as checktime,
                                                date(att.datetime) as att_date,
                                                 tcat.time_in as tcat_time_in,
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
                                                 tcat.tc_satureday_in as satureday_in,
                                                 tcat.tc_satureday_out as satureday_out,
                                                 tcat.tc_sunday_in as sunday_in,
                                                 tcat.tc_sunday_out as sunday_out,
                                                 tcat.time_out as tcat_time_out,
                                                 tcat.grace_time as tcat_grace_time
                                                from users u inner join 
                                                attendance att on u.id= att.user_id
                                                INNER join time_categories tcat on tcat.id=u.tcat_id
                                         where  (date(att.datetime) 
                                        BETWEEN '".$request->from_date."' AND '".$request->to_date."') and u.id='".$request->user_id."' 
                                            group By att.datetime";

    $data['attendance'] = collect(DB::select( DB::raw($individual_report_att))); 
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
    $holi_calen="SELECT id,title,user_id,date(date) as  
                    holiday_date,department_id,date_from,date_to 
                    FROM holiday_calendars
                    WHERE user_id='".$request->user_id."'";             
    $data['holidays'] = collect(DB::select( DB::raw($holi_calen)));
    $data['user_info_query'] = DB::table('users as u')
	                                		->join('clients as c','c.id','u.department_id')
	                                		->join('designations as d','d.id','u.designation_id')         
			                                ->where('u.id',$request->user_id)
			                                ->selectRaw('c.name,d.title,u.id,u.name as user_name,u.image')
			                                ->groupBy('u.name')   
		                                  ->get();
     }

  else{

       $data['to_date'] = "";
       $data['from_date'] = "";
       $data['user_info_query']="";
       $data['dates'] = "";
       $data['individual_report_query'] = "";
       // $data['user'] = $user;
       $data['leaves'] = "";
       $data['attachs'] = "";
       $data['holidays'] = "";

      }
     return view('Reports.individual_report',$data);
	}	
  
 
}
