<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\AttachEmployee;
use App\User;
use App\AttachEmployeeDetail;
use App\Client;
use DateTime;
use DatePeriod;
use DateInterval;
use Alert;
use Session;
use DB;

class AttachEmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

     if(Session::get('role_id')==1){
     
       $attachemployees=AttachEmployee::with('user')->get();
       
     }
     else{
    
      $flag="";
      $client_name=Client::where('parent_id',Session::get('dep_id'))->first();
     
      if($client_name==null){
        $users =""; 
        $client_name=Client::where('id',Session::get('dep_id'))->first();
        
        $fetch_user="SELECT id FROM users u WHERE u.department_id IN(SELECT id FROM clients WHERE parent_id='".$client_name->parent_id."')";
        $fetch_user_quer = collect(DB::select( DB::raw($fetch_user))); 
        $ids = $fetch_user_quer->pluck('id')->toArray();
       
        $attachemployees=AttachEmployee::whereIn('user_id',$ids)->get();  
       
      }
      else{
       
        $client_check_sub_child=Client::where('parent_id',$client_name->id)->first();
        if($client_check_sub_child==null){
         
         
          $dep_id=Session::get('dep_id');
          $department_id_unit =Client::select('id')->where('parent_id',$dep_id)->get();
          $id=$department_id_unit->toArray();
          
          $attachemployees=AttachEmployee::whereHas('user',function($query)use($id){
           $query->whereIn('department_id',$id);
         })->get();

          
        }
        else{

       
         $fetch_user="SELECT id FROM users u WHERE u.department_id IN(SELECT id FROM clients WHERE parent_id='".Session::get('dep_id')."')";
         $fetch_user_quer = collect(DB::select( DB::raw($fetch_user))); 
         $ids = $fetch_user_quer->pluck('id')->toArray();

         $attachemployees=AttachEmployee::where('status',0)->whereIn('user_id',$ids)->get();  
         
       }

     }    
   }
   $page_title = "Employees Attachment";  
   return view('attachemployees.index',compact('attachemployees','flag','page_title'));
 }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
   
     $dep_id = Session::get('dep_id');
     $department=Client::where('parent_id',$dep_id)->whereIn('sub_account',[1,2])->get();
     if($department->isEmpty()){
         $department=Client::where('id',$dep_id)->first();
         $dep_id = $department->parent_id;
         $department=Client::where('parent_id',$dep_id)->whereIn('sub_account',[1,2])->get();
    }
    if(Session::get('role_id')==1)
    {
     
      $user=client::get();

    }
    else
    {

     $user=client::with(['users'=>function($q){
      return $q->where('flag',1);
    }])->where('parent_id',$dep_id)->get();
     
   }
   $page_title = "Add Employee Attachment";  
   return view('attachemployees.create',compact('user','department','page_title'));
 }

 
 public function store(Request $request)
 {
  
  $this->validate($request, [
    'user_id' => 'required',
    'from_date' => 'required|date|after_or_equal:' . now()->toDateString(),
    'deparment_id'=>'required'    
  ]);
  
  if ($request->get('action') == 'save') {
    $attachemployee=new AttachEmployee();
    $attachemployee->user_id=$request->user_id;
    $attachemployee->from_date=$request->from_date;
    $attachemployee->to_date=$request->to_date;
    if($request->attachment_status == 1){
          $attachemployee->to_date= $attachemployee->from_date; 
    }
    $attachemployee->time_from=$request->time_from;
    $attachemployee->time_to=$request->time_to;
    if($request->attachment_status==1){
      $this->validate($request, [
        'time_from' => 'required',
        'time_to' => 'required'
      ]);   
    }
    $attachemployee->attachment_status=1;
    if($request->application_letter!=''){
      $file = $request->file('application_letter');
      $extension = $request->application_letter->extension(); 
      $filename =   time() . '.' . $file->getClientOriginalExtension();
      $destination_path=public_path('/photos');
      $file->move($destination_path,$filename);
      $attachemployee->application_letter=$filename;
    }
    $attachemployee->remarks=$request->remarks;
    $attachemployee->custom_sms=$request->custom_sms;
    $attachemployee->status=1;
    if($attachemployee->save()){
   
       $from_date= $attachemployee->from_date;
       $to_date= $attachemployee->to_date;
       if($request->attachment_status == 1){
          $to_date= $attachemployee->from_date; 
       }
       $begin = new DateTime($from_date);
       $end = new DateTime($to_date);
       $end = $end->modify( '+1 day' );
       $interval = new DateInterval('P1D');
       $daterange = new DatePeriod($begin, $interval ,$end);
       $dat_val=[];
       foreach($daterange as $date){
        $attact_emp_detail=new AttachEmployeeDetail();
        $attact_emp_detail->attach_date= $date->format("Y-m-d"); 
        $attact_emp_detail->user_id=$attachemployee->user_id;
        $attact_emp_detail->from_date=$attachemployee->from_date;
        $attact_emp_detail->to_date=$attachemployee->from_date;
        $attact_emp_detail->time_from= $attachemployee->time_from;
        $attact_emp_detail->time_to= $attachemployee->time_to;
        $attact_emp_detail->status=$attachemployee->status;
        $attact_emp_detail->parent_table_id=$attachemployee->id;
        $attact_emp_detail->save();
      }

      Alert::success('Record Submit Successfully ! Thank You');
      return redirect('attachemployees');
    
  }}
  if($request->get('action') == 'save_and_close') {
    $attachemployee=new AttachEmployee();
    $attachemployee->user_id=$request->user_id;
    $attachemployee->from_date=$request->from_date;
    $attachemployee->to_date=$request->to_date;
    if($request->attachment_status == 1){
      $attachemployee->to_date = $attachemployee->from_date; 
    }
    $attachemployee->time_from=$request->time_from;
    $attachemployee->time_to=$request->time_to;
        // $attachemployee->time_from=$request->time_from;
        // $attachemployee->time_to=$request->time_to;
    if($request->application_letter!=''){

      $file = $request->file('application_letter');
      $extension = $request->application_letter->extension(); 
      $filename =   time() . '.' . $file->getClientOriginalExtension();
      $destination_path=public_path('/photos');
      $file->move($destination_path,$filename);
      $attachemployee->application_letter=$filename;
    }
    $attachemployee->remarks=$request->remarks;
    $attachemployee->custom_sms=$request->custom_sms;
    $attachemployee->status=1;
    if($attachemployee->save()){

       $from_date= $attachemployee->from_date;
       $to_date= $attachemployee->to_date;
       $begin = new DateTime($from_date);
       $end = new DateTime($to_date);
       $end = $end->modify( '+1 day' );
       $interval = new DateInterval('P1D');
       $daterange = new DatePeriod($begin, $interval ,$end);
       $dat_val=[];
       foreach($daterange as $date){
        $attact_emp_detail=new AttachEmployeeDetail();
        $attact_emp_detail->attach_date= $date->format("Y-m-d"); 
        $attact_emp_detail->user_id=$attachemployee->user_id;
        $attact_emp_detail->from_date=$attachemployee->from_date;
        $attact_emp_detail->to_date=$attachemployee->from_date;
        $attact_emp_detail->time_from= $attachemployee->time_from;
        $attact_emp_detail->time_to= $attachemployee->time_to;
        $attact_emp_detail->status=$attachemployee->status;
        $attact_emp_detail->parent_table_id=$attachemployee->id;
        $attact_emp_detail->save();
      }
      return redirect('attachemployees/create')->with('msg','Record Submit Successfully');
    }
  }
}


