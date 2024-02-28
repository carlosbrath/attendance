<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Client;
use App\TimeCategory;
use App\User;
use Session;

class LeaveReportsController extends Controller
{
    public function getLeaves(){

     $department=Client::where('parent_id',Session::get('dep_id'))->where('sub_account',1)->get(); 

     return view('Reports.leaves_report',compact('department'));       
    }
    public function leave_reports(Request $request){

      $client=Client::where('parent_id',Session::get('dep_id'))->where('sub_account',1)->get();
      if($client->isEmpty()){
         $data['department']=Client::where('id',Session::get('dep_id'))->get();
      }else{
          $data['department']=Client::where('parent_id',Session::get('dep_id'))->where('sub_account',1)->get();
       } 
        if($request->has('from_date') && $request->has('to_date')){
          $this->validate($request, [
                      'department_id' => 'required',
                      'from_date' => 'required|before:to_date',
                            'to_date'=>'required|after:from_date',
                              ]
              );    
          $data['from_date']=$request->from_date;
          $data['to_date']=$request->to_date;
          $users_on_department =Client::with(['users'=>function($q){
             return $q->where('status',1)->where('flag',1);
          }])->where('id',$request->department_id)->first();
          $data['department_name']=$users_on_department->name;
        
          //  $client=Client::where('parent_id',$request->department_id)->first();
            // if($client==null){
           
            //     $data['leave']=TimeCategory::select('id','title')->where('department_id',Session::get('dep_id'))
            //     ->where('type',2)
            //     ->get()->toArray();
             

            //   array_push($data['leave'],  (array) ['id' => '0000','title'=>'Short Leave' ]);  
            //   $data['emp']=User::where('department_id',$request->department_id)->where('status',1)->where('flag',1)->get();
           
             
            // $t_count_leave_data=[];
            //   if($data['emp']){
            //      foreach($data['emp'] as $user){
            //        $q = 'SELECT * FROM emp_leave_configuration  WHERE emp_id = "'.$user->id.'"';
            //        $record=collect(DB::select(DB::raw($q)));
            //        $leave=[];
            //        foreach($record as $key => $r){
                       
            //             $leave['id']=$r->id; 
            //             $leave['leave_type_id']=$r->leave_type_id; 
            //             $leave['emp_id']=$r->emp_id; 
            //             $leave['total_leaves']=$r->total_leaves; 
                     
            //              array_push($t_count_leave_data, $leave);
                       
            //        }                 
            //      }
            //   }

            //   $data["employee_count_record"] = collect($t_count_leave_data);
                
            // }
            // else{
            //      dd(1);
            //       $dep_timecat=client::with(['timecategories' => function ($query) {
            //              $query->where('type',2); 
            //              }])->where('parent_id',$request->department_id)->get(); 
            //          $arr = [];
            //               foreach ($dep_timecat as $t){
            //               foreach ($t['timecategories'] as $m) {
            //                   $arr[] = $m;
            //                }  
            //            }
            //          $client_emp=client::with(['users' => function ($query) {
            //              $query->where('status',1)->where('flag',1); 
            //              }])->where('parent_id',$request->department_id)->get();
                         
            //           $emp_arr = [];
            //               foreach ($client_emp as $u){
            //               foreach ($u['users'] as $uu) {
            //                   $emp_arr[] = $uu;
            //                }  
            //            }

            //       $data['leave']=$arr;
            //       $data['emp']=$emp_arr;
                

            // } 


         $data['leave']=TimeCategory::select('id','title')->where('department_id',Session::get('dep_id'))
            ->where('type',2)
            ->get()->toArray();

          array_push($data['leave'],  (array) ['id' => '0000','title'=>'Short Leave' ]); 
          if(count($users_on_department->users)>0){
            $data['emp']=$users_on_department->users;
          }else{
            $data['emp']=[];
            // $data['emp']=User::where('department_id',$request->department_id)->where('status',1)->where('flag',1)->get();
          } 
          

        $t_count_leave_data=[];
      
          if($data['emp']){
          
             foreach($data['emp'] as $user){
               $q = 'SELECT * FROM emp_leave_configuration  WHERE emp_id = "'.$user->id.'"';
               $record=collect(DB::select(DB::raw($q)));
               $leave=[];
               foreach($record as $key => $r){

                    $leave['id']=$r->id; 
                    $leave['leave_type_id']=$r->leave_type_id; 
                    $leave['emp_id']=$r->emp_id; 
                    $leave['total_leaves']=$r->total_leaves; 
                     array_push($t_count_leave_data, $leave);
                   
               }                 
             }
          }

          $data["employee_count_record"] = collect($t_count_leave_data);
          

         
          $data['emp_leave']="SELECT  leave_requests_details.user_id,leave_requests.leave_type_id,leave_requests.leave_type, 
                             (SELECT IFNULL(sum(total_leaves),0) as total_leaves FROM emp_leave_configuration total_leaves WHERE emp_id = users.id AND leave_type_id = leave_requests.leave_type_id) as total_leave,
                             (select count(distinct leave_date) as avail_leave from leave_requests_details d join leave_requests r on r.id = d.parent_table_id where date(leave_date) between '".$request->from_date."' AND '".$request->to_date."'  and d.status = 1 and r.leave_type_id = leave_requests.leave_type_id and r.user_id =  users.id) as avail_leave
                              FROM `leave_requests_details`
                                  inner join leave_requests on leave_requests.id =leave_requests_details.parent_table_id
                                  INNER JOIN users on users.id=leave_requests.user_id
          WHERE leave_date BETWEEN '".$request->from_date."' AND '".$request->to_date."'  and  users.department_id='".$request->department_id."'";
         $data['emp_leave_detail'] =  collect(DB::select(DB::raw($data['emp_leave'])));  
            
        }
        else{
              

              $data['from_date']="";
              $data['to_date']="";
              $data['department_name']="";
              $data['leave']="";
              $data['emp']="";
              $data["records"]="";
              $data["employee_count_record"] ="";
              $data['emp_leave_detail']="";
             
         }
        
          return view('Reports.leaves_report',$data);
    }

    public function viewLeave(Request $request){
         
      $leaves['status']="SELECT users.id as 'user_id',users.gender, users.name as 'user_name',designations.title,clients.name,          time_categories.title as 'leave_type',leave_requests.from_date,leave_requests.to_date 
                  FROM `roaster_staffs`  inner JOIN time_categories on roaster_staffs.tcat_id=time_categories.id
               INNER JOIN users on users.id = roaster_staffs.user_id
               INNER JOIN clients on clients.id = users.department_id
               INNER JOIN designations on designations.id=users.designation_id
               LEFT JOIN leave_requests on leave_requests.user_id = users.id
                WHERE time_categories.type='2' and users.id='".$request->id."'";
    $leaves['status_query']=  DB::select(DB::raw($leaves['status']));
    return response()->json($leaves['status_query']);
  


    
    }
}
