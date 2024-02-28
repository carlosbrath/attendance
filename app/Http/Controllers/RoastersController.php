<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Roaster;
use App\Designation;
use App\TimeCategory;
use App\RoasterStaff;
use App\User;
use App\Client;
use App\Leave;
use App\RosterDetail;
use App\leaveRequest;
use DB;
use Alert;
use Session;
use DatePeriod;
use DateTime;
use DateInterval;
class RoastersController extends Controller
{
    public function index(){

     if(Session::get('role_id')==1){
        $roasters = Client::with('roaster')->get();
     }else{
        $roasters=Client::with('roaster')
                ->where('parent_id',Session::get('dep_id'))
                ->get();
        if($roasters->isEmpty()){
          $roasters=Client::with('roaster')
                ->where('id',Session::get('dep_id'))
                ->get();
        }        
     }
     $data['page_title'] = "Duty Rosters/Schedules";
     $data['roasters'] = $roasters;
     return view('Roasters.index', $data);
    }

    public function create()
    {
       $dep_id = Session::get('dep_id');
        if(Session::get('role_id')==1){
            $department=Client::where('parent_id',0)->get();
        }elseif (Session::get('role_id')==5) {
          $department=Client::where('parent_id',Session::get('dep_id'))->where('sub_account',1)->get();
          if($department->isEmpty()){
            $department=Client::where('id',Session::get('dep_id'))->where('status',1)->get();
          }
        }else{
          $department=Client::where('parent_id',Session::get('dep_id'))->where('sub_account',1)->get();
          if($department->isEmpty()){
            $department=Client::where('id',Session::get('dep_id'))->where('status',1)->get();
          }
        }
        $data['page_title'] = "Add New Duty Schedule";
        $data['department'] = $department;
        return view('Roasters.create',$data);
    }


    public function store(Request $request){
     
        $dep_id = Session::get('dep_id'); 
        $this->validate($request, ['month'=>'required','deparment_id'=>'required']);
        $roaster= new Roaster();
         $users=User::select('id','name')->where('department_id',$request->deparment_id)->get();
        if ($request->get('action') == 'save'){ 
                $roaster->title='';
                $roaster->department_id=$request->deparment_id;
                $roaster->month=date("m", strtotime($request->month));
                $roaster->year=date("Y", strtotime($request->month));
        if($roaster->save()){
                Alert::success('Record Submit Successfully ! Thank You');
                return redirect('roasters');              
             }
          } 
         elseif($request->get('action') == 'save_and_close') {

                $roaster->title=$request->title;
                $roaster->department_id=$request->deparment_id;
                $roaster->month= date("m", strtotime($request->month));
                $roaster->year=$request->year;   
              if($roaster->save()){
                  return redirect('roasters/create')->with('msg','Record Inserted Successfully ! Thank you');
              }
               

         } 
             
      
    }

    public function show($id){
        $roaster=Roaster::find($id);
        $data['page_title'] = "Configure Duties for Employees";
        $data['roaster'] = $roaster;
        return view('Roasters.show',$data); 
    }

    public function edit($id){
        $roaster=Roaster::find($id);
        if(Session::get('role_id')==1){
        $department=Client::where('parent_id',0)->get();
        }
        elseif (Session::get('role_id')==5) {
          $department=Client::where('parent_id',Session::get('dep_id'))->where('sub_account',1)->get();
          if($department->isEmpty()){
            $department=Client::where('id',Session::get('dep_id'))->where('status',1)->get();
          }
        }
        return view('Roasters.edit',compact('roaster','department'));
    }

    public function update(Request $request, $id){
        $roaster=Roaster::find($id);
        $roaster->title=$request->title; 
        $roaster->month= date("m", strtotime($request->month));
        $roaster->year=$request->year;
        if($roaster->save()){
        Alert::success('Record Update Successfully ! Thank You');
        return redirect('roasters');
        }
    }

     public function destroy($id){
        $module=Roaster::find($id);
        $module->delete();
        return redirect('roasters')->with('message','Record Deleted Successfully');
      }

