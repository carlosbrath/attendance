<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\User;
use App\ModuleRight;
use App\Module;
use App\AssignRight;
use App\RoleJurisdiction;
use DB;
use Alert;
class JurisdictionsController extends Controller
{
   
 public function index(){
   $records =RoleJurisdiction::paginate(10);
   return view('jusrisdictions.index',compact('records'));
 }

 public function create(){
   return view('jusrisdictions.create');
 }

 public function store(Request $request){
   $this->validate($request, [
    'title' => 'required'
  ]);
   $role = new Role();
   if($request->get('action') == 'save_and_close'){
       $role->title=$request->title; 
       $role->description=$request->description;
       $role->save();
       Alert::success('Record Inserted Successfully ! Thanks');
       return redirect('jusrisdictions/create')->with('message','Record inserted Successfully');
   }
   elseif ($request->get('action') == 'save') {
      $role->title=$request->title; 
       $role->description=$request->description;
       $role->save();
       Alert::success('Record Inserted Successfully ! Thanks');
      return redirect('jusrisdictions')->with('message','Record inserted Successfully');
      
   }
 }

 public function show($id){
   $role=Role::find($id);
   return view('Roles.show',compact('role'));
 }

 public function edit($id){
   $role=Role::find($id);
   return view('Roles.edit',compact('role'));
 }

 public function update(Request $request, $id){

   $role=Role::find($id);
   $role->title= $request->title;
   $role->description= $request->description;
   $role->save();
   return redirect('roles')->with('message','Record updated Successfully');
 }
 public function destroy($id){
   $role=Role::find($id);
   if(User::where('role_id',$id)->count()){
      return redirect('roles')->with('error','Role id is already used in User');
   }
   if(AssignRight::where('role_id',$id)->count()){
      return redirect('roles')->with('error','Role id is already used in AssignRight');
   }
   $role->delete();
   return redirect('roles')->with('message','Record Deleted Successfully');
 }
 
 public function assign_right($id){
    $check_module =AssignRight::get();
    $moduleright = Module::with(['rights'=>function($q) use($id){
    return $q->where('role_id',$id);}])->get();
    $parent_modules = $moduleright->where('parent_id',0);
    return view('Roles.assign_right',compact('moduleright','parent_modules','check_module'));
 }
}
