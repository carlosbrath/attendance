<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\AssignRight;
use App\Module;

class AssignRightsController extends Controller
{
	
 public function add_assign_right(Request $request){

  $assign=[];
  $modules=[]; 
  $module_id=[];
    
   foreach ($request->modules as $module) 
       {
                      
              
          $assign = AssignRight::where('module_id',$module)->where('role_id',$request->roles)->count();
          if($assign > 0)
               {
                  
                 
                 
                 $count_module=Module::Select('id')->where('parent_id',$module)->count();
                 AssignRight::where('module_id',$module)->where('role_id',$request->roles)->delete();
                 if($count_module > 0){
                 $module_id=Module::Select('id')->where('parent_id',$module)->get();
                 foreach ($module_id as $m){
                 AssignRight::where('module_id',$m->id)->where('role_id',$request->roles)->delete();
                                          }
                                      }
               }
          else
               {



                 if($module==0){
                     unset($module);
                              }
                else{

                  $modules[]=$module;    
                  $assignright = new AssignRight();
                  $assignright->role_id=$request->roles;
                  $assignright->module_id=$module;
                  $assignright->save();

                  
                  }

               }   
          
         
   
          //save code on desktop
        }
       
      
          
        return redirect('roles')->with('message','Roles is Assign Successfully');
       
      

         
      }

    




  
   }

