<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use DB;
use App\Client;

class hospitalDashboard extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function dashboardView(){
    
      $dep_id=Session::get('dep_id');
     
      $client=Client::where('parent_id',$dep_id)->pluck('id');
      $deparment_ids=[];
      foreach($client as $c){
        $deparment_ids[] = $c;
      }
      
      $users_department_clause = implode(",",$deparment_ids);
  
      $users = "select users.id,users.name,designations.title as designation,users.image,clients.name as department_name,designations.title 
                from users
                inner join clients on clients.id=users.department_id
                inner join designations on designations.id=users.designation_id 
                where 
                   users.status=1
                and 
                   users.flag=1
                and  users.department_id IN   (".$users_department_clause.")"; 
                  // users.department_id IN   (".$users_department_clause.") 
                                      
      $users_data =   collect(DB::select($users));
      $user_ids = implode(",", $users_data->pluck('id')->toArray());
      
      $date='2022-11-14';
      //date('Y-m-d');
      //'2022-11-14';
      //date('Y-m-d');
      $users_attendance_sql = "SELECT 
            a.`user_id`,
            a.`datetime` AS attendance_time,
            a.mark_attendance,
            DATE(a.`datetime`) AS attendance_date
            FROM attendance a WHERE a.`user_id` IN (
            ".$user_ids."
            )
            AND DATE(a.`datetime`)= '".$date."' GROUP BY a.user_id";
    
       $att_recs = collect(DB::select($users_attendance_sql));
       $attach_details_sql = "SELECT distinct user_id FROM 
              `attach_employees_details` 
              where user_id in (".$user_ids.") 
              and date(attach_date) = '".$date."' group by user_id";
       $attach_users = collect(DB::select($attach_details_sql));

       $leave_details = "SELECT lrd.user_id FROM 
                                  `leave_requests_details` lrd
                             JOIN leave_requests lr on lr.id = lrd.parent_table_id
                             where lrd.user_id in (".$user_ids.") 
                             and lr.leave_type = 1
                             and date(lrd.leave_date) = '".$date."' group by lrd.user_id";
                      
       $leave_users = collect(DB::select($leave_details));

       
       $user_info=[];
       $data['present']=[];
       $data['absent']=[];
       $data['attach']=[];
       $data['leave']=[];
     
       foreach($users_data as $key => $u ){
       
          // $att_recs=$att_recs->where('user_id',$u->id);
          if($att_recs->where('user_id',$u->id)->count()>0){
              $attendance['user_id']=$att_recs->where('user_id',$u->id)->first()->user_id;
              $attendance['datetime']=$att_recs->where('user_id',$u->id)->first()->attendance_time;
              $attendance['name']=$u->name;
              $attendance['designation']=$u->designation;
              $attendance['status']='present';
              $attendance['department_name']=$u->department_name;
              $data['present'][]=$attendance;
           
          }
          else if($attach_users->where('user_id',$u->id)->count()>0){
            $attach['user_id']=$u->id; 
            $attach['name']=$u->name;
            $attach['designation']=$u->designation;
            $attach['status']='attach';
            $attach['department_name']=$u->department_name;
            $data['attach'][]=$attach;
          }
         
          else if($leave_users->where('user_id',$u->id)->count()>0){
           
            $leave['user_id']=$leave_users->first()->user_id; 
            $leave['name']=$u->name;
            $leave['designation']=$u->designation;
            $leave['status']='leave';
            $leave['department_name']=$u->department_name;
          
            $data['leave'][]=$leave;
          }
          else{
              $absent['user_id']=$u->id;
              $absent['name']=$u->name;
              $absent['pic']=$u->image;
              $absent['designation']=$u->designation;
              $absent['department_name']=$u->department_name;
              $data['absent'][]= $absent;
          }
        
        }
        return view('hospital_dashboard',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
