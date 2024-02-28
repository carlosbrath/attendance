<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\User;
use App\ModuleRight;
use App\Module;
use App\AssignRight;
use App\Client;
use App\Region;
use App\Zone;
use App\Branch;
use Illuminate\Support\Facades\Cache;
use App\RoleJurisdiction;
use DB;
use Alert;
class JurisdictionsController extends Controller
{
   
 public function index(){
   $records =RoleJurisdiction::paginate(10);
   $page_title = "Jurisdictions Managment";
   return view('jusrisdictions.index',compact('records','page_title'));
 }

 public function create(){
    
    $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
     {
         return Client::generatetree(0,false,true);
     });
    $data['regions'] = Region::pluck('region_name','region_id');

   return view('jusrisdictions.create',compact('data'));
 }

 public function store(Request $request){

   $this->validate($request, [
    'title' => 'required'
   ]);
   $rj = new RoleJurisdiction();
   $rj->title=$request->title; 
   $sub_departments = '';
   $parent_departments = '';
   if($request->department_id!="all"){
    $parent_departments = implode(',',$request->department_id);
    foreach($request->department_id as $department){
     $sub_departments .= implode(',',Client::getSubDepartments($department)).",";
    }
    $sub_departments = rtrim($sub_departments, ",");
   }
   $regions = '';
   if($request->regions!="all"){
     $regions = $request->regions;
   }
   $zones = '';
   if($request->zones!="all" && $request->zones!="Please Select"){
     $zones = $request->zones;
   }
   $branches = '';

   if($request->branches!="all" && $request->branches!="Please Select"){
     $branches = $request->branches;
   }
   $rj->departments = $sub_departments;
   $rj->regions = $regions;
   $rj->parent_departments = $parent_departments;
   $rj->zones = $zones;
   $rj->branches = $branches;
   $rj->save();
   Alert::success('Record Inserted Successfully ! Thanks');
   return redirect('jurs')->with('message','Record inserted Successfully');
 }

 public function show($id){
   $role=Role::find($id);
   return view('Roles.show',compact('role'));
 }

 public function edit($id){
  
    $data['all_departments'] =  Client::generatetree(0,false,true);
    $data['regions'] = Region::pluck('region_name','region_id');
    $item = RoleJurisdiction::find($id);
    $data['item'] = $item;

    $data['zones'] = Zone::where('region_id',$item->regions)->pluck('zone_name','zone_id')->toArray();
    $data['branches'] = Branch::where('zone_id',$item->zones)->pluck('branch_name','branch_id')->toArray();

   return view('jusrisdictions.edit',compact('data'));
 }

 public function update(Request $request, $id){

   $rj = RoleJurisdiction::find($id);

   $rj->title=$request->title; 
   $sub_departments = '';
   $parent_departments = '';
   if($request->department_id!="all"){
    $parent_departments = implode(',',$request->department_id);
    foreach($request->department_id as $department){
     $sub_departments .= implode(',',Client::getSubDepartments($department)).",";
    }
    $sub_departments = rtrim($sub_departments, ",");
   }
   $regions = '';
   if($request->regions!="all"){
     $regions = $request->regions;
   }
   $zones = '';
   if($request->zones!="all" && $request->zones!="Please Select"){
     $zones = $request->zones;
   }
   $branches = '';

   if($request->branches!="all" && $request->branches!="Please Select"){
     $branches = $request->branches;
   }
   $rj->departments = $sub_departments;
   $rj->regions = $regions;
   $rj->parent_departments = $parent_departments;
   $rj->zones = $zones;
   $rj->branches = $branches;
   $rj->save();
   return redirect('jurs')->with('message','Record updated Successfully');
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
