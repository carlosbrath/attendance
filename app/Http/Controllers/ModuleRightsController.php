<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ModuleRight;
use App\Module;
use App\Role;


class ModuleRightsController extends Controller
{
  
  public function index(){
   $moduleright= ModuleRight::has('role')->has('module')->paginate(10);
   return view('ModuleRights.index',compact('moduleright'));
 }

 public function create(){
   $module=Module::all();
   $role=Role::all();
   return view('ModuleRights.create',compact('module','role'));
 }

 public function store(Request $request){
   $this->validate($request, ['role_id' => 'required','module_id' => 'required',]);
   $moduleright = new ModuleRight();
   $moduleright->role_id=$request->role_id; 
   $moduleright->module_id=$request->module_id;
   $moduleright->save();
   return redirect('modulerights')->with('message','Record inserted Successfully');
 }

 public function show($id){
   $moduleright=ModuleRight::find($id);
   $role=Role::find($moduleright->role_id);
   $module=Module::find($moduleright->module_id);
   return view('ModuleRights.show',compact('moduleright','role','module'));
 }

 public function edit($id){
   $module=Module::all();
   $role=Role::all();
   $moduleright=ModuleRight::find($id);
   return view('ModuleRights.edit',compact('moduleright','module','role'));
 }

 public function view($id){
  $moduleright=ModuleRight::find($id);
  $role=Role::find($moduleright->role_id);
  $module=Module::find($moduleright->module_id);
  return view('ModuleRights.view',compact('moduleright','role','module'));
}

public function update(Request $request, $id){
  $moduleright=ModuleRight::find($id);
  $moduleright->role_id=$request->role_id; 
  $moduleright->module_id=$request->module_id;
  $moduleright->save();
  return redirect('modulerights')->with('message','Record updated Successfully');
}

public function destroy($id){
  $moduleright=ModuleRight::find($id);
  $moduleright->delete();
  return redirect('modulerights')->with('message','Record deleted Successfully');
}

}
