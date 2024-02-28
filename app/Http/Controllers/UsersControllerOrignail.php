<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Designation;
use App\Client;
use App\Module;
use App\ModuleRight;
use App\Attendance;
use App\LeaveRequest;
use App\AssignRight;
use App\RoasterStaff;
use Hash;
use App\AttachEmployee;
use App\Branch;
use App\BranchAttendance;
use App\leaveRequestDetail;
use Mail;
use DB;
use Session;
use Alert;
class UsersController extends Controller
{
  public function index(){
                       

   $startTime='2:00:00';
   $value = date('H:i:s',strtotime('2:00:00',strtotime($startTime)));
  
    $role_id  = Session::get('role_id');
    $dep_id  = Session::get('dep_id');
    $department_id_unit =Client::select('id')->where('parent_id',$dep_id)->get();
    $id=$department_id_unit->toArray();  
    $users_q = User::with(['departments'=>function($query) use ($dep_id){
      return $query->where('parent_id',$dep_id);
    },'role','designations']);
    if($role_id==1)
    {
         $users=DB::table('users as u')
          ->join('clients as  c','u.department_id','c.id')
           ->join('roles as r','u.role_id','r.id')
            ->select('u.name','u.father_name','u.id','r.title as role_title','u.mobile_number','u.password')->where('c.parent_id',0)->get();  

        // $users = DB::table('users as u')->join('clients as c','u.department_id','c.id')
        //           ->join('roles as r','u.role_id','r.id')
        //           ->join('designations as d','u.designation_id','d.id')
        //           ->select('u.name','u.father_name','u.id','r.title as role_title','u.mobile_number','u.password')
        //           ->where('c.parent_id',0)->get();

             
         //$users = $users_q->whereIn('role_id',[1,2,3,4,5,6,7])->paginate(10);
             
     }


     else if($role_id == 7){


        $users = $users_q->where('department_id',$dep_id)->whereIn('role_id',[7,6])->paginate(10);

          
     }
      elseif ($role_id==5) {
  
             $users = $users_q->where('status',1)
                       ->whereIn('department_id',$id)
                       ->whereIn('role_id',[5,7,6])->get();
           
               } 

                   
     return view('Users.index',compact('users'));
         
 }

   public function check_id(Request $request){

                  $user_id = $request->id;
                  $user=User::select('id')->where('id',$user_id)->first();
                  if($user!=''){
                      return Response()->json($user);
                  }
                  else{
                         return Response()->json($user_id);
                  }
               
                  


            }

 public function create(){  
   $user = DB::table('users')->orderBy('id', 'desc')->first();
   $department="";
   $designation="";
   $role_id = Session::get('role_id');
   $role ="";
   $dep_id=Session::get('dep_id');
   $department_id_unit =Client::select('id')->where('parent_id',$dep_id)->get();
   $branch=Branch::select('branch_id','branch_name')->where('dep_id',$dep_id)->get();
   $id=$department_id_unit->toArray();
  

   
  if($role_id==1){//if the user is super admin

         $role=Role::select('id','title')->get();
         $department=Client::where('parent_id','=',0)->get();      
    }
  elseif($role_id==5){
            $designation=Designation::where('department_id',$dep_id)->get();
            $department=Client::where('parent_id',Session::get('dep_id'))->get();
            $role=Role::select('id','title')->where('id','!=',1)->get();//exclude super admin id
    }
  elseif($role_id==7){
          $designation=Designation::where('department_id',$dep_id)->get();
          $department=Client::where('id',Session::get('dep_id'))->get();  
           $role=Role::select('id','title')->where('id','!=',1)->get();//exclude super admin id    
    }
    
   return view('Users.create',compact('role','designation','department','user','branch'));
 }

