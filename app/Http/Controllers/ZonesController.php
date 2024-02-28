<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Zone;
use App\Region;
use Validator;
use Alert;
use Session;

class ZonesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dep_id=Session::get('dep_id');
        $zone=Zone::all();
        $page_title = "Districts";
        return view('Zones.index',compact('zone','page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dep_id=Session::get('dep_id');
        $region = Region::pluck('region_name', 'region_id');
        $page_title = "Add New District";
        return view('Zones.create',compact('region','page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $validatior=Validator::make($request->all(),
                                        [
                                        'zone_name'=>'required|max:255',
                                        'region_name'=>'required|not_in:-1',
                                         'address1'=>'required',
                                        ]);
       if($validatior->fails()){
        return redirect('zones/create')->withInput()->withErrors($validatior);
       }
        $zone = new Zone();
        $zone->zone_name=$request['zone_name'];
        $zone->zone_desc=$request['zone_desc'];
        $zone->zones_dep_id=Session::get('dep_id');
        $zone->region_id=$request['region_name'];
        $zone->address_line_1=$request['address1'];
        $zone->address_line_2=$request['address2'];
        $zone->zone_status=1;
        if ($request->get('action') == 'save') {
        if($zone->save()){
            Alert::success('Record Submit successfully ! Thank You');
             return redirect('zones');
         }
        }
        elseif ($request->get('action') == 'save_and_close') {
             return redirect('zones/create')->with('msg','Record Submit Successfully ! Thank You');
        }


    }
     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($zone_id)
    {
        $zone=Zone::where('zone_id',$zone_id)->first();
        return view('Zones.show',compact('zone'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($zone_id)
    {
        $zone=Zone::find($zone_id);
        $dep_id=Session::get('dep_id');
        $region = Region::pluck('region_name', 'region_id');

        //$region = Region::where('dep_id',$dep_id)->pluck('region_name', 'region_id');
        $page_title = "Update District";
        return view('Zones.edit',compact('zone','region','page_title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $zone_id)
    {
        $zone=Zone::find($zone_id);
        $zone->zone_name=$request['zone_name'];
        $zone->zone_desc=$request['zone_desc'];
        $zone->region_id=$request['region_name'];
        $zone->address_line_1=$request['address1'];
        $zone->address_line_2=$request['address2'];
        $zone->zone_status=1;
        if($zone->save()){
            Alert::success('Record Update successfully ! Thank You');
             return redirect('zones');
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($zone_id)
    {
         $zone=Zone::find($zone_id);
        $zone->delete();
        return redirect('zones')->with('message','Record deleted Successfully');
    }
}
