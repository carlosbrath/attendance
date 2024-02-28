<?php

namespace App\Http\Controllers;
use App\Branch;
use App\Zone;
use Alert;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class BranchsController extends Controller
{
   
    public function index()
    {
        $dep_id=Session::get('dep_id');
        $branch=Branch::all();
        $page_title = "Tehsils";  
        return view('Branchs.index',compact('branch','page_title'));
    }
    public function create()
    {
        $dep_id=Session::get('dep_id');
        $zone = Zone::pluck('zone_name', 'zone_id'); 
        $page_title = "Add New Tehsil";  
        return view('Branchs.create',compact('zone','page_title'));
    
    }

    public function store(Request $request)
    {
        
        $validatior=Validator::make($request->all(),[
            'branch_name'=>'required|max:255',
            'zone_name'=>'required|not_in:-1',
             'address1'=>'required'            
        ]);
         if($validatior->fails()){
        return redirect('branchs/create')->withInput()->withErrors($validatior);
       }
        $branch=new Branch();
        $branch->branch_name=$request['branch_name'];
        $branch->branch_desc=$request['branch_desc'];
        $branch->address_line_1=$request['address1'];
        $branch->zone_id=$request['zone_name'];
        $branch->address_line_2=$request['address2'];
        $branch->branch_status=1;
        $branch->dep_id=Session::get('dep_id');
        if ($request->get('action') == 'save') { 
        if($branch->save()){
            Alert::success('Record submit successfully ! Thank You');
            return redirect('branchs');

        }
      } 
      elseif ($request->get('action') == 'save_and_close') {
         if($branch->save()){
            Alert::success('Record submit successfully ! Thank You');
            return redirect('branchs/create')->with('msg','Record Submit successfully ! Thank You');

        }
      }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($branch_id)
    {
        $branch=Branch::where('branch_id',$branch_id)->first();
        return view('Branchs.show',compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($branch_id)
    {
        $zone = Zone::pluck('zone_name', 'zone_id'); 
        $branch=Branch::find($branch_id);
        $page_title = "Update Tehsil";  
        return view('Branchs.edit',compact('branch','zone','page_title'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$branch_id)
    {
        $branch=Branch::find($branch_id);
        $branch->branch_name=$request['branch_name'];
        $branch->branch_desc=$request['branch_desc'];
        $branch->address_line_1=$request['address1'];
        $branch->zone_id=$request['zone_name'];
        $branch->address_line_2=$request['address2'];
        $branch->branch_status=1;
        if($branch->save()){
            Alert::success('Record update successfully ! Thank You');
            return redirect('branchs');
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($branch_id)
    {
        $branch= Branch::find($branch_id);
        $branch->delete();
        return redirect('branchs')->with('message','Record deleted successfully');
    }
}
