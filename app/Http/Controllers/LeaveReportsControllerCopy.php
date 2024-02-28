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
    // $department=Client::where('parent_id',Session::get('dep_id'))->where('sub_account',1)->get(); 

     return view('Reports.leaves_report',compact('department'));       
    }
    public function leave_report_data(Request $request){
   $users=User::select('id','name')
                     ->where('department_id',$request->id)
                     ->where('status',1)
                     ->where('flag',1)
                     ->get();


              

     return json_encode(['users' => $users]);                                       
    // return Response()->json($time_cat_title);                
  }
    public function leave_reports(Request $request){


     
      $dep_id = Session::get('dep_id');

      $client=Client::where('parent_id',Session::get('dep_id'))->where('sub_account',1)->get(); 
      $data['user']=User::whereHas('departments',function($query) use ($dep_id){
                  return $query->where('parent_id',$dep_id);
                })->where('flag',1)->get();
      //dd($data['user']);

       // $user = User::with(['departments'=>function($query) use ($dep_id){
       //            return $query->where('parent_id',$dep_id);
       //          },])->where('flag',1)->get();


      if($client->isEmpty()){
         $data['department']=Client::where('id',Session::get('dep_id'))->get();
         // $user['user']= User::where('id',Session::get('dep_id'))->get();
         //$user['user']=DB::table('users')->get();
         
      }else{
            $data['department']=Client::where('parent_id',Session::get('dep_id'))->where('sub_account',1)->get();
            //$user['user']=User::where('user_id')->get();
          } 
        if($request->has('from_date') && $request->has('to_date')){
          
          
          $data['from_date']=$request->from_date;
          $data['to_date']=$request->to_date;

          if($request->filter=="dep"){
           
             $data['department_name']=Client::where('id',$request->department_id)
                                         ->first()->name;
                                        
          }
          else{
            
            $data['department_name']=Client::where('id',$request->dep_by_emp)
                                         ->first()->name;
                                       
          }
          
           $client=Client::where('parent_id',$request->department_id)->first();

            if($client==null){
                $data['leave']=TimeCategory::where('department_id',$request->department_id)
                ->where('type',2)
                ->get();
                if($request->filter=="dep"){
                 $data['emp']=User::where('department_id',$request->department_id)->where('status',1)->where('flag',1)->get();
                }else{

                   $data['emp']=User::where('department_id',$request->dep_by_emp)->where('id',$request->user_id)->where('status',1)->where('flag',1)->get();
                }
              
            }
            else{

               
                  $dep_timecat=client::with(['timecategories' => function ($query) {
                         $query->where('type',2); 
                         }])->where('parent_id',$request->department_id)->get(); 
                     $arr = [];
                          foreach ($dep_timecat as $t){
                          foreach ($t['timecategories'] as $m) {
                              $arr[] = $m;
                           }  
                       }
                       if($request->user_id ==''){
                        $client_emp=client::with(['users' => function ($query) {
                         $query->where('status',1)->where('flag',1); 
                         }])->where('parent_id',$request->department_id)->get();

                         $emp_arr = [];
                          foreach ($client_emp as $u){
                          foreach ($u['users'] as $uu) {
                              $emp_arr[] = $uu;
                           }  
                       }
                      }else{
                         
                        $client_emp=client::with(['users' => function ($query) {
                         $query->where('status',1)->where('flag',1)->where('id',$request->user_id); 
                         }])->where('parent_id',$request->department_id)->get();
                      $emp_arr = [];
                          foreach ($client_emp as $u){
                          foreach ($u['users'] as $uu) {
                              $emp_arr[] = $uu;
                           }  
                       }

                      }
                     

                  $data['leave']=$arr;
                  $data['emp']=$emp_arr;
                

            } 
       
            // if($data['leave']->isEmpty()){

            //   $data['leave']="";
            // }
         


          $data['emp_leave']="SELECT leave_requests_details.user_id,leave_requests.leave_type_id,leave_requests.leave_type FROM `leave_requests_details`
                                    inner join leave_requests on leave_requests.id =leave_requests_details.parent_table_id
                                 WHERE leave_date BETWEEN '".$request->from_date."' AND '".$request->to_date."' ";                   
          $data['emp_leave_detail'] =  collect(DB::select(DB::raw($data['emp_leave'])));   
        }
        else{
              
              $data['from_date']="";

              $data['to_date']="";
              $data['department_name']="";
              $data['leave']="";
              $data['emp']="";
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