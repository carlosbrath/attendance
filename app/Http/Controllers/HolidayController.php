<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\HolidayCalendar;
use App\Client;
use App\User;
use Session;
use Alert;
use DateTime;
use DateInterval;
use DatePeriod;
class HolidayController extends Controller
{
        public function index()
        {  
              $dep_id=Session::get('dep_id');
              $client=Client::select('id')->where('parent_id',$dep_id)->get();
              if($client->isEmpty()){
                $department= Client::where('id',$dep_id)->first();
                $dep_id= $department->parent_id;
                $client=Client::select('id')->where('parent_id',$dep_id)->get();
              }
              $client_ids=$client->toArray();
              $single_department_hcal = HolidayCalendar::with(['user','department'])->select()->where('is_all_departments',0)
             ->whereIn('department_id',$client_ids)
             ->get();
              $all_department_hcal = HolidayCalendar::with(['user','department'])->select()->where('is_all_departments',1)
             ->whereIn('department_id',$client_ids)->groupBy('title')
             ->get();

              $data['holiday_calendar'] = $single_department_hcal->merge($all_department_hcal);
              $data['page_title'] = "holidays ";
              return view('Holiday_Calendar.index',$data);
         }
        public function create()
        { 
           
            $dep_id=Session::get('dep_id'); 
            $data['department']=Client::where('parent_id',$dep_id)->get();
            if($data['department']->isEmpty()){
               $department= Client::where('id',$dep_id)->first();
               $dep_id= $department->parent_id;
               $data['department']=Client::where('parent_id',$dep_id)->get();
              
            }

            $data['department_emp']=client::with(['users'=>function($q){
                return $q->where('flag',1)->where('status',1);
            }])->where('parent_id',$dep_id)->get();
            
            $data['page_title'] = "Add New Holiday";
            return view('Holiday_Calendar.create',$data);
        }
        public function store(Request $request)
        {
         
            if($request->user_id){
                 
            }else{
                
            }
            $dep_id=Session::get('dep_id');

            if($request->holiday_info=="department"){
                   $this->validate($request, 
                [  
                       'title' => 'required',
                       'date_from'=>'required',
                       'date_to'=>'required'
                ]);

            }  if($request->holiday_info=="user"){
               
                    $this->validate($request,[  
                       'title' => 'required',
                       'date_from'=>'required|before:date_to',
                       'date_to'=>'required|after:date_from',
                       'user_id' => 'required',
                ]);
            }

            $from_date= $request->date_from;
            $to_date= $request->date_to;
            $begin = new DateTime($from_date);
            $end = new DateTime($to_date);
            $end = $end->modify( '+1 day' );
            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($begin, $interval ,$end);
            $dates=[];

            if($request->deparment_id == 'all'){
                $departments = Client::where('parent_id',$dep_id)->get();
                foreach($departments as $dept){
                    foreach($daterange as $date){
                      //$dates[]=
                        $holiday_cal =  new HolidayCalendar();
                        $holiday_cal->date=$date->format("Y-m-d");
                        $holiday_cal->department_id=$dept->id;
                        $holiday_cal->user_id=$request->user_id;
                        $holiday_cal->title = $request->title;
                        $holiday_cal->date_from = $request->date_from;
                        $holiday_cal->date_to = $request->date_to;
                        $days_count=strtotime($request->date_to)-strtotime($request->date_from);
                        $days = round($days_count/ (60 * 60 * 24));
                        $holiday_cal->days = $days;
                        $holiday_cal->is_all_departments = 1;
                        $holiday_cal->save();
                      foreach(Client::where('parent_id',$dept->id)->get() as $key=>$sub_department){
                        $holiday_cal =  new HolidayCalendar();
                        $holiday_cal->date=$date->format("Y-m-d");
                        $holiday_cal->department_id=$sub_department->id;
                        $holiday_cal->user_id=$request->user_id;
                        $holiday_cal->title = $request->title;
                        $holiday_cal->date_from = $request->date_from;
                        $holiday_cal->date_to = $request->date_to;
                        $holiday_cal->is_all_departments = 1;
                        $days_count=strtotime($request->date_to)-strtotime($request->date_from);
                        $days = round($days_count/ (60 * 60 * 24));
                        $holiday_cal->days = $days;
                        $holiday_cal->save();                    
                      }

                    }
                }
                Alert::success('Record Insert Successfully ! Thank You');
                return redirect('holidays'); 
            }elseif($request->user_id){
              
                 foreach($request->user_id as $u_id){
                   
                     foreach($daterange as $date){
                        $holiday_cal =  new HolidayCalendar();
                        $holiday_cal->date=$date->format("Y-m-d");
                        $holiday_cal->department_id=$request->deparment_id;
                        $holiday_cal->user_id=$u_id;
                        $holiday_cal->title = $request->title;
                        $holiday_cal->date_from = $request->date_from;
                        $holiday_cal->date_to = $request->date_to;
                        $days_count=strtotime($request->date_to)-strtotime($request->date_from);
                        $days = round($days_count/ (60 * 60 * 24));
                        $holiday_cal->days = $days;
                        $holiday_cal->save();
                    }
                 }

            }else{
               foreach($daterange as $date){
                    $holiday_cal =  new HolidayCalendar();
                    $holiday_cal->date=$date->format("Y-m-d");
                    $holiday_cal->department_id=$request->deparment_id;
                    $holiday_cal->user_id=$request->user_id;
                    $holiday_cal->title = $request->title;
                    $holiday_cal->date_from = $request->date_from;
                    $holiday_cal->date_to = $request->date_to;
                    $days_count=strtotime($request->date_to)-strtotime($request->date_from);
                    $days = round($days_count/ (60 * 60 * 24));
                    $holiday_cal->days = $days;
                    $holiday_cal->save();

                  foreach(Client::where('parent_id',$request->deparment_id)->get() as $key=>$sub_department){
                    $holiday_cal =  new HolidayCalendar();
                    $holiday_cal->date=$date->format("Y-m-d");
                    $holiday_cal->department_id=$sub_department->id;
                    $holiday_cal->user_id=$request->user_id;
                    $holiday_cal->title = $request->title;
                    $holiday_cal->date_from = $request->date_from;
                    $holiday_cal->date_to = $request->date_to;
                    $days_count=strtotime($request->date_to)-strtotime($request->date_from);
                    $days = round($days_count/ (60 * 60 * 24));
                    $holiday_cal->days = $days;
                    $holiday_cal->save();                    
                  }

                }
              
            }
            Alert::success('Record Insert Successfully ! Thank You');
            return redirect('holidays'); 
               
            
        //   if($request->action=="save_and_close"){
           
        //     if($holiday_cal->save()){
               
        //         return redirect('holidays/create')->with('msg','Record Submit Successfully');
        //     } 
        // }
        // if($request->action=="save"){
           
        //     if($holiday_cal->save()){
        //        
        //     } 
        // }
    }
    public function show($id)
    {
        
    }
    public function edit($id)
    {  
        $dep_id=Session::get('dep_id');
        $data['holiday_cal']= HolidayCalendar::find($id);
        $data['department']=Client::where('parent_id',$dep_id)->get();
        $data['department_emp']=client::with(['users'=>function($q){
                return $q->where('flag',1)->where('status',1);
            }])->where('parent_id',$dep_id)->get();
          
        $data['page_title'] = "Update Holiday";    
        return view('Holiday_Calendar.edit',$data);
    }
    public function update(Request $request, $id)
    {  
        $holiday_calendar= HolidayCalendar::find($id);
        $dep_id=Session::get('dep_id');
          $this->validate($request, 
            [  
                 'title' => 'required',
                 'date_from'=>'required',
                 'date_to'=>'required',
            ]);
        $holiday_calendar->department_id=$request->deparment_id;
        $holiday_calendar->title = $request->title;
        $holiday_calendar->date_from = $request->date_from;
        $holiday_calendar->date_to = $request->date_to;
        $days_count=strtotime($request->date_to)-strtotime($request->date_from);
        $days = round($days_count/ (60 * 60 * 24));
        $holiday_calendar->days = $days;

        if($holiday_calendar->save()){
            Alert::success('Record Update Successfully ! Thank You');
            return redirect('holidays');
        } 
      
    }   
    public function destroy($id)
    {      
        $holiday_calendar= HolidayCalendar::find($id);
        if($holiday_calendar->is_all_departments==1){
            //delete all relevant
            $dep_id=Session::get('dep_id');
            $departments = Client::where('parent_id',$dep_id)->get();
            $holiday_title = $holiday_calendar->title;
            foreach($departments as $department){
                HolidayCalendar::where(['title'=>$holiday_title,'department_id'=>$department->id])->delete();
            }
        }else{
            $holiday_calendar->delete();
        }
        Alert::success('Record Delete Successfully ! Thank You');
        return redirect('holidays');
    }
 }
