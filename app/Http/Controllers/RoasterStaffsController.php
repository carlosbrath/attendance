<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RoasterStaff;
use App\TimeCategory;
use App\User;

class RoasterStaffsController extends Controller
{
  public function index(){
   $roasterstaff=RoasterStaff::has('user')->has('tcat')->paginate(10);
   return view('RoasterStaff.index',compact('roasterstaff'));
 }

 public function create(){
   $user=User::all();
   $timecategory=TimeCategory::all();
   return view('RoasterStaff.create',compact('user','timecategory'));
 }

 public function store(Request $request){
   $roasterstaff=new RoasterStaff();
   $roasterstaff->user_id=$request->user_id;
   $roasterstaff->tcat_id=$request->time_cat;
   $roasterstaff->month=date("m", strtotime($request->month));
   $roasterstaff->save();
   return redirect('roastersstaff')->with('message','Record inserted Successfully');
 }

 public function show($id){
  $roasterstaff=RoasterStaff::find($id);
  $user=User::find($roasterstaff->user_id);
  $timecategory=TimeCategory::find($roasterstaff->tcat_id);
  return view('RoasterStaff.show',compact('roasterstaff','user','timecategory'));
}

public function edit($id){  
  $user=User::all();
  $timecategory=TimeCategory::all();
  $roastersstaff=RoasterStaff::find($id);
  return view('RoasterStaff.edit',compact('roastersstaff','timecategory','user'));
}

public function update(Request $request, $id){
  $roastersstaff=RoasterStaff::find($id);
  $roastersstaff->user_id=$request->user;
  $roastersstaff->tcat_id=$request->time_cat;
  $roastersstaff->month=date("m", strtotime($request->month));
  $roastersstaff->save();
  return redirect('roastersstaff')->with('message','Record updated Successfully');
}

public function destroy($id){
  $roasterstaff=RoasterStaff::find($id);
  $roasterstaff->delete();
  return redirect('roastersstaff')->with('message','Record deleted Successfully');
}
}
