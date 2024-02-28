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
              $data['holiday_calendar']= HolidayCalendar::select('id','title','department_id','days','date_from','date_to')
                                                 ->where('department_id',$dep_id)
                                                 ->get();
              return view('Holiday_Calendar.index',$data);
         }
        public function create()
        { 
            $dep_id=Session::get('dep_id'); 
            $data['department']=Client::where('parent_id',$dep_id)->get();
            $data['department_emp']=client::with(['users'=>function($q){
                return $q->where('flag',1)->where('status',1);
            }])->where('parent_id',$dep_id)->get();
            return view('Holiday_Calendar.create',$data);
        }
        public function store(Request $request)
        {
            $dep_id=Session::get('dep_id');
           
            if($request->holiday_info=="department"){
                   $this->validate($request, 
                [  
                       'title' => 'required',
                       'date_from'=>'required',
                       'date_to'=>'required',
                       'deparment_id' => 'required',
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
            foreach($daterange as $date){
              //$dates[]=
                $holiday_cal =  new HolidayCalendar();
                $holiday_cal->date=$date->format("Y-m-d");
                $holiday_cal->department_id=$dep_id;
                $holiday_cal->user_id=$request->user_id;
                $holiday_cal->title = $request->title;
                $holiday_cal->date_from = $request->date_from;
                $holiday_cal->date_to = $request->date_to;
                $days_count=strtotime($request->date_to)-strtotime($request->date_from);
                $days = round($days_count/ (60 * 60 * 24));
                $holiday_cal->days = $days;
                $holiday_cal->save();
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
        return view('Holiday_Calendar.edit',$data);
    }
    public function update(Request $request, $id)
    {  
        $holiday_calendar= HolidayCalendar::find($id);
        $dep_id=Session::get('dep_id');
          $this->validate($request, 
            [  
                 'title' => 'required',
                 'date_from'=>'required|before:date_to',
                 'date_to'=>'required|after:date_from',
            ]);
        $holiday_calendar->department_id=$dep_id;
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
        $holiday_calendar->delete();
        Alert::success('Record Delete Successfully ! Thank You');
        return redirect('holidays');
    }
 }