 public function store(Request $request){
 
 if(Session::get('role_id')==1){
   $this->validate($request, [  
                               'name' => 'required',
                               'deparment_id'=>'required',
                               'role_id' => 'required',
                                'email' => 'required'
                              ]);

                        }else{
                              $this->validate($request, [
                              'id' => 'required',
                              'name' => 'required',
                              'branch'=>'required',

                              'deparment_id'=>'required',
                              'role_id' => 'required',
                              'designation_id' => 'required',
                                                       ]);
                            }


if($request->email==''){
      $user = new User();
      $user->id=$request->id;
      $user->name=$request->name;
      $user->father_name=$request->father_name;
      $user->cnic=$request->cnic;
      $user->bps=$request->bps;
      $user->file_number=$request->file_number;
      $user->mobile_number=$request->mobile_number;
      if($request->type==1){
          $user->flag=$request->type;
      }else{$user->flag=2;}
      $user->time_in = $request->time_in;
      $user->time_out = $request->time_out; 
      $password = $request->password;
      $hashed = Hash::make("123456");
      $user->password= $hashed;
      $user->gender=$request->gender;
      $user->email=$request->email;
      $user->role_id=$request->role_id;     
      $user->designation_id=$request->designation_id;
      $user->department_id=$request->deparment_id;
      $user->dob=$request->dob;
      $user->status=1;
      $user->land_line_number=$request->land_line_number;     
      $user->mob_number_2=$request->mobile_number_2;
      $user->address=$request->address;
      $user->department_id=$request->deparment_id;

      
      $user->tags=','.trim($request->tags, ',').',';

      if ($request->get('action') == 'save') { 
      if($user->save()){
      Alert::success('Record Submit Successfully ! Thank You');
      return redirect('users');
                       }
      }
      elseif ($request->get('action') == 'save_and_close') {
      if($user->save()){
      Alert::success('Record Submit Successfully ! Thank You');
      return redirect('users/create')->with('msg','Record Submit Successfully ! Thank You');
                       }
       } 
}

else{

 
      $emailchecking = User::where('email',$request->email)->first();
     if($emailchecking){
          
         $this->validate($request, [
           'email' => 'unique:users,email'
         ]);
         }
else{
          
      $user = new User();
      if(Session::get('role_id')==1){
         $user->id=11111;
      }else{  
       $user->id=$request->id;     
      }  
      $user->name=$request->name;
      $user->father_name=$request->father_name;
      $user->cnic=$request->cnic;
      $user->bps=$request->bps;
      $user->file_number=$request->file_number;
      $user->mobile_number=$request->mobile_number;
      $password = $request->password;
      $user->time_in = $request->time_in;
      $user->time_out = $request->time_out;
      $hashed = Hash::make("123456");
      $user->password= $hashed;
      $user->gender=$request->gender;
      if($request->type==1){
      $user->flag=$request->type;
      }else{$user->flag=2;}
      $user->email=$request->email;
      $user->role_id=$request->role_id;     
      $user->designation_id=$request->designation_id;
      $user->department_id=$request->deparment_id;
      $user->dob=$request->dob;
      $user->land_line_number=$request->land_line_number;     
      $user->mob_number_2=$request->mobile_number_2;
      $user->address=$request->address;
      $user->department_id=$request->deparment_id;
      $user->tags=','.trim($request->tags, ',').',';
      if($user->save()){
      Alert::success('Record Submit Successfully ! Thank You');
      return redirect('users');
                       }
      
          }
      }
}

public function show($id){
  $user=User::find($id);
  $role=Role::find($user->role_id);
  $designation=Designation::find($user->designation_id);
  return view('Users.show',compact('user','role','designation'));
 }

public function edit($id){

  $user=User::find($id);
  $dep_id=Session::get('dep_id');
  $department_id_unit =Client::select('id')->where('parent_id',$dep_id)->get();
  $id=$department_id_unit->toArray();
 
  $branch=Branch::select('branch_name','branch_id','dep_id')->where('dep_id',$dep_id)->get();
  $designation="";
 
  
  // $designation =Client::with('designations')->where('parent_id',$dep_id)->get();
  // $department=Client::where('parent_id','!=',0)->get();  
   if(Session::get('role_id')==1){
         $role = Role::select('id','title')->get();
         $department=Client::where('parent_id','=',0)->get();
         $designation=Designation::get();
    }
   elseif(Session::get('role_id')==5){
         $role=Role::select('id','title')->where('id','!=',1)->get();//exclude super admin id
         $designation=Designation::where('department_id',$dep_id)->get();
         $department=Client::where('parent_id',Session::get('dep_id'))->get();
    }
  elseif(Session::get('role_id')==7){
          $role=Role::select('id','title')->where('id','!=',1)->get();//exclude super admin id
          $designation=Designation::where('department_id',$dep_id)->get();
          $department=Client::where('id',Session::get('dep_id'))->get();      
    }
     return view('Users.edit',compact('user','role','designation','department','branch'));
}


public function update(Request $request, $id){

      $user=User::find($id);
      // $user->name=$request->gender;
      $user->name=$request->input('name');
      $user->father_name=$request->father_name;
      $user->cnic=$request->cnic;
      $user->bps=$request->bps;
      $user->file_number=$request->file_number;
      $user->mobile_number=$request->mobile_number;
      $password = $request->password;
      $hashed = Hash::make("123456");
      $user->password= $hashed;
      $user->email=$request->email;
      $user->role_id=$request->role_id;     
      $user->designation_id=$request->designation_id;
      $user->department_id=$request->deparment_id;
      $user->dob=$request->dob;
      $user->land_line_number=$request->land_line_number;     
      $user->mob_number_2=$request->mobile_number_2;
      $user->address=$request->address;
      $user->gender=$request->gender;
      $user->department_id=$request->deparment_id;
      $user->tags=','.trim($request->tags, ',').',';
      if($user->save()){

       Alert::success('Record Update Successfully ! Thank You');
       return redirect('users');
     
     }
}

public function destroy($id){

  $user=User::find($id);
  if(RoasterStaff::where('user_id',$id)->count()){
   return redirect('users')->with('error','User id already used in RoasterStaff');
   }
  if(LeaveRequest::where('user_id',$id)->count() > 0){
    return redirect('users')->with('error','User id already used in LeaveRequest');
    }
    if(AttachEmployee::where('user_id',$id)->count()){
    return redirect('users')->with('error','User id already used in AttachEmployee');
    }

    
  $user->delete();
  return redirect('users');
}

public function dashboard(){
  $user=User::all()->count();
  $client=Client::all()->count();
  $role=Role::all()->count();
  $designation=Designation::count();
  $moduleright=ModuleRight::count();
  $module=Module::count();
  $department=Client::where('name','noor')->count();
  $device_data = DB::select("SELECT * FROM `device_info`");
  //json_encode(compact('department'))
  return view('Users.dashboard',compact('user','client','role','designation','moduleright','module','department','device_data'));
}
       
