<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmployeeDedicatedOffDay;
use App\Client;
use App\User;
use Session;
use Alert;

class EmployeeDedicatedDayOffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dep_id = Session::get('dep_id');
        $all_sub_deps = Client::where('parent_id', $dep_id)->pluck('id');
        //$data['ded_day_off']=EmployeeDedicatedOffDay::with(['users'])->where('	department_id', $dep_id)->get();

        // Assuming you have the relationship in the EmployeeDedicatedOffDay model
        $data['ded_day_off'] = EmployeeDedicatedOffDay::with(['users'])
            ->whereHas('users', function ($query) use ($all_sub_deps) {
                $query->whereIn('department_id', $all_sub_deps);
            })
            ->get();

        $data['page_title']='Employees Dedicated Day Off';
        return view('EmployeeDedicatedDayOff.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $data['department']=Client::where('parent_id',Session::get('dep_id'))->get();
        $data['page_title']='Add New Employee Dedicated Day Off';
        $data['users']=[];
        $data['department_id'] = '';
        $data['date'] ='';
        return view('EmployeeDedicatedDayOff.create',$data);
    }

    public function employeesDedicatedDayOff(Request $request)
    {
        foreach($request->emp_dep as $r){
            $input=array();
            $input['user_id'] = $r;
            $input['off_day_date'] = $request->ded_off_day_date;


            $employee_dedicated_dayoff= EmployeeDedicatedOffDay::create($input);
        }
        if(count($request->emp_dep)>0){
            Alert::success('Record Submit Successfully ! Thank You');
            return redirect('day_off');
        }
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

            'department_id' => 'required',
            'date'=>'required'

           ]);
           $input=array();
           $input['user_id'] = $request->user_id;
           $data['date'] = $request->date;
           $data['department_id'] = $request->department_id;
           $input['off_day_date'] = date('Y-m-d',strtotime($request->date));
           $data['department']=Client::where('parent_id',Session::get('dep_id'))->get();
           $data['page_title']='Add New Employee Dedicated Day Off';
           $data['users']=User::where('department_id',$request->department_id)->where('status',1)->get();
           $data['department_id'] = $request->department_id;
           $data['date'] = date('Y-m-d',strtotime($request->date));
           return view('EmployeeDedicatedDayOff.create',$data);



        //    $employee_dedicated_dayoff= EmployeeDedicatedOffDay::create($input);
        //    if($employee_dedicated_dayoff){
        //        Alert::success('Record Submit Successfully ! Thank You');
        //        return redirect('day_off');
        //    }
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
        $employee_dedicated_dayoff=EmployeeDedicatedOffDay::find($id);
         $is_delete=$employee_dedicated_dayoff->delete();
          if($is_delete){
               Alert::success('Record Submit Successfully ! Thank You');
               return redirect('day_off');
           }
    }
}
