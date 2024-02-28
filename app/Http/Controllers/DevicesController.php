<?php

namespace App\Http\Controllers;

use App\DeviceDepartment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Device;
use App\Client;
use Alert;
use Validator;
use Session;
use Illuminate\Support\Facades\Cache;

use DB;

class DevicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $departments=[];
        $dmg_col = '';
        $dmg_id = '';
        if(Session::get('role_id') == 1){
            $department_id = \Request::get('department_id');
            $selected_dmgs = \Request::get('selected_dmgs');

            if($department_id!=""){
                $departments = Client::getSubDepartments($department_id);
            }
            if($selected_dmgs!= '' && $selected_dmgs!='all')
            {
                $region_input = explode('_',$selected_dmgs);
                $dmg_check = $region_input[0];
                $dmg_id = $region_input[1];
                if($dmg_check == 'reg'){
                    $dmg_col = 'clients.region_id';
                }
                if($dmg_check == 'zone'){
                    $dmg_col = 'clients.zone_id';
                }
                if($dmg_check == 'branch'){
                    $dmg_col = 'clients.branch_id';
                }
            }
        }
        else
        {
            $departments = array(Session::get('dep_id'));

            //Here filter out jurisdiction
            $jurisdiction = \Session::get('jurisdiction');
            if($jurisdiction){
              $departments = $jurisdiction->departments;

              if($jurisdiction->departments!=""){
                $departments = explode($jurisdiction->departments,',');
              }
            }

        }
        $devices_sts = DB::table('Devices')->leftJoin('clients', 'Devices.department_id', '=', 'clients.id');
        if(count($departments)>0){
            $devices_sts = $devices_sts->whereIn('department_id',$departments);
        }
        if( $dmg_col!='' &&  $dmg_id!='')
        {
            $devices_sts = $devices_sts->where($dmg_col,$dmg_id);
        }


        $devices_sts = $devices_sts->select('Devices.status', DB::raw('count(*) as total'))
                                        ->groupBy('Devices.status')
                                        ->get();

        $statuses = ['Online'=>['Total'=>0,'devices'=>[]],'Offline'=>['Total'=>0,'devices'=>[]],'Inactive'=>['Total'=>0,'devices'=>[]]];

        foreach($devices_sts as $devices_st)
        {
            $dev = Device::leftJoin('clients', 'Devices.department_id', '=', 'clients.id')->where('Devices.status',$devices_st->status);

            if(count($departments)>0){
                $dev = $dev->whereIn('department_id',$departments);
            }
            if($dmg_col!='' && $dmg_id!='')
            {
                $dev = $dev->where($dmg_col,$dmg_id);
            }
            $statuses[$devices_st->status] = ['Total'=>$devices_st->total,'devices'=>$dev->select('devices.*')->get()];
        }

        //dd($statuses);

        $page_title = "Devices";
        $show_tab = 'Online';
        if($request->show_tab && $request->show_tab!='')
            $show_tab = $request->show_tab;

        return view('Devices.index',compact('statuses','page_title','show_tab'));
    }


    public function inbox_bc(){

        $client = new \GuzzleHttp\Client();
        if(Session::get('role_id') == 1){
            $department_id = \Request::get('department_id');
            $devices = Device::pluck('serial_number');
            if($department_id!=""){
                $sub_depts = Client::getSubDepartments($department_id);
                $devices = Device::whereIn('department_id',$sub_depts)->pluck('serial_number');
            }
        }
        else
        {
            $devices=Device::where('department_id',Session::get('dep_id'))->pluck('serial_number');

            //Here filter out jurisdiction
            $jurisdiction = \Session::get('jurisdiction');
            if($jurisdiction){
              $departments = $jurisdiction->departments;

              if($departments!=""){
                $devices = Device::whereIn('department_id',explode($departments,','))->pluck('serial_number');
              }

            }

        }

        $devices = $devices->toJson();

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
            $onlineDevices[$onlinedevice['serialno']]=$onlinedevice;
        }

        $offlineDevices=[];
        foreach ($responseJSON['offline'] as  $offlinedevice) {
            $offlineDevices[$offlinedevice['serialno']]=$offlinedevice;
        }


        //dd($offlineDevices);
        //$devices_stats = collect(array_merge($onlineDevices,$offlineDevices));

        //$devices_activity = [];
        //foreach($devices_stats as $ds){
        //    $devices_activity[$ds['serialno']] = $ds['lastactivity'];
        //}


        if(Session::get('role_id') == 1){

            $department_id = \Request::get('department_id');
            $devices = Device::all();
            if($department_id!=""){
                $sub_depts = Client::getSubDepartments($department_id);
                $devices = Device::whereIn('department_id',$sub_depts)->get();
            }

        }
        else
        {
            $devices=Device::where('department_id',Session::get('dep_id'))->get();
        }

        $page_title = "Devices";
      return view('Devices.index',compact('devices','page_title','onlineDevices','offlineDevices'));
    }

    public function create()
    {
        $id=Session::get('dep_id');



        $cdeps = Cache::remember('cdeps', now()->addMinutes(30), function()
        {
            $role_id = Session::get('role_id');
            $departments=[];
            if($role_id == 1){
                $departments=Client::generatetree();
            }
            else
            {
                $departments=Client::generatetree(Session::get('dep_id'),true,false);
            }
            return $departments;
        });

        //dd($cdeps);
       $page_title = "Add New Device";
        return view('Devices.create',compact('cdeps','page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

         $validatior=Validator::make($request->all(),[
        'title'=>'required|max:255',
        'serial_number'=>'required'
    ]);
        if($validatior->fails()){
        return redirect('devices/create')->withInput()->withErrors($validatior);
       }
        $device=new Device();
        $device->title=$request['title'];
        $device->remarks=$request['remarks'];
        $department_id = Session::get('dep_id');
        if($request->has('department_id')){
            $device->department_id = $request->department_id;
        }
        $device->serial_number = $request['serial_number'];

        //other departments:
        $other_deps = $request->input('other_deps', []);

        if ($request->get('action') == 'save') {
         if($device->save()){
             if($other_deps) {
                 foreach ($other_deps as $depId) {
                     DeviceDepartment::create([
                         'device_id' => $device->id,
                         'dep_id' => $depId,
                     ]);
                 }
             }
            Alert::success('Record Submit Successfully !Thank You');
            return redirect('devices');
                           }
         }
         elseif ($request->get('action') == 'save_and_close') {
            if($device->save()){
            Device::create($request->all());
            return redirect('devices/create')->with('msg','Record Submit Successfully ! Thank You');
                    }

         }
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $device=Device::where('id',$id)->get()->first();
        return view('Devices.show',compact('device'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $device=Device::where('id',$id)->first();

        $cdeps = Cache::remember('cdeps', now()->addMinutes(30), function()
        {
            $role_id = Session::get('role_id');
            $departments=[];
            if($role_id == 1){
                $departments=Client::generatetree();
            }
            else
            {
                $departments=Client::generatetree(Session::get('dep_id'),true,false);
            }

            return $departments;
        });
        $other_deps = DeviceDepartment::where('device_id', $id)->get();
        $other_deps = $other_deps->pluck('dep_id')->toArray();

        $page_title = "Update Device";
        return view('Devices.edit',compact('device','cdeps','page_title', 'other_deps'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $device=Device::find($id);
        $device->title=$request['title'];
        $device->remarks=$request['remarks'];
        $device->department_id=$request['department_id'];
        $device->serial_number=$request['serial_number'];

        //other departments:
        $departmentsToAdd = $request->input('other_deps', []);

        if($device->save()){
            if($departmentsToAdd){
                DeviceDepartment::where('device_id', $device->id)->delete();
                $departmentsToAdd = $request->input('other_deps', []);
                foreach ($departmentsToAdd as $depId) {
                    DeviceDepartment::create([
                        'device_id' => $device->id,
                        'dep_id' => $depId,
                    ]);
                }
            }
            Alert::success('Record Update Successfully !Thank You');
            return redirect('devices');
        }



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $device=Device::find($id);
        if($device->delete()){
            Alert::success('Record Delete Successfully !Thank You');
        return redirect('devices');
    }
    }
}
