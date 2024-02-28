<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\Leave;
use App\leaveRequest;
use App\leaveRequestDetail;
use App\Roster;
use App\TimeCategory;
use App\RoasterStaff;
use DateTime;
use DatePeriod;
use DateInterval;
use Session;
use Alert;
use DB;

class LeaveRequestsController extends Controller
{

  public function index()
  {

    $dep_id=Session::get('dep_id');

    $department_id_unit =Client::select('id')->where('parent_id',$dep_id)->get();
    $id=$department_id_unit->toArray();
    if(Session::get('role_id')==6){
      $leaverequest= leaveRequest::has('user')->where('user_id',Session::get('user_id'))->get();
    }elseif(Session::get('role_id')==1){
      $leaverequest= leaveRequest::has('user')->get();
    }else{
      if($id==null){   
        $leaverequest= leaveRequest::whereHas('user',function($query)use($id){
          return $query->where('department_id',Session::get('dep_id'));
        })->orderBy('id', 'desc')->get();
      }else{   

        $leaverequest= leaveRequest::whereHas('user',function($query)use($id){
          return $query->whereIn('department_id',$id);
        })->orderBy('id', 'desc')->get();
      }     
    }
    $page_title = "Initiate Leave Request";
    return view('LeaveRequests.index',compact('leaverequest','page_title'));

  }

  public function  getEmployee(Request $request){

    $employee=User::select('id','name')
    ->where('department_id',$request->id)
//->where('flag',1)
    ->where('status',1)
    ->orderBy('id', 'desc')
    ->get();

    return json_encode(['employee' => $employee]);                                            
  } 
  public function fetch_leave_type(Request $request){

    $dep_id = Session::get('dep_id');      

    $leave =TimeCategory::where('leave_type',$request->id)->where('department_id', $dep_id)
    ->get();
    return json_encode(['leave' => $leave,'id'=>$request->id,'dep_id'=>$dep_id]);                         

  }
  public function fetch_total_leave(Request $request){

    $start_date_of_year  = date('Y-m-d', strtotime('first day of january this year'));
    $end_date_of_year    = date('Y-m-d', strtotime('last day of december this year'));
    $leave_id =  $request->leave_id;
    if($request->leave_id == "Please Select..."){
      $leave_id = 0;
    }       

    $total_variable_leaves_sql = "SELECT IFNULL(sum(total_leaves),0) as total_leaves FROM emp_leave_configuration total_leaves WHERE emp_id = ".$request->id." AND leave_type_id = ".$leave_id;

    $emp_leave="select count(distinct leave_date) as avail_leave 
    from leave_requests_details d 
    join leave_requests r on r.id = d.parent_table_id 
    where date(leave_date) between '".$start_date_of_year."' and '".$end_date_of_year."' and 
    d.status = 1 and r.leave_type_id = '".$request->leave_id."' and r.user_id = '".$request->id."'";


    $check_employee_leave= collect(DB::select( DB::raw($emp_leave)));
    $total_variable_leaves = collect(DB::select( DB::raw($total_variable_leaves_sql)));

    return json_encode(['total_leave'=>$total_variable_leaves[0]->total_leaves,'check_emp_leave'=> $check_employee_leave[0]->avail_leave]);                  

  } 

