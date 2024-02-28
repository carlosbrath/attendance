<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Allowance;
use Session;
use Alert;

class AllowancesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allowance=Allowance::all();
        return view('allowances.index',compact('allowance'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('allowances.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required'
            ]);
       
    
        $dep_id=Session::get('dep_id');
        $this->validate($request, ['title' => 'required']); 
        $titlechecking = Allowance::where('title',$request->title)->where('department_id',$dep_id)->first();
        if($titlechecking){
         return redirect('allowances/create')->with('message','Same Title Exist Please Submit Different');
                 }
    else{
           
            if ($request->get('action') == 'save') { 
                  $allowance = new Allowance();
                  $allowance->title=$request->title;
                  $allowance->department_id=Session::get('dep_id');
                  if($allowance->save()){
                  Alert::success('Record Submit Successfully ! Thank You');
                  return redirect('allowances');         
                    }
              }
            elseif ($request->get('action') == 'save_and_close') {
                    $allowance = new Allowance();   
                    $allowance->title=$request->title;
                    $allowance->department_id=Session::get('dep_id');
                    if($allowance->save()){
                    return redirect('allowances/create')->with('msg','Record Submit Successfully ! Thank you');         
                 }
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
      
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $allownace=Allowance::find($id);
        return view('allowances.edit',compact('allownace'));
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
        $allowance=Allowance::find($id);
        $allowance->title=$request->title; 
        if($allowance->save()){
            Alert::success('Record Update Successfully ! Thank You');
              return redirect('allowances');
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
        $allowance=Allowance::find($id);
       
         $allowance->delete();
         return redirect('allowances')->with('error','Record Deleted Successfully');
    }
}
