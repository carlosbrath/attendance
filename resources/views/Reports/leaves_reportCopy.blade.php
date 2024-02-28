
@extends('layout.layout')
@section('title','Leave Report')
@section('content')
<style type="text/css">
        .form-actions {
        background-color: #f6f7f8;
        border: 0px;
        margin-bottom: 20px;
        margin-top: 0px;
        padding: 19px 20px 9px;
        }
        .grid {
        clear: both;
        margin-top: 0px;
        margin-bottom: 0px;
        padding: 0px;
        }
        </style>
<div class="row">
	<div class="col-md-12">
		<div class="grid simple form-grid">
			<div class="grid-body no-border">
				<form class="form-no-horizontal-spacing" method="post" action="{{route('employee_leave_reports')}}" id="form-condensed" novalidate="novalidate">
					@csrf
					<div class="row column-seperation">
						<div class="col-md-12">
							<h4></h4>
                            

                            <!-- <div class="row form-rows">
                           	<div class="col-md-6">
                                <label class="form-label"><strong>Status</strong></label>
                             
								<div class="radio">
								<input id="male" type="radio" name="dep" class="Depart" value="deparment" checked="checked">
								<label for="male">Select Department</label>
								<input id="female" type="radio"  class="user" name="dep" value="employee">
								<label for="female">Select Employee</label>
								</div>
								
                               
                            </div>
                        
								<div class="col-md-6" id="dpr">
								  <label class="form-label">Department</label>
									<select name="department_id" id="form3Gender"  class="select2 form-control" data-init-plugin="select2">
										<option>Please Select </option>
										 @foreach($department as $d)
								     <option value="{{$d->id}}">{{$d->name}}</option>
								     @endforeach
							 		</select>
									@if($errors->any())
									<p style="color:red">
										{{$errors->first('department_id','please select Department')}}
									</p>
									@endif          

								</div>
							</div> --> 
							 <div class="row form-row">
						<div class="col-md-4" style="width:379px;">
						   <div class="grid simple">
			               			   	<div class="col-md-12">
                <label class="form-label"><strong></strong></label>
                <div class="radio">
                <input id="dep" type="radio" name="filter" value="dep" checked="checked">
                <label for="dep">Filter by dep</label>
                <input id="emp" type="radio" name="filter" value="emp">
                <label for="emp">Filter  emp & dep</label>
              </div>
              </div>
							<div class="grid-title no-border" style="padding-bottom: 0px">
							</div>	

						<div class="row" id="dep_select">

							<div class="col-md-12">
							<br>
							<label class="form-label"  style="font-size:15px">Department</label>
									<select name="dep_by_emp" id="leave_department_id"  class="select2 form-control">
										<option value="">
										 Please Select...
										</option>
										@foreach($department as $d)
									      <option name=""  value="{{$d->id}}">{{$d->name}}</option>
									    @endforeach
									  </select>
										 @if($errors->any())
									   <p style="color:red">{{$errors->first('dep_by_emp','Department field is required')}}
									   </p>
									  @endif
							</div>
						</div>
						<div class="row" style="display: none"  id="show_only_dep">

							<div class="col-md-12">
							<br>
							<label class="form-label"  style="font-size:15px">Department</label>
									<select name="department_id" id="leave_department_id"  class="select2 form-control">
										<option value="">
										 Please Select...
										</option>
										@foreach($department as $d)
									      <option name=""  value="{{$d->id}}">{{$d->name}}</option>
									    @endforeach
									  </select>
										 @if($errors->any())
									   <p style="color:red">{{$errors->first('deparment_id','Department field is required')}}
									   </p>
									  @endif
							</div>
						</div>
						 <div class="col-md-4" style="width:379px;">
						   <div class="grid simple">
							<div class="grid-title no-border">
							</div>
						
						<div class="row" style="display: none" id="emp_select">
							<div class="col-md-12">
							<br>
							<label class="form-label"  style="font-size:15px">Employee</label>
								<select name="user_id" id="source1" style="width:100%">
									<option value="">Please Select Employee</option>
									
								</select>
								<p id="emp_msg" style="color: red"></p>
							</div>
						</div>
					</div>
					</div>

					</div>
					</div>
					  <div class="col-md-2">
				        <div class="loader" id="preloader" style="display:none;">	  
				  </div>
			     </div>	
              

				</div>

				 <div class="row form-row">
						
				</div>
						</div>
								<!-- <div class="row form-row">
								 <div class="col-md-6" id="usrs">
								  <label class="form-label">Employee</label>
									<select name="user_id" id="form3Gender"  class="select2 form-control">
										<option>Please Select </option>
										 @foreach($user as $us)
								     <option value="{{$us->id}}">ID:{{$us->id}}-{{$us->name}}</option>
								     @endforeach
							 		</select>
									@if($errors->any())
									<p style="color:red">
										{{$errors->first('user_id','please select Employee')}}
									</p>
									@endif           

								</div>
								 
							</div> -->
                    <div class="row">
                      <div class="col-md-12">
							<div class="row form-row">
								<div class="col-md-12">
									<label class="form-label"  style="font-size:15px ">From Date</label>
									<div style="width: 338px;" class="input-append success date  no-padding">
										<input type="text" autocomplete="off" name="from_date" placeholder="From Date" class="form-control">
										<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
									@if($errors->any())
						            <p style="color:red">{{$errors->first('from_date')}}</p>
						            @endif  
									</div>
								</div>
							
								<div class="col-md-12" >
									<label class="form-label"  style="font-size:15px ">To Date</label>
									<div style="width: 338px;" class="input-append success date no-padding">
										<input type="text" autocomplete="off" name="to_date" placeholder="To Date" class="form-control">
										<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
									@if($errors->any())
						               <p style="color:red">{{$errors->first('to_date')}}</p>
						            @endif
									</div>
								</div>
							</div>


                         </div>
                     </div>
						
						</div>
						
					</div>



					<div class="form-actions">
						<div class="pull-left">
							<button class="btn btn-success btn-cons" type="submit"><i class="icon-ok"></i>Generate Report</button>								
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@if($leave!="")
<div class="row" style="margin-top:20px">
	<div class="col-md-4 col-vlg-3 col-sm-6">
