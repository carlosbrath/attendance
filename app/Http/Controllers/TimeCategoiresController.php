<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Client;
use App\TimeCategory;
use App\RoasterStaff;
use Session;
use Alert;

class TimeCategoiresController extends Controller
{
  
    public function index(){
        $dep_id  = Session::get('dep_id');
        $department = Client::select('id')
                      ->where('id',$dep_id)
                      ->orWhere(function($query) use ($dep_id) {
                          $query->where('parent_id', $dep_id)->where('parent_id', $dep_id);})
                      ->get();
        
                    

        $department_id=[];
        foreach ($department as $d ) {
            $department_id[]= $d->id;
         }
        $timecategories=TimeCategory::where('department_id',Session::get('dep_id'))->where('type',1)->get();
        $page_title='timecategories';
        return view('TimeCategoires.index',compact('timecategories','page_title'));
    }

    public function create(){ 

        $dep_id  = Session::get('dep_id');
        $count_dep = Client::where('parent_id',$dep_id)->get();
        if(count($count_dep)==1){             // check for if department show in create form
             $department=Client::where('parent_id',$dep_id)->where('sub_account',1)->get();
        }else{        
          $department=Client::where('parent_id',$dep_id)->where('sub_account',1)->get();
          if($department->isEmpty()){
              $department=Client::where('id',$dep_id)->where('status',1)->get();
           }           
         } 
         $page_title='Add TimeCategory';         
       return view('TimeCategoires.create',compact('department','page_title'));
    }

    public function store(Request $request){

      if($request->time_cat_type==1){
            $this->validate($request, [                                  
                'title' => 'required',
                'grace_time'=>'required',
            ]);
        }else{     
          $this->validate($request, [
                    'roster_title' => 'required',
                 ]
          );
       }
      
       
        $inputs = $request->all();
        
        if($request->day_off==3)
        {
            $inputs['type']=3;
            $inputs['department_id'] =$request->deparment_id;
            $inputs['hours'] =$request->hours;
            $inputs['color'] =$request->color;
            $inputs['time_cat_type'] =$request->time_cat_type;
            $inputs['is_night_shift']="";

        if($request->night_shift==1){
            $inputs['is_night_shift']=$request->night_shift;
            if(TimeCategory::create($inputs)){
               Alert::success('Record Submit Successfully ! Thank you');
               return redirect('timecategories');       
            }else{
               Alert::warning('Record Insertion Failed');
               return redirect('timecategories');
           }
        }else{
            
            $inputs['is_night_shift']= 0;
             if(TimeCategory::create($inputs)){
                 Alert::success('Record Submit Successfully ! Thank you');
                 return redirect('timecategories');                  
            }
        else{
            Alert::warning('Record Insertion Failed');
           return redirect('timecategories');
           }
            
         }

        }



   else{
        
       
         
         if($request->time_cat_type==1){
                $inputs['title'] =$request->title;   
                $inputs['department_id'] =Session::get('dep_id');
                $inputs['grace_time'] = date('G:i', strtotime($request->grace_time));
                $inputs['time_cat_type'] =$request->time_cat_type;

            }else{

                    $inputs['color'] =$request->color;
                    $inputs['time_cat_type'] =$request->time_cat_type;
                    $inputs['department_id'] =Session::get('dep_id');
                    ///$request->roster_department_id;
                    $inputs['title']=$request->roster_title;
                    
            }
        $inputs['type']=1;
       
        // $inputs['time_in'] =  date('G:i', strtotime($request->time_in));
        // $inputs['time_out'] = date('G:i', strtotime($request->time_out));
       
        $inputs['is_night_shift']="";
        if($request->night_shift==1){
           
           $inputs['is_night_shift']=$request->night_shift;
         
         }else{
             $inputs['is_night_shift']= 0;
         }  
      
        if ($request->get('action') == 'save') {     

             if(TimeCategory::create($inputs)){   
                 Alert::success('Record Submit Successfully ! Thank you');
                 return redirect('timecategories');  
            }
         }
        elseif($request->get('action') == 'save_and_close'){
             if(TimeCategory::create($inputs)){       
                 return redirect('timecategories/create')->with('msg','Record Submit Successfully ! Thank You');             
            }      
         }
       
     } 
  }
    
    public function show($id){
        $timecategory=TimeCategory::find($id);
        $department=Client::find($timecategory->department_id);
        return view('TimeCategoires.show',compact('department','timecategory'));
    }
    public function edit($id){
        $dep_id  = Session::get('dep_id');
        // $department=Client::where('id',$dep_id)
        //            ->orWhere(function($query)use($dep_id){
        //                   $query->where('parent_id',$dep_id);})
        //            ->get();
         $department=Client::where('parent_id',$dep_id)->where('sub_account',1)->get();
          if($department->isEmpty()){
              $department=Client::where('id',$dep_id)->where('status',1)->get();
          }
      $timecategories=TimeCategory::find($id);
      $page_title='Update TimeCategory';
      if($timecategories->time_cat_type==1){
        return view('TimeCategoires.edit',compact('timecategories','department','page_title'));
      }else{
              return view('TimeCategoires.roster_edit',compact('timecategories','department')); 
          }
    }

     public function update(Request $request, $id){

       
         if($request->time_cat_type==1){
            $this->validate($request, [
                             'title' => 'required',
                             'grace_time'=>'required'              
                         ]);
        }else{
            $this->validate($request, [
            'roster_department_id' =>'required' ,
            'roster_title' => 'required',]);
        }
        $timecategory=TimeCategory::find($id);
         
        if($request->time_cat_type==2){
           $timecategory->title = $request->roster_title;
           $timecategory->department_id = $request->roster_department_id;         
        }else{
      
          $timecategory->title = $request->title; 
          $timecategory->grace_time = $request->grace_time;
          $timecategory->tc_monday_in =$request->tc_monday_in;
          $timecategory->tc_monday_out = $request->tc_monday_out;
          $timecategory->tc_tuesday_in =$request->tc_tuesday_in;
          $timecategory->tc_tuesday_out = $request->tc_tuesday_out;
          $timecategory->tc_wednesday_in = $request->tc_wednesday_in;
          $timecategory->tc_wednesday_out =$request->tc_wednesday_out;
          $timecategory->tc_thursday_in = $request->tc_thursday_in;
          $timecategory->tc_thursday_out = $request->tc_thursday_out;
         
          $timecategory->tc_friday_in = $request->tc_friday_in;
          $timecategory->tc_friday_out = $request->tc_friday_out;
          $timecategory->tc_saturday_in =$request->tc_saturday_in;
          $timecategory->tc_saturday_out =$request->tc_saturday_out;
          $timecategory->tc_sunday_in =$request->tc_sunday_in;
          $timecategory->tc_sunday_out =$request->tc_sunday_out;
          $timecategory->department_id = Session::get('dep_id');
          $timecategory->is_night_shift=$request->is_night_shift;
       }
        if($timecategory->save()){
             Alert::success('Record Update Successfully ! Thank you');
            return redirect('timecategories');
        }
    }
    public function destroy($id){

        $timecategory=TimeCategory::find($id);
        if(RoasterStaff::where('tcat_id',$id)->count()){
        return redirect('timecategories')->with('error','TimeCategory id already used in RoasterStaff');
        }
        $timecategory->delete();
        return redirect('timecategories')->with('error','Record Deleted Succssfully');
    }    
}
