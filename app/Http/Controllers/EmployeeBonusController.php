<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmployeeBonus;
use App\Client;
use Session;
use Alert;

class EmployeeBonusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

      public function fetch_user_for_dep(Request $request){
       
         $client=Client::where('parent_id',$request->id)->first();
               if($client==null){ 
                     $user=client::with(['users' => function ($query) {
                         $query->where('status',1)->where('flag',1); 
                         }])->where('id',$request->id)->get(); 
                          $arr = [];
                          foreach ($user as $u){
                          foreach ($u['users'] as $m) {
                           $arr[] = $m;
                           }  
                       }
                     
               }
               else{

                   $user=client::with(['users' => function ($query) {
                         $query->where('status',1)->where('flag',1); 
                         }])->where('parent_id',$request->id)->get(); 
                          $arr = [];
                          foreach ($user as $u){
                          foreach ($u['users'] as $m) {
                           $arr[] = $m;
                           }  
                       }
                   
               }

                return json_encode(['employee' => $arr]);   
    }



    public function index()
    {
        $data['bonuses']=EmployeeBonus::with(['users'])->get();
        $data['page_title']=' Employees Bonuses/Dedications'; 
        return view('EmployeesBonuses.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

      $data['department']=Client::where('parent_id',Session::get('dep_id'))->get();
      $data['page_title']='Add New Employee Bonus/Dedication'; 
      return view('EmployeesBonuses.create',$data);       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {  

     
            $this->validate($request, [
            'user_id' => 'required',
            'department_id' => 'required',
             'amount' => 'required',
             'date'=>'required',
              'remarks'=>'required'
           ]);     
          $input=array();
          if($request->bonus_deduction_status==1){
              $input['status'] = 1;
             
           }else{
              
               $input['status'] = 2;
               
            }
           $input['user_id'] = $request->user_id; 
           $input['remarks'] = $request->remarks;
           $input['amount'] = $request->amount;
           $input['date'] = date('Y-m-d',strtotime($request->date));
           $employee_bonus= EmployeeBonus::create($input);
           if($employee_bonus){
               Alert::success('Record Submit Successfully ! Thank You');
               return redirect('employees_bonuses');
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
         $employee_bonus=EmployeeBonus::find($id);
         $is_delete=$employee_bonus->delete();
          if($is_delete){
               Alert::success('Record Submit Successfully ! Thank You');
               return redirect('employees_bonuses');
           } 
    }
}
