
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
							<div class="row form-row">
								<div class="col-md-4">
									<label class="form-label" style="font-size:15px ">Department</label>
									<select name="department_id" id="form3Gender" style="width: 373px"  class="select2 form-control">
									  <option value="">Please Select</option>
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
							</div>
							<div class="row form-row">
								<div class="col-md-12">
									<label class="form-label"  style="font-size:15px ">From Date</label>
									<div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
										<input type="text" autocomplete="off" name="from_date" placeholder="From Date" class="form-control">
										<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
									@if($errors->any())
						            <p style="color:red">{{$errors->first('from_date')}}</p>
						            @endif  
									</div>
								</div>
							</div>
						  	<div class="row form-row">
								<div class="col-md-12">
									<label class="form-label"  style="font-size:15px ">To Date</label>
									<div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
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
							 <th>{{$l['title']}}</th>
							@endforeach
							
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
							    <?php
								 $total_leave=0; 
							    $total_short_leave=0;
								$total_leave_count=0;
								?>
					
					@if($employee_count_record)
						@foreach($employee_count_record->where('emp_id',$e->id)->where('leave_type_id',$l['id']) as $leave_count)
						<?php  $total_leave_count  =  $leave_count['total_leaves']; ?>
						@endforeach   
					@endif	
                              
						 
					@foreach($emp_leave_detail->where('user_id',$e->id)->where('leave_type_id',$l['id']) as $leve_app)
					      
					             
					      
							    @if($l['id']==$leve_app->leave_type_id && $leve_app->leave_type==2)
								 
                                  <?php $total_leave+=1; ?>
								@else
								<?php $total_short_leave+=1; ?>
							   @endif   

							   @endforeach
										@if($l['title']!='Short Leave')
										<td style="font-size:10px">A : {{$total_leave}} , T : {{$total_leave_count}}</td>
										@else
										<td>{{$total_short_leave}}</td>
										@endif 
							   @endforeach 
							   
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