public function show($id)
{

  $attach_emp=AttachEmployee::find($id);
  $user=User::find($attach_emp->user_id);
  $page_title = "Attach Employee Details";
  return view('AttachEmployees.show',compact('attach_emp','user','page_title'));

}


public function edit($id)
{   
  
  $dep_id=Session::get('dep_id');
  $department=Client::where('parent_id',$dep_id)->where('sub_account',1)->get();
  if($department->isEmpty()){
    $department=Client::where('id',$dep_id)->where('status',1)->get();
  }
  $attachemployee=AttachEmployee::find($id);
  $user=client::with('users')->where('parent_id',$dep_id)->get();
  $page_title = "Update Employee Attachment";  
  return view('attachemployees.edit',compact('attachemployee','user','department','page_title'));
}


public function update(Request $request, $id)
{
  $attachemployee=AttachEmployee::find($id);
  $attachemployee->user_id=$request->user_id;
  $attachemployee->from_date=$request->from_date;
  $attachemployee->to_date=$request->to_date;
  $attachemployee->time_from=$request->time_from;
  $attachemployee->time_to=$request->time_to;
  $file = $request->file('application_letter');
  $extension = $request->application_letter->extension(); 
  $filename =   time() . '.' . $file->getClientOriginalExtension();
  $destination_path=public_path('/photos');
  $file->move($destination_path,$filename);
  $attachemployee->application_letter=$filename;
  $attachemployee->remarks=$request->remarks;
  $attachemployee->custom_sms=$request->custom_sms;
  if($attachemployee->save()){
    Alert::success('Record Update Successfully ! Thank You');
    return redirect('attachemployees');
  }
}


