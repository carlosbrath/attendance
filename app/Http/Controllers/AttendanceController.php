<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Attendance;
use App\ExternalDepartment;
use App\ExternalDepartmentsStat;
use App\Device;
use App\Client;
use App\ClientTree;
use App\User;
use App\SyncHistory;
use Session;
use Alert;
use DB;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Cache;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function mark_checkin(Request $request){
        
        $att = Attendance::create(['user_id'=>$request->user_id,'datetime'=>date("Y-m-d H:i:s",strtotime($request->checktime)),'att_checktime'=>$request->checktime,'dev_id'=>'----']);
        if($att){
            return json_encode(['status'=>1,"msg"=>"Attendance Marked Successfully"]);
        }else{
            return json_encode(['status'=>0,"msg"=>"Something Went Wrong, Please Try again"]);
        }
    }
    public function get_employees(Request $request){
        $department_id = $request->department_id;
        $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$department_id." or c.id = ".$department_id;
        $departments = DB::select($users_department_clause);
        $users_sql = "SELECT u.id as user_id,u.name,c.name as department_name,c.parent_id as d_parent_id,d.title as designation FROM users u 
                          JOIN time_categories t on t.id = u.tcat_id
                          JOIN clients c on c.id = u.department_id
                          JOIN designations d on d.id = u.designation_id
                          WHERE u.department_id IN (
                              ".$users_department_clause."
                          )
                        AND  u.status = 1 and u.app_attendance=1";
          $users = collect(DB::select($users_sql));
        return json_encode(["status"=>1,"employees"=>$users]);
    }

    public function esef_attendance(Request $request){
        if($request->att_date==""){
            return json_encode(['status'=>0,"msg"=>"Date field in required"]);
        }       
        $att = Attendance::where(['dev_id'=>'CJH9220460049'])->where(DB::raw("DATE(datetime) = '".$request->att_date."'"))->get();
        if($att){
            return json_encode(['status'=>1,"data"=>$att]);
        }else{
            return json_encode(['status'=>0,"msg"=>"Something Went Wrong, Please Try again"]);
        }
    }

    public function fetch_attendance($datefor,$testrecords)
    {
        $client = new \GuzzleHttp\Client();
        $devices = Device::pluck('serial_number');
        $devices = $devices->toJson();

        $response = $client->request(
            'POST',
            'http://168.119.186.114:8080/api/webster_attendance',
            [
                'form_params' => [
                    'devices' => $devices,
                    'att_date' => $datefor
                ]
            ]
        );
        
        $responseJSON = json_decode($response->getBody(), true);
        if($responseJSON['status'] == 1){
            if($testrecords==1)
            {
                dd(count($responseJSON['data']));
            }
            
            $sql = "DELETE FROM attendance WHERE mark_attendance = 0 AND DATE(`datetime`) = '" . $datefor . "'";
            DB::select($sql);

            // foreach($responseJSON['data'] as $rec){
            //     $at_rec = [
            //         'user_id' => $rec['user_id'],
            //         'att_checktime' => $rec['att_checktime'],
            //         'datetime' => $rec['datetime'],
            //         'dev_id' => $rec['dev_id'],
            //         'att_checkinout_id' => 0,
            //         'mark_attendance' => $rec['mark_attendance']
            //     ];
            //     $new_ar = Attendance::create($at_rec);
            //     //dd($new_ar);
            // }


            $batchSize = 500;
            $recordsToInsert = [];

            foreach ($responseJSON['data'] as $rec) {
                $at_rec = [
                    'user_id' => $rec['user_id'],
                    'att_checktime' => $rec['att_checktime'],
                    'datetime' => $rec['datetime'],
                    'dev_id' => $rec['dev_id'],
                    'att_checkinout_id' => 0,
                    'mark_attendance' => $rec['mark_attendance']
                ];

                $recordsToInsert[] = $at_rec;

                // Check if the batch size is reached, then insert the records and reset the array
                if (count($recordsToInsert) == $batchSize) {
                    Attendance::insert($recordsToInsert);
                    $recordsToInsert = [];
                }
            }

            // Insert any remaining records
            if (!empty($recordsToInsert)) {
                Attendance::insert($recordsToInsert);
            }
        }
        else if($testrecords==1)
        {
            dd($devices);
            //dd($responseJSON);
        }
    }

    

    public function fetch_attendance_external_deps_all($datefor)
    {
        $msg='Nothing updated.';
        
        $updated='';
        $notupdated='';

        $edeps = ExternalDepartment::where('status',1)->get();
        foreach($edeps as $dep)
        {
            try{
                $dstats = file_get_contents($dep->stats_api);
                $stats_array = json_decode($dstats, true);
                $dataset = ['dep_id'=>$dep->id,
                            'total'=>(isset($stats_array['Total']))?$stats_array['Total']:0,
                            'present'=>(isset($stats_array['Present']))?$stats_array['Present']:0,
                            'absent'=>(isset($stats_array['Absent']))?$stats_array['Absent']:0,
                            'late'=>(isset($stats_array['Late']))?$stats_array['Late']:0,
                            'onleave'=>(isset($stats_array['onLeave']))?$stats_array['onLeave']:0,
                            'ontour'=>(isset($stats_array['onTour']))?$stats_array['onTour']:0,
                            'onschedule'=>(isset($stats_array['onSchedule']))?$stats_array['onSchedule']:0,
                            'onattach'=>(isset($stats_array['onAttach']))?$stats_array['onAttach']:0,
                            'shift_not_started'=>(isset($stats_array['ShiftNotStarted']))?$stats_array['ShiftNotStarted']:0,
                            'early_exit'=>(isset($stats_array['early_exit']))?$stats_array['early_exit']:0,
                            'off_days'=>(isset($stats_array['off_days']))?$stats_array['off_days']:0,
                            'updated_date'=>date('Y-m-d')];

                $alreadyExists = ExternalDepartmentsStat::where('updated_date',date('Y-m-d'))->where('dep_id',$dep->id)->first();
                if($alreadyExists)
                {
                    $alreadyExists->update($dataset);
                }
                else
                {
                    ExternalDepartmentsStat::create($dataset);
                }

                $dep->last_updated = date('Y-m-d H:i:s');

                $dep->update();
                
                $updated= (empty($updated))?$dep->department_name:', '.$dep->department_name;  

            }catch(Exception $e){
                $notupdated= (empty($notupdated))?$dep->department_name:', '.$dep->department_name;
            }
        }

        
        $msg=(empty($updated))?'':$updated.' attandance updated for today up to now.';
        $msg .= (empty($notupdated))?'':'Something went wrong for '.$notupdated.'! the server may be down.';
        return $msg;
        
    }
    public function fetch_attendance_external_dep($dep_id,$datefor)
    {
        $msg='Nothing updated.';
        try{
            $edep = ExternalDepartment::where('status',1)->where('id',$dep_id)->get();
            foreach($edep as $dep)
            {
                $dstats = file_get_contents($dep->stats_api);
                $stats_array = json_decode($dstats, true);
                $dataset = ['dep_id'=>$dep->id,
                            'total'=>(isset($stats_array['Total']))?$stats_array['Total']:0,
                            'present'=>(isset($stats_array['Present']))?$stats_array['Present']:0,
                            'absent'=>(isset($stats_array['Absent']))?$stats_array['Absent']:0,
                            'late'=>(isset($stats_array['Late']))?$stats_array['Late']:0,
                            'onleave'=>(isset($stats_array['onLeave']))?$stats_array['onLeave']:0,
                            'ontour'=>(isset($stats_array['onTour']))?$stats_array['onTour']:0,
                            'onschedule'=>(isset($stats_array['onSchedule']))?$stats_array['onSchedule']:0,
                            'onattach'=>(isset($stats_array['onAttach']))?$stats_array['onAttach']:0,
                            'shift_not_started'=>(isset($stats_array['ShiftNotStarted']))?$stats_array['ShiftNotStarted']:0,
                            'early_exit'=>(isset($stats_array['early_exit']))?$stats_array['early_exit']:0,
                            'off_days'=>(isset($stats_array['off_days']))?$stats_array['off_days']:0,
                            'updated_date'=>date('Y-m-d')];

                $alreadyExists = ExternalDepartmentsStat::where('updated_date',date('Y-m-d'))->where('dep_id',$dep->id)->first();
                if($alreadyExists)
                {
                    $alreadyExists->update($dataset);
                }
                else
                {
                    ExternalDepartmentsStat::create($dataset);
                }
                //$dep->update(['last_updated',date('Y-m-d H:i:s')]);

                $dep->last_updated = date('Y-m-d H:i:s');

                $dep->update();

                $msg=$dep->department_name.' attandance updated for today up to now.';
            }
        }catch(Exception $e){
            $msg = 'Something went wrong! the server may be down.';
        }

        return $msg;
    }
    public function sync_devices(){

        $devices = Device::pluck('serial_number');
        $devices = $devices->toJson();

        
        $client = new \GuzzleHttp\Client();
        $dev_response = $client->request(
                    'POST',
                    'http://168.119.186.114:8080/api/devices_stats',
                    [
                        'form_params' => [
                            'devices' => $devices
                        ]
                    ]
                );

        $responseJSON = json_decode($dev_response->getBody(), true);

        $onlineDevices=[];
        foreach ($responseJSON['online'] as  $onlinedevice) {
            //dd($onlinedevice);
            $device = Device::where('serial_number',$onlinedevice['serialno'])->get()->first();
            $device->update(['status'=>'Online','lastActivity'=>$onlinedevice['lastactivity']]);
        }

        foreach ($responseJSON['offline'] as  $offlinedevice) {
            $device = Device::where('serial_number',$offlinedevice['serialno'])->get()->first();
            $device->update(['status'=>'Offline','lastActivity'=>$offlinedevice['lastactivity']]);
        }
    }
    public function sync_attendance(Request $request){
        $page_title = 'Sync Attendance';

        $edeps = ($request->edeps)?$request->edeps:0;
        $dep_id = ($request->dep_id)?$request->dep_id:0;

        $datefor = ($request->datefor)?$request->datefor:date('Y-m-d');
        $testrecords = ($request->testrecords)?$request->testrecords:0;

        $msg = '';
        if($request->sync && $request->sync == 'go')
        {
            $this->sync_devices();

            if($edeps==1)
            {
                if($dep_id>0)
                {
                    $msg = $this->fetch_attendance_external_dep($dep_id,$datefor);
                }
                else
                {
                    $msg = $this->fetch_attendance_external_deps_all($datefor);
                }
            }
            else
            {

                $this->fetch_attendance($datefor,$testrecords);
                $msg = 'Attendance records synced for date: '.$datefor;

                // if($testrecords!=1 && $datefor == date('Y-m-d'))
                // {
                //     $yesterday_synced = Cache::remember('yesterday_synced', now()->addMinutes(1440), function () {
                //         $pdatefor = date('Y-m-d', strtotime('-1 days'));
                //         $this->fetch_attendance($pdatefor, 0);
                //         return true;
                //     });
                //     $msg = 'Attendance records synced for today up to now.';
                // }

                $sh =SyncHistory::create(['stats'=>'new']);
                DB::table('sync_history')->insert(
                    ['stats' => 'new']
                );
            }
        }
        
        $last_sync = SyncHistory::latest('sync_on')->first();
        //dd($last_sync);
        $edeps = ExternalDepartment::where('status',1)->get();
        Artisan::call('cache:clear');

        return view('Dashboards.sync_attendance',compact('page_title','datefor','msg','edeps','last_sync'));
       
        
    }

    public function generate_clients_tree(Request $request)
    {
        
        if($request->dep_id)
        {
            $this->updateTree($request->dep_id);
        }
        else
        {
            $this->updateTree(0);
        }
    }

    protected function updateTree($pid)
    {
        $clients = Client::where('parent_id',$pid)->get();
        $ids = [];
        if($clients)
        {
            foreach($clients as $client)
            {
                $ids[] = $client->id;
    
                $ids[] = $this->updateTree($client->id);
    
                dd($ids);
            }
        
        }
        return $ids;
    }


}
