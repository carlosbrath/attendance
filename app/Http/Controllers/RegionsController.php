<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Region;
use Alert;
use Validator;
use Session;
class RegionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {     
          $dep_id=Session::get('dep_id');
          
          $regions=Region::paginate(10);
          $page_title = "Regions";
          return view('Regions.index',compact('regions','page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = "Add New Region";   
        return view('Regions.create',compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

          $this->validate($request, 
                [

                 'region_name' => 'required',
                 'address1' => 'required'
                 
                 ]);

                    $region= new Region();
                    $region->region_name=$request["region_name"];
                    $region->dep_id=278;
                    $region->region_desc=$request["region_desc"];
                    $region->address_line_1=$request["address1"];
                    $region->address_line_2=$request["address2"];
                    $region->region_status=1;
                if ($request->get('action') == 'save') { 
                    if($region->save()){

                         Alert::success('Record Submit Successfully ! Thank You');
                        return redirect('regions');

                    }
                }
                    elseif($request->get('action') == 'save_and_close'){
                     
                        if($region->save()){
                         
                        return redirect('regions/create')->with('msg','Record Submit Successfully ! Thank you');

                     }
                }
        }
       
       
 


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($region_id)
    {   
       
        
        $region=Region::where('region_id',$region_id)->first();
        return view('Regions.show',compact('region'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($region_id)
    {
        $region=Region::where('region_id',$region_id)->first();
        $page_title = "Update Region";   
        return view('Regions.edit',compact('region','page_title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $region_id)
    {
        
               $region=Region::where('region_id',$region_id)->first();
               $region->region_name=$request->region_name;
               $region->region_desc=$request->region_desc;
               $region->address_line_1=$request->address_line_1;
               $region->address_line_2=$request->address_line_2;
               $region->region_status=1;
               $region->save();
               if($region->save()){
               Alert::success('Record Update Successfully ! Thank You');
               return redirect('regions');
                                }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($region_id)
    {
         $region=Region::find($region_id);
         $region->delete();
         return redirect('regions');
    }
}