  public function create()
  {

    $dep_id = Session::get('dep_id');


    $department=Client::where('parent_id',$dep_id)->whereIn('sub_account',[1,2])->get();

    if($department->isEmpty()){

      $department=Client::where('id',$dep_id)->first();
      $dep_id = $department->parent_id;

      $department=Client::where('parent_id',$dep_id)->whereIn('sub_account',[1,2])->get();


    }
          
    if(Session::get('role_id')==1){       
      $user = client::get();
    }
    else
    {

      $user   = client::with(['users' => function($q){
        return $q->where('flag',1);
      }])->where('parent_id',$dep_id)->get();

    }
    $page_title = "Initiate Leave Request";
    return  view('LeaveRequests.create',compact('page_title','user','department'));
  }    
  public function store(Request $request)
  {

    $leave=new leaveRequest();
    $data=$request->all();

    if(Session::get('role_id')==6)
    {
      dd('It looks suspicious, Please contact with Syed fakhar in case of any issue.');
      if ($request->get('action') == 'save') {

        if($request->application_letter!=''){
          $file = $request->file('application_letter');
          $extension = $request->application_letter->extension(); 
          $filename =   time() . '.' . $file->getClientOriginalExtension();
          $destination_path=public_path('/photos');
          $file->move($destination_path,$filename);
          $leave->application_letter=$filename;
        }

        $leave->user_id=$data['user'];
        $leave->leave_type_id=$data['leave_type'];
        $leave->from_date=$data['date_from'];
        $leave->to_date=$data['date_to'];
        $leave->time_from=$data['time_from'];
        $leave->time_to=$data['time_to']; 
        $leave->status=1; 
        if($leave->save())
        {
          $this->leave_approved($leave->id);
          Alert::success('Record Submit Successfully ! Thank You');
          return redirect('leaverequests');
        } 
      }
      elseif ($request->get('action') == 'save_and_close') {

        if($request->application_letter!=''){
          $file = $request->file('application_letter');
          $extension = $request->application_letter->extension(); 
          $filename =   time() . '.' . $file->getClientOriginalExtension();
          $destination_path=public_path('/photos');
          $file->move($destination_path,$filename);
          $leave->application_letter=$filename;
        }
        $leave->user_id=$data['user'];
        $leave->leave_type_id=$data['leave_type'];
        $leave->from_date=$data['date_from'];
        $leave->to_date=$data['date_to'];
        $leave->time_from=$data['time_from'];
        $leave->time_to=$data['time_to']; 
        $leave->status=1; 
        if($leave->save())
        {
          $this->leave_approved($leave->id);
          Alert::success('Record Submit Successfully ! Thank You');
          return redirect('leaverequests/create')->with('msg','Record Inserted Successfully ! Thank you');
        }   
      }
    }
    else{

      if($request->get('action') == 'save') {

        $leave->user_id=$data['user'];
        $leave->leave_type_id=$data['leave_type'];
        $leave->from_date=$data['date_from'];
        $leave->to_date=$data['date_to'];
        if($request->leave_type==1){
          $this->validate($request, 
            [  

              'deparment_id'=>'required',
              'date_from' => 'required|date:' . now()->toDateString(),
              'date_to'=>'required|date:' . now()->toDateString(),
              'time_to'=>'required',
              'time_from'=>'required'                                           
            ]);
        }else{

          $this->validate($request, 
            [   
              'leave' => 'required',
              'leave_nature' => 'required',
              'deparment_id'=>'required',
              'date_from' => 'required|date:' . now()->toDateString(),
              'date_to'=> 'required|date:' . now()->toDateString(),                             
            ]);

        }

        if($request->application_letter!=''){

          $file = $request->file('application_letter');
          $extension = $request->application_letter->extension(); 
          $filename =   time() . '.' . $file->getClientOriginalExtension();
          $destination_path=public_path('/photos');
          $file->move($destination_path,$filename);
          $leave->application_letter=$filename;
        }

        $leave->time_from=$data['time_from'];
        $leave->time_to=$data['time_to'];
        $leave->leave_type_id=$data['leave'];
        $leave->leave_fix_var=$data['leave_nature']; 
        $leave->leave_type=$data['leave_type'];
        $leave->remarks=$data['remarks'];
        $leave->status=1;

        if($leave->save())
        {
          $this->leave_approved($leave->id);
          Alert::success('Record Submit Successfully ! Thank You');
          return redirect('leaverequests');

        }

      }
      elseif ($request->get('action') == 'save_and_close') {

        $leave->user_id=$data['user'];
        $leave->leave_type_id=$data['leave_type'];
        $leave->from_date=$data['date_from'];
        $leave->to_date=$data['date_to'];
        if($request->leave_type==1){
          $this->validate($request, 
            [  

              'deparment_id'=>'required',
              'date_from' => 'required|date|after_or_equal:' . now()->toDateString(),
              'date_to'=>'required|date|after_or_equal:' . now()->toDateString(),
              'time_to'=>'required',
              'time_from'=>'required'                                           
            ]);
        }else{

          $this->validate($request, 
            [   
              'leave' => 'required',
              'leave_nature' => 'required',
              'deparment_id'=>'required',
              'date_from' => 'required|date|after_or_equal:' . now()->toDateString(),
              'date_to'=>'required|date|after_or_equal:' . now()->toDateString(),
              'user' => 'required'

            ]);

        }
        if($request->application_letter!=''){
          $file = $request->file('application_letter');
          $extension = $request->application_letter->extension(); 
          $filename =   time() . '.' . $file->getClientOriginalExtension();
          $destination_path=public_path('/photos');
          $file->move($destination_path,$filename);
          $leave->application_letter=$filename;
        }
        $leave->from_date=$data['date_from'];
        $leave->to_date=$data['date_to'];
        $leave->time_from=$data['time_from'];
        $leave->leave_fix_var=$data['leave_nature']; 
        $leave->time_to=$data['time_to']; 
        $leave->leave_type_id=$data['leave']; 
        $leave->leave_type=$data['leave_type']; 
        $leave->remarks=$data['remarks'];
        $leave->status=1;
        if($leave->save())
        {   
          $this->leave_approved($leave->id);        
          return redirect('leaverequests/create')->with('msg','Record Submit Successfully ! Thank You');
        }
      }
    }
  }