    public function roaster_assign($id){
        set_time_limit(0);
        $dep_id=Session::get('dep_id');
        $roster = Roaster::findOrFail($id);
        $timecategory=TimeCategory::where(function($q){
              $q->where('time_cat_type',2)->orWhere('type',2);
            })->where('department_id',Session::get('dep_id'))->get();

    
        $users=User::select('id','name')
               ->where('department_id',$roster->department_id)
               ->where('status',1)->where('flag',2)->get();

         
        $roaster_staff = RoasterStaff::with(['user','tcat'])->where('roster_id',$id)->get();
        $dept_tcats = TimeCategory::where('department_id',Session::get('dep_id'))->get(); //load departmetn categories
        
        $att_sql = "select 
                          date(a.datetime) as checkdate,
                          a.datetime as checktime,
                          a.user_id 
                  from attendance a 
		              JOIN roster_details rd on rd.user_id = a.user_id
                  join time_categories tc on tc.id = rd.tcat_id
                  where rd.roster_id  =".$id."
                  and date(a.datetime) between '".$roster->year."-".$roster->month."-01' and '".$roster->year."-".$roster->month."-31'
                  group by a.datetime";
        
        $roster_details_sql = "select rd.tcat_id,rd.user_id,rd.tcat_from_date,rd.tcat_to_date,tc.time_cat_type,tc.title,tc.type 
        from roster_details rd
        join time_categories tc on tc.id = rd.tcat_id
    where rd.roster_id = ".$id; 

        $leave_of_user_roster = "SELECT 
                                       leave_requests_details.user_id,
                                       leave_requests_details.leave_date,
                                       users.department_id 
                                  from leave_requests_details 
                                  inner join users
                                  on leave_requests_details.user_id =  users.id 
                                  where users.department_id='".$roster->department_id."'";

        $attach_of_user_roster = "SELECT 
                                       attach_employees_details.user_id,
                                       attach_employees_details.attach_date,
                                       users.department_id 
                                  from attach_employees_details 
                                  inner join users
                                  on attach_employees_details.user_id =  users.id 
                                  where users.department_id='".$roster->department_id."' and attach_employees_details.deleted_at is null";

       $close_icon_for_leav= "SELECT leave_requests.from_date,leave_requests.to_date,leave_requests.user_id,users.department_id  from leave_requests INNER JOIN users on users.id = leave_requests.user_id
       INNER join clients on users.department_id=clients.id
      WHERE leave_requests.status=1 and  users.department_id='".Session::get('dep_id')."'";
      $leaves_icon = collect(DB::select($close_icon_for_leav));                       
      $roster_leaves = collect(DB::select($leave_of_user_roster));                    
      $roster_attachs = collect(DB::select($attach_of_user_roster));                                   
      $attendance = collect(DB::select($att_sql));
      $roster_user_tcats = collect(DB::select($roster_details_sql));   
      $roster_details = RosterDetail::where('roster_id',$id)->get();
      $data['page_title'] = "Configure Duties for Employees";
      $data['roster'] = $roster;
      $data['users'] = $users;
      $data['timecategory'] = $timecategory;
      $data['roaster_staff'] = $roaster_staff;
      $data['roster_details'] = $roster_details;
      $data['dept_tcats'] = $dept_tcats;
      $data['attendance'] = $attendance;
      $data['roster_user_tcats'] = $roster_user_tcats;
      $data['roster_leaves'] = $roster_leaves;
      $data['roster_attachs'] = $roster_attachs;
      $data['leaves_icon'] = $leaves_icon;
      return view('Roasters.roaster_assign',$data);
       
    }
    public function roster_detail(Request $request){
    
        $roster_detail=DB::table('roster_details')
                    ->where('tcat_from_date',$request->tcat_from_date)
                    ->where('tcat_to_date',$request->tcat_to_date)
                    ->where('user_id',$request->user_id)
                    ->where('roster_id',$request->roster_id)->count();
        $period = new DatePeriod(new DateTime($request->tcat_from_date), new DateInterval('P1D'), new DateTime($request->tcat_to_date.' +1 day'));
        if($roster_detail > 0){    
            $msg='fail';
            return Response()->json($msg);
        }else{
             $msg='success';  
                foreach ($period as $date) {
                    RosterDetail::create([
                        'tcat_id' => $request->tcat_id,
                        'tcat_from_date' => $date->format("Y-m-d"),
                        'tcat_to_date' => $date->format("Y-m-d"),
                        'user_id' => $request->user_id,
                        'roster_id' => $request->roster_id
                    ]);
                }
             return Response()->json($msg);
        }
    }
    public function roster_delete(Request $request){
      
     $roster_detail= RosterDetail::select('tcat_to_date')->where('tcat_from_date',$request->tcat_from_date)
                                   ->where('user_id',$request->user_id)->first();
      
      DB::select(DB::raw("delete from roster_details where tcat_from_date = '".$request->tcat_from_date."' 
                          and tcat_to_date = '".$roster_detail->tcat_to_date."' 
                          and user_id ='".$request->user_id."'")); 
      $flag='success';
       return Response()->json($flag);

    }
    public function roster_leave_delete(Request $request){
       
       $roster_leave= leaveRequest::select('to_date','id')
                      ->where('from_date',$request->from_date)
                      ->where('user_id',$request->user_id)->first();
                      
      DB::select(DB::raw("delete from leave_requests where from_date = '".$request->from_date."' 
                          and to_date = '".$roster_leave->to_date."' 
                          and user_id ='".$request->user_id."'")); 
      DB::select(DB::raw("delete from leave_requests_details where parent_table_id = '". $roster_leave->id."'"));  
                        
      $flag='success';
      return Response()->json($flag);  
    }
    public function assigntime(Request $request){
    $data = explode('-',$request->timecate_val);
    $tcat_id=$data[0];
    $user_id=$data[1]; 
    $roster = Roaster::findOrFail($request->id);
    $month= $roster->month;
    $year=$roster->year;
    $day=$request->date;
    $date=$year.'-'.$month.'-0'.$day;
    // $check_hours = DB::table('time_categories')
    //                  ->select('hours')
    //                  ->where('id',$tcat_id)->first();
    $roasterstaff_delete =RoasterStaff::where('user_id',$user_id)->where('date',$date)->first();
    if($roasterstaff_delete !='')
       {
             DB::table('roaster_staffs')
                ->where('user_id', $roasterstaff_delete->user_id)
                ->where('date',$roasterstaff_delete->date)
                ->where('tcat_id',$roasterstaff_delete->tcat_id)
                ->update(['tcat_id' => $tcat_id]);   
        }

   else{

            $roasterstaff =new RoasterStaff();
            $roasterstaff->tcat_id = $tcat_id;
            $roasterstaff->user_id = $user_id;
            $roasterstaff->month= $month;
            $roasterstaff->year= $year;
            $roasterstaff->date=$date;
            $roasterstaff->roster_id=$request->id; 
            $roasterstaff->save();            
            // return response()->json($date);
          
      }
}
  public function  filterRoaster(){
    $dep_id=Session::get('dep_id');
    $department=Client::where('parent_id',Session::get('dep_id'))->get(); 
    return view('Roasters.roaster_filter',compact('dep_id','department'));
   }

   public function copy_to_next_month($id){
        $id = \Crypt::decrypt($id);
        $old_roster = Roaster::find($id);
        $month = $old_roster->month;
        $year = $old_roster->year;

        if($month == '12'){
            $next_month = '01';
            $next_year = $year+1;
        }else{
            $next_month = $month+1;
            $next_year  = $year;
        }
        $new_roster = Roaster::create([
            'department_id' => $old_roster->department_id,
            'title' => $old_roster->title."-Month ".$next_month,
            'month' => $next_month,
            'year' => $next_year
        ]);    

        $old_roster_details = RosterDetail::where('roster_id',$old_roster->id)->get();
        foreach($old_roster_details as $key=>$val){
            $user_id = $val->user_id;
            $tcat_id = $val->tcat_id;
            $roster_id = $new_roster->id;
            $tcat_from_date = $next_year."-".$next_month."-".date("d",strtotime($val->tcat_from_date));
            $tcat_to_date = $next_year."-".$next_month."-".date("d",strtotime($val->tcat_to_date));

            RosterDetail::create([
                'user_id' => $user_id,
                'tcat_id' => $tcat_id,
                'roster_id' => $roster_id,
                'tcat_from_date' => $tcat_from_date,
                'tcat_to_date' => $tcat_to_date
            ]);
        }

        Alert::success('Roster Copied to next month Successfully!');
        return redirect()->back();
   }
}