<div class="tiles blue m-b-10">
<div class="tiles-body">

<div class="tiles-title text-white"><b>Department : </b>{{$department_name}}</div>
<div class="tiles-title text-white"><b>From Date : </b>{{$from_date}}</div>
<div class="tiles-title text-white"><b>To Date : </b>{{$to_date}}</div>
</div>
</div>
</div>
</div>	

<div class="row" ">
<div class="row-fluid">
	<div class="span12">
		<div class="grid simple ">

			<div class="grid-body ">
				<table class="table dataTable" id="dataexample" aria-describedby="example3_info">
					<thead>
						<tr role="row">
							<th>Id</th>
							<th>Name</th>
					       @foreach($leave as $l)
							 <th>{{$l->title}}</th>
							@endforeach
							<th>Short Leave</th>
							<!-- @if(count($leave)>0) 
							<th>Total</th>
                            @endif -->
				     	</tr>
					</thead>
				   <tbody role="alert" aria-live="polite" aria-relevant="all">
						
							@foreach($emp as $e)
							<tr>
								<td>{{$e->id}}</td>
								<td>{{$e->name}}</td>
								
							   @foreach($leave as $l)
							    <?php $total_leave=0; ?>
							    <?php $total_short_leave=0; ?>
							    @foreach($emp_leave_detail->where('user_id',$e->id)->where('leave_type_id',$l->id) as $leve_app)
							    @if($l->id==$leve_app->leave_type_id && $leve_app->leave_type==2)
                                  <?php   $total_leave+=1;?>
							   @endif    
							   @endforeach
							   <td>{{$total_leave}}</td>
							   @endforeach 
							   @if(count($leave)>0)
							    @foreach($emp_leave_detail->where('user_id',$e->id) as $short_leave)
							     @if($short_leave->leave_type==1)
							       <?php   $total_short_leave+=1;?>
							     @endif
							    @endforeach
                                  <td>{{$total_short_leave}}</td>
                                @endif 
							</tr>

							@endforeach
				</tbody>	
		       </table>
	      </div>
	    </div>
      </div>
	</div>
</div>
@endif
@include('include.important')
@endsection