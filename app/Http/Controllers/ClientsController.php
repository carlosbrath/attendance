<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;
use App\Designation;
use App\Region;
use App\Zone;
use App\Branch;
use App\ClientTree;
use App\DepartmentLevel;
use App\RoleJurisdiction;
use App\Role;
use Alert;
use Hash;
use DB;

class ClientsController extends Controller
{
    public function index(){
        $client=Client::with('parent')->get();
        $page_title = 'All Departments';
        return view('Clients.index',compact('client','page_title'));
    }
    
    public function clientsNew(Request $request){
        
        if($request->has('parent_id') && $request->get('parent_id')>0){
            $parent_id = $request->get('parent_id');
            $pclient=Client::where('id',$parent_id)->first();
            
            $prev_parent_id = $pclient->parent_id;;
            $page_title = 'Childs of '.$pclient->name;
            $client=Client::with('parent')->where('parent_id',$parent_id)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
        }
        else{
            $prev_parent_id = 0;
            $parent_id = 0;
            $client=Client::with('parent')->where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
            $page_title = 'Departments';
        }
        
        $page_title = 'Departments hierarchy';

        return view('Clients.indexNew',compact('client','parent_id','prev_parent_id','page_title'));
    }
    public function create(){

         $role=Role::select('id','title')->whereIn('id',[5,7])->get();
         $departments = Client::select('id','name')->get();
         $regions = Region::pluck('region_name','region_id')->toArray();        
         $levels = DepartmentLevel::pluck('title','id');

         $page_title = 'Add Departments';
         return view('Clients.create',compact('role','departments','regions','levels','page_title'));
    }
    public function store(Request $request){

        $this->validate($request,
            ['name' => 'required',
            'contact' => 'required',
            'email' => 'required|email',
            'region_id' => 'required',
            'zone_id' => 'required',
            'branch_id' => 'required',
            'department_level_id' => 'required'
            ]
        );


         $emailchecking = Client::where('email',$request->email)->where('email','!=','')->first();
          if($emailchecking){
          $this->validate($request, [
           'email' => 'unique :users,email'
          ]);
        }
         $titlechecking = Client::where('name',$request->name)->first();
        if($titlechecking){
                return redirect('clients/create')->with('message','Same Name Exist Please Enter Different One');
        }else{

            $client = new Client();
            $client->name=$request->name;
            $client->email=$request->email;
            $client->contact=$request->contact;
            $client->department_level_id=$request->department_level_id;
            $client->parent_id=$request->department_id;
            $client->region_id=$request->region_id;
            $client->zone_id=$request->zone_id;
            $client->branch_id=$request->branch_id;       
            
            $client->total_employees=$request->total_employees;  
            $client->vacant_posts=$request->vacant_posts;  

            $client->focal_person=$request->focal_person;  
            $client->sort_order=$request->sort_order;   

            $client->logo="";
            if($client->save()){
                //$user_id = User::whereRaw('id = (SELECT MAX(`id`) FROM users WHERE LENGTH(id) = 9)')->first();
                $user_id = User::whereRaw('id = (SELECT MAX(`id`) FROM users)')->first();
                $idd=$user_id->id;
                $idd = $idd+1;
                $department_id = $client->id;


                $sql = "insert into `users` (`id`, `department_id`, `name`, `email`, `role_id`, `password`) values (".$idd.", ".$department_id.", '".$request->name."', '".$request->email."', '".$request->role_id."', '".Hash::make("123456")."')";
                DB::insert($sql);
            }

            Alert::success('Record Inserted Successfully ! Thanks');
            return redirect('clients')->with('message','Record Inserted Successfully');
        }
    }
    public function show($id){
        $client=Client::find($id);
        
        return view('Clients.index',compact('client'));
    }

    public function edit($id){
        $client=Client::find($id);
        $departments = Client::select('id','name')->get();
        $levels = DepartmentLevel::pluck('title','id');
        $regions = Region::pluck('region_name','region_id')->toArray();

        $zones = Zone::where('region_id',$client->region_id)->pluck('zone_name','zone_id')->toArray();
        $branches = Branch::where('zone_id',$client->zone_id)->pluck('branch_name','branch_id')->toArray();
        
        $page_title = 'Edit Departments';
        return view('Clients.edit',compact('client','departments','levels','regions','zones','branches','page_title'));
    }
    public function clients_account_res_password(){

       $q="SELECT 

            users.id as user_id,
            users.email,
            users.name,
            users.password,
            clients.id as client_id ,
            clients.name as client_name,
            roles.title as role,
            parent.name as parent_dept
        from clients 
        INNER JOIN users on users.department_id= clients.id 
        INNER JOIN roles on roles.id= users.role_id 
        LEFT JOIN clients as parent on parent.id = clients.parent_id
        WHERE users.role_id in(5,7)";
        $client_creadential = collect(DB::select( DB::raw($q)));
        
        return view('Clients.client_reset_password',compact('client_creadential'));
    }
    public function client_show($id){
       $user=User::find($id);
       $jurs = RoleJurisdiction::pluck('title','id');
       return view('Clients.client_edit',compact('user','jurs'));
    }
    public function update_client_cread(Request $request, $id){
       $user=User::find($id);
       $data=$request->all();
       if($data['email']!=""){
            $user->email=$data['email'];
       }
       if($data['password']!=""){       
           $user->password=Hash::make($data['password']);
           //dd($user);
       } 
       $user->jurisdiction_id = $request->jurisdiction_id;
       $user->save();
        return redirect('super_admin_reset_acc')->with('message','Password updated Successfully');
    }
    public function update(Request $request, $id){

        $this->validate($request,
            ['name' => 'required',
            'contact' => 'required',
            'email' => 'required|email',
            'department_level_id' => 'required'
            ]
        );

        $client=Client::find($id);
        $client->name=$request->name;
        $client->parent_id=$request->department_id;
        $client->email=$request->email;
        $client->department_level_id=$request->department_level_id;
        $client->region_id=$request->region_id;
        $client->zone_id=$request->zone_id;
        $client->branch_id=$request->branch_id;
        $client->total_employees=$request->total_employees;
        $client->vacant_posts=$request->vacant_posts;
        $client->sort_order=$request->sort_order;
        $client->focal_person=$request->focal_person;  

        if($request->file('logo')!=''){
            $file = $request->file('logo');
            $filename =   time() . '.' . $file->getClientOriginalExtension();
            $destination_path=public_path('/photos');
            $file->move($destination_path,$filename);
            // $path = $file->storeAs('photos', $filename);
            $client->logo=$filename;
        }
        $client->contact=$request->contact;
        if($client->save()){
        $user=User::where('email',$request->email)->first();
        if($user){
            $user->name=$request->name;
            $user->save();
        }
       }
        return redirect('clients')->with('message','Record updated Successfully');
    }
    public function rebuild(Request $request)
    {
        ClientTree::generatetree();
        ClientTree::generate_all_child_ids();
        return redirect('clients')->with('message','Tree Generated Successfully');
    }
    public function destroy($id){
        $client=Client::find($id);
      if(User::where('department_id',$id)->count()){
        return redirect('clients')->with('error','Deparment id already used in User');
        }
      if(Designation::where('department_id',$id)->count()){
        return redirect('clients')->with('error','Deparment id already used in Designation');
        }
        $client->delete();
        return redirect('clients')->with('message','Record deleted Successfully');
    }
}