       public function forgotPassword(Request $request){

             if($request->isMethod('post')){
                $data=$request->all();
                $userCount= User::where('email',$data['email'])->count();
                if($userCount==0){
                 
                 return redirect()->back()->with('flash_message_error','Email Does not Exits!');
                
                }
                  $userDetail= User::where('email',$data['email'])->first();
                  $randompassword=str_random(8);
                  $new_password=bcrypt($randompassword);
                  User::where('email',$data['email'])->update(['password'=>$new_password]);
                  $email=$data['email'];
                  $name=$userDetail->name;
                  $messageData=['email'=>$email,'name'=>$name ,'password'=>$new_password];
                  Mail::send('emails.forgotpassword',$messageData,function($message)use($email){
                        $message->to($email)->subject('New Password - TimeNex');
                  });
              }
             

             return view('Login.forgotpassword');
                        
            }
            public function inactiveEmployee(){

                $users = DB::table('users')
                         ->select('r.title as role_title','d.title as designation_title','users.id','users.name as name','users.mobile_number as contact','users.father_name as fname','users.status as status')
                         ->join('roles as r','users.role_id','r.id')
                         ->join('designations as d','users.designation_id','d.id')
                         ->where('users.status',2)->get();

                return view('Users.inactive',['users' => $users]);

                  }
                  
            public function inactive_edit($id){
                        

                        $user=User::find($id);
                        $users="update users set status='2' where id='".$id."'";
                         DB::select( DB::raw($users));
                           Alert::success('Employee is Inactive Successfully ! Thank You');
                           return redirect('users');
                    } 
              public function active_employee($id){
    
                        $user=User::find($id);
                        $users="update users set status='1' where id='".$id."'";
                         DB::select( DB::raw($users));
                           Alert::success('Employee is Active Successfully
                            ! Thank You');
                           return redirect('inactives');
                    }                       
          
             }