public function destroy($id)
{
  $attachemployee=AttachEmployee::find($id);
  $attachemployee->delete();
  //delete its child table entries 
  AttachEmployeeDetail::where('parent_table_id',$id)->delete();

  return redirect('attachemployees')->with('message','Record deleted successfully');
}
public function attach_approvel($id){
  $attachemployee=AttachEmployee::find($id);
  $attachemployee->status=1;
  $attachemployee->save();
  if($attachemployee->status=1)
  {
   
   $from_date= $attachemployee->from_date;
   $to_date= $attachemployee->to_date;

   
   $begin = new DateTime($from_date);
   $end = new DateTime($to_date);
   $end = $end->modify( '+1 day' );
   $interval = new DateInterval('P1D');
   $daterange = new DatePeriod($begin, $interval ,$end);
   $dat_val=[];
   foreach($daterange as $date){
    $attact_emp_detail=new AttachEmployeeDetail();
    $attact_emp_detail->attach_date= $date->format("Y-m-d"); 
    $attact_emp_detail->user_id=$attachemployee->user_id;
    $attact_emp_detail->from_date=$attachemployee->from_date;
    $attact_emp_detail->to_date=$attachemployee->from_date;
    $attact_emp_detail->time_from= $attachemployee->time_from;
    $attact_emp_detail->time_to= $attachemployee->time_to;
    $attact_emp_detail->status=$attachemployee->status;
    $attact_emp_detail->save();
  }
  
  




  
       // $period = new DatePeriod(
       //   new DateTime($from_date),
       //   new DateInterval('P1D'),
       //   new DateTime($to_date)
       // );

       // foreach ($period as $key => $value) {
       //  $attact_emp_detail=new AttachEmployeeDetail();
       //  $attact_emp_detail->attach_date= $value->format('Y-m-d'); 
       //  $attact_emp_detail->user_id=$attachemployee->user_id;
       //  $attact_emp_detail->from_date=$attachemployee->from_date;
       //  $attact_emp_detail->to_date=$attachemployee->from_date;
       //  $attact_emp_detail->time_from= $attachemployee->time_from;
       //  $attact_emp_detail->time_to= $attachemployee->time_to;
       //  $attact_emp_detail->attach_type_id= $attachemployee->id;
       //  $attact_emp_detail->status=$attachemployee->status;
       //  $attact_emp_detail->save();

       // }
}
return redirect('attachemployees')->with('message','Attach is Approved');

}

public function attach_rejected($id){
 $attachemployee=AttachEmployee::find($id);
 $attachemployee->status=2;
 $attachemployee->save();
 return redirect('attachemployees')->with('message','Attach Rejected');
}
}