<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;
use App\Designation;
use App\TimeCategory;
use Session;
use Alert;
use Hash;
class DepartmentsController extends Controller
{
    public function index(){
       
       if(Session::get('role_id')==1){
          $departments=Client::with('TimeCategory')->where('parent_id','!=',0)->get();
        }else{
          $departments=Client::with('TimeCategory')->where('parent_id',Session::get('dep_id'))->get();
       }
       $timecategories =TimeCategory::where('department_id',Session::get('dep_id'))->where('type',1)->where('time_cat_type',1)->pluck('title','id')->toArray();
        $page_title = "Departments"; 
        return view('departments.index',compact('departments','page_title','timecategories'));
    }

    public function create(){
        $dep_id=Session::get('dep_id');
        $check_subaccount=Client::where('id',$dep_id)->first();
    
        $page_title = "Add New Department"; 
        return view('departments.create',compact('page_title','check_subaccount'));
    }

    public function store(Request $request){
       $this->validate($request,
                     [
                        'name' => 'required',                                    
                     ]
                  );
        $namechecking = Client::where('name',$request->name)->where('id',Session::get('dep_id'))->first();
        if($namechecking){
        return redirect('departments/create')->with('message','Same department Exist Please enter Different');
        }

       if($request->check_account==2){
             // 2 is for sub account
              $this->validate($request, [
               'email' => 'required'
              ]); 
           $emailchecking = Client::where('email',$request->email)->where('email','!=','')->first();
            if($emailchecking){ 
              $this->validate($request, [
               'email' => 'unique:users,email'
              ]); 
            } 
       }

      if($request->get('action') == 'save') {

        $inputs = $request->all();
        $inputs['parent_id'] = Session::get('dep_id');
        $inputs['status']=1;
        $inputs['sub_account']=$request->check_account;
        $client=Client::create($inputs);
        if($client){
         if($request->check_account==2){  

           $user = new User();
           $user_rec = User::whereRaw('id = (select max(`id`) from users where length(id)<8)')->first();
           $idd=$user_rec->id+1;
           $user->id=$user_rec->id+1;
           $user->department_id=$client->id;
           $user->name=$request->name; 
           $user->email=$request->email;
           $user->role_id=7;
           $hashed = Hash::make("123456");
           $user->password=$hashed;
           $user->save();
            }
            Alert::success('Record Submit Successfully ! Thank You');
            return redirect('departments');   
          }else{
              return redirect()->back()->with('error','Something Went Wrong');
          }
      }
      elseif ($request->get('action') == 'save_and_close') {

        $inputs = $request->all();
        $inputs['sub_account']=$request->check_account;
        $inputs['parent_id'] = Session::get('dep_id');
        $client=Client::create($inputs);
        if($client){
          if($request->check_account==2){  
             $user = new User();
             $user_id = User::whereRaw('id = (select max(`id`) from users where length(id)<8)')->first();
             $idd=$user_id->id+1;
             $user->id=$idd;
             $user->department_id=$client->id;
             $user->name=$request->name; 
             $user->email=$request->email;
             $user->role_id=5;
             $hashed = Hash::make("123456");
             $user->password=$hashed;
             $user->save();
           }
             return redirect('departments/create')->with('msg','Record Submit Successfully ! Thank You');   
        }else{
            return redirect()->back()->with('error','Something Went Wrong');
        }

      }
   
  }

    public function show($id){


        $client=Client::find($id)->where('parent_id', Session::get('dep_id'));
        return view('Clients.view',compact('client'));
    }

    public function edit($id){

        $department=Client::find($id);
        $dep_id=Session::get('dep_id');
        $check_subaccount=Client::where('id',$dep_id)->first();
        $page_title = "Update Department"; 
        
        return view('departments.edit',compact('department','page_title','check_subaccount'));
    }

    public function update(Request $request, $id){
       
        $client=Client::find($id);
        $client->sub_account=$request->check_account;
        $client->name=$request->name; 
        $client->description=$request->description;
        if($client->save()){
             Alert::success('Record Update Successfully ! Thank You');
            return redirect('departments');   
        }
     }

    public function destroy($id){
        $client=Client::find($id);
      if(User::where('department_id',$id)->count()){
        return redirect('departments')->with('error','Deparment id already used in User');
        }
      if(Designation::where('department_id',$id)->count()){
        return redirect('departments')->with('error','Deparment id already used in Designation');
        }
        $client->delete();
        return redirect('departments')->with('error','Record deleted Successfully');
    }
}
