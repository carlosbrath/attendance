@extends('layout.layout')
  @section('title','Add Employee')
  @section('content')
@if(session('message'))
<div class="alert alert-success" style="text-align: center;">{{session('message')}}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="text-align: center;">{{session('error')}}
</div>
@endif
<div class="row">
  <div class="col-md-12">
    <div class="grid simple form-grid">
      <div class="grid-title no-border">
        <h4>Employee Registration Form</h4>
      </div>
      <div class="grid-body no-border">
        <form method="post" action="{{route('employee.step1_store')}}" enctype="multipart/form-data" id="commentForm" >
              @csrf
              <fieldset>
                <legend><i class="fa fa-leaf"></i> Basic Information</legend>
                  <div class="row form-row">
                    @if(is_null($user->id))
                    <div class="col-md-4">
                      <label class="form-label"><strong>Employee ID</strong> <span style="color:red">*</span></label>
                      {!! Form::text('id',$user->id,['class' => 'form-control','id'=>'id','placeholder'=>'Unique Employee ID','required' => 'required']) !!}
                      <p style="color:red">{{$errors->first('id')}}</p>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">&nbsp;</label>
                      <a class="btn btn-sm btn-success" id="suggest_unused_ids_btn" data-toggle="modal" data-target="#suggested_ids_modal"><i class="fa fa-search"> Suggested IDs</i></a>
                    </div>
                    @endif
                    <div class="col-md-3">
                      <label class="form-label"><strong>Employee Name</strong> <span style="color:red">*</span></label>
                      {!! Form::text('name', $user->name,['class' => 'form-control','placeholder'=>'Employee Name','required' => 'required']) !!}
                      <p style="color:red">{{$errors->first('id')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Father Name</strong></label>
                      {!! Form::text('father_name', $user->father_name,['class' => 'form-control','placeholder'=>'Employee Father Name']) !!}
                      <p style="color:red">{{$errors->first('father_name')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Type</strong> <span style="color:red">*</span></label>
                        <div class="radio">
                            <input id="nonroster" type="radio"  name="flag"
                              @if($user->flag == '1') checked @endif value="1"
                            checked="checked" >
                            <label for="nonroster">Non Roster</label>
                            <input id="roster" type="radio" name="flag"
                              @if($user->flag == '2') checked @endif
                             value="2">
                            <label for="roster">Roster</label>
                        </div>
                      <p style="color:red">{{$errors->first('flag')}}</p>
                    </div>
                  </div>
                  <div class="row form-row">
                    <div class="col-md-3">
                      <label class="form-label"><strong>CNIC #</strong><span style="color:red">*</span></label>
                      {!! Form::text('cnic', $user->cnic,['class' => 'form-control','placeholder'=>'NIC Number','id'=>'tin', 'required' => 'required']) !!}
                      <p style="color:red">{{$errors->first('cnic')}}</p>
                    </div><div class="col-md-3">
                      <label class="form-label"><strong>Join Date</strong><span style="color:red">*</span></label>
                      {!! Form::date('reg_date', $user->reg_date,['class' => 'form-control','id'=>'reg_date','placeholder'=>'Reg Date','required' => 'required']) !!}
                      <p style="color:red">{{$errors->first('reg_date')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>CNIC Issue Date</strong></label>
                      {!! Form::date('cnic_issue_date', $user->cnic_issue_date,['class' => 'form-control','id'=>'cnic_issue_date','placeholder'=>'CNIC Issue Date']) !!}
                      <p style="color:red">{{$errors->first('cnic_issue_date')}}</p>
                    </div>
                    <div class="col-md-3">

                      <label class="form-label"><strong>Cnic Expiry Date</strong></label>
                      {!! Form::date('cnic_expiry_date', $user->cnic_expiry_date,['class' => 'form-control','id'=>'cnic_expiry_date','placeholder'=>'CNIC Expiry Date']) !!}
                      <p style="color:red">{{$errors->first('cnic_expiry_date')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Date of Birth</strong></label>
                      {!! Form::date('dob', $user->dob,['class' => 'form-control','id'=>'date_of_birth','placeholder'=>'Date of Birth','id'=>'date_of_birth']) !!}
                      <p style="color:red">{{$errors->first('dob')}}</p>
                    </div>
                   </div>

                  <div class="row form-row">
                    <div class="col-md-3">
                    @php  $role = \App\Role::find(6); @endphp
                      <label class="form-label"><strong>Role</strong> <span style="color:red">*</span></label>
                      <select name="role_id">
                        <option value="{{$role->id}}"> {{$role->title}}</option>
                      </select>
                      <p style="color:red">{{$errors->first('role_id')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Department</strong> <span style="color:red">*</span></label>
                      {!! Form::select('department_id',[''=>'Choose Department']+$departments,$user->department_id,['id'=>'department_id', 'class' => 'select2 form-control load_sub_departments ','required' => 'required','data-init-plugin'=>""]) !!}
                      <p style="color:red">{{$errors->first('department_id')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Designation</strong> <span style="color:red">*</span></label>
                      {!! Form::select('designation_id',[''=>'Choose Designation']+$designations,$user->designation_id,['class'=>'select2 form-control','id'=>'leave','required' => 'required']) !!}
                      <p style="color:red">{{$errors->first('designation_id')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>App Mark Attendance</strong> <span style="color:red">*</span></label>
                        <div class="radio">
                            <input id="app_a_a" type="radio"  name="app_attendance"
                              @if($user->app_attendance == '1') checked @endif value="1"
                            checked="checked" >
                            <label for="app_a_a">Allowed</label>
                            <input id="app_n_a" type="radio" name="app_attendance"
                              @if($user->app_attendance == '2') checked @endif
                             value="2">
                            <label for="app_n_a">Not Allowed</label>
                        </div>
                      <p style="color:red">{{$errors->first('app_n_a')}}</p>
                    </div>
                  </div>
                  <div class="row form-row">
                    <div class="col-md-3" id="gender_section">
                      <label class="form-label"><strong>Gender</strong> <span style="color:red">*</span></label>
                        <div class="radio">
                            <input id="male" type="radio"  name="gender"
                              @if($user->gender == 'male') checked @endif value="male"
                            checked="checked" >
                            <label for="male">Male</label>
                            <input id="female" type="radio" name="gender"
                              @if($user->gender == 'female') checked @endif
                             value="female">
                            <label for="female">Female</label>
                            <input id="other" type="radio" name="gender"
                                  @if($user->gender == 'other') checked @endif
                              value="other">
                            <label for="other">Other</label>
                        </div>
                      <p style="color:red">{{$errors->first('gender')}}</p>
                    </div>
                    @if($flag=='edit')
                    <div class="col-md-3">
                      <label class="form-label"><strong>Employee Transfer To</strong> </label>
                      <!-- {!! Form::select('transfer_to_dept_id',[''=>'Choose Department']+$units,null,['id'=>'units','class' => 'select2 form-control ','data-init-plugin'=>""]) !!}  -->
                      <select name="transfer_to_dept_id" id="transfer_to_department" class="select2 form-control">

                      </select>
                    </div>
                    @endif
                    <div class="col-md-2">
                      <label class="form-label"><strong>Official/Profile Photo</strong></label>
                      {!! Form::file('image', NULL,['placeholder'=>'NIC Number']) !!}
                      <p style="color:red">{{$errors->first('image')}}</p>
                    </div>
                    <div class="col-md-1">
                        @if($user->image != '') <img src="{{ asset('emp_images') }}/{{ $user->image }}" width="50px" /> @endif
                    </div>
                  </div>
                  <div class="row form-row">
                    <div class="col-md-3" id="field_staff_section">
                      <label class="form-label"><strong>Field Staff</strong> <span style="color:red">*</span> <small>Note: Please do not register him on biometric attendance machine in case of "Yes".</label>
                        <div class="radio">
                            <input id="yes" type="radio"  name="field_staff" value="1" @if($user->field_staff == '1') checked @endif >
                            <label for="yes">Yes</label>
                            <input id="no" type="radio" name="field_staff" value="0" @if($user->field_staff == '0' || !isset($user->field_staff)) checked @endif >
                            <label for="no">No</label>
                        </div>
                      <p style="color:red">{{$errors->first('field_staff')}}</p>
                    </div>
                    <div class="col-md-6" id="remarks_section">
                      <label class="form-label"><strong>Remarks</strong> </label>
                      {!! Form::text('remarks',$user->remarks,['class' => 'form-control','placeholder'=>'Add remarks for field staff.']) !!}
                      <p style="color:red">{{$errors->first('remarks')}}</p>
                    </div>
                    <div class="col-md-3" id="emp_type_section">
                      <label class="form-label"><strong>Employee Type</strong> <span style="color:red">*</span></label>
                      {!! Form::select('emp_type',['Regular'=>'Regular','Adhoc'=>'Adhoc','Contract'=>'Contract','Contingency'=>'Contingency','Temporary'=>'Temporary','Attach'=>'Attach'],$user->emp_type,['class' => 'form-control','id'=>'emp_type']) !!}
                      <p style="color:red">{{$errors->first('emp_type')}}</p>
                    </div>
                  </div>
                  @if(!is_null($user))
                    <input type="hidden" value="{{ $user->id }}" name="edit_employee">
                  @endif
              </fieldset>
              <fieldset>
                <legend><i class="fa fa-building"></i> Department & Service Information</legend>
                  <div class="row form-row">
                    <div class="col-md-3">
                      <label class="form-label"><strong>Personal File #</strong></label>
                      {!! Form::text('file_number', $user->file_number,['class' => 'form-control','placeholder'=>'Personal File Number']) !!}
                      <p style="color:red">{{$errors->first('file_number')}}</p>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label"><strong>Service Scale</strong> <span style="color:red">*</span> </label>
                      {!! Form::text('bps', $user->bps,['class' => 'form-control','placeholder'=>'Service/Pay Scale', 'required' => 'required']) !!}
                      <p style="color:red">{{$errors->first('bps')}}</p>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label"><strong>Gazzetted?</strong> <span style="color:red">*</span></label>
                        <div class="radio">
                            <input id="gazzetted" type="radio"  name="is_gazetted"
                              @if($user->is_gazetted == 1) checked @endif value="1"
                            checked="checked" >
                            <label for="gazzetted">Yes</label>
                            <input id="non_gazzetted" type="radio" name="is_gazetted"
                              @if($user->is_gazetted == 0) checked @endif
                             value="0">
                            <label for="non_gazzetted">No</label>
                        </div>
                      <p style="color:red">{{$errors->first('gazetted')}}</p>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label"><strong>Salary Amount</strong> </label>
                      {!! Form::text('fixed_salary', $user->fixed_salary,['class' => 'form-control','placeholder'=>'Fixed Salary Amount']) !!}
                      <p style="color:red">{{$errors->first('fixed_salary')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Per Hour Rate</strong></label>
                      {!! Form::text('salary_hour_rate', $user->hour_rate_salary,['class' => 'form-control','placeholder'=>'Per Hour Rate in PKR']) !!}
                      <p style="color:red">{{$errors->first('salary_hour_rate')}}</p>
                    </div>
                  </div>
                  <div class="row form-row">
                    <div class="col-md-3">
                      <label class="form-label"><strong>Email Address/Login</strong> </label>
                      {!! Form::text('email', $user->email,['class' => 'form-control','placeholder'=>'Email Address/Login']) !!}
                      <p style="color:red">{{$errors->first('email')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Password</strong> </label>
                      {!! Form::text('password', null,['class' => 'form-control','placeholder'=>'leave Empty to keep the old password']) !!}
                      <p style="color:red">{{$errors->first('password')}}</p>
                    </div>
                  </div>
                </fieldset>
              <fieldset>
                <legend><i class="fa fa-clock-o"></i> Time & Roster Information</legend>
                  <div class="row form-row">
                    <div class="col-md-3">

                      <label class="form-label"><strong>Region</strong> <span style="color:red">*</span></label>
                      {!! Form::select('region_id',[''=>'Choose Region']+$regions,$user->region_id,['class' => 'form-control','id'=>'region_id', 'required' => 'required']) !!}
                      <p style="color:red">{{$errors->first('region_id')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Zone</strong> <span style="color:red">*</span></label>

                      {!! Form::select('zone_id',[''=>'Choose Zone']+$zones,$user->zone_id,['class' => 'form-control','id'=>'r_zone_id' , 'required' => 'required']) !!}
                      <p style="color:red">{{$errors->first('zone_id')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Branch</strong> <span style="color:red">*</span></label>
                      {!! Form::select('branch_id',[''=>'Choose Branch']+$branches,$user->branch_id,['class' => 'form-control','id'=>'z_branch_id','required' => 'required']) !!}
                      <p style="color:red">{{$errors->first('region_id')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Time Category</strong> <span style="color:red">*</span></label>
                      {!! Form::select('tcat_id',[''=>'Choose Time Category']+$timecategories,$user->tcat_id,['class' => 'form-control ','required' => 'required','data-init-plugin'=>""]) !!}
                      <p style="color:red">{{$errors->first('region_id')}}</p>
                    </div>
                  </div>
                </fieldset>
              <fieldset>
                <legend><i class="fa fa-phone-square"></i> Contact Information</legend>
                  <div class="row form-row">
                    <div class="col-md-3">
                      <label class="form-label"><strong>Primary Mobile Number</strong><span style="color:red">*</span></label>
                      {!! Form::text('mobile_number', $user->mobile_number,['class' => 'form-control','placeholder'=>'Mobile Number', 'required' => 'required']) !!}
                      <p style="color:red">{{$errors->first('mobile_number')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Secondary Mobile Number</strong> </label>
                      {!! Form::text('mob_number_2', $user->mob_number_2,['class' => 'form-control','placeholder'=>'Mobile Number']) !!}
                      <p style="color:red">{{$errors->first('mob_number_2')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Landline Number</strong></label>
                      {!! Form::text('land_line_number', $user->land_line_number,['class' => 'form-control','placeholder'=>'Landline Number']) !!}
                      <p style="color:red">{{$errors->first('land_line_number')}}</p>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label"><strong>Address</strong></label>
                      {!! Form::textarea('address',$user->address,['class' => 'form-control','rows'=>2,'placeholder'=>'Address']) !!}
                      <p style="color:red">{{$errors->first('address')}}</p>
                    </div>
                  </div>
                </fieldset>

               <fieldset>
                <legend><i class="fa fa-dollar"></i> Allowances</legend>
                  <div class="row form-row">
                      <div id="allowance_holder">
                        <div class="col-md-12">
                            <div class="col-md-3">
                              <label class="form-label"><strong>Allowances</strong></label>

                              {!! Form::select('id',[''=>'Choose allowances']+$allowances,$user->tcat_id,['name'=>'allowances[]','class' => 'form-control ','data-init-plugin'=>""]) !!}

                            </div>
                            <div class="col-md-3">
                              <label class="form-label"><strong>Amount</strong> </label>
                              {!! Form::text('allowance_amount', $user->mob_number_2,['name'=>'allowance_amounts[]','class' => 'form-control','placeholder'=>'Allowance Amount']) !!}
                              <p style="color:red">{{$errors->first('mob_number_2')}}</p>
                            </div>
                            <div class="col-md-3">
                              <label>&nbsp;</label>
                              <button class="btn btn-success add_allowances_row" type="button" >
                              <i class="fa fa-plus"> Add Another</i>

                            </div>
                        </div>
                  </div>
                </fieldset>
              <fieldset>
              <legend><i class="fa fa-list"></i> Deductions</legend>
                  <div class="row form-row">
                  <div id="deduction_holder">
                    <div class="col-md-12">
                        <div class="col-md-3">
                          <label class="form-label"><strong>Deductions</strong></label>

                          {!! Form::select('id',[''=>'Choose Deductions']+$deductions,$user->tcat_id,['name'=>'deductions[]',  'class' => 'form-control ','data-init-plugin'=>""]) !!}

                        </div>
                        <div class="col-md-3">
                          <label class="form-label"><strong>Amount</strong> </label>
                          {!! Form::text('deduction_amount', $user->mob_number_2,[ 'name'=>'deduction_amounts[]','class' => 'form-control','placeholder'=>'Deduction Amount']) !!}
                          <p style="color:red">{{$errors->first('mob_number_2')}}</p>
                        </div>
                        <div class="col-md-3">
                                  <label>&nbsp;</label>
                                    <button class="btn btn-success add_deduction_row" type="button" >
                                        <i class="fa fa-plus"> Add Another</i>
                                    </button>
                        </div>
                      </div>
                      </div>
                  </div>
                </fieldset>

              <fieldset>
                <legend><i class="fa fa-paperclip"></i> Designated Leaves </legend>
                  <div class="row form-row">
                  <div id="leaves_holder">
                      <?php $designated_leaves=[];?>
                      @if(isset($designated_leaves) && count($designated_leaves)>0)
                      @foreach($designated_leaves as $dleaves)
                      <div class="col-md-12">
                        <div class='d_leaves_row' >
                            <div class="col-md-4">
                              <label class="form-label"><strong>Leave Type</strong> <span style="color:red">*</span></label>
                                <select name="leave_type_id[]" class="form-control">
                                @foreach($leaves as $id=>$leave)
                                    <option @if($id== $dleaves->leave_type_id) selected @endif value="{{ $id }}">{{ $leave }}</option>
                                  @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                              <label class="form-label"><strong>Number of Days</strong> <span style="color:red">*</span></label>
                                <input type="text" class="form-control" value="{{ $dleaves->total_leaves }}" name="number_of_days[]" placeholder="Number of Days" required >
                            </div>
                            <div class="col-md-4">
                              <label>&nbsp;</label>
                                <button class="btn btn-success add_leave_row" type="button" >
                                    <i class="fa fa-plus"> Add Another</i>
                                </button>
                            </div>
                        </div>
                      </div>
                        @endforeach
                      @else

                      <div class="col-md-12">
                        <div class='d_leaves_row' >
                            <div class="col-md-4">
                              <label class="form-label"><strong>Leave Type</strong> <span style="color:red">*</span></label>
                                <select name="leave_type_id[]" class="form-control leave_type_id">
                                  @foreach($leaves as $id=>$leave)
                                    <option  value="{{ $id }}">{{ $leave }}</option>
                                  @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">

                              <label class="form-label"><strong>Number of Days </strong> <span style="color:red">*</span></label>

                                <input type="text" class="form-control number_days" name="number_of_days[]" placeholder="Number of Days" required >
                            </div>
                        </div>
                      </div>
                      @endif
                      </div>
                    </div>
              </fieldset>


              <fieldset>

              <legend><i class="fa fa-calendar"></i> Time Categories Log</legend>
                <div class="col-md-5">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>S#</th>
                          <th class="text-center">Time Category</th>
                          <th class="text-center">Assigned Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($tcat_hist as $key=>$tcat_history)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td class="text-center">{{ $tcat_history->timeCategory?$tcat_history->timeCategory->title:'' }}</td>
                          <td class="text-center">{{ date("Y-m-d",strtotime($tcat_history->join_date)) }} <i class="fa fa-pencil" data-toggle="modal" data-target="#update_tcat_modal"
                            onclick='update_tcat({{ $tcat_history->id }},{{ $tcat_history->tcat_id }},"{{ \Carbon\Carbon::parse($tcat_history->join_date)->format('Y-m-d') }}")'></i></td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                </div>
              </fieldset>
              <fieldset>

              @php
                  $transfer_histories = App\TransferHistory::where('user_id', $user->id)->withoutTrashed()->get();
                  $s_no = 1;

              @endphp
              @if(count($transfer_histories) >0 )
                      <fieldset>
                          <legend><i class="fa fa-calendar"></i> Transfer History Log</legend>
                          <div class="col-md-5">
                              <table class="table table-bordered">
                                  <thead>
                                  <tr>
                                      <th>S#</th>
                                      <th class="text-center">From Department</th>
                                      <th class="text-center">To Department</th>
                                      <th class="text-center">Transfer Date</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  @foreach($transfer_histories as $th)
                                    <tr>
                                        <td>{{$s_no ++}}</td>
                                        @php
                                            $from_dep = App\Client::find($th->from_dep_id);
                                            $to_dep = App\Client::find($th->to_dep_id);
                                        @endphp
                                        <td>{{$from_dep->name}}</td>
                                        <td>{{$to_dep->name}}</td>
                                        <td>{{ \Carbon\Carbon::parse($th->date)->format('d-m-Y') }}</td>
                                    </tr>
                                  @endforeach
                                  </tbody>
                              </table>
                          </div>
                      </fieldset>
                      <fieldset>

              @endif

              @php
                  $promotion_histories = App\PromotionHistory::where('user_id', $user->id)->withoutTrashed()->get();
                  $s_no = 1;

              @endphp
              @if(count($promotion_histories) >0 )
                      <fieldset>
                          <legend><i class="fa fa-calendar"></i> Promotion History Log</legend>
                          <div class="col-md-5">
                              <table class="table table-bordered">
                                  <thead>
                                  <tr>
                                      <th>S#</th>
                                      <th class="text-center">Old Scale</th>
                                      <th class="text-center">New Scale</th>
                                      <th class="text-center">Old Designation</th>
                                      <th class="text-center">New Designation</th>
                                      <th class="text-center">Date</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  @foreach($promotion_histories as $ph)
                                    <tr>
                                        <td>{{$s_no ++}}</td>
                                        @php
                                            $old_designation = App\Designation::find($ph->old_designation);
                                           //dd($old_designation);
                                            $new_designation = App\Designation::find($ph->new_designation);
                                        @endphp
                                        <td>{{$ph->old_scale}}</td>
                                        <td>{{$ph->new_scale}}</td>
                                         <td>{{$old_designation->title}}</td>
                                        <td>{{$new_designation->title}}</td>
                                        <td>{{ \Carbon\Carbon::parse($ph->date)->format('d-m-Y') }}</td>
                                    </tr>
                                  @endforeach
                                  </tbody>
                              </table>
                          </div>
                      </fieldset>
                      <fieldset>
              @endif

              <legend><i class="fa fa-tags"></i> AI Search Tags</legend>
                <div class="col-md-12">
                    <input type="text" name="tags" value="{{@$user->tags}}" class="form-control" placeholder="Deputy Commissioners,DCs,Deputy Commissioner Mirpur,DC Mirpur">
                </div>
              </fieldset>
            <div class="form-actions">
              <div class="pull-right simple">
                <button class="btn btn-danger btn-cons" id="basic_action_save" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
                 <button class="btn btn-success btn-cons" id="" name="action" value="save_next" ><i class="icon-ok"></i>Cancel</button>
              </div>
            </div>
    </form>
</div>
</div>
</div>
</div>
<div class="modal fade" id="suggested_ids_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Suggested IDs</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-stripped" id="suggested_missing_ids_table">
          <thead>
          <tr>
            <th>ID</th>
            <th style="float: right;">Action</th>
          </tr>
        </thead>
        <tbody id="suggested_missing_ids_table_body">
        </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="update_tcat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Time Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{route('user_tc_update') }}">
          {{ csrf_field() }}
        <div class="row">
          <div class="col-md-4">
            <input type="hidden" name="tcat_hist_id" id="tcat_hist_id" value="">
            <label class="form-label"><strong>Time Category</strong> <span style="color:red">*</span></label>
            {!! Form::select('update_tcat_id',[''=>'Choose Time Category']+$timecategories,null,['class' => 'form-control ','id' => 'update_tcat_id','data-init-plugin'=>""]) !!}
          </div>
          <div class="col-md-4">
            <label class="form-label"><strong>Date</strong> <span style="color:red">*</span></label>
            <input type="date" name="update_tcat_date" id="update_tcat_date" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" >Update</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>
@push('scripts')
<script src=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js "></script>
<link href=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css " rel="stylesheet">

<link href="{{url('select2totree/select2totree.css')}}" rel="stylesheet" type="text/css" />
<script src="{{url('select2totree/select2totree.js')}}" type="text/javascript"></script>
<script type="text/javascript">
   function update_tcat(id,tcat_id,jdate){
    $('#tcat_hist_id').val(id);
    $('#update_tcat_id').val(tcat_id);
    $('#update_tcat_date').val(jdate);
   }


   //departments
   var all_depts_data = @php echo json_encode($transfer_to_department);@endphp;

    $("#transfer_to_department").select2ToTree({treeData: {dataArr: all_depts_data}, maximumSelectionLength: 3});
    $("#transfer_to_department").val("@php echo (isset($data['transfer_to_dept_id']))?$data['department_id']:'choose one';@endphp").trigger('change');
</script>

@endpush
@include('include.important')
@endsection

