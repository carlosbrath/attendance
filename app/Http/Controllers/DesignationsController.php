<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Designation;
use App\Client;
use App\User;
use Session;
use App\WebsterCheckInOut;
use App\Attendance;
use Alert;
class DesignationsController extends Controller
{
  
  public function index(){
     

      $dep_id=Session::get('dep_id');
      $designation=Designation::where('department_id',$dep_id)->get();
      $page_title='Designations';  
     return view('Designations.index',compact('designation','page_title'));
  }

  public function create(){ 

   
   if(Session::get('role_id')==1){
        
          $department=Client::where('parent_id','!=',0)->get();          
     }
   else
       {
         $department=Client::where('parent_id',Session::get('dep_id'))->get();
       }
       $page_title='Add Designation';  
    // $department=Client::all();
    return view('Designations.create',compact('department','page_title'));
  }
  public function store(Request $request){
      

       $validatedData = $request->validate([
        'title' => 'required'
        ]);
   

    $dep_id=Session::get('dep_id');
    $this->validate($request, ['title' => 'required']); 
    $titlechecking = Designation::where('title',$request->title)->where('department_id',$dep_id)->first();
    if($titlechecking){
     return redirect('designations/create')->with('message','Same Title Exist Please Submit Different');
                      }
else{
       
        if ($request->get('action') == 'save') { 
              $designation = new Designation();
              $designation->title=$request->title;
          
              $designation->description=$request->desc;
              $designation->department_id=Session::get('dep_id');
              if($designation->save()){
              Alert::success('Record Submit Successfully ! Thank You');
              return redirect('designations');         
                   }
          }
        elseif ($request->get('action') == 'save_and_close') {
                $designation = new Designation();
                $designation->title=$request->title;
                $designation->description=$request->desc;
                $designation->department_id=Session::get('dep_id');
                if($designation->save()){
                return redirect('designations/create')->with('msg','Record Submit Successfully ! Thank you');         
             }
          }


    }
  }
  public function addNew(Request $request){

    return response()->json(1);
  }

  public function show($id){
    $designation=Designation::find($id);
    return view('Designations.show',compact('designation')); 
  }


  public function edit($id){
    $designation=Designation::find($id);
    $page_title='Update Designation';  
    return view('Designations.edit',compact('designation','page_title'));
  }

  public function update(Request $request, $id){
    $designation=Designation::find($id);
    $designation->title=$request->title; 
    $designation->description=$request->desc;
    if($designation->save()){
    Alert::success('Record Update Successfully ! Thank You');
    return redirect('designations');
                            }
  }

  public function destroy($id){
    $designation=Designation::find($id);
    if(User::where('designation_id',$id)->count()){
      return redirect('designations')->with('error','Designation id already used in User');
      }
    $designation->delete();
    return redirect('designations')->with('error','Record Deleted Successfully');
  }
}
