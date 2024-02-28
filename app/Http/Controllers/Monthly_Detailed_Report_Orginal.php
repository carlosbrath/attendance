<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use DB;
use Session;
use DateTime;

class Monthly_Detailed_Report extends Controller
{

    public function reports(Request $request){

        $department= Client::where('parent_id',Session::get('dep_id'))->pluck('name','id');
        if($request->isMethod('post')){
            $this->validate($request, [

                'deparment_id' => 'required',
                'from_date'=>'required',
                'to_date'=>'required',
                'month'=>'required',
            ]);
            $from_date=$request->from_date;
            $to_date=$request->to_date;
            // $from_date= strtotime( $from_date);
            // echo date('d-M-Y', $from_date);
            // die;

            $first_day=$request->month;
            $yrdata= strtotime($request->month);
            $month_year_name= date('M-Y', $yrdata);
            $client_parent_id = Client::find($request->deparment_id)->parent_id;
            dd($client_parent_id);

            if($client_parent_id==null){
                
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
                                     date(datetime) BETWEEN '".$request->from_date."' AND '".$request->to_date."'
                                      
                                      order by users.id,att_date";



            }
            else{
                
                // this is child department, we need to compare its id with department id
               
                $users = DB::table('users')
                    ->join('clients', 'users.department_id', '=', 'clients.id')
                    ->join('designations','designations.id','=','users.designation_id')
                    ->select('users.id','users.name','designations.title','clients.parent_id as dep_id')
                    ->where('clients.id',$request->deparment_id)
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
                                     and clients.id='".$request->deparment_id."'
                                     and
                                     date(a.datetime) BETWEEN '".$request->from_date."' AND '".$request->to_date."'
                                 
                                    order by users.id,att_date";
            }

            $department_name=$request->deparment_id;
            $time_in = DB::select( DB::raw($emp_status_query));

           
            $emp_att_data = [];
            foreach ($time_in as $tin_data){
                $att_unique_idex = $tin_data->id."--".$tin_data->date_time;
                $emp_att_data[$att_unique_idex][] = $tin_data;
            }

            $emp_status_time_in = $emp_att_data;
            $emp_status_leave= "SELECT 
                                    users.id,time_categories.title,
                                    leave_requests_details.leave_date 
                                FROM  users INNER JOIN leave_requests on users.id=leave_requests.user_id
                                INNER JOIN time_categories on time_categories.id=leave_requests.leave_type_id
                                INNER JOIN clients on clients.id=users.department_id
                                INNER JOIN leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
                                WHERE clients.id='".$request->deparment_id."'
                                and date(leave_requests_details.leave_date) BETWEEN '".$request->from_date."' AND '".$request->to_date."'
                                ";

            $leave_Status = DB::select(DB::raw($emp_status_leave));

            $emp_status_leave_status = collect($leave_Status);
            return view('Reports.monthly_detailed_report',compact(
                    'users',
                    'first_day',
                    'emp_status_time_in',
                    'emp_status_leave_status',
                    'department',
                    'department_name',
                    'from_date',
                    'to_date',
                    'month_year_name',
                                                                 )
            );
        }
        $users='';
        $department_name="";
        $month_year_name="";

        return view('Reports.monthly_detailed_report',compact('users','department','department_name','month_year_name'));

    }
}
