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
use DB;
use Alert;
use Session;

class RoastersController extends Controller
{
    public function index(){
     if(Session::get('role_id')==1){
        $roasters = Client::with('roaster')->get();
     }else{
        $roasters=Client::with('roaster')
                ->where('parent_id',Session::get('dep_id'))
                ->get();
     }
     return view('Roasters.index', compact('roasters'));
    }

    public function create()
    {
       $dep_id = Session::get('dep_id');
       if(Session::get('role_id')==1){
        $department=Client::where('parent_id',0)->get();
        }elseif (Session::get('role_id')==5) {
        $department=Client::where('parent_id',Session::get('dep_id'))->get();
        }
        return view('Roasters.create',compact('department'));
    }


    public function store(Request $request){
        $dep_id = Session::get('dep_id'); 
        $this->validate($request, ['title' => 'required','month'=>'required','deparment_id'=>'required','year'=>'required' ]);
        $roaster= new Roaster();
        if ($request->get('action') == 'save'){ 
                $roaster->title=$request->title;
                $roaster->department_id=$request->deparment_id;
                $roaster->month= date("m", strtotime($request->month));
                $roaster->year=$request->year;
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
                  return redirect('roasters/create')->with('message','Record Inserted Successfully ! Thank you');
              }        
         } 
    }

    public function show($id){
        $roaster=Roaster::find($id);
        return view('Roasters.show',compact('roaster')); 
    }

    public function edit($id){
        $roaster=Roaster::find($id);
        if(Session::get('role_id')==1){
        $department=Client::where('parent_id',0)->get();
        }
        elseif (Session::get('role_id')==5) {
        $department=Client::where('parent_id',Session::get('dep_id'))->get(); 
        }
        return view('Roasters.edit',compact('roaster','department'));
    }

    public function update(Request $request, $id){
        $roaster=Roaster::find($id);
        $roaster->title=$request->title; 
        $roaster->department_id=$request->deparment_id;
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
       // $leave=TimeCategory::where('type',2)->pluck('title','id');
       // $day_off=TimeCategory::where('type',3)->pluck('title','id');
        $timecategory=TimeCategory::select('id','title','type','color')->where('department_id',$roster->department_id)                   ->get();
                      
        // $timecategory=DB::select('CALL viewTimeCategory("'.$roster->department_id.'","'.$dep_id.'")');
        // $users=DB::select('CALL viewUsers("'.$roster->department_id.'")');               
        $users=User::select('id','name')
               ->where('department_id',$roster->department_id)
               ->where('status',1)->where('flag',2)->get();
         
        $roaster_staff = RoasterStaff::with(['user','tcat'])->where('roster_id',$id)->get();
        $dept_tcats = TimeCategory::where('department_id',$roster->department_id)->get(); //load departmetn categories
        $att_sql = "select 
                          date(a.datetime) as checkdate,
                          a.datetime as checktime,
                          a.user_id 
                  from attendance a 
		          JOIN roster_details rd on rd.user_id = a.user_id
                  join time_categories tc on tc.id = rd.tcat_id
                  where rd.roster_id  =".$id;
        $roster_details_sql = "select rd.tcat_id,rd.user_id,rd.tcat_from_date,rd.tcat_to_date,tc.time_cat_type,tc.title,tc.type from roster_details rd
                                    join time_categories tc on tc.id = rd.tcat_id
                                where rd.roster_id = ".$id; 
                                                     
        $attendance = collect(DB::select($att_sql));
        $roster_user_tcats = collect(DB::select($roster_details_sql));   
        $roster_details = RosterDetail::where('roster_id',$id)->get();
        return view('Roasters.roaster_assign',compact('users','timecategory','roaster_staff','roster',
          'roster_details','dept_tcats','attendance','roster_user_tcats'));
       
    }
    public function roster_detail(Request $request){
    
      $roster_detail=DB::table('roster_details')
                    ->where('tcat_from_date',$request->tcat_from_date)
                    ->where('tcat_to_date',$request->tcat_to_date)->where('user_id',$request->user_id)->count();
     if($roster_detail > 0){
        
        $msg='fail';
        return Response()->json($msg);

         }
     else{

         $msg='success';               
         RosterDetail::create($request->all());
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

   
   


}