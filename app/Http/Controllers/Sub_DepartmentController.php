<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Client;
use App\User;
use App\Designation;
use Alert;
use Session;

class Sub_DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('SubDepartment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

         $titlechecking = Client::where('name',$request->name)->first();
        
        if($titlechecking){
            return redirect('SubDepartment/create')->with('message_for_supdepartment','Same Name Exist Please Enter Different');
          }
    else{

        $client = new Client();
        $client->name=$request->title; 
        $client->parent_id=Session::get('dep_id');
        $client->email=$request->email;
        $client->contact=$request->contact;
        $client->status=1;
        $file = $request->file('logo');
        $extension = $request->logo->extension(); 
        $filename =   time() . '.' . $file->getClientOriginalExtension();
        $destination_path=public_path('/photos');
        $file->move($destination_path,$filename);

        // $path = $file->storeAs('photos', $filename);
        $client->logo=$filename;
        $client->save();
        Alert::success('Record Inserted Successfully ! Thanks');
        return redirect('SubDepartment');

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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