  public function show($id)
  {    
    $leaverequest = leaveRequest::find($id);
    $user = User::find($leaverequest->user_id); 
    $page_title="Leave Request";

    return view('LeaveRequests.show',compact('leaverequest','user','page_title'));
  }


  public function edit($id)
  { 

    $dep_id=Session::get('dep_id');
    $leave=TimeCategory::select('id','title','leave_type')
    ->where('type',2)
    ->where('department_id',Session::get('dep_id'))
    ->get();
    $department=Client::where('parent_id',$dep_id)->where('sub_account',1)->get();
    if($department->isEmpty()){
      $department=Client::where('id',$dep_id)->where('status',1)->get();
    }
    $leaverequest=leaveRequest::find($id);
    $dep_id = Session::get('dep_id');
    if(Session::get('role_id')==1) {     
      $user=client::with('users')->get();
    }
    else{

      $user=client::with('users')->where('parent_id',$dep_id)->get();

    }
    $page_title = "Initiate Leave Request";  
    return view('LeaveRequests.edit',compact('leaverequest','user','leave','department','page_title'));
  }

  public function update(Request $request, $id)
  {

    if($request->leave_type==1){
      $this->validate($request, ['date_from' => 'required','date_to'=>'required','time_from' => 'required','time_to'=>'required','user_id'=>'required','deparment_id'=>'required']);
    }
    else{
      $this->validate($request, ['date_from' => 'required','date_to'=>'required','leave'=>'required','user_id'=>'required','deparment_id'=>'required']);
    }
    $leaverequest=leaveRequest::find($id);
    $data=$request->all();
    $leaverequest->user_id=$data['user_id'];
    $leaverequest->from_date=$data['date_from'];
    $leaverequest->to_date=$data['date_to'];
    $leaverequest->time_from=$data['time_from'];
    $leaverequest->leave_type_id=$data['leave'];
    $leaverequest->time_to=$data['time_to'];
    $leaverequest->time_from=$data['time_from'];
    $leaverequest->time_to=$data['time_to'];
    if($leaverequest->save()){
      Alert::success('Record Update Successfully ! Thank You');
      return redirect('leaverequests');             
    }
  }

  public function leave_revert($id){
    $leave_request_detail=DB::table('leave_requests_details')->where('parent_table_id',$id)->first();
    if($leave_request_detail==null){    
      DB::table('leave_requests')->where('id',$id)->delete();
      Alert::success('Leave Revert Successfully ! Thank You');
      return redirect('leaverequests');
    }
    else{

      DB::table('leave_requests_details')->where('parent_table_id',$id)->delete();
      DB::table('leave_requests')->where('id',$id)->delete();
      Alert::success('Leave Revert Successfully ! Thank You');
      return redirect('leaverequests');
    }
  }
  public function destroy($id)
  {
    $leave_request=leaveRequest::find($id);
    $leave_request->delete();
    return redirect('leaverequests')->with('message','Record Deleted Successfully');
  }

  public function leave_approved($id){
    $leaverequest=leaveRequest::find($id);
    $leaverequest->status=1;
    $leaverequest->save();
    if($leaverequest->status=1)
    {
      $from_date= $leaverequest->from_date;
      $to_date= $leaverequest->to_date;
      $begin = new DateTime($from_date);
      $end = new DateTime($to_date);
      $end = $end->modify( '+1 day' );
      $interval = new DateInterval('P1D');
      $daterange = new DatePeriod($begin, $interval ,$end);
      foreach($daterange as $date){
        $date->format("Y-m-d");
        $leave_request_detail=new leaveRequestDetail();
        $leave_request_detail->leave_date= $date->format("Y-m-d"); 
        $leave_request_detail->user_id=$leaverequest->user_id;
        $leave_request_detail->time_from= $leaverequest->time_from;
        $leave_request_detail->time_to= $leaverequest->time_to;
        $leave_request_detail->status=$leaverequest->status;
        $leave_request_detail->parent_table_id=$leaverequest->id;
        $leave_request_detail->save();


        if(RoasterStaff::where('user_id',$leaverequest->user_id)->where('date',$date->format("Y-m-d"))->delete()){        
          $month = date("m",strtotime($date->format("Y-m-d")));
          $year = date("Y",strtotime($date->format("Y-m-d")));
          RoasterStaff::create(['user_id'=>$leaverequest->user_id,'tcat_id'=> $leaverequest->leave_type_id ,'month'=> $month,'year'=> $year , 'date' => $date->format("Y-m-d")  ]);

        }   

      }

    }

    return redirect('leaverequests')->with('message','Leave Approved Successfully');
  }

  public function leave_reject($id){
    $leaverequest=leaveRequest::find($id);
    $leaverequest->status=2;
    $leaverequest->save();
    return redirect('leaverequests')->with('message','Leave Rejected');

  }
}
?>