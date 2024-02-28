<?php
namespace App\Http\Controllers;
use App\RoleJurisdiction;
use Carbon\Carbon;
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
#use Hash;
use App\AttachEmployee;
use App\Region;
use App\Zone;
use App\Branch;
use App\Allowance;
use App\TransferHistory;
use App\PromotionHistory;
use App\Deduction;
use App\BranchAttendance;
use App\leaveRequestDetail;
use App\TimeCategory;
use App\UserFile;
use App\EmployeeAllowance;
use App\EmployeeDeduction;
use App\EmployeeLeavesConfiguration;
use App\TimeCategoryHistory;

use DateTime;
use Mail;
use DB;
use Session;
use Alert;
use Validator;
use Input;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
#use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class UsersController extends Controller
{
    public function getDownload($id)
    {
        $file=UserFile::find($id);
        return response()->download(public_path('files/'.$file->file));

    }
    public function getDelete($id)
    {
        $file=UserFile::find($id);
        $file->delete();
        return redirect('/users/'.$file->user_id);
    }
    public function  addEmpFiles(Request $request){

        if($request->hasfile('userfiles'))
        {
            $res=[];
            $n=[];
            $f=[];
            $iss_date=[];
            $expire_date=[];
            foreach($request->name as $k => $v){
                $n[$k]= (array) ['name' => $request->name[$k]];
                $iss_date[$k]= (array) ['issue_date' => $request->issue_date[$k]];
                $expire_date[$k]= (array) ['expire_date' => $request->expire_date[$k]];
                $f[$k]= (array) ['file_name' => $request->file('userfiles')[$k]];
                $res[$k] = array_merge($n[$k],$f[$k],$iss_date[$k],$expire_date[$k]);
            }
            // $_FILES['image']['size'];
            $data=[];
            foreach($res as $v =>$r) {

                $randomNumber = rand(1,1000);
                $file_size=$r['file_name']->getSize();
                $name= $r['file_name']->getClientOriginalName();
                $fileUniqueName=$randomNumber."-".$name;
                $r['file_name']->move(public_path().'/files/',$fileUniqueName);
                $recs['file']=  $fileUniqueName;
                $recs['name']=$r['name'];
                $recs['issue_date']=$r['issue_date'];
                $recs['expire_date']=$r['expire_date'];
                $recs['file_size']=$file_size;
                $recs['user_id']=$request->user_id;
                $data[]=$recs;

                UserFile::create($recs);

            }
            //  dd($data);
            return redirect('/users/'.$request->user_id);
        }
    }
    public function index(Request $request){

        $choosen_departments = [];
        $choosen_designations = [];
        $users = [];
        $role_id  = Session::get('role_id');
        $dep_id  = Session::get('dep_id');
        if($request->has('search')){
            $department_ids_sql = "SELECT id  FROM `clients` WHERE `parent_id` = ".$dep_id;
            $department_ids = collect(DB::select($department_ids_sql))->pluck('id');
            $department_ids[] = $dep_id;
            $users = User::with(['departments','role','designations'])->where('status',1)->whereIn('department_id',$department_ids);



            //lets apply filters
            if($request->has('department')){
                $choosen_departments = $request->department;
                $users->whereIn('department_id',$choosen_departments);
            }

            if($request->has('designation')){
                $choosen_designations = $request->designation;
                $users->whereIn('designation_id',$choosen_designations);
            }
            if($request->has('name') && !is_null($request->name)){
                $users->where('name', 'like', '%' . $request->name . '%');
            }
            if($request->has('id') && !is_null($request->id)){
                $users->where('id', 'like', '%' . $request->id . '%');
            }
            if($request->has('cnic') && !is_null($request->cnic)){
                $users->where('cnic', $request->cnic);
            }
            if($request->has('father_name') && !is_null($request->father_name)){
                $users->where('father_name', 'like', '%' . $request->father_name . '%');
            }
            if($request->has('cnic') && !is_null($request->cnic)){
                $users->where('cnic', 'like', '%' . $request->cnic . '%');
            }
            if($request->has('file_number') && !is_null($request->file_number)){
                $users->where('file_number', 'like', '%' . $request->file_number . '%');
            }
            if($request->has('gender') && !is_null($request->gender)){
                if($request->gender!="All"){
                    $users->where('gender', 'like', '%' . $request->gender . '%');
                }
            }
            if($request->has('bps') && !is_null($request->bps)){
                $users->where('bps', 'like', '%' . $request->bps . '%');
            }
            //$users->where('role_id','!=',5);
            $users = $users->get();
        }

        $departments = Client::where('parent_id',$dep_id)->get();
        $designations = Designation::where('department_id',$dep_id)->get();
        $page_title = "Search Employees";
        return view('Users.index',compact('users','departments','designations','choosen_departments','choosen_designations','page_title'));
    }

    public function check_personal_file_no(Request $request){

        $employee = User::select('file_number')->where('file_number',$request->file_number)->first();
        if($employee!=null){
            $msg='Personal file number has been already taken';
            return  json_encode($msg);
        }
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
    public function step1($id=null){

        $data = [
        'page_title' => 'Employee Registration Form',
        'roles' => Role::where('id','!=',1)->pluck('title','id'),
        'user' => NULL
        ];
        $data['allowances']=Allowance::pluck('title','id')->toArray();
        $data['deductions']=Deduction::pluck('title','id')->toArray();
        $data['tcat_hist'] = TimeCategoryHistory::with('timeCategory')->where('user_id',$id)->orderby('join_date','asc')->get();

        $department = Client::find(Session::get('dep_id'));

        $departments = Client::where(['parent_id'=>Session::get('dep_id'),'sub_account'=>1])->
        orWhere('id',Session::get('dep_id'))->pluck('name','id')->toArray();
        $data['departments'] = $departments;
        $data['units'] = Client::where(['parent_id'=>Session::get('dep_id'),'sub_account'=>2])->pluck('name','id')->toArray();

        if($department->parent_id != 0){
            $data['units'] = Client::where(['parent_id'=>$department->parent_id,'sub_account'=>1])->pluck('name','id')->toArray();
        }
        $data['flag']='';
        $data['regions'] = Region::pluck('region_name','region_id')->toArray();
        /*if($department->sub_account==2){
        $data['regions'] = Region::where('dep_id',$department->parent_id)->pluck('region_name','region_id')->toArray();
        }*/
        $data['zones'] = [];
        $data['branches'] = [];

        $data['timecategories']=TimeCategory::where('department_id',Session::get('dep_id'))->where('type',1)->where('time_cat_type',1)->pluck('title','id')->toArray();
        $data['designations']= Designation::where('department_id',Session::get('dep_id'))->pluck('title','id')->toArray();
        $data['leaves'] = TimeCategory::where('department_id',Session::get('dep_id'))->where('type',2)->pluck('title','id');

        if($id != NULL){
            $data['flag']='edit';
            $user = User::find($id);
            $data['user'] = $user;
            $data['zones'] = Zone::where('region_id',$user->region_id)->pluck('zone_name','zone_id')->toArray();
            $data['branches'] = Branch::where('zone_id',$user->zone_id)->pluck('branch_name','branch_id')->toArray();
            $data['designated_leaves'] = EmployeeLeavesConfiguration::where('emp_id',$user->id)->get();
        }else{
            $data['user'] = New User();
        }
        $data['transfer_to_department'] =  Cache::remember('transfer_to_department', now()->addMinutes(1440), function()
        {
            return Client::generatetree(0,false,false, true);
        });

        return view('Users.step1',$data);
    }

    public function subDeparments(Request $request){

        $client = Client::select('name', 'id')->where('parent_id',$request->department_id)->get();

        if(count($client) == 0){
            $deparment = Client::find($request->department_id);

            if($deparment){
                $parent_level_dep =  Client::where('id',$deparment->parent_id)->first();

                $client = Client::select('name', 'id')->where('parent_id',$parent_level_dep->parent_id)->get();

            }

        }
        return $client;
    }

    public function basic_info_create($id=null){
        $role_id = Session::get('role_id');
        if($role_id==1){//if the user is super admin
            $role=Role::select('id','title')->get();
            $department=Client::where('parent_id','=',0)->get();
            return view('Users.create_account',compact('role','department'));
        }else{
            $user = null;
            if(!is_null($id)){
                $user = User::find($id);
            }
            return view('Users.basic_info',compact('user'));
        }
    }

    public function step1_store(Request $request){
        if($request->hasFile('image')) {
            $ext = $request->image->getClientOriginalExtension();
            if(!in_array($ext,['jpeg','png','jpg'])){
                return redirect()->back()->with('error','Only jpeg,png,jpg files are allowed');
            }
        }
        $user = User::where('cnic', $request->cnic)->first();
        if($user){
            $validator = Validator::make($request->all(), [
                'cnic' => 'required|string|unique:users,cnic,' . $user->id,
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('error', 'CNIC should be unique')->withInput();
            }

        }
        $transfer_histories = NULL;
        $user_exists = User::where('id',$request->id);
        if($request->has('edit_employee') && $request->edit_employee !=""){
            //Update Employee Details
            $user = User::find($request->edit_employee);
            $transfer_histories = TransferHistory::where('user_id', $user->id)->get();
            $user->name = $request->name;
            $user->father_name = $request->father_name;
            $user->cnic = $request->cnic;
            $user->dob = $request->dob;
            $user->reg_date = $request->reg_date;
            $user->cnic_issue_date  = $request->cnic_issue_date;
            $user->cnic_expiry_date = $request->cnic_expiry_date;
            $user->gender = $request->gender;
            $user->role_id = $request->role_id;
            $user->is_gazetted = $request->is_gazetted;
            if(isset($request->transfer_to_dept_id) && $request->transfer_to_dept_id != 'choose one'){
                $user->department_id = $request->transfer_to_dept_id;

                // add transfer log:
                $transfer_hist = new TransferHistory();
                $transfer_hist->user_id = $user->id;
                $transfer_hist->from_dep_id = $request->department_id;
                $transfer_hist->to_dep_id = $request->transfer_to_dept_id;
                $transfer_hist->date = Carbon::now();
                $transfer_hist->save();
            }else{
                $user->department_id = $request->department_id;
            }
            if($user->bps != $request->bps || $user->designation_id != $request->designation_id){
                $promotion_hist = new PromotionHistory();
                $promotion_hist->user_id = $user->id;
                $promotion_hist->old_designation = $user->designation_id;
                $promotion_hist->new_designation = $request->designation_id;
                $promotion_hist->old_scale = $user->bps;
                $promotion_hist->new_scale = $request->bps;
                $promotion_hist->date = Carbon::now();
                $promotion_hist->save();
            }
            $user->file_number = $request->file_number;
            $user->bps = $request->bps;
            $user->designation_id = $request->designation_id;
            $user->fixed_salary = $request->fixed_salary;
            $user->hour_rate_salary = $request->salary_hour_rate;
            $user->region_id = $request->region_id;
            $user->zone_id = $request->zone_id;
            $user->branch_id = $request->branch_id;
            $user->tcat_id = $request->tcat_id;
            $user->mobile_number = $request->mobile_number;
            $user->mob_number_2 = $request->mob_number_2;
            $user->land_line_number = $request->land_line_number;
            $user->email=$request->email;
            if($request->password){
            $user->password=Hash::make($request->password);
            }
            $user->address = $request->address;
            $user->status = 1;
            $user->flag = $request->flag;
            $user->email = $request->email;
            $user->app_attendance = $request->app_attendance;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $name = $user->id.'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/emp_images');
                $image->move($destinationPath, $name);
                $user->image = $name;
            }


            $user->tags=','.trim($request->tags, ',').',';
            $user->field_staff=$request->field_staff?1:0;

            $user->emp_type=$request->emp_type;
            $user->remarks=$request->remarks;

            $user->save();
            $time_category_hist=array('user_id'=>$user->id,"tcat_id"=>$request->tcat_id,"join_date"=>date('Y-m-d'));
            DB::table('time_category_histories')->insert($time_category_hist);
            EmployeeLeavesConfiguration::where('emp_id',$user->id)->delete();

            $index = 0;

            foreach($request->leave_type_id as $key => $ltype){

                EmployeeLeavesConfiguration::create([
                    'emp_id' => $user->id,
                    'leave_type_id' => $request->leave_type_id[$index],
                    'total_leaves' => $request->number_of_days[$index]
                ]);
                $index = $index+1;
            }
            return redirect(route('users'));
        //return redirect()->back();
        }else{

            if($user_exists->count()>0){
                return redirect()->back()->with('error','The User ID already exists, Please choose another');
                exit();
            }
            //create new user
            $user = User::create([
            'id'=>$request->id,
            'name'=>$request->name,
            'father_name'=>$request->father_name,
            'cnic'=>$request->cnic,
            'reg_date'=>$request->reg_date,
            'cnic_issue_date'=>$request->cnic_issue_date,
            'cnic_expiry_date'=>$request->cnic_expiry_date,
            'dob'=>$request->dob,
            'gender'=>$request->gender,
            'role_id'=>$request->role_id,
            'department_id'=>$request->department_id,
            'file_number'=>$request->file_number,
            'bps'=>$request->bps,
            'designation_id'=>$request->designation_id,
            'fixed_salary'=>$request->fixed_salary,
            'hour_rate_salary'=>$request->salary_hour_rate,
            'region_id' => $request->region_id,
            'zone_id' => $request->zone_id,
            'branch_id' => $request->branch_id,
            'is_gazetted' => $request->is_gazetted,
            'tcat_id' => $request->tcat_id,
            'mobile_number' => $request->mobile_number,
            'mobile_number_2' => $request->mobile_number2,
            'land_line_number' => $request->land_line_number,
            'address' => $request->address,
            'email'   => $request->email,
            'password'=> Hash::make($request->password),
            'tags'=> ','.trim($request->tags, ',').',',
            'field_staff'=>$request->field_staff?1:0,
            'emp_type'=>$request->emp_type,
            'remarks'=>$request->remarks,
            'status' => 1,
            'flag' => 1
            ]);
            if($request->has('tcat_id')){
                $time_category_hist=array('user_id'=>$request->id,"tcat_id"=>$request->tcat_id,"join_date"=>$request->reg_date);
                DB::table('time_category_histories')->insert($time_category_hist);
            }
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $name = $user->id.'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/emp_images');
                $image->move($destinationPath, $name);
                $user = User::find($user->id);
                $user->image = $name;
                $user->save();
            }

            $index = 0;

            foreach($request->leave_type_id as $key => $ltype){

                EmployeeLeavesConfiguration::create( [
                'emp_id' => $request->id,
                'leave_type_id' => $request->leave_type_id[$index],
                'total_leaves' => $request->number_of_days[$index]
                ]);

                $index = $index+1;

            }
            $index1 = 0;
            if($request->allowances[0]!=null || $request->allowance_amounts[0]!=null){
                foreach($request->allowances as $key =>$allowance){
                    $emp_allowance = new EmployeeAllowance();
                    $emp_allowance->allowance_id=$allowance;
                    $emp_allowance->employee_id=$request->id;
                    $emp_allowance->amount=$request->allowance_amounts[$index1];
                    $emp_allowance->save();
                    $index1 = $index1+1;

                }
            }
            $index2 = 0;
            if($request->deduction_amounts[0]!=null || $request->deductions[0]!=null){
                foreach($request->deductions as $key =>$deduction){

                    $emp_allowance = new EmployeeDeduction();
                    $emp_allowance->deduction_id=$deduction;
                    $emp_allowance->employee_id =$request->id;
                    $emp_allowance->amount=$request->deduction_amounts[$index2];
                    $emp_allowance->save();
                    $index2 = $index2+1;

                }
            }
            return redirect()->back()->with('success','Registered Successfully');
            exit();
        }
    }

    public function getEmpLeave(){

        $emp_leave_config= DB::table('emp_leave_configuration')
        ->where('emp_id',request()->get('emp_id'))
        ->where('leave_type_id',request()->get('leave_id'))
        ->selectRaw('total_leaves')->first();
        return response()->json($emp_leave_config);
    }

    public function load_zone(Request $request){
        return json_encode(Zone::where('region_id',$request->region_id)->get());
    }
    public function load_branch(Request $request){
        return json_encode(Branch::where('zone_id',$request->zone_id)->get());
    }
    public function suggest_unused_ids(Request $request){
        /*
        $sql = "SELECT
        (t1.id + 1) as MissingID
        FROM users t1
        WHERE NOT EXISTS
        (SELECT t2.id
        FROM users t2
        WHERE t2.id = t1.id + 1)

        order by MissingID asc limit 8";*/
        $sql = "SELECT MissingID
        FROM (
        SELECT FLOOR(RAND() * (2400000 - 2300000 + 1) + 2300000) AS MissingID
        ) AS random_ids
        LEFT JOIN users ON random_ids.MissingID = users.id
        WHERE users.id IS NULL
        LIMIT 10";
        //and (t1.id+1) BETWEEN 2300000 AND 2400000
        return json_encode(DB::select($sql));
    }
    public function basic_info_store(Request $request){

        $validator = Validator::make($request->all(), [
        'id'=>'required',
        'name' => 'required',
        'dob' => 'required',
        ]);

        if($request->id <= 2300000 || $request->id >= 2400000){
            return redirect('users')->with('error','User id Exists');
        }

        if ($validator->fails()) {
            return redirect('employee/basic_info')->withErrors($validator)->withInput();
        }else{

            if($request->found_user_id==null){

                if($request->file('image')!=''){
                    $file = $request->file('image');
                    $extension = $request->image->extension();
                    $filename =   time() . '.' . $file->getClientOriginalExtension();
                    $destination_path=public_path('/photos');
                    $file->move($destination_path,$filename);
                }
                else{
                    $filename="";
                }
                $employee=new User();
                $employee->id=$request->id;
                $employee->age=$request->age;
                $employee->name=$request->name;
                $employee->image=$filename;
                $employee->fixed_salary=$request->fixed_salary;
                $employee->hour_rate_salary=$request->salary_hour_rate;
                $employee->overtime_check=$request->overtime_check;
                $employee->father_name=$request->father_name;
                $employee->cnic=$request->cnic;
                $employee->dob=$request->dob;
                $employee->gender=$request->gender;
                $employee->email=$request->email;
                $employee->password=Hash::make($request->password);
                $employee->status=1;
                $emp_id= $employee->id;
                $employee->save();
                if($request->action=="save"){
                    return redirect('employee/basic_info/'.$emp_id);
                }elseif($request->action=="save_next"){
                    return redirect('employee/dep_info_create/'.$emp_id);
                }
            }else{

                $employee=User::find($request->found_user_id);
                $employee->id=$request->id;
                $employee->name=$request->name;
                $employee->father_name=$request->father_name;
                $employee->cnic=$request->cnic;
                $employee->dob=$request->dob;
                $employee->gender=$request->gender;
                $employee->email=$request->email;
                $employee->password=Hash::make($request->password);
                $employee->save();
                if($request->action=="save"){
                    return redirect('employee/basic_info/'.$request->found_user_id);
                }elseif($request->action=="save_next"){
                    return redirect('employee/dep_info_create/'.$request->found_user_id);
                }
            }
        }
    }

    public function dep_info_create($id = null){

        if($id!=null){

            $data['emp']=User::find($id);
        }
        $role_id =Session::get('role_id');
        $dep_id=Session::get('dep_id');

        $data['branch']=Branch::select('branch_id','branch_name')->where('dep_id',$dep_id)->get();
        // if($data['branch']->isEmpty()){

        //   $data['branch']=Branch::select('branch_id','branch_name')->where('dep_id',$dep_id)->get();

        // }

        $data['designation']=Designation::where('department_id',$dep_id)->get();
        $data['role']=Role::select('id','title')->where('id','!=',1)->get();
        return view('Users.department_info',$data);
    }

    public function dep_info_store(Request $request,$id){

        $validator = Validator::make($request->all(), [
        'branch'=>'required',
        'designation_id' => 'required',
        'role_id' => 'required',
        'contract_type'=>'required'
        ]);
        if($validator->fails()){
            return redirect('employee/dep_info_create/'.$id)->withErrors($validator)->withInput();
        }else{

            $user=User::find($id);
            $user->role_id=$request->role_id;
            $user->designation_id=$request->designation_id;
            $user->branch_id=$request->branch;
            $user->contract_type=$request->contract_type;
            $user->file_number=$request->file_number;
            $user->bps=$request->bps;
            $user->save();
            if($request->action=="save"){
                return redirect('employee/dep_info_create/'.$id);
            }
            if($request->action=="save_next"){
                return redirect('employee/roster_create/'.$id);
            }
        }
    }
    public function profile(){
        $page_title='Update Password';
        return view('users.profile',compact('page_title'));

    }
    public function filterEmployee(){

        $emp_search=  Input::get('emp_search');
        $page_row=  Input::get('page_row');
        $dep_id=Session::get('dep_id');
        $department_id_unit =Client::select('id')->where('parent_id',$dep_id)->get();
        $id=$department_id_unit->toArray();

        $users_q = User::with(['departments'=>function($query) use ($dep_id){
        return $query->where('parent_id',$dep_id);
        },'role','designations']);
        if(!empty($emp_search)){
        $users = $users_q->where('status',1)
        ->whereIn('department_id',$department_id_unit)
        ->whereIn('role_id',[5,7,6])
        ->where('users.name', 'like', '%' . $emp_search . '%')
        ->orWhere('users.id',$emp_search)
        ->orWhere('users.cnic',$emp_search);
        $users->paginate($page_row);
        }elseif(!empty($page_row)){
        $users = $users_q->where('status',1)
        ->whereIn('department_id',$id)
        ->whereIn('role_id',[5,7,6])->paginate($page_row);

        }else{
        $users = $users_q->where('status',1)
        ->whereIn('department_id',$id)
        ->whereIn('role_id',[5,7,6])->paginate($page_row);

        }
        $flag=2;
        return view('Users.index',compact('users','flag'));


    }
    public function profile_update(Request $request , $id){
        $validatedData = $request->validate([
        'confirm_password' => 'required',
        'new_password' => 'required',
        ]);
        $user=User::find($id);
        if($request->new_password==$request->confirm_password){
        $user->password=Hash::make($request->confirm_password);
        $user->save();
        }
        return redirect()->back()->with('success', 'password updated successfully ! thank you');

        }
        public function roster_create($id = null){
        if($id!=null){
        $data['emp']=User::find($id);
        }
        $data['department']=Client::where('parent_id',Session::get('dep_id'))->where('sub_account',1)->get();
        $client=Client::where('parent_id',Session::get('dep_id'))->get();
        $data['timecategory']=TimeCategory::where('department_id',Session::get('dep_id'))->where('type',1)->where('time_cat_type',1)->get();
        $data['leave']=TimeCategory::select('id','title','department_id','type')
        ->where('department_id',Session::get('dep_id'))
        ->where('type',2)
        ->where('leave_type',1)
        ->get();


        if($client->isEmpty()){
            $data['department']=Client::where('id',Session::get('dep_id'))->where('status',1)->get();
        }
        return view('Users.roster_info',$data);
    }
    public function roster_store(Request $request,$id){
        $validator = Validator::make($request->all(), [
        'deparment_id'=>'required',
        // 'tcat_id'=>'required',
        // 'leave_type'=>'required',


        ]);

        if($validator->fails()){
            return redirect('employee/roster_create/'.$id)->withErrors($validator)
            ->withInput();
        }else{
            $user=User::find($id);
            if($request->roster==1){
                $user->tcat_id=$request->tcat_id;
            }else{
                $user->tcat_id="";
            }
            $user->flag=$request->roster;
            $hashed = Hash::make("123456");
            $user->password= $hashed;
            $user->department_id=$request->deparment_id;
            $user->save();
            if($request->action=="save"){
                return redirect('employee/roster_create/'.$id);
            }
            if($request->action=="save_next"){
                return redirect('employee/contact_create/'.$id);
            }
        }
    }

    public function MarkAttendance_index(){

        return view('Users.markindex');
    }
    public function mark_attendances($id){
        $user=User::find($id);
        return view('Users.mark_attendance',compact('user'));
    }
    public function stor_Markattedance(Request $request){

        DB::table('attendance')->insert(
        ['user_id' => $request->id, 'datetime' => date("Y-m-d H:i:s",strtotime($request->time_in)), 'mark_attendance' => 1]
        );
        return redirect('marks_attendance');

    }
    public function Search(Request $request){
        $employee = $request->search;
        $dep_id  = Session::get('dep_id');

        $department_ids_sql = "SELECT id  FROM `clients` WHERE `parent_id` = ".$dep_id;
        $department_ids = collect(DB::select($department_ids_sql))->pluck('id');
        $ids = $department_ids->toArray();
        $empm =DB::table('users')
        ->join('clients', 'users.department_id','=','clients.id')
        ->join('designations', 'users.designation_id','=','designations.id')
        ->select(
        'users.id',
        'users.cnic',
        'users.name as emp_name',
        'users.department_id as department_id',
        'clients.name as dep_name',
        'designations.title as title'

        )->where('users.name', 'like', '%' . $employee . '%')
        ->orWhere('users.id',$employee)
        ->orWhere('users.cnic',$employee);
        $employees = collect($empm->get());
        $empm = $employees->whereIn('department_id',$ids);

        return view('Users.markindexo',compact('empm'));
    }

    public function contact_create($id = null){
        if($id!=null){
            $data['emp']=User::find($id);
        }

        return view('Users.contact_info',$data);
    }
    public function contact_store(Request $request,$id){
        $this->validate($request, [
        'address' => 'required'
        ]);
        $user =User::find($id);
        $user->mobile_number=$request->mobile_number;
        $user->mob_number_2=$request->mobile_number_2;
        $user->land_line_number=$request->land_line_number;
        $emailchecking = User::where('email',$request->email)->where('email','!=','')->first();
        if($emailchecking){
            $this->validate($request, [
            'email' => 'unique:users,email'
            ]);
        }else{
            $user->email=$request->email;
        }

        $user->address=$request->address;
        $user->save();
        if($request->action=="save"){
            return redirect('employee/contact_create/'.$id);
        }
        if($request->action=="finish"){
            Alert::success('Record Submit Successfully ! Thank You');
            return redirect('users');
        }

    }

    public function create(){
        $role_id = Session::get('role_id');
        if($role_id==1){//if the user is super admin

            $role=Role::select('id','title')->get();
            $department=Client::where('parent_id','=',0)->get();
            return view('Users.create_account',compact('role','department'));
        }
        else{
            return view('Users.basic_info');
        }
    }

    public function employee_leave_configuration_detail($id){
        $emp_leave_config= DB::table('emp_leave_configuration')
        ->join('users','users.id','emp_leave_configuration.emp_id')
        ->join('time_categories','time_categories.id','emp_leave_configuration.leave_type_id')
        ->where('emp_id',$id)
        ->selectRaw('distinct users.name,users.id,emp_leave_configuration.id as emp_leave_config_id ,time_categories.title,emp_leave_configuration.total_leaves')->get();
        return  json_encode($emp_leave_config);
    }
    public function employee_leave_configuration(Request $request, $id){
        $total_leave=$request->total_leave;
        $check_lev_type=DB::table('emp_leave_configuration')
        ->join('time_categories','time_categories.id','emp_leave_configuration.leave_type_id')
        ->where('emp_id',$id)
        ->where('leave_type_id',$request->leave_type)
        ->first();

        if($check_lev_type==null){
        $data=  array('leave_type_id'=> $request->get('leave_type'), 'total_leaves'=> $request->get('total_leave'),'emp_id'=>$id
        );
        DB::table('emp_leave_configuration')->insert($data);
        $emp_leave_config= DB::table('emp_leave_configuration')
        ->join('users','users.id','emp_leave_configuration.emp_id')
        ->join('time_categories','time_categories.id','emp_leave_configuration.leave_type_id')
        ->where('emp_id',$id)
        ->selectRaw('distinct users.name,users.id,emp_leave_configuration.id as emp_leave_config_id ,time_categories.title,emp_leave_configuration.total_leaves')->get();
        return  json_encode($emp_leave_config);

        }
        else{


        $emp_leave_config= DB::table('emp_leave_configuration')
        ->join('users','users.id','emp_leave_configuration.emp_id')
        ->join('time_categories','time_categories.id','emp_leave_configuration.leave_type_id')
        ->where('emp_id',$id)
        ->selectRaw('distinct users.name,users.id,emp_leave_configuration.id as emp_leave_config_id ,time_categories.title,emp_leave_configuration.total_leaves')->get();
        $flag=1;
        return  json_encode(['emp_leave_config'=>$emp_leave_config, 'flag'=>$flag , 'leave_name'=> $check_lev_type->title,'total_leave'=>$total_leave]);

        }

    }
    public function employee_leave_configuration_delete(Request $request,$id){
        DB::table('emp_leave_configuration')->where('id',$request->id)->delete();
        $emp_leave_config= DB::table('emp_leave_configuration')
        ->join('users','users.id','emp_leave_configuration.emp_id')
        ->join('time_categories','time_categories.id','emp_leave_configuration.leave_type_id')
        ->where('emp_id',$id)
        ->selectRaw('distinct users.name,users.id,emp_leave_configuration.id as emp_leave_config_id ,time_categories.title,emp_leave_configuration.total_leaves')->get();
        return  json_encode($emp_leave_config);


    }
    public function getCatName(Request $request){
        $time_cat_title=TimeCategory::select('id','title','department_id','type')
        ->where('department_id',$request->id)
        ->where('type',1)
        ->where('time_cat_type',1)
        ->get();




        return json_encode(['timecategory' => $time_cat_title,'leave_type'=>$leave]);
        // return Response()->json($time_cat_title);
    }
    public function store(Request $request){
        if(Session::get('role_id')==1){
            $this->validate($request, [
            'id' => 'required',
            'name' => 'required',
            'deparment_id'=>'required',
            'role_id' => 'required',
            'email' => 'required',


            ]);

        }else{
            $this->validate($request, [

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
            $user->tcat_id = $request->tcat_id;
            $user->flag = $request->flag;
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
            if($user->save()){
                Alert::success('Record Submit Successfully ! Thank You');
                return redirect('users');
            }
        }else{

            $emailchecking = User::where('email',$request->email)->first();
            if($emailchecking){

                $this->validate($request, [
                'email' => 'unique:users,email'
                ]);
            }
            else{
                $user = new User();
                $user->id=$request->id;
                $user->name=$request->name;
                $user->father_name=$request->father_name;
                $user->cnic=$request->cnic;
                $user->bps=$request->bps;
                $user->file_number=$request->file_number;
                $user->mobile_number=$request->mobile_number;
                $password = $request->password;
                $hashed = Hash::make("123456");
                $user->password= $hashed;
                $user->gender=$request->gender;
                $user->status=1;
                $user->flag=$request->flag;
                $user->email=$request->email;
                $user->role_id=$request->role_id;
                $user->designation_id=$request->designation_id;
                $user->department_id=$request->deparment_id;
                $user->dob=$request->dob;
                $user->land_line_number=$request->land_line_number;
                $user->mob_number_2=$request->mobile_number_2;
                $user->address=$request->address;
                $user->department_id=$request->deparment_id;
                if($user->save()){
                    Alert::success('Record Submit Successfully ! Thank You');
                    return redirect('users');
                }
            }
        }
    }

    public function edit($id){
        $user=User::find($id);
        $dep_id=Session::get('dep_id');
        $department_id_unit =Client::select('id')->where('parent_id',$dep_id)->get();
        $id=$department_id_unit->toArray();
        $branch=Branch::select('branch_name','branch_id','dep_id')->where('dep_id',$dep_id)->get();
        $leave=TimeCategory::select('id','title','department_id','type')
        ->where('department_id',Session::get('dep_id'))
        ->where('type',2)
        ->where('leave_type',1)
        ->get();
        $designation="";
        // $designation =Client::with('designations')->where('parent_id',$dep_id)->get();
        // $department=Client::where('parent_id','!=',0)->get();
        if(Session::get('role_id')==1){
            $role = Role::select('id','title')->get();
            $department=Client::where('parent_id','=',0)->get();
            $designation=Designation::get();
        }elseif(Session::get('role_id')==5){

            $role=Role::select('id','title')->where('id','!=',1)->get();//exclude super admin id
            $designation=Designation::where('department_id',$dep_id)->get();
            $department=Client::where('parent_id',Session::get('dep_id'))->get();

            $timecategory=TimeCategory::where('department_id',$dep_id)
            ->where('type',1)
            ->where('time_cat_type',1)
            ->get();
        }elseif(Session::get('role_id')==7){
            $role=Role::select('id','title')->where('id','!=',1)->get();//exclude super admin id
            $designation=Designation::where('department_id',$dep_id)->get();
            $department=Client::where('id',Session::get('dep_id'))->get();
        }

        return view('Users.edit',compact('user','role','designation','department','branch','timecategory','leave'));
    }


    public function empByDepartment(Request $request){
        $users= User::where('department_id',$request->id)->where('status',1)->where('flag',1)->get();
        return json_encode($users);

    }
    public function fetch_department(Request $request){
        $department=Client::where('parent_id',$request->id)->get();
        return  json_encode($department);
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
        $user->tcat_id=$request->tcat_id;
        $hashed = Hash::make("123456");
        $user->password= $hashed;
        $user->email=$request->email;
        $user->flag=$request->roster;
        $user->fixed_salary=$request->fixed_salary;
        $user->hour_rate_salary=$request->hour_rate_salary;
        $user->role_id=$request->role_id;
        $user->designation_id=$request->designation_id;
        if(isset($request->sub_dep_unit)){
            $user->department_id=$request->sub_dep_unit;
        }else{
            $user->department_id=$request->deparment_id;
        }
        $user->dob=$request->dob;
        $user->land_line_number=$request->land_line_number;
        $user->mob_number_2=$request->mobile_number_2;
        $user->address=$request->address;
        $user->gender=$request->gender;

        if($user->save()){

            Alert::success('Record Update Successfully ! Thank You');
            return redirect('users');

        }
    }
    public function show($id){



        $data['user_Files']=UserFile::where('user_id',$id)->get();
        $data['user']=DB::table('users as u')
        ->select('u.id as user_id', 'u.name as user_name','u.father_name','u.mobile_number','u.image','u.email','u.cnic','u.file_number','u.dob','u.bps','d.title as designation_title','c.name as department_name','r.title as role_title')
        ->join('roles as  r','u.role_id','r.id')
        ->join('clients as c','u.department_id','c.id')
        ->join('designations as d','u.designation_id','d.id')
        ->where('u.id',$id)->first();
        return view('Users.show',$data);
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
        ->select('r.title as role_title',
        'd.title as designation_title',
        'users.id','users.dob',
        'users.cnic',
        'users.bps',
        'users.file_number',
        'users.name as name',
        'users.mobile_number as contact',
        'users.father_name as fname',
        'c.name as department_name',
        'users.status as status')
        ->join('roles as r','users.role_id','r.id')
        ->join('designations as d','users.designation_id','d.id')
        ->join('clients as c','users.department_id','c.id')
        ->whereIn('users.status',[0,2])
        ->where('c.parent_id',Session::get('dep_id'))
        ->get();

        return view('Users.inactive',['users' => $users,'page_title'=>'inactive employees']);

    }

    public function inactive_edit($id){


        // $user=User::find($id);
        $users="update users set status='2' where id='".$id."'";
        DB::select( DB::raw($users));
        Alert::success('Employee is Inactive Successfully ! Thank You');
        return redirect('users');
    }
    public function active_employee($id){

        $user=User::find($id);
        $users="update users set status='1' where id='".$id."'";
        DB::select( DB::raw($users));
        Alert::success('Employee is Active Successfully ! Thank You');
        return redirect('inactives');
    }

    public function absent_employee(){
        return 1;

    }

    public function api_authenticate(Request $request)
    {

        $credentials = $request->only('email', 'password');

        try {
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'invalid_credentials'], 400);
        }
        } catch (JWTException $e) {
            return response()->json($e);
        }
        $currentUser = auth()->user();
        return response()->json(compact('currentUser','token'));
    }

    public function user_dashboard(Request $request){
        $currentUser = auth()->user();
        return response()->json(compact('currentUser'));
    }
    public function generate_passwords(Request $request){
        $currentUser = auth()->user();
        return response()->json(compact('currentUser'));
    }

    public function bulk_tc_update(Request $request){
        $tcat_id = $request->tcat_id;
        $users = User::where('department_id',$request->id)->get();
        Client::where('id',$request->id)->update(['bulk_tcat_id'=>$tcat_id]);
        foreach($users as $id => $user){
        $luser = User::find($user->id);
        $luser->tcat_id = $tcat_id;
        $luser->save();
        $time_category_hist=array('user_id'=>$user->id,"tcat_id"=>$tcat_id,"join_date"=>date('Y-m-d'));
        DB::table('time_category_histories')->insert($time_category_hist);
        }
        return redirect()->back()->with('success','Time Categories Updated Successfully');
    }
    public function user_tc_update(Request $request){
        TimeCategoryHistory::where('id',$request->tcat_hist_id)->update(['tcat_id'=>$request->update_tcat_id,'join_date'=>$request->update_tcat_date]);
        return redirect()->back()->with('success','Time Category Updated Successfully');
    }

    public function allEmployees(Request $request){
        $data = [];
        $page_title = 'All Employees';
        $data['department_id'] = 'all';
        if($request->has('department_id') && $request->get('department_id')!='all'){

            $data['department_id'] = $request->department_id;
        }

        $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function(){
            return Client::generatetree(0,false,true);
        });

        $data['allregions'] = session()->get('allregions');
        $data['selected_dmgs'] = 'all';
        if(  $request->has('allregions'))
        {
            $data['selected_dmgs'] = $request->allregions;
        }

        $users = User::leftJoin('designations', 'users.designation_id', '=', 'designations.id')
                    ->leftJoin('clients', 'users.department_id', '=', 'clients.id')
            ->select('users.*')
            ->whereIn('users.role_id', [6,7])
            ->where('users.flag', 1)
            ->orderBy('users.bps','DESC')
            ->orderBy('users.name','ASC');

        if($request->has('ai_query') && $request->ai_query!=''){
            $nusers = clone $users;
            $nusers = $nusers->where(function($nusers) use ($request) {
                $nusers->where('users.tags', 'like', '%,'.$request->ai_query.',%');
            });
            $trecs = $nusers->count();

            if($trecs<1)
            {
                $nusers = clone $users;
                $nusers = $nusers->where('users.name', 'like', '%'.$request->ai_query.'%');
                $trecs = $nusers->count();


                if($trecs<1)
                {
                    $nusers = clone $users;
                    $nusers = $nusers->where('designations.title', 'like', '%'.$request->ai_query.'%');
                    $trecs = $nusers->count();
                    if($trecs<1)
                    {
                        $nusers = clone $users;
                        $nusers = $nusers->where('clients.name', 'like', '%'.$request->ai_query.'%');
                    }
                }
            }
            $users = $nusers;
        }

        if($request->department_id && $request->department_id !='all'){

            $filtered_departments= Cache::remember('filtered_departments_'.$data['department_id'], now()->addMinutes(1440), function() use($data) {
                return Client::getSubDepartments($data['department_id']);
            });
            $users = $users->whereIn('users.department_id', $filtered_departments);
        }

        if( $data['selected_dmgs'] &&  $data['selected_dmgs'] !="all"){
            $region_input = explode('_', $data['selected_dmgs']);
            $dmg_check = $region_input[0];
            $dmg_id = $region_input[1];

            if($dmg_check == 'reg'){
            $users = $users->where('users.region_id', $dmg_id);
            }
            if($dmg_check == 'zone'){
            $users = $users->where('users.zone_id', $dmg_id);
            }
            if($dmg_check == 'branch'){
            $users = $users->where('users.branch_id', $dmg_id);
            }
            $data['dmg_check'] = $dmg_check;
            $data['dmg_id'] = $dmg_id;
        }
        if($request->has('name') && $request->name!=''){
            $users = $users->where('users.name', 'like', '%'.$request->name.'%');
        }

        if($request->has('emp_id') && $request->emp_id!=''){
            $users = $users->where('users.id', $request->emp_id);
        }
        if($request->has('cnic') && $request->cnic!=''){
            $users = $users->where('users.cnic', $request->cnic);
        }

        if($request->has('status') && $request->status!='all'){
            $users = $users->where('users.status', $request->status);
        }elseif(!$request->has('status')){
            $users = $users->where('users.status', "1");
            $request->status=1;
        }

        if($request->has('field_staff') && $request->field_staff!='all'){
            $users = $users->where('users.field_staff', $request->field_staff);
        }
        if($request->has('emp_type') && $request->emp_type!='all'){
            $users = $users->where('users.emp_type', $request->emp_type);
        }

        if($request->has('bps') && $request->bps!=''){
            $bps_input = explode(':', $request->bps);
            if(count($bps_input)>1)
            {
                $users = $users->where('users.bps',$bps_input[0],$bps_input[1]);
            }
            else
            {
                $users = $users->where('users.bps', $request->bps);
            }
        }
        if($request->has('designation') && $request->designation!=''){
            $users = $users->where('designations.title', 'like', '%'.$request->designation.'%');
        }
        if($request->has('reg_date') && $request->reg_date!='') {
            $users = User::where('reg_date', $request->reg_date);
        }

        //dd($users->pluck('users.id'));

        $data['users'] = $users->paginate(20);
        $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
        $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
        //dd($data['users']);
        return view('Users.allemployees')->with([
        'data' => $data,
        'page_title' => $page_title
        ]);
    }

    public function employeeReport(Request $request)
    {
        if(!$request->has('from_date')){
            $request->from_date = date('Y-m-').'01';
            $data['from_date'] =  date('Y-m-').'01';
        }
        if(!$request->has('to_date') ){
            $request->to_date = date('Y-m-d');
            $data['to_date'] =  date('Y-m-d');
        }
        $time1='4:00';
        $time2='5:00';
        $result=strtotime($time1)+strtotime($time2);
        //dd($request);

        $dep_id=Session::get('dep_id');
        $date_from = ""; $date_to = "";
        if($request->has('from_date') && $request->has('to_date')){
            $date_from = $request->from_date;
            $date_to = $request->to_date;
        }

        //get user_id, department etc
        //dd($request->all());
        $user_id = $request->user_id;
        $user = User::find($user_id);
        //dd($user);
        $dep_id = $user->department_id;

        if(Session::get('user')->role_id == 1){
            $dep_id = 278;
        }

        $loged_in_department = Client::find($dep_id);

        //removed has links condition from these lines
        if(Session::get('user')->role_id == 11 && $loged_in_department->parent_id!=0){
            $dep_id = $loged_in_department->parent_id;
        }



        if($date_from && $date_to){

            $timecategory=TimeCategory::where('department_id',$request->deparment_id)->first();
            $this->validate($request, [
                'user_id' => 'required',
                'from_date' => 'required',
                'to_date'=>'required',
            ]);

            $data['from_date'] = $date_from;
            $data['to_date'] = $date_to;


            $individual_report_att = "SELECT
                    u.id as user_id,
                    u.name as user_name,
                    att.datetime as checktime,
                    att.mark_attendance,
                    att.dev_id,
                    date(att.datetime) as att_date,
                    tcat.id as tcat_id,
                    tcat.time_in as tcat_time_in,
                    tcat.is_night_shift as is_night_shift,
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
                    tcat.tc_saturday_in as satureday_in,
                    tcat.tc_saturday_out as satureday_out,
                    tcat.tc_sunday_in as sunday_in,
                    tcat.tc_sunday_out as sunday_out,
                    tcat.time_out as tcat_time_out,
                    tcat.grace_time as tcat_grace_time
                    from users u inner join
                    timenex_new.attendance att on u.id= att.user_id

                INNER join time_categories tcat on tcat.id=u.tcat_id
                where  (date(att.datetime)
                BETWEEN '".date('Y-m-d', strtotime($date_from.' -1 day'))."' AND '".$date_to."') and u.id='".$request->user_id."'
                group By att.datetime";

            $data['attendance'] = collect(DB::select( DB::raw($individual_report_att)));


            $timcat_history="SELECT date(time_category_histories.join_date) as date,
                users.id,
                time_categories.time_in as tcat_time_in,
                time_categories.is_night_shift as is_night_shift,
                time_categories.time_out as tcat_time_out,
                time_category_histories.is_current,
                time_categories.id as cat_id,
                time_categories.tc_monday_in as monday_in,
                time_categories.tc_monday_out as monday_out,
                time_categories.tc_tuesday_in as tuesday_in,
                time_categories.tc_tuesday_out as tuesday_out,
                time_categories.tc_wednesday_in as wednesday_in,
                time_categories.tc_wednesday_out as wednesday_out,
                time_categories.tc_thursday_in as thursday_in,
                time_categories.tc_thursday_out as thursday_out,
                time_categories.tc_friday_in as friday_in,
                time_categories.tc_friday_out as friday_out,
                time_categories.tc_saturday_in as satureday_in,
                time_categories.tc_saturday_out as satureday_out,
                time_categories.tc_sunday_in as sunday_in,
                time_categories.tc_sunday_out as sunday_out,

                time_categories.grace_time as tcat_grace_time
                FROM `time_category_histories`
                INNER JOIN users
                on users.id=time_category_histories.user_id
                INNER JOIN time_categories
                on time_categories.id=time_category_histories.tcat_id
                where users.id='".$request->user_id."' order by date asc";
            $data['timcat_his'] = collect(DB::select( DB::raw($timcat_history)));


            $for_dayoff="select * from users INNER join clients
                on clients.id=users.department_id
                INNER JOIN time_category_histories on time_category_histories.user_id=users.id
                INNER JOIN time_categories on time_categories.id=time_category_histories.tcat_id
                WHERE clients.id='".$request->deparment_id."' and users.id='".$request->user_id."'";
            $data['day_off'] = collect(DB::select( DB::raw($for_dayoff)));



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

            if(Session::get('role_id') == 7){
            //department head & we will show leave of its own department + sections
                $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$loged_in_department->id." or c.id = ".$loged_in_department->id;
            }else{

                $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$loged_in_department->id." or c.id = ".$loged_in_department->id;
                if($request->has('department')){
                    $child_departments = Client::wherein('parent_id',$request->deparment_id)->pluck('id');
                    $user_departments  = array_merge($child_departments->toArray(),$request->department);
                    $users_department_clause = implode(",",$user_departments);
                }
            }


            $holi_calen="SELECT id,title,user_id,date as
                holiday_date,department_id,date_from,date_to
                FROM holiday_calendars
                WHERE department_id in (".$users_department_clause.")
                and deleted_at is null and  date BETWEEN '".$request->from_date."' and '".$date_from."'";
            if($loged_in_department->sub_account==2){

                $holi_calen="SELECT id,title,user_id,date as
                holiday_date,department_id,date_from,date_to
                FROM holiday_calendars
                WHERE department_id in (".$users_department_clause.")
                and deleted_at is null  and  date BETWEEN '".$request->from_date."' and '".$date_to."'";

            }
            $dedicate_off_day="select * FROM employee_dedicated_off_days where user_id= '".$request->user_id."'";
            $data['dedicate_off_day'] = collect(DB::select( DB::raw($dedicate_off_day)));

            $data['holidays'] = collect(DB::select( DB::raw($holi_calen)));

            //SELECT min(date(datetime)) FROM `attendance` WHERE user_id=22211;

            $data['emp_min_transaction']=DB::table('attendance')->selectRaw('min(date(datetime)) as date')->where('user_id',$request->user_id)->first();

            $data['user_info_query'] = DB::table('users as u')
            ->join('clients as c','c.id','u.department_id','left')
            ->leftjoin('designations as d','d.id','u.designation_id','left')
            ->where('u.id',$request->user_id)
            ->selectRaw('c.name,d.title,u.id,u.name as user_name,u.image,u.reg_date,u.cnic_issue_date,cnic_expiry_date')
            ->groupBy('u.name')
            ->get();
        }
        $data['user_id'] = $request->user_id;
        $data['page_title'] = "Employee Report";
        //$data['linked'] = $request->linked;
        return view('Users.employee_report',$data);
    }

    public function employeeYearlyReport(Request $request)
    {
        $current_month = $request->month;
        $current_year = $request->year;
        $first_attendance = Attendance::where('user_id', $request->user_id)
        ->orderBy('datetime')
        ->first();
        $first_date = $first_attendance->datetime;

        if(!$request->has('from_date')){
            $data['from_date'] =  date('Y-m-01');
            $date_from = date('Y-m-01');
        }
        if(!$request->has('to_date') ){
            $data['to_date'] =  date('Y-m-d');
            $date_to =  date('Y-m-d');
        }



        $time1='4:00';
        $time2='5:00';
        $result=strtotime($time1)+strtotime($time2);

        $dep_id=Session::get('dep_id');
        $date_from = ""; $date_to = "";
        if($request->has('from_date') && $request->has('to_date'))
        {
            $date_from = $request->from_date;
            $date_to = $request->to_date;
        }
        else
        {
            $date_from =  date('Y-m-01');
            $date_to = date('Y-m-d');
        }


        $user_id = $request->user_id;
        $user = User::find($user_id);

        $dep_id = $user->department_id;

        if(Session::get('user')->role_id == 1){
            $dep_id = 278;
        }

        $loged_in_department = Client::find($dep_id);

        //removed has links condition from these lines
        if(Session::get('user')->role_id == 11 && $loged_in_department->parent_id!=0){
            $dep_id = $loged_in_department->parent_id;
        }

        if($date_from && $date_to){
        // dd($date_from);
        $timecategory=TimeCategory::where('department_id',$request->deparment_id)->first();


        $data['from_date'] = $date_from;
        $data['to_date'] = $date_to;



        $individual_report_att = "SELECT
            u.id as user_id,
            u.name as user_name,
            att.datetime as checktime,
            att.mark_attendance,
            att.dev_id,
            date(att.datetime) as att_date,
            tcat.id as tcat_id,
            tcat.time_in as tcat_time_in,
            tcat.is_night_shift as is_night_shift,
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
            tcat.tc_saturday_in as satureday_in,
            tcat.tc_saturday_out as satureday_out,
            tcat.tc_sunday_in as sunday_in,
            tcat.tc_sunday_out as sunday_out,
            tcat.time_out as tcat_time_out,
            tcat.grace_time as tcat_grace_time
            from users u inner join
            timenex_new.attendance att on u.id= att.user_id

            INNER join time_categories tcat on tcat.id=u.tcat_id
            where  (date(att.datetime)
            BETWEEN '".date('Y-m-d', strtotime($date_from.' -1 day'))."' AND '".$date_to."') and u.id='".$request->user_id."'
            group By att.datetime";

        $data['attendance'] = collect(DB::select( DB::raw($individual_report_att)));

        $timcat_history="SELECT date(time_category_histories.join_date) as date,
            users.id,
            time_categories.time_in as tcat_time_in,
            time_categories.is_night_shift as is_night_shift,
            time_categories.time_out as tcat_time_out,
            time_category_histories.is_current,
            time_categories.id as cat_id,
            time_categories.tc_monday_in as monday_in,
            time_categories.tc_monday_out as monday_out,
            time_categories.tc_tuesday_in as tuesday_in,
            time_categories.tc_tuesday_out as tuesday_out,
            time_categories.tc_wednesday_in as wednesday_in,
            time_categories.tc_wednesday_out as wednesday_out,
            time_categories.tc_thursday_in as thursday_in,
            time_categories.tc_thursday_out as thursday_out,
            time_categories.tc_friday_in as friday_in,
            time_categories.tc_friday_out as friday_out,
            time_categories.tc_saturday_in as satureday_in,
            time_categories.tc_saturday_out as satureday_out,
            time_categories.tc_sunday_in as sunday_in,
            time_categories.tc_sunday_out as sunday_out,

            time_categories.grace_time as tcat_grace_time
            FROM `time_category_histories`
            INNER JOIN users
            on users.id=time_category_histories.user_id
            INNER JOIN time_categories
            on time_categories.id=time_category_histories.tcat_id
            where users.id='".$request->user_id."' order by date asc";
        $data['timcat_his'] = collect(DB::select( DB::raw($timcat_history)));



        $for_dayoff="select * from users INNER join clients
            on clients.id=users.department_id
            INNER JOIN time_category_histories on time_category_histories.user_id=users.id
            INNER JOIN time_categories on time_categories.id=time_category_histories.tcat_id
            WHERE clients.id='".$request->deparment_id."' and users.id='".$request->user_id."'";
        $data['day_off'] = collect(DB::select( DB::raw($for_dayoff)));



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

            if(Session::get('role_id') == 7){
                //department head & we will show leave of its own department + sections
                $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$loged_in_department->id." or c.id = ".$loged_in_department->id;
            }
            else{

                $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$loged_in_department->id." or c.id = ".$loged_in_department->id;
                if($request->has('department')){
                    $child_departments = Client::wherein('parent_id',$request->deparment_id)->pluck('id');
                    $user_departments  = array_merge($child_departments->toArray(),$request->department);
                    $users_department_clause = implode(",",$user_departments);
                }
            }


            $holi_calen="SELECT id,title,user_id,date as
                holiday_date,department_id,date_from,date_to
                FROM holiday_calendars
                WHERE department_id in (".$users_department_clause.")
                and deleted_at is null and  date BETWEEN '".$request->from_date."' and '".$date_from."'";
            if($loged_in_department->sub_account==2){

                $holi_calen="SELECT id,title,user_id,date as
                    holiday_date,department_id,date_from,date_to
                    FROM holiday_calendars
                    WHERE department_id in (".$users_department_clause.")
                    and deleted_at is null  and  date BETWEEN '".$request->from_date."' and '".$date_to."'";

            }
            $dedicate_off_day="select * FROM employee_dedicated_off_days where user_id= '".$request->user_id."'";
            $data['dedicate_off_day'] = collect(DB::select( DB::raw($dedicate_off_day)));

            $data['holidays'] = collect(DB::select( DB::raw($holi_calen)));

            //SELECT min(date(datetime)) FROM `attendance` WHERE user_id=22211;

            $data['emp_min_transaction']=DB::table('attendance')->selectRaw('min(date(datetime)) as date')->where('user_id',$request->user_id)->first();

            $data['user_info_query'] = DB::table('users as u')
            ->join('clients as c','c.id','u.department_id','left')
            ->leftjoin('designations as d','d.id','u.designation_id','left')
            ->where('u.id',$request->user_id)
            ->selectRaw('c.name,d.title,u.id,u.name as user_name,u.image,u.reg_date,u.cnic_issue_date,cnic_expiry_date')
            ->groupBy('u.name')
            ->get();

        }

        $data['user_id'] = $request->user_id;
        $data['page_title'] = "Employee Report";
        //$data['linked'] = $request->linked;



        $start_date = new DateTime($data['from_date']);
        $to_date = new DateTime($data['to_date']);
        $counter = 0;
        $sum = 0;
        $total_exp_hr = 0;
        $total_hr_work = 0;
        $total_present = 0;
        $total_late = 0;
        $total_absent = 0;
        $total_holiday=0;
        $total_leave = 0;
        $leave = 0;
        $total_day_off=0;
        $total_work = [];
        $expected_hours_work = [];
        $hour_short_array=[];
        $overtime_array=[];
        $attach_hrs=[];
        $monday_in = "";
        $monday_out = "";
        $tuesday_in = "";
        $tuesday_out = "";
        $wednesday_in = "";
        $wednesday_out = "";
        $thursday_in = "";
        $thursday_out = "";
        $friday_in = "";
        $friday_out = "";
        $satureday_in = "";
        $satureday_out = "";
        $sunday_in = "";
        $sunday_out = "";
        $tcat_grace_time = "";
        $night_shift = "";
        $exp_tcat_time_in = "";
        $exp_tcat_time_out = "";
        $dataLeave=[];

        $attendance=$data['attendance'];
        $attachs=$data['attachs'];
        $leaves=$data['leaves'];
        $day_off=$data['day_off'];
        $holidays=$data['holidays'];
        $timcat_his=$data['timcat_his'];


        for($i = $start_date;$i<=$to_date;$i->modify('+1 day')){

        $day = date("D", strtotime($i->format('d-m-Y')));
        $checktimes = [];
        $date_attendance = $attendance->where('att_date', $i->format('Y-m-d'));
        foreach ($date_attendance as $key => $val)
        {
            $checktimes[] = $val->checktime;
        }

        $date_attach = $attachs->where('attach_date', $i->format('Y-m-d'));

        $date_leaves = $leaves->where('leave_date', $i->format('Y-m-d'));





        $timein = "--";
        $timeout = "--";
        $hours = "--";
        $expected_hours = "--";
        $hours_calculation = "--";
        $time_category = "--";
        $status = "Absent";
        $timecalculation_remarks = "--";
        $pdate = date('Y-m-d', strtotime('-1 day', strtotime($i->format('Y-m-d'))));
        $pdate_attendance = $attendance->where('att_date', $pdate);

        if ($date_attendance->count() > 0)
        {

            $time_cat_history = $timcat_his->where('date', '<=', $date_attendance->first()->att_date);

            if ($time_cat_history->count() == 1)
            {
                $tcat_hist = $time_cat_history->first();
            }
            elseif ($time_cat_history->count() > 1)
            {
                $tcat_hist = $time_cat_history->last();
            }

            $night_shift = $tcat_hist->is_night_shift;
            $exp_tcat_time_in = $tcat_hist->tcat_time_in;
            $exp_tcat_time_out = $tcat_hist->tcat_time_out;
            $monday_in = $tcat_hist->monday_in;
            $monday_out = $tcat_hist->monday_out;
            $tuesday_in = $tcat_hist->tuesday_in;
            $tuesday_out = $tcat_hist->tuesday_out;
            $wednesday_in = $tcat_hist->wednesday_in;
            $wednesday_out = $tcat_hist->wednesday_out;
            $thursday_in = $tcat_hist->thursday_in;
            $thursday_out = $tcat_hist->thursday_out;
            $friday_in = $tcat_hist->friday_in;
            $friday_out = $tcat_hist->friday_out;
            $satureday_in = $tcat_hist->satureday_in;
            $satureday_out = $tcat_hist->satureday_out;
            $sunday_in = $tcat_hist->sunday_in;
            $sunday_out = $tcat_hist->sunday_out;
            $tcat_grace_time = $tcat_hist->tcat_grace_time;

            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
            {
                $selectedTime = $monday_in;
                $min = date('i', strtotime($tcat_grace_time));
                $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_in = date('H:i:s', $endTime);
                $selectedTime = $monday_out;
                $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_out = date('H:i:s', $time_out);
                $expected_time_in = $time_in;
                $expected_seconds = strtotime($monday_out) - strtotime($monday_in);
                $expected_hours= timeCategoryTotalHourWork($expected_seconds);

            }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday"){

                $selectedTime = $tuesday_in;
                $min = date('i', strtotime($tcat_grace_time));
                $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));

                $time_in = date('H:i:s', $endTime);
                $expected_time_in = $time_in;
                $selectedTime = $tuesday_out;
                $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_out = date('H:i:s', $time_out);
                $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                $expected_hours= timeCategoryTotalHourWork($expected_seconds);
            }
            elseif (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday"){

                $selectedTime = $wednesday_in;
                $min = date('i', strtotime($tcat_grace_time));
                $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_in = date('H:i:s', $endTime);

                $expected_time_in = $time_in;
                $selectedTime = $wednesday_out;
                $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_out = date('H:i:s', $time_out);
                $expected_seconds = strtotime($wednesday_out) - strtotime($wednesday_in);
                $expected_hours= timeCategoryTotalHourWork($expected_seconds);

            }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Thursday"){

                $selectedTime = $thursday_in;
                $min = date('i', strtotime($tcat_grace_time));
                $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_in = date('H:i:s', $endTime);
                $expected_time_in = $time_in;
                $selectedTime = $thursday_out;
                $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_out = date('H:i:s', $time_out);
                $expected_seconds = strtotime($thursday_out) - strtotime($thursday_in);
                $expected_hours = date('h:i:s', $expected_seconds);
            }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Friday"){
                $selectedTime = $friday_in;
                $min = date('i', strtotime($tcat_grace_time));
                $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_in = date('H:i:s', $endTime);
                $expected_time_in = $time_in;
                $selectedTime = $friday_out;
                $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_out = date('H:i:s', $time_out);
                $expected_seconds = strtotime($friday_out) - strtotime($friday_in);
                $expected_hours = date('h:i:s', $expected_seconds);

            }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Saturday"){

                $selectedTime = $satureday_in;
                $min = date('i', strtotime($tcat_grace_time));
                $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_in = date('H:i:s', $endTime);
                $expected_time_in = $time_in;
                $selectedTime = $satureday_out;
                $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_out = date('H:i:s', $time_out);
                $expected_seconds = strtotime($satureday_out) - strtotime($satureday_in);
                $expected_hours = date('h:i:s', $expected_seconds);


            }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Sunday"){

                $selectedTime = $sunday_in;
                $min = date('i', strtotime($tcat_grace_time));
                $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_in = date('H:i:s', $endTime);
                $expected_time_in = $time_in;
                $selectedTime = $sunday_out;
                $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                $time_out = date('H:i:s', $time_out);
                $expected_seconds = strtotime($sunday_out) - strtotime($sunday_in);
                $expected_hours = date('h:i:s', $expected_seconds);
            }
            sort($checktimes);

            if($night_shift == 1){
                $date = $i->format('Y-m-d');
                $pdate = date('Y-m-d', strtotime('-1 day', strtotime($i->format('Y-m-d'))));
                $ndate = date('Y-m-d', strtotime('+1 day', strtotime($i->format('Y-m-d'))));
                $pdate_attendance = $attendance->where('att_date', $pdate);
                $ndate_attendance = $attendance->where('att_date', $ndate);
                $exp_tcat_time_in = $tcat_hist->tcat_time_in;
                $exp_tcat_time_out = $tcat_hist->tcat_time_out;
                $monday_in = $tcat_hist->monday_in;
                $monday_out = $tcat_hist->monday_out;
                $tuesday_in = $tcat_hist->tuesday_in;
                $tuesday_out = $tcat_hist->tuesday_out;
                $wednesday_in = $tcat_hist->wednesday_in;
                $wednesday_out = $tcat_hist->wednesday_out;
                $thursday_in = $tcat_hist->thursday_in;
                $thursday_out = $tcat_hist->thursday_out;
                $friday_in = $tcat_hist->friday_in;
                $friday_out = $tcat_hist->friday_out;
                $satureday_in = $tcat_hist->satureday_in;
                $satureday_out = $tcat_hist->satureday_out;
                $sunday_in = $tcat_hist->sunday_in;
                $sunday_out = $tcat_hist->sunday_out;
                $day_in_pointer = strtolower(date("l",strtotime($date)))."_in";
                if($day_in_pointer == 'saturday_in'){
                    $day_in_pointer = "satureday_in";
                }
                $nchecktimes = [];
                foreach ($ndate_attendance as $key => $val)
                {
                    $nchecktimes[] = $val->checktime;
                }
                sort($nchecktimes);
                $timein = "--";
                if (count($checktimes) > 0)
                {
                    $ctimein = $checktimes[count($checktimes) - 1];
                    $timein_limit = date($date." H:i:s", strtotime('-120 minutes', strtotime($date . " " . $time_in)));
                    if ($ctimein > $timein_limit)
                    {
                        $timein = $ctimein;
                    }
                }
                if (count($nchecktimes) > 0)
                {
                    sort($nchecktimes);
                    $timeout_limit = date($ndate." H:i:s", strtotime('+120 minutes', strtotime($ndate . " " . $time_out)));
                    if ($nchecktimes[0] < $timeout_limit)
                    {
                        $timeout = $nchecktimes[0];

                    }
                }
                if(is_null($tcat_hist->$day_in_pointer)){
                    if(count($checktimes) == 1){
                        $timein = $checktimes[0];
                        $timeout = "--";
                    }else if(count($checktimes)>1){
                        $timein = $checktimes[0];
                        $timeout = $checktimes[count($checktimes)-1];
                    }
                }

                if(date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                    $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                elseif(date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                    $time_category = '[ ' . $tuesday_in . '-' . $tuesday_out . ' ]';
                elseif(date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                    $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                elseif(date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                    $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                elseif(date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                    $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                elseif(date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                    $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                elseif(date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                    $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';


                $checktimes = [];
                if (isset($timein))
                {
                    $checktimes[] = $timein;
                    $timein = $timein;
                }
                if (isset($timeout))
                {
                    $checktimes[] = $timeout;
                }
            }else{
            $timein = $checktimes[0];
            $timeout = $checktimes[count($checktimes) - 1];
            }

            if ($timein == "--" || $timeout == "--")
            {
                $seonds = 0;
            }else{
                $seonds = strtotime($timeout) - strtotime($timein);
            }

            $date = $date_attendance->first()->att_date;
            $hours = date("H:i:s", $seonds);
            $total_work[] = $hours;
            $expected_hours_work[] = $expected_hours;
            $checktime = date("H:i", strtotime($date_attendance->first()->checktime));
            if (strtotime($hours) > strtotime($expected_hours))
            {
                $timecalculation = strtotime($hours) - strtotime($expected_hours);
            }
            else
            {
                $timecalculation = strtotime($expected_hours) - strtotime($hours);
            }
            $timecalculation_remarks = date('H:i:s', $timecalculation);
            $expected_time = date("H:i:s", strtotime($timein));
            $sum += $expected_seconds;
            if ($selectedTime == null)
            {
                $time_in = "--";
                $timein = "--";
                $hours = "--";
                $expected_hours = "--";
                $expected_time = "--";

            }
            else
            {

            $attach = $attachs->where('attach_date', $i->format('Y-m-d'));


            $attach_date = "";
            if ($attach->count() > 0)
            {
            $attach_date = $attach->first()->attach_date;

            }

            if ($expected_time <= $time_in && $expected_time!="00:00:00")
            {

            $total_exp_hr += strtotime($expected_hours);
            $total_hr_work += strtotime($hours);
            $total_present += 1;
            if ($date_attendance->first()->mark_attendance == 1)
            {
            $status = 'Present *';
            }
            else
            {
            $status = 'Present';
            }

            $flag = 1;

            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
            {

            $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
            {

            $time_category = '[ ' . $tuesday_in . '-' . $tuesday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
            {

            $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
            {

            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
            {

            $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
            {

            $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
            {

            $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';
            }

            }elseif ($expected_time > $time_in ){

            $expect_time_check = strtotime($expected_hours);
            $total_exp_hr += strtotime($expected_hours);

            $total_hr_work += strtotime($hours);
            $hour_time_check = strtotime($hours);
            $diff = $expect_time_check - $hour_time_check;

            $hours_calculation = date('H:i:s', $diff) . ' time late';

            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
            {

            $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
            {

            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
            {

            $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
            {

            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
            {

            $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
            {

            $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
            {

            $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';

            }
            if ($date_attendance->first()->mark_attendance == 1)
            {
            $status = 'Late *';
            }
            else
            {

            $status = 'Late';

            }
            $total_late += 1;

            }
            }
            }else{
            if($pdate_attendance->count()>0){
            $time_cat_history = $timcat_his->where('date', '<=', $pdate_attendance->first()
            ->att_date);

            if ($time_cat_history->count() == 1)
            {
            $tcat_hist = $time_cat_history->first();
            }
            elseif ($time_cat_history->count() > 1)
            {
            $tcat_hist = $time_cat_history->last();
            }

            $night_shift = $tcat_hist->is_night_shift;
            $exp_tcat_time_in = $tcat_hist->tcat_time_in;
            $exp_tcat_time_out = $tcat_hist->tcat_time_out;
            $monday_in = $tcat_hist->monday_in;
            $monday_out = $tcat_hist->monday_out;
            $tuesday_in = $tcat_hist->tuesday_in;
            $tuesday_out = $tcat_hist->tuesday_out;
            $wednesday_in = $tcat_hist->wednesday_in;
            $wednesday_out = $tcat_hist->wednesday_out;
            $thursday_in = $tcat_hist->thursday_in;
            $thursday_out = $tcat_hist->thursday_out;
            $friday_in = $tcat_hist->friday_in;
            $friday_out = $tcat_hist->friday_out;
            $satureday_in = $tcat_hist->satureday_in;
            $satureday_out = $tcat_hist->satureday_out;
            $sunday_in = $tcat_hist->sunday_in;
            $sunday_out = $tcat_hist->sunday_out;
            $tcat_grace_time = $tcat_hist->tcat_grace_time;

            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday"){
            $selectedTime = $monday_in;
            $min = date('i', strtotime($tcat_grace_time));
            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_in = date('H:i:s', $endTime);
            $selectedTime = $monday_out;
            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_out = date('H:i:s', $time_out);
            $expected_time_in = $time_in;
            $expected_seconds = strtotime($monday_out) - strtotime($monday_in);
            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
            }
            elseif (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday"){

            $selectedTime = $tuesday_in;
            $min = date('i', strtotime($tcat_grace_time));
            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));

            $time_in = date('H:i:s', $endTime);
            $expected_time_in = $time_in;
            $selectedTime = $tuesday_out;
            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_out = date('H:i:s', $time_out);
            $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
            }
            elseif (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday"){

            $selectedTime = $wednesday_in;
            $min = date('i', strtotime($tcat_grace_time));
            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_in = date('H:i:s', $endTime);

            $expected_time_in = $time_in;
            $selectedTime = $wednesday_out;
            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_out = date('H:i:s', $time_out);
            $expected_seconds = strtotime($wednesday_in) - strtotime($wednesday_in);
            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
            }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Thursday"){

            $selectedTime = $thursday_in;
            $min = date('i', strtotime($tcat_grace_time));
            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_in = date('H:i:s', $endTime);
            $expected_time_in = $time_in;
            $selectedTime = $thursday_out;
            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_out = date('H:i:s', $time_out);
            $expected_seconds = strtotime($thursday_out) - strtotime($thursday_in);

            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
            }elseif(date('l', strtotime($i->format('Y-m-d'))) == "Friday"){
            $selectedTime = $friday_in;
            $min = date('i', strtotime($tcat_grace_time));
            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_in = date('H:i:s', $endTime);
            $expected_time_in = $time_in;
            $selectedTime = $friday_out;
            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_out = date('H:i:s', $time_out);
            $expected_seconds = strtotime($friday_out) - strtotime($friday_in);
            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
            }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Saturday"){
            $selectedTime = $satureday_in;
            $min = date('i', strtotime($tcat_grace_time));
            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_in = date('H:i:s', $endTime);
            $expected_time_in = $time_in;
            $selectedTime = $satureday_out;
            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_out = date('H:i:s', $time_out);
            $expected_seconds = strtotime($satureday_out) - strtotime($satureday_in);
            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
            }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Sunday"){
            $selectedTime = $sunday_in;
            $min = date('i', strtotime($tcat_grace_time));
            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_in = date('H:i:s', $endTime);
            $expected_time_in = $time_in;
            $selectedTime = $sunday_out;
            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
            $time_out = date('H:i:s', $time_out);
            $expected_seconds = strtotime($sunday_out) - strtotime($sunday_in);
            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
            }

            sort($checktimes);
            if ($night_shift == 1)
            {
            $pdate = date('Y-m-d', strtotime('-1 day', strtotime($i->format('Y-m-d'))));
            $ndate = date('Y-m-d', strtotime('+1 day', strtotime($i->format('Y-m-d'))));
            $pdate_attendance = $attendance->where('att_date', $pdate);
            $ndate_attendance = $attendance->where('att_date', $ndate);

            $nchecktimes = [];
            foreach ($ndate_attendance as $key => $val)
            {
            $nchecktimes[] = $val->checktime;
            }
            sort($checktimes);
            $timein = "--";
            if (count($checktimes) > 0)
            {
            $ctimein = $checktimes[count($checktimes) - 1];
            $timein_limit = date("Y-m-d H:i:s", strtotime('-120 minutes', strtotime($date . " " . $time_in)));
            if ($ctimein > $timein_limit)
            {
            $timein = $ctimein;
            }
            }
            if (count($nchecktimes) > 0)
            {
            sort($nchecktimes);
            $timeout_limit = date("Y-m-d H:i:s", strtotime('+120 minutes', strtotime($ndate . " " . $time_out)));
            if ($nchecktimes[0] < $timeout_limit)
            {
            $timeout = $nchecktimes[0];

            }
            }

            $checktimes = [];
            if (isset($timein))
            {
            $checktimes[] = $timein;
            $timein = $timein;
            }
            if (isset($timeout))
            {
            $checktimes[] = $timeout;
            }
            }
            if ($timein == "--" || $timeout == "--")
            {
            $seonds = 0;
            }
            else
            {
            $seonds = strtotime($timeout) - strtotime($timein);
            }

            $date = $pdate_attendance->first()->att_date;

            $hours = date("H:i:s", $seonds);

            if($status=="Absent"){
            $total_work[] = '00:00';

            $expected_hours_work[] = '00:00';

            }else{
            $total_work[] = $hours;

            $expected_hours_work[] = $expected_hours;
            }

            $checktime = date("H:i", strtotime($pdate_attendance->first()
            ->checktime));
            if (strtotime($hours) > strtotime($expected_hours))
            {
            $timecalculation = strtotime($hours) - strtotime($expected_hours);
            }
            else
            {
            $timecalculation = strtotime($expected_hours) - strtotime($hours);
            }
            $timecalculation_remarks = date('H:i:s', $timecalculation);
            $expected_time = date("H:i:s", strtotime($timein));
            $sum += $expected_seconds;


            if($selectedTime == null || $status=="Absent" )
            {

            $time_in = "--";
            $timein = "--";
            $hours = "--";
            $expected_hours = "--";

            $expected_time = "--";
            }else{

            $attach = $attachs->where('attach_date', $i->format('Y-m-d'));


            $attach_date = "";
            if ($attach->count() > 0)
            {
            $attach_date = $attach->first()->attach_date;

            }
            if ($expected_time <= $time_in )
            {

            $total_exp_hr += strtotime($expected_hours);
            $total_hr_work += strtotime($hours);
            $total_present += 1;
            if ($date_attendance->first()->mark_attendance == 1)
            {
            $status = 'Present *';
            }
            else
            {
            $status = 'Present';
            }

            $flag = 1;

            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
            {

            $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
            {

            $time_category = '[ ' . $tuesday_in . '-' . $tuesday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
            {

            $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
            {

            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
            {

            $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
            {

            $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
            {

            $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';
            }
            }elseif ($expected_time > $time_in){
            $expect_time_check = strtotime($expected_hours);
            $total_exp_hr += strtotime($expected_hours);


            $total_hr_work += strtotime($hours);
            $hour_time_check = strtotime($hours);
            $diff = $expect_time_check - $hour_time_check;

            $hours_calculation = date('H:i:s', $diff) . ' time late';

            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
            {

            $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
            {

            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
            {

            $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
            {

            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
            {

            $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
            {

            $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
            }
            else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
            {

            $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';
            }
            if ($date_attendance->first()->mark_attendance == 1)
            {
            $status = 'Late *';
            }
            else
            {

            $status = 'Late';

            }
            $total_late += 1;
            }
            }
            }


            if($date_attach->count()>0){
            foreach ($date_attach as $key => $val)
            {

            $time_cat_history= $timcat_his->where('date', '<', $val->attach_date);

            if ($time_cat_history->count() == 1)
            {
            $tcat_hist = $time_cat_history->first();
            }
            elseif ($time_cat_history->count() > 1)
            {
            $tcat_hist = $time_cat_history->last();
            }
            $monday_in = $tcat_hist->monday_in;
            $monday_out = $tcat_hist->monday_out;
            $tuesday_in = $tcat_hist->tuesday_in;
            $tuesday_out = $tcat_hist->tuesday_out;
            $wednesday_in = $tcat_hist->wednesday_in;
            $wednesday_out = $tcat_hist->wednesday_out;
            $thursday_in = $tcat_hist->thursday_in;
            $thursday_out = $tcat_hist->thursday_out;
            $friday_in = $tcat_hist->friday_in;
            $friday_out = $tcat_hist->friday_out;
            $satureday_in = $tcat_hist->satureday_in;
            $satureday_out = $tcat_hist->satureday_out;
            $sunday_in = $tcat_hist->sunday_in;
            $sunday_out = $tcat_hist->sunday_out;
            $tcat_grace_time = $tcat_hist->tcat_grace_time;


            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
            {
            $expected_seconds = strtotime($monday_out) - strtotime($monday_in);
            $attach_hours= timeCategoryTotalHourWork($expected_seconds);

            }
            if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
            {
            $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
            $attach_hours= timeCategoryTotalHourWork($expected_seconds);

            }
            if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
            {
            $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
            }
            if(date('l', strtotime($i->format('Y-m-d'))) == "Wednesday"){
            $expected_seconds = strtotime($wednesday_out) - strtotime($wednesday_in);
            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
            }
            if(date('l', strtotime($i->format('Y-m-d'))) == "Thursday"){
            $expected_seconds = strtotime($thursday_out) - strtotime($thursday_in);
            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
            }
            if( date('l', strtotime($i->format('Y-m-d'))) == "Friday"){
            $expected_seconds = strtotime($friday_out) - strtotime($friday_in);
            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
            }
            if( date('l', strtotime($i->format('Y-m-d'))) == "Saturday"){
            $expected_seconds = strtotime($satureday_in) - strtotime($satureday_in);
            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
            }
            if( date('l', strtotime($i->format('Y-m-d'))) == "Sunday"){
            $expected_seconds = strtotime($sunday_out) - strtotime($sunday_in);
            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
            }
            $attach_hrs[]= $attach_hours;
            $status = 'Attach';
            }
            }elseif ($date_leaves->count() > 0){
            foreach ($date_leaves as $key => $val)
            {
            $dataLeave[]=$val->leave_date;
            if ($val->leave_type == 2)
            {
            $status = $val->title;
            }

            else
            {
            $status = 'Short Leave';
            }

            $total_leave +=1;
            $leave_type = $val->type;
            $flag = 2;

            }
            }
            }


            if ($status == 'Absent'){
            $expected_hours="--";
            $hours="--";
            $monday_off = "";
            $tuesday_off = "";
            $wednesday_off = "";
            $thursday_off = "";
            $friday_off = "";
            $satureday_off = "";
            $sunday_off = "";
            if ($day_off->count() > 0)
            {

            $day_off_base_on_time_cat_his = $day_off->where('join_date', '<=', $i->format('Y-m-d'));
            if ($day_off_base_on_time_cat_his->count() == 0)
            {
            $monday_off = "emp_not_register_in_date";
            $tuesday_off = "emp_not_register_in_date";
            $wednesday_off = "emp_not_register_in_date";
            $thursday_off = "emp_not_register_in_date";
            $friday_off = "emp_not_register_in_date";
            $satureday_off = "emp_not_register_in_date";
            $sunday_off = "emp_not_register_in_date";
            }
            elseif ($day_off_base_on_time_cat_his->count() == 1)
            {

            $monday_off = $day_off_base_on_time_cat_his->first()->tc_monday_in;
            $tuesday_off = $day_off_base_on_time_cat_his->first()->tc_tuesday_in;
            $wednesday_off = $day_off_base_on_time_cat_his->first()->tc_wednesday_in;
            $thursday_off = $day_off_base_on_time_cat_his->first()->tc_thursday_in;
            $friday_off = $day_off_base_on_time_cat_his->first()->tc_friday_in;

            $satureday_off = $day_off_base_on_time_cat_his->first()->tc_saturday_in;
            $sunday_off = $day_off_base_on_time_cat_his->first()->tc_sunday_in;

            }
            elseif ($day_off_base_on_time_cat_his->count() > 1)
            {
            $monday_off = $day_off_base_on_time_cat_his->last()->tc_monday_in;
            $tuesday_off = $day_off_base_on_time_cat_his->last()->tc_tuesday_in;
            $wednesday_off = $day_off_base_on_time_cat_his->last()->tc_wednesday_in;
            $thursday_off = $day_off_base_on_time_cat_his->last()->tc_thursday_in;
            $friday_off = $day_off_base_on_time_cat_his->last()->tc_friday_in;
            $satureday_off = $day_off_base_on_time_cat_his->last()->tc_saturday_in;
            $sunday_off = $day_off_base_on_time_cat_his->last()->tc_sunday_in;
            }
            $check_dedicated_dayoff = $dedicate_off_day->where('off_day_date', $i->format('Y-m-d'));
            if ($check_dedicated_dayoff->count() > 0)
            {
            $day_off_day = strtotime($check_dedicated_dayoff->first()
            ->off_day_date);
            $check_day_off_day = date('l', $day_off_day);

            if ($check_day_off_day == 'Monday')
            {
            $monday_off = "";
            }
            if ($check_day_off_day == 'Tuesday')
            {
            $tuesday_off = "";
            }
            if ($check_day_off_day == 'Wednesday')
            {
            $wednesday_off = "";
            }
            if ($check_day_off_day == 'Thursday')
            {
            $thursday_off = "";
            }
            if ($check_day_off_day == 'Friday')
            {
            $friday_off = "";

            }
            if ($check_day_off_day == 'Saturday')
            {
            $satureday_off = "";
            }
            if ($check_day_off_day == 'Sunday')
            {
            $sunday_off = "";
            }

            }
            else
            {
            $day_off_day = strtotime($i->format('Y-m-d'));

            }

            if (date('l', $day_off_day) == "Monday")
            {

            if (empty($monday_off))
            {
            $total_day_off += 1;

            }
            else
            {
            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
            ->first();
            if ($check_emp_holiday == null)
            {

            if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
            {
            $total_absent += 1;
            }

            }
            else
            {
            $total_holiday += 1;

            }
            }

            }
            if (date('l', $day_off_day) == "Tuesday")
            {
            if (empty($tuesday_off))
            {
            $total_day_off += 1;

            }
            else
            {
            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
            ->first();
            if ($check_emp_holiday == null)
            {

            if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
            {
            $total_absent += 1;
            }
            }
            else
            {
            $total_holiday += 1;
            }
            }

            }
            if (date('l', $day_off_day) == "Wednesday")
            {

            if (empty($wednesday_off))
            {
            $total_day_off += 1;
            }
            else
            {
            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
            ->first();
            if ($check_emp_holiday == null)
            {
            if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
            {
            $total_absent += 1;
            }
            }
            else
            {
            $total_holiday += 1;
            }
            }

            }
            if (date('l', $day_off_day) == "Thursday")
            {

            if (empty($thursday_off))
            {
            $total_day_off += 1;
            }
            else
            {
            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
            ->first();
            if ($check_emp_holiday == null)
            {
            if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
            {
            $total_absent += 1;
            }
            }
            else
            {
            $total_holiday += 1;
            }
            }

            }

            if (date('l', $day_off_day) == "Friday")
            {

            if (empty($friday_off))
            {

            $total_day_off += 1;
            }
            else
            {
            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
            ->first();
            if ($check_emp_holiday == null)
            {
            if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
            {
            $total_absent += 1;
            }
            }
            else
            {
            $total_holiday += 1;
            }
            }
            }
            if (date('l', $day_off_day) == "Saturday")
            {

            if (empty($satureday_off))
            {
            $total_day_off += 1;
            }
            else
            {
            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
            ->first();
            if ($check_emp_holiday == null)
            {

            if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
            {
            $total_absent += 1;
            }
            }
            else
            {
            $total_holiday += 1;
            }
            }

            }
            if (date('l', $day_off_day) == "Sunday")
            {


            if (empty($sunday_off))
            {

            $total_day_off += 1;
            }
            else
            {
            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
            ->first();
            if ($check_emp_holiday == null)
            {
            if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
            {
            $total_absent += 1;
            }
            }
            else
            {
            $total_holiday += 1;

            }
            }

            }

            }
            else
            {

            $in_var = strtolower(date('l', strtotime($i->format('Y-m-d'))))."_in";
            if($in_var == "saturday_in"){
            $in_var = "satureday_in";
            }
            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
            ->first();
            if ($check_emp_holiday == null)
            {

            if(is_null($$in_var)){
            $total_day_off += 1;
            }else{
            $total_absent += 1;
            }


            }else
            {
            $total_holiday += 1;
            }
            }
            }elseif ($status == 'Late'){

            if (!empty($attach_date))
            {
            }
            else
            {
            }

            ?>

            <?php
            }elseif ($status == 'Late *'){

            if (!empty($attach_date))
            {
            }
            else
            {
            }
            ?>


            <?php
            }elseif ($status == 'Present'){ ?>

            <?php
            if (!empty($attach_date))
            {
            }
            else
            {
            }

            ?>

            <?php
            }elseif ($status == 'Present *'){
            if (!empty($attach_date))
            {
            }
            else
            {

            }
            }elseif ($status == 'Attach'){

            }elseif ($flag == 2){

            $leave += 1;
            }

            if(sizeof($checktimes) == 1){
            if($selectedTime==null){
            }else{
            }
            }else{
            if(strtotime($hours)>strtotime($expected_hours)){
            $overtime_array[]=$timecalculation_remarks;
            }elseif(strtotime($hours) < strtotime($expected_hours)){
            $hour_short_array[]=$timecalculation_remarks;
            }
            }
        }


        $obj_new = (object) [
        'start_date' => $start_date,
        'to_date' => $to_date,
        'counter' => $counter,
        'sum' => $sum,
        'total_exp_hr' => $total_exp_hr,
        'total_hr_work' => $total_hr_work,
        'total_present' => $total_present,
        'total_late' => $total_late,
        'total_absent' => $total_absent,
        'total_holiday' => $total_holiday,
        'total_leave' => $total_leave,
        'leave' => $leave,
        'total_day_off' => $total_day_off,
        'total_work' => $total_work,
        'expected_hours_work' => $expected_hours_work,
        'hour_short_array' => $hour_short_array,
        'overtime_array' => $overtime_array,
        'attach_hrs' => $attach_hrs,
        ];
        // dd($obj_new);

        return view('Users.employee_report_monthly',$data);
    }

    public function monthlyReport(Request $request, $user_id){
       // dd($user_id);
        $user_information= DB::table('users as u')
            ->join('clients as c','c.id','u.department_id','left')
            ->leftjoin('designations as d','d.id','u.designation_id','left')
            ->where('u.id',$user_id)
            ->selectRaw('c.name,d.title,u.id,u.name as user_name,u.image,u.reg_date,u.cnic_issue_date,cnic_expiry_date')
            ->groupBy('u.name')
            ->first();
        $page_title = $user_information->user_name . ' Report';

        $first_attendance = Attendance::where('user_id', $user_id)
            ->orderBy('datetime')
            ->first();
        if(!$first_attendance){
            $monthlyYearlyAttendance = 0;
            return view('Users.monthly_report', compact('monthlyYearlyAttendance', 'user_information', 'page_title'));
        }
        $first_date = $first_attendance->datetime;

        $currentYear = now()->year;
        $currentMonth = now()->month;

        $startYear = date('Y', strtotime($first_date));
        $startMonth = date('m', strtotime($first_date));

        $monthlyYearlyAttendance =[];
        while ($startYear <= $currentYear) {
            $monthStartDate = date('Y-m-d', strtotime($first_date));
            if($startYear ==$currentYear && $startMonth == $currentMonth){
                $monthEndDate = date('Y-m-d');
            }
            else{
                $monthEndDate = date('Y-m-t', strtotime($first_date));
            }

            // Fetch attendance for the current month and user
            $attendance = $this->employeeYearlyReportOne( $request, $monthStartDate, $monthEndDate, $user_id);
            $monthlyYearlyAttendance[$startYear][$startMonth] = $attendance;
           // dd($monthlyYearlyAttendance);
            if ($startYear == $currentYear && $startMonth == $currentMonth) {
                break;
            }
            else{
                $first_date = date('Y-m-01', strtotime($first_date . ' +1 month'));
                $startMonth++;

                if ($startMonth > 12) {
                    $startMonth = 1; // Reset to 1 for the next year
                    $startYear++;
                }
            }


            // If the next month exceeds the current year, move to the next year

        }
        return view('Users.monthly_report', compact('monthlyYearlyAttendance', 'user_information', 'page_title'));




    }
    public  function employeeYearlyReportOne(Request $request, $from_date, $to_date, $user_id)
    {
        $request->from_date = $from_date;
        $request->to_date = $to_date;
        $date_from = $request->from_date;
        $date_to = $request->to_date;

        $user = User::find($user_id);
        $dep_id = $user->department_id;

        $time1='4:00';
        $time2='5:00';
        $result=strtotime($time1)+strtotime($time2);


        if(Session::get('user')->role_id == 1){
            $dep_id = 278;
        }

        $loged_in_department = Client::find($dep_id);

        //removed has links condition from these lines
        if(Session::get('user')->role_id == 11 && $loged_in_department->parent_id!=0){
            $dep_id = $loged_in_department->parent_id;
        }
        try{
            if($date_from && $date_to){
            // dd($date_from);
            $timecategory=TimeCategory::where('department_id',$request->deparment_id)->first();


            $data['from_date'] = $date_from;
            $data['to_date'] = $date_to;



            $individual_report_att = "SELECT
            u.id as user_id,
            u.name as user_name,
            att.datetime as checktime,
            att.mark_attendance,
            att.dev_id,
            date(att.datetime) as att_date,
            tcat.id as tcat_id,
            tcat.time_in as tcat_time_in,
            tcat.is_night_shift as is_night_shift,
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
            tcat.tc_saturday_in as satureday_in,
            tcat.tc_saturday_out as satureday_out,
            tcat.tc_sunday_in as sunday_in,
            tcat.tc_sunday_out as sunday_out,
            tcat.time_out as tcat_time_out,
            tcat.grace_time as tcat_grace_time
            from users u inner join
            timenex_new.attendance att on u.id= att.user_id

            INNER join time_categories tcat on tcat.id=u.tcat_id
            where  (date(att.datetime)
            BETWEEN '".date('Y-m-d', strtotime($date_from.' -1 day'))."' AND '".$date_to."') and u.id='".$user_id."'
            group By att.datetime";

            $data['attendance'] = collect(DB::select( DB::raw($individual_report_att)));

            $timcat_history="SELECT date(time_category_histories.join_date) as date,
            users.id,
            time_categories.time_in as tcat_time_in,
            time_categories.is_night_shift as is_night_shift,
            time_categories.time_out as tcat_time_out,
            time_category_histories.is_current,
            time_categories.id as cat_id,
            time_categories.tc_monday_in as monday_in,
            time_categories.tc_monday_out as monday_out,
            time_categories.tc_tuesday_in as tuesday_in,
            time_categories.tc_tuesday_out as tuesday_out,
            time_categories.tc_wednesday_in as wednesday_in,
            time_categories.tc_wednesday_out as wednesday_out,
            time_categories.tc_thursday_in as thursday_in,
            time_categories.tc_thursday_out as thursday_out,
            time_categories.tc_friday_in as friday_in,
            time_categories.tc_friday_out as friday_out,
            time_categories.tc_saturday_in as satureday_in,
            time_categories.tc_saturday_out as satureday_out,
            time_categories.tc_sunday_in as sunday_in,
            time_categories.tc_sunday_out as sunday_out,

            time_categories.grace_time as tcat_grace_time
            FROM `time_category_histories`
            INNER JOIN users
            on users.id=time_category_histories.user_id
            INNER JOIN time_categories
            on time_categories.id=time_category_histories.tcat_id
            where users.id='".$user_id."' order by date asc";
            $data['timcat_his'] = collect(DB::select( DB::raw($timcat_history)));



            $for_dayoff="select * from users INNER join clients
            on clients.id=users.department_id
            INNER JOIN time_category_histories on time_category_histories.user_id=users.id
            INNER JOIN time_categories on time_categories.id=time_category_histories.tcat_id
            WHERE clients.id='".$request->deparment_id."' and users.id='".$user_id."'";
            $data['day_off'] = collect(DB::select( DB::raw($for_dayoff)));



            $indivi_leav="SELECT tcat.title,tcat.type, leave_requests_details.leave_date as leave_date,leave_requests.leave_type
            FROM users
            INNER JOIN leave_requests on users.id=leave_requests.user_id
            INNER JOIN  time_categories tcat on tcat.id = leave_requests.leave_type_id
            INNER JOIN leave_requests_details ON leave_requests_details.parent_table_id=leave_requests.id
            WHERE users.id='".$user_id."'";
            $data['leaves'] = collect(DB::select( DB::raw($indivi_leav)));

            $indivi_attach="SELECT user_id,attach_date  FROM attach_employees_details
            WHERE user_id='".$user_id."'";
            $data['attachs'] = collect(DB::select( DB::raw($indivi_attach)));

            if(Session::get('role_id') == 7){
                //department head & we will show leave of its own department + sections
                $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$loged_in_department->id." or c.id = ".$loged_in_department->id;
            }
            else{

                $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$loged_in_department->id." or c.id = ".$loged_in_department->id;
                if($request->has('department')){
                    $child_departments = Client::wherein('parent_id',$request->deparment_id)->pluck('id');
                    $user_departments  = array_merge($child_departments->toArray(),$request->department);
                    $users_department_clause = implode(",",$user_departments);
                }
            }


            $holi_calen="SELECT id,title,user_id,date as
            holiday_date,department_id,date_from,date_to
            FROM holiday_calendars
            WHERE department_id in (".$users_department_clause.")
            and deleted_at is null and  date BETWEEN '".$request->from_date."' and '".$date_from."'";
            if($loged_in_department->sub_account==2){

                $holi_calen="SELECT id,title,user_id,date as
            holiday_date,department_id,date_from,date_to
            FROM holiday_calendars
            WHERE department_id in (".$users_department_clause.")
            and deleted_at is null  and  date BETWEEN '".$request->from_date."' and '".$date_to."'";

            }
            $dedicate_off_day="select * FROM employee_dedicated_off_days where user_id= '".$request->user_id."'";
            $data['dedicate_off_day'] = collect(DB::select( DB::raw($dedicate_off_day)));

            $data['holidays'] = collect(DB::select( DB::raw($holi_calen)));

            //SELECT min(date(datetime)) FROM `attendance` WHERE user_id=22211;

            $data['emp_min_transaction']=DB::table('attendance')->selectRaw('min(date(datetime)) as date')->where('user_id',$user_id)->first();

            $data['user_info_query'] = DB::table('users as u')
                ->join('clients as c','c.id','u.department_id','left')
                ->leftjoin('designations as d','d.id','u.designation_id','left')
                ->where('u.id',$user_id)
                ->selectRaw('c.name,d.title,u.id,u.name as user_name,u.image,u.reg_date,u.cnic_issue_date,cnic_expiry_date')
                ->groupBy('u.name')
                ->get();

        }
        } catch (\Exception $e) {

        }

        $data['user_id'] = $user_id;
        $data['page_title'] = "Employee Report";
        //$data['linked'] = $request->linked;



        $start_date = new DateTime($data['from_date']);
        $to_date = new DateTime($data['to_date']);
        $counter = 0;
        $sum = 0;
        $total_exp_hr = 0;
        $total_hr_work = 0;
        $total_present = 0;
        $total_late = 0;
        $total_absent = 0;
        $total_holiday=0;
        $total_leave = 0;
        $leave = 0;
        $total_day_off=0;
        $total_work = [];
        $expected_hours_work = [];
        $hour_short_array=[];
        $overtime_array=[];
        $attach_hrs=[];
        $monday_in = "";
        $monday_out = "";
        $tuesday_in = "";
        $tuesday_out = "";
        $wednesday_in = "";
        $wednesday_out = "";
        $thursday_in = "";
        $thursday_out = "";
        $friday_in = "";
        $friday_out = "";
        $satureday_in = "";
        $satureday_out = "";
        $sunday_in = "";
        $sunday_out = "";
        $tcat_grace_time = "";
        $night_shift = "";
        $exp_tcat_time_in = "";
        $exp_tcat_time_out = "";
        $dataLeave=[];

        $attendance=$data['attendance'];
        $attachs=$data['attachs'];
        $leaves=$data['leaves'];
        $day_off=$data['day_off'];
        $holidays=$data['holidays'];
        $timcat_his=$data['timcat_his'];


        for($i = $start_date;$i<=$to_date;$i->modify('+1 day')){

            $day = date("D", strtotime($i->format('d-m-Y')));
            $checktimes = [];
            $date_attendance = $attendance->where('att_date', $i->format('Y-m-d'));
            foreach ($date_attendance as $key => $val)
            {
                $checktimes[] = $val->checktime;
            }

            $date_attach = $attachs->where('attach_date', $i->format('Y-m-d'));

            $date_leaves = $leaves->where('leave_date', $i->format('Y-m-d'));





            $timein = "--";
            $timeout = "--";
            $hours = "--";
            $expected_hours = "--";
            $hours_calculation = "--";
            $time_category = "--";
            $status = "Absent";
            $timecalculation_remarks = "--";
            $pdate = date('Y-m-d', strtotime('-1 day', strtotime($i->format('Y-m-d'))));
            $pdate_attendance = $attendance->where('att_date', $pdate);

            if ($date_attendance->count() > 0)
            {

                $time_cat_history = $timcat_his->where('date', '<=', $date_attendance->first()->att_date);

                if ($time_cat_history->count() == 1)
                {
                    $tcat_hist = $time_cat_history->first();
                }
                elseif ($time_cat_history->count() > 1)
                {
                    $tcat_hist = $time_cat_history->last();
                }

                $night_shift = $tcat_hist->is_night_shift;
                $exp_tcat_time_in = $tcat_hist->tcat_time_in;
                $exp_tcat_time_out = $tcat_hist->tcat_time_out;
                $monday_in = $tcat_hist->monday_in;
                $monday_out = $tcat_hist->monday_out;
                $tuesday_in = $tcat_hist->tuesday_in;
                $tuesday_out = $tcat_hist->tuesday_out;
                $wednesday_in = $tcat_hist->wednesday_in;
                $wednesday_out = $tcat_hist->wednesday_out;
                $thursday_in = $tcat_hist->thursday_in;
                $thursday_out = $tcat_hist->thursday_out;
                $friday_in = $tcat_hist->friday_in;
                $friday_out = $tcat_hist->friday_out;
                $satureday_in = $tcat_hist->satureday_in;
                $satureday_out = $tcat_hist->satureday_out;
                $sunday_in = $tcat_hist->sunday_in;
                $sunday_out = $tcat_hist->sunday_out;
                $tcat_grace_time = $tcat_hist->tcat_grace_time;

                if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                {
                    $selectedTime = $monday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_in = date('H:i:s', $endTime);
                    $selectedTime = $monday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_time_in = $time_in;
                    $expected_seconds = strtotime($monday_out) - strtotime($monday_in);
                    $expected_hours= timeCategoryTotalHourWork($expected_seconds);

                }
                elseif (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                {

                    $selectedTime = $tuesday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));

                    $time_in = date('H:i:s', $endTime);
                    $expected_time_in = $time_in;
                    $selectedTime = $tuesday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                    $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                }
                elseif (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                {

                    $selectedTime = $wednesday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_in = date('H:i:s', $endTime);

                    $expected_time_in = $time_in;
                    $selectedTime = $wednesday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_seconds = strtotime($wednesday_out) - strtotime($wednesday_in);
                    $expected_hours= timeCategoryTotalHourWork($expected_seconds);

                }
                elseif (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                {

                    $selectedTime = $thursday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_in = date('H:i:s', $endTime);
                    $expected_time_in = $time_in;
                    $selectedTime = $thursday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_seconds = strtotime($thursday_out) - strtotime($thursday_in);
                    $expected_hours = date('h:i:s', $expected_seconds);
                }
                elseif (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                {
                    $selectedTime = $friday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_in = date('H:i:s', $endTime);
                    $expected_time_in = $time_in;
                    $selectedTime = $friday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_seconds = strtotime($friday_out) - strtotime($friday_in);
                    $expected_hours = date('h:i:s', $expected_seconds);

                }
                elseif (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                {

                    $selectedTime = $satureday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_in = date('H:i:s', $endTime);
                    $expected_time_in = $time_in;
                    $selectedTime = $satureday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_seconds = strtotime($satureday_out) - strtotime($satureday_in);
                    $expected_hours = date('h:i:s', $expected_seconds);


                }
                elseif (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                {

                    $selectedTime = $sunday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_in = date('H:i:s', $endTime);
                    $expected_time_in = $time_in;
                    $selectedTime = $sunday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_seconds = strtotime($sunday_out) - strtotime($sunday_in);
                    $expected_hours = date('h:i:s', $expected_seconds);
                }
                sort($checktimes);

                if($night_shift == 1){
                    $date = $i->format('Y-m-d');
                    $pdate = date('Y-m-d', strtotime('-1 day', strtotime($i->format('Y-m-d'))));
                    $ndate = date('Y-m-d', strtotime('+1 day', strtotime($i->format('Y-m-d'))));
                    $pdate_attendance = $attendance->where('att_date', $pdate);
                    $ndate_attendance = $attendance->where('att_date', $ndate);
                    $exp_tcat_time_in = $tcat_hist->tcat_time_in;
                    $exp_tcat_time_out = $tcat_hist->tcat_time_out;
                    $monday_in = $tcat_hist->monday_in;
                    $monday_out = $tcat_hist->monday_out;
                    $tuesday_in = $tcat_hist->tuesday_in;
                    $tuesday_out = $tcat_hist->tuesday_out;
                    $wednesday_in = $tcat_hist->wednesday_in;
                    $wednesday_out = $tcat_hist->wednesday_out;
                    $thursday_in = $tcat_hist->thursday_in;
                    $thursday_out = $tcat_hist->thursday_out;
                    $friday_in = $tcat_hist->friday_in;
                    $friday_out = $tcat_hist->friday_out;
                    $satureday_in = $tcat_hist->satureday_in;
                    $satureday_out = $tcat_hist->satureday_out;
                    $sunday_in = $tcat_hist->sunday_in;
                    $sunday_out = $tcat_hist->sunday_out;
                    $day_in_pointer = strtolower(date("l",strtotime($date)))."_in";
                    if($day_in_pointer == 'saturday_in'){
                        $day_in_pointer = "satureday_in";
                    }
                    $nchecktimes = [];
                    foreach ($ndate_attendance as $key => $val)
                    {
                        $nchecktimes[] = $val->checktime;
                    }
                    sort($nchecktimes);
                    $timein = "--";
                    if (count($checktimes) > 0)
                    {
                        $ctimein = $checktimes[count($checktimes) - 1];
                        $timein_limit = date($date." H:i:s", strtotime('-120 minutes', strtotime($date . " " . $time_in)));
                        if ($ctimein > $timein_limit)
                        {
                            $timein = $ctimein;
                        }
                    }

                    if (count($nchecktimes) > 0)
                    {
                        sort($nchecktimes);
                        $timeout_limit = date($ndate." H:i:s", strtotime('+120 minutes', strtotime($ndate . " " . $time_out)));
                        if ($nchecktimes[0] < $timeout_limit)
                        {
                            $timeout = $nchecktimes[0];

                        }
                    }
                    if(is_null($tcat_hist->$day_in_pointer)){
                        if(count($checktimes) == 1){
                            $timein = $checktimes[0];
                            $timeout = "--";
                        }else if(count($checktimes)>1){
                            $timein = $checktimes[0];
                            $timeout = $checktimes[count($checktimes)-1];
                        }
                    }

                    if(date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                        $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                    elseif(date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                        $time_category = '[ ' . $tuesday_in . '-' . $tuesday_out . ' ]';

                    elseif(date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                        $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';

                    elseif(date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                        $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';

                    elseif(date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                        $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';

                    elseif(date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                        $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';

                    elseif(date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                        $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';


                    $checktimes = [];
                    if (isset($timein))
                    {
                        $checktimes[] = $timein;
                        $timein = $timein;
                    }
                    if (isset($timeout))
                    {
                        $checktimes[] = $timeout;
                    }
                }
                else
                {
                    $timein = $checktimes[0];
                    $timeout = $checktimes[count($checktimes) - 1];
                }

                if ($timein == "--" || $timeout == "--")
                {
                    $seonds = 0;
                }
                else
                {
                    $seonds = strtotime($timeout) - strtotime($timein);
                }

                $date = $date_attendance->first()->att_date;
                $hours = date("H:i:s", $seonds);
                $total_work[] = $hours;
                $expected_hours_work[] = $expected_hours;
                $checktime = date("H:i", strtotime($date_attendance->first()->checktime));
                if (strtotime($hours) > strtotime($expected_hours))
                {
                    $timecalculation = strtotime($hours) - strtotime($expected_hours);
                }
                else
                {
                    $timecalculation = strtotime($expected_hours) - strtotime($hours);
                }
                $timecalculation_remarks = date('H:i:s', $timecalculation);
                $expected_time = date("H:i:s", strtotime($timein));
                $sum += $expected_seconds;
                if ($selectedTime == null)
                {
                    $time_in = "--";
                    $timein = "--";
                    $hours = "--";
                    $expected_hours = "--";
                    $expected_time = "--";

                }
                else
                {

                    $attach = $attachs->where('attach_date', $i->format('Y-m-d'));


                    $attach_date = "";
                    if ($attach->count() > 0)
                    {
                        $attach_date = $attach->first()->attach_date;

                    }

                    if ($expected_time <= $time_in && $expected_time!="00:00:00")
                    {

                        $total_exp_hr += strtotime($expected_hours);
                        $total_hr_work += strtotime($hours);
                        $total_present += 1;
                        if ($date_attendance->first()->mark_attendance == 1)
                        {
                            $status = 'Present *';
                        }
                        else
                        {
                            $status = 'Present';
                        }

                        $flag = 1;

                        if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                        {

                            $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                        {

                            $time_category = '[ ' . $tuesday_in . '-' . $tuesday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                        {

                            $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                        {

                            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                        {

                            $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                        {

                            $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                        {

                            $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';
                        }

                    }elseif ($expected_time > $time_in ){

                        $expect_time_check = strtotime($expected_hours);
                        $total_exp_hr += strtotime($expected_hours);

                        $total_hr_work += strtotime($hours);
                        $hour_time_check = strtotime($hours);
                        $diff = $expect_time_check - $hour_time_check;

                        $hours_calculation = date('H:i:s', $diff) . ' time late';

                        if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                        {

                            $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                        {

                            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                        {

                            $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                        {

                            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                        {

                            $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                        {

                            $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                        {

                            $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';

                        }
                        if ($date_attendance->first()->mark_attendance == 1)
                        {
                            $status = 'Late *';
                        }
                        else
                        {

                            $status = 'Late';

                        }
                        $total_late += 1;

                    }
                }
            }else{
                if($pdate_attendance->count()>0){
                    $time_cat_history = $timcat_his->where('date', '<=', $pdate_attendance->first()
                        ->att_date);

                    if ($time_cat_history->count() == 1)
                    {
                        $tcat_hist = $time_cat_history->first();
                    }
                    elseif ($time_cat_history->count() > 1)
                    {
                        $tcat_hist = $time_cat_history->last();
                    }

                    $night_shift = $tcat_hist->is_night_shift;
                    $exp_tcat_time_in = $tcat_hist->tcat_time_in;
                    $exp_tcat_time_out = $tcat_hist->tcat_time_out;
                    $monday_in = $tcat_hist->monday_in;
                    $monday_out = $tcat_hist->monday_out;
                    $tuesday_in = $tcat_hist->tuesday_in;
                    $tuesday_out = $tcat_hist->tuesday_out;
                    $wednesday_in = $tcat_hist->wednesday_in;
                    $wednesday_out = $tcat_hist->wednesday_out;
                    $thursday_in = $tcat_hist->thursday_in;
                    $thursday_out = $tcat_hist->thursday_out;
                    $friday_in = $tcat_hist->friday_in;
                    $friday_out = $tcat_hist->friday_out;
                    $satureday_in = $tcat_hist->satureday_in;
                    $satureday_out = $tcat_hist->satureday_out;
                    $sunday_in = $tcat_hist->sunday_in;
                    $sunday_out = $tcat_hist->sunday_out;
                    $tcat_grace_time = $tcat_hist->tcat_grace_time;

                    if (date('l', strtotime($i->format('Y-m-d'))) == "Monday"){
                        $selectedTime = $monday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_in = date('H:i:s', $endTime);
                        $selectedTime = $monday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_time_in = $time_in;
                        $expected_seconds = strtotime($monday_out) - strtotime($monday_in);
                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }
                    elseif (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday"){

                        $selectedTime = $tuesday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));

                        $time_in = date('H:i:s', $endTime);
                        $expected_time_in = $time_in;
                        $selectedTime = $tuesday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }
                    elseif (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday"){

                        $selectedTime = $wednesday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_in = date('H:i:s', $endTime);

                        $expected_time_in = $time_in;
                        $selectedTime = $wednesday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_seconds = strtotime($wednesday_in) - strtotime($wednesday_in);
                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Thursday"){

                        $selectedTime = $thursday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_in = date('H:i:s', $endTime);
                        $expected_time_in = $time_in;
                        $selectedTime = $thursday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_seconds = strtotime($thursday_out) - strtotime($thursday_in);

                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }elseif(date('l', strtotime($i->format('Y-m-d'))) == "Friday"){
                        $selectedTime = $friday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_in = date('H:i:s', $endTime);
                        $expected_time_in = $time_in;
                        $selectedTime = $friday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_seconds = strtotime($friday_out) - strtotime($friday_in);
                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Saturday"){
                        $selectedTime = $satureday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_in = date('H:i:s', $endTime);
                        $expected_time_in = $time_in;
                        $selectedTime = $satureday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_seconds = strtotime($satureday_out) - strtotime($satureday_in);
                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Sunday"){
                        $selectedTime = $sunday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_in = date('H:i:s', $endTime);
                        $expected_time_in = $time_in;
                        $selectedTime = $sunday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_seconds = strtotime($sunday_out) - strtotime($sunday_in);
                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }

                    sort($checktimes);
                    if ($night_shift == 1)
                    {
                        $pdate = date('Y-m-d', strtotime('-1 day', strtotime($i->format('Y-m-d'))));
                        $ndate = date('Y-m-d', strtotime('+1 day', strtotime($i->format('Y-m-d'))));
                        $pdate_attendance = $attendance->where('att_date', $pdate);
                        $ndate_attendance = $attendance->where('att_date', $ndate);

                        $nchecktimes = [];
                        foreach ($ndate_attendance as $key => $val)
                        {
                            $nchecktimes[] = $val->checktime;
                        }
                        sort($checktimes);
                        $timein = "--";
                        if (count($checktimes) > 0)
                        {
                            $ctimein = $checktimes[count($checktimes) - 1];
                            $timein_limit = date("Y-m-d H:i:s", strtotime('-120 minutes', strtotime($date . " " . $time_in)));
                            if ($ctimein > $timein_limit)
                            {
                                $timein = $ctimein;
                            }
                        }
                        if (count($nchecktimes) > 0)
                        {
                            sort($nchecktimes);
                            $timeout_limit = date("Y-m-d H:i:s", strtotime('+120 minutes', strtotime($ndate . " " . $time_out)));
                            if ($nchecktimes[0] < $timeout_limit)
                            {
                                $timeout = $nchecktimes[0];

                            }
                        }

                        $checktimes = [];
                        if (isset($timein))
                        {
                            $checktimes[] = $timein;
                            $timein = $timein;
                        }
                        if (isset($timeout))
                        {
                            $checktimes[] = $timeout;
                        }
                    }
                    if ($timein == "--" || $timeout == "--")
                    {
                        $seonds = 0;
                    }
                    else
                    {
                        $seonds = strtotime($timeout) - strtotime($timein);
                    }

                    $date = $pdate_attendance->first()->att_date;

                    $hours = date("H:i:s", $seonds);

                    if($status=="Absent"){
                        $total_work[] = '00:00';

                        $expected_hours_work[] = '00:00';

                    }else{
                        $total_work[] = $hours;

                        $expected_hours_work[] = $expected_hours;
                    }

                    $checktime = date("H:i", strtotime($pdate_attendance->first()
                        ->checktime));
                    if (strtotime($hours) > strtotime($expected_hours))
                    {
                        $timecalculation = strtotime($hours) - strtotime($expected_hours);
                    }
                    else
                    {
                        $timecalculation = strtotime($expected_hours) - strtotime($hours);
                    }
                    $timecalculation_remarks = date('H:i:s', $timecalculation);
                    $expected_time = date("H:i:s", strtotime($timein));
                    $sum += $expected_seconds;


                    if($selectedTime == null || $status=="Absent" )
                    {

                        $time_in = "--";
                        $timein = "--";
                        $hours = "--";
                        $expected_hours = "--";

                        $expected_time = "--";
                    }else{

                        $attach = $attachs->where('attach_date', $i->format('Y-m-d'));


                        $attach_date = "";
                        if ($attach->count() > 0)
                        {
                            $attach_date = $attach->first()->attach_date;

                        }
                        if ($expected_time <= $time_in )
                        {

                            $total_exp_hr += strtotime($expected_hours);
                            $total_hr_work += strtotime($hours);
                            $total_present += 1;
                            if ($date_attendance->first()->mark_attendance == 1)
                            {
                                $status = 'Present *';
                            }
                            else
                            {
                                $status = 'Present';
                            }

                            $flag = 1;

                            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                            {

                                $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                            {

                                $time_category = '[ ' . $tuesday_in . '-' . $tuesday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                            {

                                $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                            {

                                $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                            {

                                $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                            {

                                $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                            {

                                $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';
                            }
                        }elseif ($expected_time > $time_in){
                            $expect_time_check = strtotime($expected_hours);
                            $total_exp_hr += strtotime($expected_hours);


                            $total_hr_work += strtotime($hours);
                            $hour_time_check = strtotime($hours);
                            $diff = $expect_time_check - $hour_time_check;

                            $hours_calculation = date('H:i:s', $diff) . ' time late';

                            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                            {

                                $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                            {

                                $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                            {

                                $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                            {

                                $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                            {

                                $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                            {

                                $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                            {

                                $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';
                            }
                            if ($date_attendance->first()->mark_attendance == 1)
                            {
                                $status = 'Late *';
                            }
                            else
                            {

                                $status = 'Late';

                            }
                            $total_late += 1;
                        }
                    }
                }


                if($date_attach->count()>0){
                    foreach ($date_attach as $key => $val)
                    {

                        $time_cat_history= $timcat_his->where('date', '<', $val->attach_date);

                        if ($time_cat_history->count() == 1)
                        {
                            $tcat_hist = $time_cat_history->first();
                        }
                        elseif ($time_cat_history->count() > 1)
                        {
                            $tcat_hist = $time_cat_history->last();
                        }
                        $monday_in = $tcat_hist->monday_in;
                        $monday_out = $tcat_hist->monday_out;
                        $tuesday_in = $tcat_hist->tuesday_in;
                        $tuesday_out = $tcat_hist->tuesday_out;
                        $wednesday_in = $tcat_hist->wednesday_in;
                        $wednesday_out = $tcat_hist->wednesday_out;
                        $thursday_in = $tcat_hist->thursday_in;
                        $thursday_out = $tcat_hist->thursday_out;
                        $friday_in = $tcat_hist->friday_in;
                        $friday_out = $tcat_hist->friday_out;
                        $satureday_in = $tcat_hist->satureday_in;
                        $satureday_out = $tcat_hist->satureday_out;
                        $sunday_in = $tcat_hist->sunday_in;
                        $sunday_out = $tcat_hist->sunday_out;
                        $tcat_grace_time = $tcat_hist->tcat_grace_time;


                        if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                        {
                            $expected_seconds = strtotime($monday_out) - strtotime($monday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);

                        }
                        if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                        {
                            $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);

                        }
                        if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                        {
                            $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        if(date('l', strtotime($i->format('Y-m-d'))) == "Wednesday"){
                            $expected_seconds = strtotime($wednesday_out) - strtotime($wednesday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        if(date('l', strtotime($i->format('Y-m-d'))) == "Thursday"){
                            $expected_seconds = strtotime($thursday_out) - strtotime($thursday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        if( date('l', strtotime($i->format('Y-m-d'))) == "Friday"){
                            $expected_seconds = strtotime($friday_out) - strtotime($friday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        if( date('l', strtotime($i->format('Y-m-d'))) == "Saturday"){
                            $expected_seconds = strtotime($satureday_in) - strtotime($satureday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        if( date('l', strtotime($i->format('Y-m-d'))) == "Sunday"){
                            $expected_seconds = strtotime($sunday_out) - strtotime($sunday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        $attach_hrs[]= $attach_hours;
                        $status = 'Attach';
                    }
                }elseif ($date_leaves->count() > 0){
                    foreach ($date_leaves as $key => $val)
                    {
                        $dataLeave[]=$val->leave_date;
                        if ($val->leave_type == 2)
                        {
                            $status = $val->title;
                        }

                        else
                        {
                            $status = 'Short Leave';
                        }

                        $total_leave +=1;
                        $leave_type = $val->type;
                        $flag = 2;

                    }
                }
            }


            if ($status == 'Absent'){
                $expected_hours="--";
                $hours="--";
                $monday_off = "";
                $tuesday_off = "";
                $wednesday_off = "";
                $thursday_off = "";
                $friday_off = "";
                $satureday_off = "";
                $sunday_off = "";
                if ($day_off->count() > 0)
                {

                    $day_off_base_on_time_cat_his = $day_off->where('join_date', '<=', $i->format('Y-m-d'));
                    if ($day_off_base_on_time_cat_his->count() == 0)
                    {
                        $monday_off = "emp_not_register_in_date";
                        $tuesday_off = "emp_not_register_in_date";
                        $wednesday_off = "emp_not_register_in_date";
                        $thursday_off = "emp_not_register_in_date";
                        $friday_off = "emp_not_register_in_date";
                        $satureday_off = "emp_not_register_in_date";
                        $sunday_off = "emp_not_register_in_date";
                    }
                    elseif ($day_off_base_on_time_cat_his->count() == 1)
                    {

                        $monday_off = $day_off_base_on_time_cat_his->first()->tc_monday_in;
                        $tuesday_off = $day_off_base_on_time_cat_his->first()->tc_tuesday_in;
                        $wednesday_off = $day_off_base_on_time_cat_his->first()->tc_wednesday_in;
                        $thursday_off = $day_off_base_on_time_cat_his->first()->tc_thursday_in;
                        $friday_off = $day_off_base_on_time_cat_his->first()->tc_friday_in;

                        $satureday_off = $day_off_base_on_time_cat_his->first()->tc_saturday_in;
                        $sunday_off = $day_off_base_on_time_cat_his->first()->tc_sunday_in;

                    }
                    elseif ($day_off_base_on_time_cat_his->count() > 1)
                    {
                        $monday_off = $day_off_base_on_time_cat_his->last()->tc_monday_in;
                        $tuesday_off = $day_off_base_on_time_cat_his->last()->tc_tuesday_in;
                        $wednesday_off = $day_off_base_on_time_cat_his->last()->tc_wednesday_in;
                        $thursday_off = $day_off_base_on_time_cat_his->last()->tc_thursday_in;
                        $friday_off = $day_off_base_on_time_cat_his->last()->tc_friday_in;
                        $satureday_off = $day_off_base_on_time_cat_his->last()->tc_saturday_in;
                        $sunday_off = $day_off_base_on_time_cat_his->last()->tc_sunday_in;
                    }
                    $check_dedicated_dayoff = $dedicate_off_day->where('off_day_date', $i->format('Y-m-d'));
                    if ($check_dedicated_dayoff->count() > 0)
                    {
                        $day_off_day = strtotime($check_dedicated_dayoff->first()
                            ->off_day_date);
                        $check_day_off_day = date('l', $day_off_day);

                        if ($check_day_off_day == 'Monday')
                        {
                            $monday_off = "";
                        }
                        if ($check_day_off_day == 'Tuesday')
                        {
                            $tuesday_off = "";
                        }
                        if ($check_day_off_day == 'Wednesday')
                        {
                            $wednesday_off = "";
                        }
                        if ($check_day_off_day == 'Thursday')
                        {
                            $thursday_off = "";
                        }
                        if ($check_day_off_day == 'Friday')
                        {
                            $friday_off = "";

                        }
                        if ($check_day_off_day == 'Saturday')
                        {
                            $satureday_off = "";
                        }
                        if ($check_day_off_day == 'Sunday')
                        {
                            $sunday_off = "";
                        }

                    }
                    else
                    {
                        $day_off_day = strtotime($i->format('Y-m-d'));

                    }

                    if (date('l', $day_off_day) == "Monday")
                    {

                        if (empty($monday_off))
                        {
                            $total_day_off += 1;

                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {

                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }

                            }
                            else
                            {
                                $total_holiday += 1;

                            }
                        }

                    }
                    if (date('l', $day_off_day) == "Tuesday")
                    {
                        if (empty($tuesday_off))
                        {
                            $total_day_off += 1;

                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {

                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }
                            }
                            else
                            {
                                $total_holiday += 1;
                            }
                        }

                    }
                    if (date('l', $day_off_day) == "Wednesday")
                    {

                        if (empty($wednesday_off))
                        {
                            $total_day_off += 1;
                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {
                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }
                            }
                            else
                            {
                                $total_holiday += 1;
                            }
                        }

                    }
                    if (date('l', $day_off_day) == "Thursday")
                    {

                        if (empty($thursday_off))
                        {
                            $total_day_off += 1;
                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {
                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }
                            }
                            else
                            {
                                $total_holiday += 1;
                            }
                        }

                    }

                    if (date('l', $day_off_day) == "Friday")
                    {

                        if (empty($friday_off))
                        {

                            $total_day_off += 1;
                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {
                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }
                            }
                            else
                            {
                                $total_holiday += 1;
                            }
                        }
                    }
                    if (date('l', $day_off_day) == "Saturday")
                    {

                        if (empty($satureday_off))
                        {
                            $total_day_off += 1;
                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {

                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }
                            }
                            else
                            {
                                $total_holiday += 1;
                            }
                        }

                    }
                    if (date('l', $day_off_day) == "Sunday")
                    {


                        if (empty($sunday_off))
                        {

                            $total_day_off += 1;
                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {
                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }
                            }
                            else
                            {
                                $total_holiday += 1;

                            }
                        }

                    }

                }
                else
                {

                    $in_var = strtolower(date('l', strtotime($i->format('Y-m-d'))))."_in";
                    if($in_var == "saturday_in"){
                        $in_var = "satureday_in";
                    }
                    $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                        ->first();
                    if ($check_emp_holiday == null)
                    {

                        if(is_null($$in_var)){
                            $total_day_off += 1;
                        }else{
                            $total_absent += 1;
                        }


                    }else
                    {
                        $total_holiday += 1;
                    }
                }
            }elseif ($status == 'Late'){

                if (!empty($attach_date))
                {
                }
                else
                {
                }

                ?>

                <?php
            }elseif ($status == 'Late *'){

                if (!empty($attach_date))
                {
                }
                else
                {
                }
                ?>


                <?php
            }elseif ($status == 'Present'){ ?>

                <?php
                if (!empty($attach_date))
                {
                }
                else
                {
                }

                ?>

                <?php
            }elseif ($status == 'Present *'){
                if (!empty($attach_date))
                {
                }
                else
                {

                }
            }elseif ($status == 'Attach'){

            }elseif ($flag == 2){

                $leave += 1;
            }

            if(sizeof($checktimes) == 1){
                if($selectedTime==null){
                }else{
                }
            }else{
                if(strtotime($hours)>strtotime($expected_hours)){
                    $overtime_array[]=$timecalculation_remarks;
                }elseif(strtotime($hours) < strtotime($expected_hours)){
                    $hour_short_array[]=$timecalculation_remarks;
                }
            }
        }



        $data['start_date'] = $start_date;
        $data['to_date'] = $to_date;
        $data['counter'] = $counter;
        $data['sum'] = $sum;
        $data['total_exp_hr'] = $total_exp_hr;
        $data['total_hr_work'] = $total_hr_work;
        $data['total_present'] = $total_present;
        $data['total_late'] = $total_late;
        $data['total_absent'] = $total_absent;
        $data['total_holiday'] = $total_holiday;
        $data['total_leave'] =$total_leave;
        $data['leave'] = $leave;
        $data['total_day_off'] = $total_day_off;
        $data['total_work'] = $total_work;
        $data['expected_hours_work'] = $expected_hours_work;
        $data['hour_short_array'] = $hour_short_array;
        $data['overtime_array'] = $overtime_array;
        $data['attach_hrs'] = $attach_hrs;
        //dd($data);
        return view('Users.test_report',$data);
    }

    public static function employeeYearlyReportCopy($from_date, $to_date, $user_id)
    {
        $request = new Request();
        $request->user_id =$user_id;
        $request->from_date = $from_date;
        $request->to_date = $to_date;
        $date_from = $request->from_date;
        $date_to = $request->to_date;

        $user_id = $request->user_id;
        $user = User::find($user_id);
        $dep_id = $user->department_id;


        $time1='4:00';
        $time2='5:00';
        $result=strtotime($time1)+strtotime($time2);


        if(Session::get('user')->role_id == 1){
            $dep_id = 278;
        }

        $loged_in_department = Client::find($dep_id);

        //removed has links condition from these lines
        if(Session::get('user')->role_id == 11 && $loged_in_department->parent_id!=0){
            $dep_id = $loged_in_department->parent_id;
        }

        if($date_from && $date_to){
            // dd($date_from);
            $timecategory=TimeCategory::where('department_id',$request->deparment_id)->first();


            $data['from_date'] = $date_from;
            $data['to_date'] = $date_to;



            $individual_report_att = "SELECT
            u.id as user_id,
            u.name as user_name,
            att.datetime as checktime,
            att.mark_attendance,
            att.dev_id,
            date(att.datetime) as att_date,
            tcat.id as tcat_id,
            tcat.time_in as tcat_time_in,
            tcat.is_night_shift as is_night_shift,
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
            tcat.tc_saturday_in as satureday_in,
            tcat.tc_saturday_out as satureday_out,
            tcat.tc_sunday_in as sunday_in,
            tcat.tc_sunday_out as sunday_out,
            tcat.time_out as tcat_time_out,
            tcat.grace_time as tcat_grace_time
            from users u inner join
            timenex_new.attendance att on u.id= att.user_id

            INNER join time_categories tcat on tcat.id=u.tcat_id
            where  (date(att.datetime)
            BETWEEN '".date('Y-m-d', strtotime($date_from.' -1 day'))."' AND '".$date_to."') and u.id='".$request->user_id."'
            group By att.datetime";

                    $data['attendance'] = collect(DB::select( DB::raw($individual_report_att)));

                    $timcat_history="SELECT date(time_category_histories.join_date) as date,
            users.id,
            time_categories.time_in as tcat_time_in,
            time_categories.is_night_shift as is_night_shift,
            time_categories.time_out as tcat_time_out,
            time_category_histories.is_current,
            time_categories.id as cat_id,
            time_categories.tc_monday_in as monday_in,
            time_categories.tc_monday_out as monday_out,
            time_categories.tc_tuesday_in as tuesday_in,
            time_categories.tc_tuesday_out as tuesday_out,
            time_categories.tc_wednesday_in as wednesday_in,
            time_categories.tc_wednesday_out as wednesday_out,
            time_categories.tc_thursday_in as thursday_in,
            time_categories.tc_thursday_out as thursday_out,
            time_categories.tc_friday_in as friday_in,
            time_categories.tc_friday_out as friday_out,
            time_categories.tc_saturday_in as satureday_in,
            time_categories.tc_saturday_out as satureday_out,
            time_categories.tc_sunday_in as sunday_in,
            time_categories.tc_sunday_out as sunday_out,

            time_categories.grace_time as tcat_grace_time
            FROM `time_category_histories`
            INNER JOIN users
            on users.id=time_category_histories.user_id
            INNER JOIN time_categories
            on time_categories.id=time_category_histories.tcat_id
            where users.id='".$request->user_id."' order by date asc";
                    $data['timcat_his'] = collect(DB::select( DB::raw($timcat_history)));



                    $for_dayoff="select * from users INNER join clients
            on clients.id=users.department_id
            INNER JOIN time_category_histories on time_category_histories.user_id=users.id
            INNER JOIN time_categories on time_categories.id=time_category_histories.tcat_id
            WHERE clients.id='".$request->deparment_id."' and users.id='".$request->user_id."'";
                    $data['day_off'] = collect(DB::select( DB::raw($for_dayoff)));



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

                    if(Session::get('role_id') == 7){
                        //department head & we will show leave of its own department + sections
                        $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$loged_in_department->id." or c.id = ".$loged_in_department->id;
                    }
                    else{

                        $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$loged_in_department->id." or c.id = ".$loged_in_department->id;
                        if($request->has('department')){
                            $child_departments = Client::wherein('parent_id',$request->deparment_id)->pluck('id');
                            $user_departments  = array_merge($child_departments->toArray(),$request->department);
                            $users_department_clause = implode(",",$user_departments);
                        }
                    }


            $holi_calen="SELECT id,title,user_id,date as
            holiday_date,department_id,date_from,date_to
            FROM holiday_calendars
            WHERE department_id in (".$users_department_clause.")
            and deleted_at is null and  date BETWEEN '".$request->from_date."' and '".$date_from."'";
                    if($loged_in_department->sub_account==2){

                        $holi_calen="SELECT id,title,user_id,date as
            holiday_date,department_id,date_from,date_to
            FROM holiday_calendars
            WHERE department_id in (".$users_department_clause.")
            and deleted_at is null  and  date BETWEEN '".$request->from_date."' and '".$date_to."'";

            }
            $dedicate_off_day="select * FROM employee_dedicated_off_days where user_id= '".$request->user_id."'";
            $data['dedicate_off_day'] = collect(DB::select( DB::raw($dedicate_off_day)));

            $data['holidays'] = collect(DB::select( DB::raw($holi_calen)));

            //SELECT min(date(datetime)) FROM `attendance` WHERE user_id=22211;

            $data['emp_min_transaction']=DB::table('attendance')->selectRaw('min(date(datetime)) as date')->where('user_id',$request->user_id)->first();

            $data['user_info_query'] = DB::table('users as u')
                ->join('clients as c','c.id','u.department_id','left')
                ->leftjoin('designations as d','d.id','u.designation_id','left')
                ->where('u.id',$request->user_id)
                ->selectRaw('c.name,d.title,u.id,u.name as user_name,u.image,u.reg_date,u.cnic_issue_date,cnic_expiry_date')
                ->groupBy('u.name')
                ->get();

        }

        $data['user_id'] = $request->user_id;
        $data['page_title'] = "Employee Report";
        //$data['linked'] = $request->linked;



        $start_date = new DateTime($data['from_date']);
        $to_date = new DateTime($data['to_date']);
        $counter = 0;
        $sum = 0;
        $total_exp_hr = 0;
        $total_hr_work = 0;
        $total_present = 0;
        $total_late = 0;
        $total_absent = 0;
        $total_holiday=0;
        $total_leave = 0;
        $leave = 0;
        $total_day_off=0;
        $total_work = [];
        $expected_hours_work = [];
        $hour_short_array=[];
        $overtime_array=[];
        $attach_hrs=[];
        $monday_in = "";
        $monday_out = "";
        $tuesday_in = "";
        $tuesday_out = "";
        $wednesday_in = "";
        $wednesday_out = "";
        $thursday_in = "";
        $thursday_out = "";
        $friday_in = "";
        $friday_out = "";
        $satureday_in = "";
        $satureday_out = "";
        $sunday_in = "";
        $sunday_out = "";
        $tcat_grace_time = "";
        $night_shift = "";
        $exp_tcat_time_in = "";
        $exp_tcat_time_out = "";
        $dataLeave=[];

        $attendance=$data['attendance'];
        $attachs=$data['attachs'];
        $leaves=$data['leaves'];
        $day_off=$data['day_off'];
        $holidays=$data['holidays'];
        $timcat_his=$data['timcat_his'];


        for($i = $start_date;$i<=$to_date;$i->modify('+1 day')){

            $day = date("D", strtotime($i->format('d-m-Y')));
            $checktimes = [];
            $date_attendance = $attendance->where('att_date', $i->format('Y-m-d'));
            foreach ($date_attendance as $key => $val)
            {
                $checktimes[] = $val->checktime;
            }

            $date_attach = $attachs->where('attach_date', $i->format('Y-m-d'));

            $date_leaves = $leaves->where('leave_date', $i->format('Y-m-d'));





            $timein = "--";
            $timeout = "--";
            $hours = "--";
            $expected_hours = "--";
            $hours_calculation = "--";
            $time_category = "--";
            $status = "Absent";
            $timecalculation_remarks = "--";
            $pdate = date('Y-m-d', strtotime('-1 day', strtotime($i->format('Y-m-d'))));
            $pdate_attendance = $attendance->where('att_date', $pdate);

            if ($date_attendance->count() > 0)
            {

                $time_cat_history = $timcat_his->where('date', '<=', $date_attendance->first()->att_date);

                if ($time_cat_history->count() == 1)
                {
                    $tcat_hist = $time_cat_history->first();
                }
                elseif ($time_cat_history->count() > 1)
                {
                    $tcat_hist = $time_cat_history->last();
                }

                $night_shift = $tcat_hist->is_night_shift;
                $exp_tcat_time_in = $tcat_hist->tcat_time_in;
                $exp_tcat_time_out = $tcat_hist->tcat_time_out;
                $monday_in = $tcat_hist->monday_in;
                $monday_out = $tcat_hist->monday_out;
                $tuesday_in = $tcat_hist->tuesday_in;
                $tuesday_out = $tcat_hist->tuesday_out;
                $wednesday_in = $tcat_hist->wednesday_in;
                $wednesday_out = $tcat_hist->wednesday_out;
                $thursday_in = $tcat_hist->thursday_in;
                $thursday_out = $tcat_hist->thursday_out;
                $friday_in = $tcat_hist->friday_in;
                $friday_out = $tcat_hist->friday_out;
                $satureday_in = $tcat_hist->satureday_in;
                $satureday_out = $tcat_hist->satureday_out;
                $sunday_in = $tcat_hist->sunday_in;
                $sunday_out = $tcat_hist->sunday_out;
                $tcat_grace_time = $tcat_hist->tcat_grace_time;

                if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                {
                    $selectedTime = $monday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_in = date('H:i:s', $endTime);
                    $selectedTime = $monday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_time_in = $time_in;
                    $expected_seconds = strtotime($monday_out) - strtotime($monday_in);
                    $expected_hours= timeCategoryTotalHourWork($expected_seconds);

                }
                elseif (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                {

                    $selectedTime = $tuesday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));

                    $time_in = date('H:i:s', $endTime);
                    $expected_time_in = $time_in;
                    $selectedTime = $tuesday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                    $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                }
                elseif (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                {

                    $selectedTime = $wednesday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_in = date('H:i:s', $endTime);

                    $expected_time_in = $time_in;
                    $selectedTime = $wednesday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_seconds = strtotime($wednesday_out) - strtotime($wednesday_in);
                    $expected_hours= timeCategoryTotalHourWork($expected_seconds);

                }
                elseif (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                {

                    $selectedTime = $thursday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_in = date('H:i:s', $endTime);
                    $expected_time_in = $time_in;
                    $selectedTime = $thursday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_seconds = strtotime($thursday_out) - strtotime($thursday_in);
                    $expected_hours = date('h:i:s', $expected_seconds);
                }
                elseif (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                {
                    $selectedTime = $friday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_in = date('H:i:s', $endTime);
                    $expected_time_in = $time_in;
                    $selectedTime = $friday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_seconds = strtotime($friday_out) - strtotime($friday_in);
                    $expected_hours = date('h:i:s', $expected_seconds);

                }
                elseif (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                {

                    $selectedTime = $satureday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_in = date('H:i:s', $endTime);
                    $expected_time_in = $time_in;
                    $selectedTime = $satureday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_seconds = strtotime($satureday_out) - strtotime($satureday_in);
                    $expected_hours = date('h:i:s', $expected_seconds);


                }
                elseif (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                {

                    $selectedTime = $sunday_in;
                    $min = date('i', strtotime($tcat_grace_time));
                    $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_in = date('H:i:s', $endTime);
                    $expected_time_in = $time_in;
                    $selectedTime = $sunday_out;
                    $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                    $time_out = date('H:i:s', $time_out);
                    $expected_seconds = strtotime($sunday_out) - strtotime($sunday_in);
                    $expected_hours = date('h:i:s', $expected_seconds);
                }
                sort($checktimes);

                if($night_shift == 1){
                    $date = $i->format('Y-m-d');
                    $pdate = date('Y-m-d', strtotime('-1 day', strtotime($i->format('Y-m-d'))));
                    $ndate = date('Y-m-d', strtotime('+1 day', strtotime($i->format('Y-m-d'))));
                    $pdate_attendance = $attendance->where('att_date', $pdate);
                    $ndate_attendance = $attendance->where('att_date', $ndate);
                    $exp_tcat_time_in = $tcat_hist->tcat_time_in;
                    $exp_tcat_time_out = $tcat_hist->tcat_time_out;
                    $monday_in = $tcat_hist->monday_in;
                    $monday_out = $tcat_hist->monday_out;
                    $tuesday_in = $tcat_hist->tuesday_in;
                    $tuesday_out = $tcat_hist->tuesday_out;
                    $wednesday_in = $tcat_hist->wednesday_in;
                    $wednesday_out = $tcat_hist->wednesday_out;
                    $thursday_in = $tcat_hist->thursday_in;
                    $thursday_out = $tcat_hist->thursday_out;
                    $friday_in = $tcat_hist->friday_in;
                    $friday_out = $tcat_hist->friday_out;
                    $satureday_in = $tcat_hist->satureday_in;
                    $satureday_out = $tcat_hist->satureday_out;
                    $sunday_in = $tcat_hist->sunday_in;
                    $sunday_out = $tcat_hist->sunday_out;
                    $day_in_pointer = strtolower(date("l",strtotime($date)))."_in";
                    if($day_in_pointer == 'saturday_in'){
                        $day_in_pointer = "satureday_in";
                    }
                    $nchecktimes = [];
                    foreach ($ndate_attendance as $key => $val)
                    {
                        $nchecktimes[] = $val->checktime;
                    }
                    sort($nchecktimes);
                    $timein = "--";
                    if (count($checktimes) > 0)
                    {
                        $ctimein = $checktimes[count($checktimes) - 1];
                        $timein_limit = date($date." H:i:s", strtotime('-120 minutes', strtotime($date . " " . $time_in)));
                        if ($ctimein > $timein_limit)
                        {
                            $timein = $ctimein;
                        }
                    }

                    if (count($nchecktimes) > 0)
                    {
                        sort($nchecktimes);
                        $timeout_limit = date($ndate." H:i:s", strtotime('+120 minutes', strtotime($ndate . " " . $time_out)));
                        if ($nchecktimes[0] < $timeout_limit)
                        {
                            $timeout = $nchecktimes[0];

                        }
                    }
                    if(is_null($tcat_hist->$day_in_pointer)){
                        if(count($checktimes) == 1){
                            $timein = $checktimes[0];
                            $timeout = "--";
                        }else if(count($checktimes)>1){
                            $timein = $checktimes[0];
                            $timeout = $checktimes[count($checktimes)-1];
                        }
                    }

                    if(date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                        $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                    elseif(date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                        $time_category = '[ ' . $tuesday_in . '-' . $tuesday_out . ' ]';

                    elseif(date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                        $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';

                    elseif(date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                        $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';

                    elseif(date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                        $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';

                    elseif(date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                        $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';

                    elseif(date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                        $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';


                    $checktimes = [];
                    if (isset($timein))
                    {
                        $checktimes[] = $timein;
                        $timein = $timein;
                    }
                    if (isset($timeout))
                    {
                        $checktimes[] = $timeout;
                    }
                }
                else
                {
                    $timein = $checktimes[0];
                    $timeout = $checktimes[count($checktimes) - 1];
                }

                if ($timein == "--" || $timeout == "--")
                {
                    $seonds = 0;
                }
                else
                {
                    $seonds = strtotime($timeout) - strtotime($timein);
                }

                $date = $date_attendance->first()->att_date;
                $hours = date("H:i:s", $seonds);
                $total_work[] = $hours;
                $expected_hours_work[] = $expected_hours;
                $checktime = date("H:i", strtotime($date_attendance->first()->checktime));
                if (strtotime($hours) > strtotime($expected_hours))
                {
                    $timecalculation = strtotime($hours) - strtotime($expected_hours);
                }
                else
                {
                    $timecalculation = strtotime($expected_hours) - strtotime($hours);
                }
                $timecalculation_remarks = date('H:i:s', $timecalculation);
                $expected_time = date("H:i:s", strtotime($timein));
                $sum += $expected_seconds;
                if ($selectedTime == null)
                {
                    $time_in = "--";
                    $timein = "--";
                    $hours = "--";
                    $expected_hours = "--";
                    $expected_time = "--";

                }
                else
                {

                    $attach = $attachs->where('attach_date', $i->format('Y-m-d'));


                    $attach_date = "";
                    if ($attach->count() > 0)
                    {
                        $attach_date = $attach->first()->attach_date;

                    }

                    if ($expected_time <= $time_in && $expected_time!="00:00:00")
                    {

                        $total_exp_hr += strtotime($expected_hours);
                        $total_hr_work += strtotime($hours);
                        $total_present += 1;
                        if ($date_attendance->first()->mark_attendance == 1)
                        {
                            $status = 'Present *';
                        }
                        else
                        {
                            $status = 'Present';
                        }

                        $flag = 1;

                        if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                        {

                            $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                        {

                            $time_category = '[ ' . $tuesday_in . '-' . $tuesday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                        {

                            $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                        {

                            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                        {

                            $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                        {

                            $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                        {

                            $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';
                        }

                    }elseif ($expected_time > $time_in ){

                        $expect_time_check = strtotime($expected_hours);
                        $total_exp_hr += strtotime($expected_hours);

                        $total_hr_work += strtotime($hours);
                        $hour_time_check = strtotime($hours);
                        $diff = $expect_time_check - $hour_time_check;

                        $hours_calculation = date('H:i:s', $diff) . ' time late';

                        if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                        {

                            $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                        {

                            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                        {

                            $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                        {

                            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                        {

                            $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                        {

                            $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                        }
                        else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                        {

                            $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';

                        }
                        if ($date_attendance->first()->mark_attendance == 1)
                        {
                            $status = 'Late *';
                        }
                        else
                        {

                            $status = 'Late';

                        }
                        $total_late += 1;

                    }
                }
            }else{
                if($pdate_attendance->count()>0){
                    $time_cat_history = $timcat_his->where('date', '<=', $pdate_attendance->first()
                        ->att_date);

                    if ($time_cat_history->count() == 1)
                    {
                        $tcat_hist = $time_cat_history->first();
                    }
                    elseif ($time_cat_history->count() > 1)
                    {
                        $tcat_hist = $time_cat_history->last();
                    }

                    $night_shift = $tcat_hist->is_night_shift;
                    $exp_tcat_time_in = $tcat_hist->tcat_time_in;
                    $exp_tcat_time_out = $tcat_hist->tcat_time_out;
                    $monday_in = $tcat_hist->monday_in;
                    $monday_out = $tcat_hist->monday_out;
                    $tuesday_in = $tcat_hist->tuesday_in;
                    $tuesday_out = $tcat_hist->tuesday_out;
                    $wednesday_in = $tcat_hist->wednesday_in;
                    $wednesday_out = $tcat_hist->wednesday_out;
                    $thursday_in = $tcat_hist->thursday_in;
                    $thursday_out = $tcat_hist->thursday_out;
                    $friday_in = $tcat_hist->friday_in;
                    $friday_out = $tcat_hist->friday_out;
                    $satureday_in = $tcat_hist->satureday_in;
                    $satureday_out = $tcat_hist->satureday_out;
                    $sunday_in = $tcat_hist->sunday_in;
                    $sunday_out = $tcat_hist->sunday_out;
                    $tcat_grace_time = $tcat_hist->tcat_grace_time;

                    if (date('l', strtotime($i->format('Y-m-d'))) == "Monday"){
                        $selectedTime = $monday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_in = date('H:i:s', $endTime);
                        $selectedTime = $monday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_time_in = $time_in;
                        $expected_seconds = strtotime($monday_out) - strtotime($monday_in);
                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }
                    elseif (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday"){

                        $selectedTime = $tuesday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));

                        $time_in = date('H:i:s', $endTime);
                        $expected_time_in = $time_in;
                        $selectedTime = $tuesday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }
                    elseif (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday"){

                        $selectedTime = $wednesday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_in = date('H:i:s', $endTime);

                        $expected_time_in = $time_in;
                        $selectedTime = $wednesday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_seconds = strtotime($wednesday_in) - strtotime($wednesday_in);
                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Thursday"){

                        $selectedTime = $thursday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_in = date('H:i:s', $endTime);
                        $expected_time_in = $time_in;
                        $selectedTime = $thursday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_seconds = strtotime($thursday_out) - strtotime($thursday_in);

                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }elseif(date('l', strtotime($i->format('Y-m-d'))) == "Friday"){
                        $selectedTime = $friday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_in = date('H:i:s', $endTime);
                        $expected_time_in = $time_in;
                        $selectedTime = $friday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_seconds = strtotime($friday_out) - strtotime($friday_in);
                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Saturday"){
                        $selectedTime = $satureday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_in = date('H:i:s', $endTime);
                        $expected_time_in = $time_in;
                        $selectedTime = $satureday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_seconds = strtotime($satureday_out) - strtotime($satureday_in);
                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Sunday"){
                        $selectedTime = $sunday_in;
                        $min = date('i', strtotime($tcat_grace_time));
                        $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_in = date('H:i:s', $endTime);
                        $expected_time_in = $time_in;
                        $selectedTime = $sunday_out;
                        $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                        $time_out = date('H:i:s', $time_out);
                        $expected_seconds = strtotime($sunday_out) - strtotime($sunday_in);
                        $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                    }

                    sort($checktimes);
                    if ($night_shift == 1)
                    {
                        $pdate = date('Y-m-d', strtotime('-1 day', strtotime($i->format('Y-m-d'))));
                        $ndate = date('Y-m-d', strtotime('+1 day', strtotime($i->format('Y-m-d'))));
                        $pdate_attendance = $attendance->where('att_date', $pdate);
                        $ndate_attendance = $attendance->where('att_date', $ndate);

                        $nchecktimes = [];
                        foreach ($ndate_attendance as $key => $val)
                        {
                            $nchecktimes[] = $val->checktime;
                        }
                        sort($checktimes);
                        $timein = "--";
                        if (count($checktimes) > 0)
                        {
                            $ctimein = $checktimes[count($checktimes) - 1];
                            $timein_limit = date("Y-m-d H:i:s", strtotime('-120 minutes', strtotime($date . " " . $time_in)));
                            if ($ctimein > $timein_limit)
                            {
                                $timein = $ctimein;
                            }
                        }
                        if (count($nchecktimes) > 0)
                        {
                            sort($nchecktimes);
                            $timeout_limit = date("Y-m-d H:i:s", strtotime('+120 minutes', strtotime($ndate . " " . $time_out)));
                            if ($nchecktimes[0] < $timeout_limit)
                            {
                                $timeout = $nchecktimes[0];

                            }
                        }

                        $checktimes = [];
                        if (isset($timein))
                        {
                            $checktimes[] = $timein;
                            $timein = $timein;
                        }
                        if (isset($timeout))
                        {
                            $checktimes[] = $timeout;
                        }
                    }
                    if ($timein == "--" || $timeout == "--")
                    {
                        $seonds = 0;
                    }
                    else
                    {
                        $seonds = strtotime($timeout) - strtotime($timein);
                    }

                    $date = $pdate_attendance->first()->att_date;

                    $hours = date("H:i:s", $seonds);

                    if($status=="Absent"){
                        $total_work[] = '00:00';

                        $expected_hours_work[] = '00:00';

                    }else{
                        $total_work[] = $hours;

                        $expected_hours_work[] = $expected_hours;
                    }

                    $checktime = date("H:i", strtotime($pdate_attendance->first()
                        ->checktime));
                    if (strtotime($hours) > strtotime($expected_hours))
                    {
                        $timecalculation = strtotime($hours) - strtotime($expected_hours);
                    }
                    else
                    {
                        $timecalculation = strtotime($expected_hours) - strtotime($hours);
                    }
                    $timecalculation_remarks = date('H:i:s', $timecalculation);
                    $expected_time = date("H:i:s", strtotime($timein));
                    $sum += $expected_seconds;


                    if($selectedTime == null || $status=="Absent" )
                    {

                        $time_in = "--";
                        $timein = "--";
                        $hours = "--";
                        $expected_hours = "--";

                        $expected_time = "--";
                    }else{

                        $attach = $attachs->where('attach_date', $i->format('Y-m-d'));


                        $attach_date = "";
                        if ($attach->count() > 0)
                        {
                            $attach_date = $attach->first()->attach_date;

                        }
                        if ($expected_time <= $time_in )
                        {

                            $total_exp_hr += strtotime($expected_hours);
                            $total_hr_work += strtotime($hours);
                            $total_present += 1;
                            if ($date_attendance->first()->mark_attendance == 1)
                            {
                                $status = 'Present *';
                            }
                            else
                            {
                                $status = 'Present';
                            }

                            $flag = 1;

                            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                            {

                                $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                            {

                                $time_category = '[ ' . $tuesday_in . '-' . $tuesday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                            {

                                $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                            {

                                $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                            {

                                $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                            {

                                $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                            {

                                $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';
                            }
                        }elseif ($expected_time > $time_in){
                            $expect_time_check = strtotime($expected_hours);
                            $total_exp_hr += strtotime($expected_hours);


                            $total_hr_work += strtotime($hours);
                            $hour_time_check = strtotime($hours);
                            $diff = $expect_time_check - $hour_time_check;

                            $hours_calculation = date('H:i:s', $diff) . ' time late';

                            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                            {

                                $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                            {

                                $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                            {

                                $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                            {

                                $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                            {

                                $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                            {

                                $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                            {

                                $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';
                            }
                            if ($date_attendance->first()->mark_attendance == 1)
                            {
                                $status = 'Late *';
                            }
                            else
                            {

                                $status = 'Late';

                            }
                            $total_late += 1;
                        }
                    }
                }


                if($date_attach->count()>0){
                    foreach ($date_attach as $key => $val)
                    {

                        $time_cat_history= $timcat_his->where('date', '<', $val->attach_date);

                        if ($time_cat_history->count() == 1)
                        {
                            $tcat_hist = $time_cat_history->first();
                        }
                        elseif ($time_cat_history->count() > 1)
                        {
                            $tcat_hist = $time_cat_history->last();
                        }
                        $monday_in = $tcat_hist->monday_in;
                        $monday_out = $tcat_hist->monday_out;
                        $tuesday_in = $tcat_hist->tuesday_in;
                        $tuesday_out = $tcat_hist->tuesday_out;
                        $wednesday_in = $tcat_hist->wednesday_in;
                        $wednesday_out = $tcat_hist->wednesday_out;
                        $thursday_in = $tcat_hist->thursday_in;
                        $thursday_out = $tcat_hist->thursday_out;
                        $friday_in = $tcat_hist->friday_in;
                        $friday_out = $tcat_hist->friday_out;
                        $satureday_in = $tcat_hist->satureday_in;
                        $satureday_out = $tcat_hist->satureday_out;
                        $sunday_in = $tcat_hist->sunday_in;
                        $sunday_out = $tcat_hist->sunday_out;
                        $tcat_grace_time = $tcat_hist->tcat_grace_time;


                        if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                        {
                            $expected_seconds = strtotime($monday_out) - strtotime($monday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);

                        }
                        if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                        {
                            $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);

                        }
                        if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                        {
                            $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        if(date('l', strtotime($i->format('Y-m-d'))) == "Wednesday"){
                            $expected_seconds = strtotime($wednesday_out) - strtotime($wednesday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        if(date('l', strtotime($i->format('Y-m-d'))) == "Thursday"){
                            $expected_seconds = strtotime($thursday_out) - strtotime($thursday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        if( date('l', strtotime($i->format('Y-m-d'))) == "Friday"){
                            $expected_seconds = strtotime($friday_out) - strtotime($friday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        if( date('l', strtotime($i->format('Y-m-d'))) == "Saturday"){
                            $expected_seconds = strtotime($satureday_in) - strtotime($satureday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        if( date('l', strtotime($i->format('Y-m-d'))) == "Sunday"){
                            $expected_seconds = strtotime($sunday_out) - strtotime($sunday_in);
                            $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        $attach_hrs[]= $attach_hours;
                        $status = 'Attach';
                    }
                }elseif ($date_leaves->count() > 0){
                    foreach ($date_leaves as $key => $val)
                    {
                        $dataLeave[]=$val->leave_date;
                        if ($val->leave_type == 2)
                        {
                            $status = $val->title;
                        }

                        else
                        {
                            $status = 'Short Leave';
                        }

                        $total_leave +=1;
                        $leave_type = $val->type;
                        $flag = 2;

                    }
                }
            }


            if ($status == 'Absent'){
                $expected_hours="--";
                $hours="--";
                $monday_off = "";
                $tuesday_off = "";
                $wednesday_off = "";
                $thursday_off = "";
                $friday_off = "";
                $satureday_off = "";
                $sunday_off = "";
                if ($day_off->count() > 0)
                {

                    $day_off_base_on_time_cat_his = $day_off->where('join_date', '<=', $i->format('Y-m-d'));
                    if ($day_off_base_on_time_cat_his->count() == 0)
                    {
                        $monday_off = "emp_not_register_in_date";
                        $tuesday_off = "emp_not_register_in_date";
                        $wednesday_off = "emp_not_register_in_date";
                        $thursday_off = "emp_not_register_in_date";
                        $friday_off = "emp_not_register_in_date";
                        $satureday_off = "emp_not_register_in_date";
                        $sunday_off = "emp_not_register_in_date";
                    }
                    elseif ($day_off_base_on_time_cat_his->count() == 1)
                    {

                        $monday_off = $day_off_base_on_time_cat_his->first()->tc_monday_in;
                        $tuesday_off = $day_off_base_on_time_cat_his->first()->tc_tuesday_in;
                        $wednesday_off = $day_off_base_on_time_cat_his->first()->tc_wednesday_in;
                        $thursday_off = $day_off_base_on_time_cat_his->first()->tc_thursday_in;
                        $friday_off = $day_off_base_on_time_cat_his->first()->tc_friday_in;

                        $satureday_off = $day_off_base_on_time_cat_his->first()->tc_saturday_in;
                        $sunday_off = $day_off_base_on_time_cat_his->first()->tc_sunday_in;

                    }
                    elseif ($day_off_base_on_time_cat_his->count() > 1)
                    {
                        $monday_off = $day_off_base_on_time_cat_his->last()->tc_monday_in;
                        $tuesday_off = $day_off_base_on_time_cat_his->last()->tc_tuesday_in;
                        $wednesday_off = $day_off_base_on_time_cat_his->last()->tc_wednesday_in;
                        $thursday_off = $day_off_base_on_time_cat_his->last()->tc_thursday_in;
                        $friday_off = $day_off_base_on_time_cat_his->last()->tc_friday_in;
                        $satureday_off = $day_off_base_on_time_cat_his->last()->tc_saturday_in;
                        $sunday_off = $day_off_base_on_time_cat_his->last()->tc_sunday_in;
                    }
                    $check_dedicated_dayoff = $dedicate_off_day->where('off_day_date', $i->format('Y-m-d'));
                    if ($check_dedicated_dayoff->count() > 0)
                    {
                        $day_off_day = strtotime($check_dedicated_dayoff->first()
                            ->off_day_date);
                        $check_day_off_day = date('l', $day_off_day);

                        if ($check_day_off_day == 'Monday')
                        {
                            $monday_off = "";
                        }
                        if ($check_day_off_day == 'Tuesday')
                        {
                            $tuesday_off = "";
                        }
                        if ($check_day_off_day == 'Wednesday')
                        {
                            $wednesday_off = "";
                        }
                        if ($check_day_off_day == 'Thursday')
                        {
                            $thursday_off = "";
                        }
                        if ($check_day_off_day == 'Friday')
                        {
                            $friday_off = "";

                        }
                        if ($check_day_off_day == 'Saturday')
                        {
                            $satureday_off = "";
                        }
                        if ($check_day_off_day == 'Sunday')
                        {
                            $sunday_off = "";
                        }

                    }
                    else
                    {
                        $day_off_day = strtotime($i->format('Y-m-d'));

                    }

                    if (date('l', $day_off_day) == "Monday")
                    {

                        if (empty($monday_off))
                        {
                            $total_day_off += 1;

                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {

                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }

                            }
                            else
                            {
                                $total_holiday += 1;

                            }
                        }

                    }
                    if (date('l', $day_off_day) == "Tuesday")
                    {
                        if (empty($tuesday_off))
                        {
                            $total_day_off += 1;

                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {

                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }
                            }
                            else
                            {
                                $total_holiday += 1;
                            }
                        }

                    }
                    if (date('l', $day_off_day) == "Wednesday")
                    {

                        if (empty($wednesday_off))
                        {
                            $total_day_off += 1;
                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {
                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }
                            }
                            else
                            {
                                $total_holiday += 1;
                            }
                        }

                    }
                    if (date('l', $day_off_day) == "Thursday")
                    {

                        if (empty($thursday_off))
                        {
                            $total_day_off += 1;
                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {
                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }
                            }
                            else
                            {
                                $total_holiday += 1;
                            }
                        }

                    }

                    if (date('l', $day_off_day) == "Friday")
                    {

                        if (empty($friday_off))
                        {

                            $total_day_off += 1;
                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {
                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }
                            }
                            else
                            {
                                $total_holiday += 1;
                            }
                        }
                    }
                    if (date('l', $day_off_day) == "Saturday")
                    {

                        if (empty($satureday_off))
                        {
                            $total_day_off += 1;
                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {

                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }
                            }
                            else
                            {
                                $total_holiday += 1;
                            }
                        }

                    }
                    if (date('l', $day_off_day) == "Sunday")
                    {


                        if (empty($sunday_off))
                        {

                            $total_day_off += 1;
                        }
                        else
                        {
                            $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                                ->first();
                            if ($check_emp_holiday == null)
                            {
                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                    $total_absent += 1;
                                }
                            }
                            else
                            {
                                $total_holiday += 1;

                            }
                        }

                    }

                }
                else
                {

                    $in_var = strtolower(date('l', strtotime($i->format('Y-m-d'))))."_in";
                    if($in_var == "saturday_in"){
                        $in_var = "satureday_in";
                    }
                    $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                        ->first();
                    if ($check_emp_holiday == null)
                    {

                        if(is_null($$in_var)){
                            $total_day_off += 1;
                        }else{
                            $total_absent += 1;
                        }


                    }else
                    {
                        $total_holiday += 1;
                    }
                }
            }elseif ($status == 'Late'){

                if (!empty($attach_date))
                {
                }
                else
                {
                }

                ?>

                <?php
            }elseif ($status == 'Late *'){

                if (!empty($attach_date))
                {
                }
                else
                {
                }
                ?>


                <?php
            }elseif ($status == 'Present'){ ?>

                <?php
                if (!empty($attach_date))
                {
                }
                else
                {
                }

                ?>

                <?php
            }elseif ($status == 'Present *'){
                if (!empty($attach_date))
                {
                }
                else
                {

                }
            }elseif ($status == 'Attach'){

            }elseif ($flag == 2){

                $leave += 1;
            }

            if(sizeof($checktimes) == 1){
                if($selectedTime==null){
                }else{
                }
            }else{
                if(strtotime($hours)>strtotime($expected_hours)){
                    $overtime_array[]=$timecalculation_remarks;
                }elseif(strtotime($hours) < strtotime($expected_hours)){
                    $hour_short_array[]=$timecalculation_remarks;
                }
            }
        }



             $data['start_date'] = $start_date;
             $data['to_date'] = $to_date;
             $data['counter'] = $counter;
             $data['sum'] = $sum;
             $data['total_exp_hr'] = $total_exp_hr;
             $data['total_hr_work'] = $total_hr_work;
             $data['total_present'] = $total_present;
             $data['total_late'] = $total_late;
             $data['total_absent'] = $total_absent;
             $data['total_holiday'] = $total_holiday;
             $data['total_leave'] =$total_leave;
             $data['leave'] = $leave;
             $data['total_day_off'] = $total_day_off;
             $data['total_work'] = $total_work;
             $data['expected_hours_work'] = $expected_hours_work;
             $data['hour_short_array'] = $hour_short_array;
             $data['overtime_array'] = $overtime_array;
             $data['attach_hrs'] = $attach_hrs;
       //dd($data);

        return view('Users.test_report',$data);
    }

    public function showEmployeesTag(Request $request, $id){

        $user = User::find($id);
        return view('Users.addEmployeeTags', compact('user'));
    }

    public function editEmployeesTag(Request $request, $id){
        $user = User::find($id);
        $user->tags = ',' . str_replace(', ', ',', trim($request->tags, ',')) . ',';

        $user->save();

        return redirect()->back();
    }

    public function RegisteredEmployees(Request $request){
        $startDate = Carbon::now()->subDays(29); // 30 days ago from today
        $endDate = Carbon::now(); // Today

        if($request->date_from && $request->date_to){
            $startDate = $request->date_from;
            $endDate = $request->date_to;
        }
        $users_by_date = User::whereBetween('reg_date', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(reg_date, "%Y-%m-%d") as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();
        $page_title = 'Date wise Registered Employees';
        return view('Users.registeredEmployees')->with([
            'users_by_date' => $users_by_date,
            'page_title' => $page_title,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    public function ManageUsersIndex(){
       $users = User::whereNotIn('role_id', [5, 6])->get();


        $page_title = 'Manage Users';
       return view('Users.manage_users_index')->with([
           'users' => $users,
           'page_title' => $page_title
       ]);
    }

    public function ManageUsersCreate(){
        $roles = Role::all();
        $jurisdiction = RoleJurisdiction::all();
        $page_title = 'Create New User';

       return view('Users.manage_users_create')->with([
           'page_tile' => $page_title,
           'roles' => $roles,
           'jurisdiction' => $jurisdiction
       ]);
    }
    public function ManageUsersEdit($id){
        $user = User::find($id);
        $roles = Role::all();
        $page_title = 'Edit User';
       return view('Users.manage_users_edit')->with([
           'page_tile' => $page_title,
            'roles' => $roles,
           'user' => $user
       ]);
    }
    public function ManageUsersStore(Request $request)
    {
        $lastUserId = User::latest('id')->pluck('id')->first();

        if ($request->get('action') == 'save_and_close') {

            $user = User::create([
                'id' => $lastUserId + 1, // Set the ID to the next available ID
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $request->role_id,
                'password' => Hash::make($request->password),
            ]);

            return redirect('manage_users_create')->with('message', 'Record inserted Successfully');
        } elseif ($request->get('action') == 'save') {
            $user = User::create([
                'id' => $lastUserId + 1, // Set the ID to the next available ID
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $request->role_id,
                'password' => Hash::make($request->password),
            ]);
            Alert::success('Record Inserted Successfully ! Thanks');
            return redirect('manage_users_index')->with('message', 'Record inserted Successfully');
        }
    }

    public function  ManageUsersUpdate(Request $request, $id)
    {
        $user = User::find($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->password =Hash::make($request->password);

        $user->save();

        return redirect('manage_users_index')->with('message', 'Record updated Successfully');
    }
}

