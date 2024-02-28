<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Leave;
use Validator;
use Alert;
use App\TimeCategory;
use App\Client;
use Session;
use DB;
class LeavesController extends Controller
{

    public function index()
    {
        $dep_id  = Session::get('dep_id');

// $posts = App\Post::whereHas('comments', function (Builder $query) {
//     $query->where('content', 'like', 'foo%');
// })->get();
        ///$posts = App\Post::doesntHave('comments')->get();      
       // $leave=TimeCategory::whereHas('departments')->with(['departments'=>function($query)use($dep_id){
       //     return $query->where('parent_id',$dep_id);
       //   }])->where('type',2)->get();

         // $leave=TimeCategory::whereHas('departments', function($q)use($dep_id) {
         //  return $q->where('parent_id',$dep_id)->orWhere('id',$dep_id);
         //  })->where('type',2)->get();

         $leave=TimeCategory::where('department_id',Session::get('dep_id'))->where('type',2)->get();
        
         
        // $leave=TimeCategory::Has(['departments'=>function($query)use($dep_id){
        //    return $query->where('parent_id',$dep_id);
        //  }])->where('type',2)->get();
        $page_title = "leaves";
        return view('Leaves.index', compact('leave','page_title'));       
    }
    public function create()
    {
        $dep_id  = Session::get('dep_id');     
        $department=Client::where('parent_id',$dep_id)->where('sub_account',1)->get();
        if($department->isEmpty()){
          $department=Client::where('id',$dep_id)->where('status',1)->get();
        }
                
        $page_title = "Add New leave";
        return view('Leaves.create',compact('department','page_title'));
    }

    
   public function getleaveName(Request $request){

      $time_cat_title=TimeCategory::select('id','title','department_id','type')
                     ->where('department_id',$request->id)
                     ->where('type',2)
                     ->get();                      
      return Response()->json($time_cat_title); 

   }
    public function store(Request $request)
    {
       $validatior=Validator::make($request->all(),[
        'type'=>'required|max:255',
        'type'=>'required',
        'leave_type'=>'required',
      ]);
       if($validatior->fails()){
        return redirect('leaves/create')->withInput()->withErrors($validatior);
       }
        $leave=new TimeCategory();
       if($request->deparment_id==''){
           $leave->department_id=Session::get('dep_id');
        }
      else{
            $leave->department_id=Session::get('dep_id');
          } 
        $leave->title= $request->type;
        $leave->type= 2;
        $leave->description= $request->desc;
        $leave->color= $request->color;
        $leave->leave_type=$request->leave_type; 

        if($leave->save()){
        Alert::success('Record Submit Successfully ! Thank You');
        return redirect('leaves');
        }
    }

    
    public function show($id)
    {
  
        $leave=Leave::find($id);
        return view('Leaves.show',compact('leave')); 

    }  
    public function edit($id)
    {   

         $dep_id  = Session::get('dep_id');     
         $department=Client::where('parent_id',$dep_id)->get();
        if($department->isEmpty()){
          $department=Client::where('id',$dep_id)->where('status',1)->get();
        }
         $leave=TimeCategory::find($id);
         $page_title = "Update leave";
        return view('Leaves.edit',compact('leave','department','page_title'));
    }
      public function update(Request $request, $id){

        $leave=TimeCategory::find($id);
        $leave->title=$request->type;
      //   if($request->deparment_id==''){
      //      $leave->deparment_id=Session::get('dep_id');
      //   }
      // else{
      //       $leave->department_id=$request->deparment_id;
      //     } 
        // $leave->title= $request->type;  
        // $leave->department_id=$request->deparment_id;
        $leave->color=$request->color; 
        $leave->description= $request->desc;
        $leave->leave_type= $request->leave_type;
        if($leave->save()){
        Alert::success('Record Update Successfully ! Thank You');
        return redirect('leaves');
        }
    }
      public function destroy($id)
      {     
        $leave=TimeCategory::find($id);
        $leave->delete();
        return redirect('leaves')->with('message','Record deleted Successfully');
      }
}
