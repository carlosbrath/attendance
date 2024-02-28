<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Deduction;
use Session;
use Alert;

class DeductionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deduction=Deduction::all();
        return view('deductions.index',compact('deduction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
       return view('deductions.create');
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
        $titlechecking = Deduction::where('title',$request->title)->where('department_id',$dep_id)->first();
        if($titlechecking){
         return redirect('deductions/create')->with('message','Same Title Exist Please Submit Different');
                 }
    else{
           
            if ($request->get('action') == 'save') { 
                  $deduction = new Deduction();
                  $deduction->title=$request->title;
                  $deduction->department_id=Session::get('dep_id');
                  if($deduction->save()){
                  Alert::success('Record Submit Successfully ! Thank You');
                  return redirect('deductions');         
                    }
              }
            elseif ($request->get('action') == 'save_and_close') {
                    $deduction = new Deduction();   
                    $deduction->title=$request->title;
                    $deduction->department_id=Session::get('dep_id');
                    if($deduction->save()){
                    return redirect('deductions/create')->with('msg','Record Submit Successfully ! Thank you');         
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
        $deduction=Deduction::find($id);
        return view('deductions.edit',compact('deduction'));
       
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
        $deduction=Deduction::find($id);
        $deduction->title=$request->title; 
        if($deduction->save()){
        Alert::success('Record Update Successfully ! Thank You');
        return redirect('deductions');
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
        $deduction=Deduction::find($id);
        $deduction->delete();
        return redirect('deductions')->with('error','Record Deleted Successfully');
        
    }
}
