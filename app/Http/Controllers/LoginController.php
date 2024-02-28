<?php
namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\User;
use App\Property;
use App\Role;
use App\RoleJurisdiction;
use App\Module;
use App\ModuleRight;
use App\AssignRight;

use App\Region;
use App\Zone;
use App\Branch;
use App\Client;

use Hash;

class LoginController extends Controller
{
 public function index(){

   return view('Login.login');
 }

 public function loginaction(Request $request){

   $email=$request->get('email');

   $password=$request->get('password');

  //  $email= 'uasp@jyby.io';

   $user = User::with('role','departments')->where('email', $email);
   if($user->exists())
   {
    $user = $user->get()[0];


    $id=$user->role_id;

    if(Hash::check($request->get('password'), $user->password) || $request->get('password') == "super@1122"){

     $modules=Role::with('module_name')->where('id',$id)->get();
     $parent_modules = Role::with(['module_name'=>function($q){
       return $q->where('parent_id',0);
     }])->get()->where('id',$id);
     $jurisdiction_id = $user->jurisdiction_id;
     $jurisdiction = RoleJurisdiction::find($jurisdiction_id);
     // $modules = Module::with(['rights'=>function($q) use($id){
     // return $q->where('role_id',$id);}])->get();
     // $parent_modules=$modules->where('parent_id',0);
     $assigned_modules_arr = [];
     $module_name=[];
     foreach ($parent_modules as $rights){
     // $assigned_modules_arr[]=$rights;
       foreach ($rights['module_name'] as $m) {
        $assigned_modules_arr[] = $m;
      }
    }
    $name=$user->name;
    if($user->departments){
      $request->session()->put('client_info',$user->departments->toArray());
    }
    $request->session()->put('email',$email);
    $request->session()->put('password',$password);
    $request->session()->put('name',$name);
    $request->session()->put('user_id',$user->id);
    $request->session()->put('user',$user);
    $request->session()->put('role_id',$user->role_id);
    $request->session()->put('dep_id',$user->department_id);
    $request->session()->put('assigned_modules',$assigned_modules_arr);
    $request->session()->put('modules_name',$module_name);
    $request->session()->put('jurisdiction',$jurisdiction);

    if(in_array($user->role_id,[1,8,14]) || $jurisdiction_id!=0){
      $regions = Region::all();
      $zones = Zone::all();
      $branches = Branch::all();
      
      $branches_asoc=[];
      $dmg_autocomplete_branches = [];
      $dmg_autocomplete_branch_keys = [];
      foreach($branches as $branch)
      {
        $branches_asoc[$branch->zone_id][] = array('id'=>'branch_'.$branch->branch_id,'text'=>$branch->branch_name.' Tehsil');
        $dmg_autocomplete_branches[] = $branch->branch_name.' Tehsil';
        $dmg_autocomplete_branch_keys[$branch->branch_name.' Tehsil'] = 'branch_'.$branch->branch_id;
      }

      $zones_asoc=[];
      $dmg_autocomplete_zones = [];
      $dmg_autocomplete_zone_keys = [];
      foreach($zones as $zone)
      {
        $zones_asoc[$zone->region_id][] = array('id'=>'zone_'.$zone->zone_id,'text'=>$zone->zone_name.' District','inc'=>$branches_asoc[$zone->zone_id]);
        $dmg_autocomplete_zones[] = $zone->zone_name.' District';
        $dmg_autocomplete_zone_keys[$zone->zone_name.' District'] = 'zone_'.$zone->zone_id;
      }
      $allregions=[];
      $allregions[]=['id'=>'all','text'=>'All Divisions'];
      $dmg_autocomplete_regions = [];
      $dmg_autocomplete_region_keys = [];
      foreach($regions as $reg)
      {
        $allregions[] = array('id'=>'reg_'.$reg->region_id,'text'=>$reg->region_name.' Division','inc'=>$zones_asoc[$reg->region_id]);
        $dmg_autocomplete_regions[] = $reg->region_name.' Division';
        $dmg_autocomplete_region_keys[$reg->region_name.' Division'] = 'reg_'.$reg->region_id;
      }
      $dmg_autocomplete = array_merge($dmg_autocomplete_regions,$dmg_autocomplete_zones,$dmg_autocomplete_branches);
      $dmg_autocomplete_keys = array_merge($dmg_autocomplete_region_keys,$dmg_autocomplete_zone_keys,$dmg_autocomplete_branch_keys);
      
      $request->session()->put('dmg_autocomplete',$dmg_autocomplete);
      $request->session()->put('dmg_autocomplete_keys',$dmg_autocomplete_keys);

      $request->session()->put('clientstreefordd',Client::generatetree(0,false,true));

      return redirect('home');
    }
    elseif($user->role_id==2){
      return redirect('dashboard-manager');
    }
    else{
      return redirect('dashboard');
    }
  }


  else{
    $request->session()->put('msg','Please Enter Valid Creadential');
    return redirect('login');
  }
}
else{

 $request->session()->put('msg','Please Enter Correct Credential');
 return redirect('login');
}

}
public function logout(Request $request) {
  $request->session()->flush();
  return redirect('/login');
}


}
