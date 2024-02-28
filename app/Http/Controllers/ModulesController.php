<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Module;
use App\AssignRight;
use Validator;
use Alert;
class ModulesController extends Controller
{
    public function index(){
     $module=Module::all();
     return view('Modules.index',compact('module'));
 }
   public function create(){
       $module = Module::where('parent_id',0)->select('name', 'id')->get();
       return view('Modules.create',compact('module'));
   }
   public function store(Request $request){
        $validatior=Validator::make($request->all(),
            [
              'name'=>'required|max:255','route'=>'required'
            ]
               )->setAttributeNames(
                 ['parent_id' => 'Parent','route'=>'Route']
               );
        if($validatior->fails()){
        return redirect('modules/create')->withInput()->withErrors($validatior);
       }

     // $this->validate($request, 
     //     [ 'parent_id'=>'required' , 'name' => 'required','route' => 'required',]);
     $module = new Module();
     $module->name=$request->name; 
     $module->route=$request->route;

     if($request->parent_id==''){
    
     $module->parent_id=0;
     $module->icon_class=$request->module_class;      
    
    }
     else{

         $module->parent_id=$request->parent_id;
      }
      Alert::success('Record Inserted Successfully ! Thanks');
       $module->save();
     return redirect('modules')->with('message','Record inserted Successfully');
 }

 public function show($id){
     $module=Module::find($id);
     return view('Modules.show',compact('module'));
 }

 public function edit($id){
     $module_name = Module::where('parent_id',0)->select('name', 'id')->get();
     $module=Module::find($id);
     return view('Modules.edit',compact('module','module_name'));
 }

 public function update(Request $request, $id){
     $this->validate($request, 
         [ 'name' => 'required','route' => 'required']);
     $module=Module::find($id);
     $module->name=$request->name; 
     $module->route=$request->route;
     $module->icon_class=$request->module_class;
     $module->save();
     return redirect('modules')->with('message','Record updated Successfully');
 }

 public function destroy($id){
   $module=Module::find($id);
   if(AssignRight::where('module_id',$id)->count()){
    return redirect('modules')->with('error','Module id already used in AssignRight');
    }
   $module->delete();
   return redirect('modules')->with('message','Record deleted Successfully');
}

}
