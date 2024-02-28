
@extends('layout.layout')
@section('title','Add Leave Request')
@section('content')
 @if(session('msg'))
   <style type="text/css">
       .form-actions {
       background-color:#f6f7f8;
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

<!-- <div class="alert alert-success" style="text-align: center;">{{session('msg')}}
</div> -->
@endif

<div class="row">
@if($errors->any())
    {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
<div class="grid simple">
                    <div class="grid-title no-border">
                      <h4>{{ $page_title }}</h4>
                      <div class="tools">
                        <a href="javascript:;" class="collapse"></a>
                        <a href="#grid-config" data-toggle="modal" class="config"></a>
                        <a href="javascript:;" class="reload"></a>
                        <a href="javascript:;" class="remove"></a>
                      </div>
                    </div>

			<div class="grid-body no-border">
				<form class="form-no-horizontal-spacing" method="post" 
				      action ="{{route('leaverequests.store')}}"  enctype="multipart/form-data"  id="form-condensed">
					@csrf
					<div class="row column-seperation">
					 <div class="row form-row">
					  <div class="col-md-2">
							<label class="form-label"><strong>Type</strong></label>
							<div class="col-md-12">
							<div class="radio">
							<input id="male" type="radio"  name="leave_type" class="short_leve" value="1" checked="">
							<label for="male">Short Leave</label>
							<input id="female" type="radio"  class="leave" name="leave_type" value="2">
							<label for="female">Full Leave</label>
							</div>
							</div>
					  </div>
					  <div class="col-md-2">
							<label class="form-label"><strong>Nature</strong></label>
							<div class="col-md-12">
							<div class="controls">
								<select id="leave_nature" name="leave_nature" class="form-control">
									<option value="0">Please Select</option>
									<option value="1">Departmental Fix</option>
									<option value="2">Employee Fixed</option>
								</select>
							</div>
							</div>
					  </div>
					 <div class="col-md-4">
					 	<div class="form-group">
	                        <label class="form-label">Leave Category</label>
	                        <span class="help"  style="font-size:11px!important">e.g. "Departmental Leave Categories"</span>
	                        <div class="controls">
				 				<select name="leave" id="leave" autocomplete="off"   style="width:100%" >
				 					<option value="0">Please Select...</option>
				 					{{--  @foreach($leave as $cat)
				 						<option value="{{ $cat->id }}">{{ $cat->title }}</option>
				 					@endforeach 
									 --}}
				 				</select> 
								 @if($errors->any())
									   <p style="color:red">{{$errors->first('user','Leave Category is required')}}</p>
	    						  @endif
	                        </div>
                      	</div>
                      </div>
                    <div class="clearfix"></div>	
					 <div class="col-md-4">
					 	<div class="form-group">
						
	                        <label class="form-label">Department</label>
	                        <span class="help" >e.g. "Administration"</span>
							
							@if(\Session::get('role_id')==6)
							@php
								$user=\Session::get('user');
								$department=$department->where('id', $user->department_id);
							@endphp
	                        <div class="controls">
								<select name="deparment_id" id="leave_deparment_id" autocomplete="off"   class="select2 form-control" required='required'>
									<option value="0">Please Select...</option>
									@if($department->count()>0)
									@foreach($department as $d)
								      <option name=""  value="{{$d->id}}">{{$d->name}}</option>
								    @endforeach
									@endif
								</select>
								 @if($errors->any())
									   <p style="color:red">{{$errors->first('deparment_id','Department field is required')}}</p>
	    						  @endif
	                        </div>
							
							@else
							<div class="controls">
								<select name="deparment_id" id="leave_deparment_id" autocomplete="off"   class="select2 form-control">
									<option value="0">Please Select...</option>
									
									@foreach($department as $d)
								      <option name=""  value="{{$d->id}}">{{$d->name}}</option>
								    @endforeach
								</select>
								 @if($errors->any())
									   <p style="color:red">{{$errors->first('deparment_id','Department field is required')}}</p>
	    						  @endif
	                        </div>

							@endif
                      	</div>
                      </div>
					 <div class="col-md-4">
					 	<div class="form-group">
	                        <label class="form-label">Employee</label>
	                        <span class="help" style="font-size:11px!important">e.g. "Employee of the choosen department"</span>
	                        <div class="controls">
				 				<select name="user" autocomplete="off"  id="source" required='required' style="width:100%" required tabindex="-1" class="select2-offscreen employees_list leave_apply_form_user_id">
				 					<option value="0">Please Select...</option>
				 				</select> 
								 @if($errors->any())
									   <p style="color:red">{{$errors->first('user','Employee field is required')}}</p>
	    						  @endif
	                        </div>
                      	</div>
                      </div>
                      <div class="clearfix"></div>
						
					 <div class="col-md-4">
					 	<div class="form-group">
	                        <label class="form-label">Total Leaves</label>
	                        <span class="help">e.g. "Total Leaves in this category"</span>
	                        <div class="controls">
								<input type="text" autocomplete="off" id="total_leaves" value="" class="form-control" disabled="
							" id="total_leave" name="total_leave">  
	                        </div>
                      	</div>
                      </div>
					 <div class="col-md-4">
					 	<div class="form-group">
	                        <label class="form-label">Availed Leaves</label>
	                        <span class="help"  style="font-size:11px!important" >e.g. "Availed Leaves in this category"</span>
	                        <div class="controls">
								<input type="text" autocomplete="off" id="availed_leaves" value="" class="form-control" disabled="
							" id="avail_leave" name="avail_leave">  
	                        </div>
                      	</div>
                      </div>
                      <div class="clearfix"></div>
					 <div class="col-md-4">
					 	<div class="form-group">
							<label class="form-label">From Date</label>
							<span class="help">e.g. "Leave Applicable From date"</span>
							<div class="input-append success date col-md-11 col-lg-11 no-padding">
								<input type="text" autocomplete="off" name="date_from" placeholder="From Date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
								<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
								@if($errors->any())
								<p style="color:red">{{$errors->first('date_from')}}</p>
								@endif
							</div>
											
                      	</div>
                      </div>
					 <div class="col-md-4">
					 	<div class="form-group">
					 	<label class="form-label">To Date</label>
					 	<span class="help">e.g. "Leave Applicable To date"</span>
						<div class="input-append success date col-md-11 col-lg-11 no-padding">
							<input type="text" autocomplete="off" name="date_to" placeholder="To Date" class="form-control">
							<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
						    @if($errors->any())
	                        <p style="color:red">{{$errors->first('date_to')}}</p>
	                        @endif
						</div>
                      	</div>
                      </div>
                      <div class="clearfix"></div>
					 <div class="col-md-4" id="time_from">
					 	<div class="form-group">
					 	<label class="form-label">From Time</label>
					 	<span class="help">e.g. "Mention Time Interval incase of shortleave"</span>
						<div class="input-append success clockpicker col-md-11 col-lg-11 no-padding">
							<input type="text" autocomplete="off" name="time_from" placeholder="From Time" class="form-control">
							<span class="add-on"><span class="arrow"></span><i class="fa fa-clock-o"></i></span>
						    @if($errors->any())
	                        <p style="color:red">{{$errors->first('time_from')}}</p>
	                        @endif
						</div>
                      	</div>
                      </div>
					 <div class="col-md-4" id="time_to">
					 	<div class="form-group">
					 	<label class="form-label">To Time</label>
					 	<span class="help">e.g. "Mention Time Interval incase of shortleave"</span>
						<div class="input-append success clockpicker col-md-11 col-lg-11 no-padding">
							<input type="text" autocomplete="off" name="time_to" placeholder="To Time" class="form-control">
							<span class="add-on"><span class="arrow"></span><i class="fa fa-clock-o"></i></span>
						    @if($errors->any())
	                        <p style="color:red">{{$errors->first('time_to')}}</p>
	                        @endif
						</div>
                      	</div>
                      </div>
                      <div class="clearfix"></div>
					 <div class="col-md-8" >
					 	<div class="form-group">
					 	<label class="form-label">Leave Application</label>
					 	<span class="help">e.g. "Approve Application Letter"</span>
						<div class="input-append success col-md-12 col-lg-12 no-padding">
							<input type="file" autocomplete="off"  name="application_letter" >
						</div>
                      	</div>
                      </div>
                      <div class="clearfix"></div>
					 <div class="col-md-8" >
					 	<div class="form-group">
					 	<label class="form-label">Remarks</label>
					 	<span class="help">e.g. "Departmental Notes/Acknowledgement"</span>
						<div class="input-append success col-md-12 col-lg-12 no-padding">
							<textarea rowspan="6" autocomplete="off" placeholder="Remarks here" class="col-md-12" name="remarks" ></textarea>  
						</div>
                      	</div>
                      </div>
                      <div class="clearfix"></div>
				  <div class="col-md-4">
				        <div class="loader" id="preloader" style="display:none;">	  
				  </div>
			     </div>	

	
					   </div>
					</div>
					<div class="form-actions" id="leave_req_act_btn">
						<div class="pull-left">
							<button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Save & Add New</button>	
							<button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
							<button class="btn btn-white btn-cons" type="reset">Cancel</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">  
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" >
         <div class="row form-row">
             <h4>your current leave. <span id="total_number_leave"></span> must be small in number than.<span id="leave_number"></span></h4>      
         </div>
      <div class="modal-footer" style="padding: 0px ;background-color:#e1e6e8">
        <button type="button" class="btn btn-danger btn-cons" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@include('include.important')
@endsection