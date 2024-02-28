 
@extends('layout.layout')
@section('title','Add Attach Employee')
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
 <!-- @if(session('msg'))
      <div class="alert alert-success" style="text-align: center;">{{session('msg')}}
      </div>
 @endif -->
<div class="row">
	<div class="col-md-12">
		<div class="grid simple form-grid">

			<div class="grid-body no-border">
				<form class="form-no-horizontal-spacing"  method="post" 
				action="{{route('attachemployees.store')}}" id="form-condensed"  enctype="multipart/form-data" novalidate="novalidate">
					@csrf
					<div class="row column-seperation">
						
							<h4></h4>
					      <div class="row form-rows">
                           	<div class="col-md-12">
                                <label class="form-label"><strong>Attachment Status</strong></label>
                              <div class="col-md-8">
								<div class="radio">
									<input id="single_day" type="radio" name="attachment_status" class="day_attach" value="1" checked="checked">
									<label for="single_day">Single Day Attachment</label>
									<input id="is_tour" type="radio"  class="is_tour" name="attachment_status" value="2">
									<label for="is_tour">Multiple Days Attachment</label>
								</div>
							  </div>                       
                            </div>
                         </div>
                
			    
			        <div class="row column-seperation">
							<div class="col-md-5" style="border-right: none;">
								&nbsp;<label class="control-label">Department<span style="color: red;"> *</span></label>
							   <select name="deparment_id" id="leave_deparment_id"  class="select2 form-control">
								<option value="">Please Select</option>
									@foreach($department as $d)
									<option name=""  value="{{$d->id}}">{{$d->name}}</option>
									@endforeach
								</select>
								@if($errors->any())
								<p style="color:red">  
								{{$errors->first('deparment_id','Department field is Required')}}
								</p>
								@endif
							</div>				
							 <div class="col-md-4 pull-right">
								<div class="loader" id="preloader" style="display: none;">	  
								</div>
							</div>
					</div>		
						<div class="row column-seperation">
							<div class="col-md-5">
								&nbsp;<label class="control-label">Employee</label>
							   <select name="user_id" id="source"  class="select2 form-control">
								<option value="">Please Select</option>
						       </select>
						       <p id="emp_msg"></p>
							</div>
						</div>
							<br>
					  <div class="row form-row">
						  <div class="col-md-6">
							<label class="control-label">From Date <span style="color: red;">*</span></label>
						     <div class="input-append success date col-md-9 col-lg-9 no-padding">
								<input type="text" autocomplete="off" name="from_date" placeholder="From Date" class="form-control">
								<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
								@if($errors->any())
								<p style="color:red">
								{{$errors->first('from_date')}}
								</p>
								@endif 
							</div>
						  </div>
					   </div>
					   <div class="row form-row" id="attach_to_date_block" style="display: none">	  
					   <div  class="col-md-6">
							<label class="control-label">To Date <span style="color: red;">*</span></label>
						   <div class="input-append success date col-md-9 col-lg-9 no-padding">
								<input type="text" autocomplete="off" name="to_date" placeholder="To Date" class="form-control">
								<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
								@if($errors->any())
								<p style="color:red">{{$errors->first('to_date')}}</p>
								@endif
						   </div>
						</div>
					  </div>		
					
						 <div class="row form-row">
								<div class="col-md-6" id="attach_time_from">
									<div class="form-group">
										<label class="control-label">Time From <span style="color: red;">*</span> </label>
										<div class="controls">
											<div class="input-group transparent clockpicker col-md-10 col-lg-10">
												<input type="text" class="form-control" autocomplete="off" name="time_from" placeholder="Time From">
												<span class="input-group-addon ">
													<i class="fa fa-clock-o"></i>
												</span>
											</div>
											    @if($errors->any())
		                                        <p style="color:red">{{$errors->first('time_from')}}</p>
		                                        @endif 
										</div>
									</div>
						         </div>
						    </div>
						      <div class="row form-row">
								<div class="col-md-6" id="attach_time_to">
									<div class="form-group">
										<label class="control-label">Time To <span style="color: red;">*</span> </label>
										<div class="controls">
											<div class="input-group transparent clockpicker col-md-10 col-lg-10">
												<input type="text" class="form-control" autocomplete="off" name="time_to" placeholder="Time To">
												<span class="input-group-addon ">
													<i class="fa fa-clock-o"></i>
												</span>
											</div>
											 @if($errors->any())
		                                        <p style="color:red">{{$errors->first('time_to')}}</p>
		                                      @endif 
										</div>
									</div>
								</div>
                             </div>

							
							
							<div class="row form-row">	
							</div>
						<div class="row form-row">
						  <div class="col-md-12">
						     <div class="form-group">
							        <label class="control-label">Attach Application Letter </label>
							   <div class="controls">
								<div class="input-group transparent  col-md-5">
									<input type="file" autocomplete="off" class="form-control" name="application_letter" placeholder="Time To">
								</div>
								</div>
							</div>
						  </div>
						</div>
						  <div class="row form-row">
						   <div class="col-md-8">
								<label class="form-label"><strong>Remarks</strong></label>
								<textarea rowspan="6" placeholder="Remarks here" class="col-md-8" name="remarks"></textarea>   

								<br>
								<div class="clearfix"></div>
							</div>
						</div>
						<div class="row form-row">
							<div class="col-md-8">
                              <label class="form-label"><strong>Custom SMS</strong></label>
								 <textarea rowspan="4" placeholder="custom sms here" class="col-md-8" name="custom_sms"></textarea>      									
								<br>
								<div class="clearfix"></div>          
        					</div>
						</div>		
					</div>
					<div class="form-actions" id="leave_req_act_btn">
						<div class="pull-left">
							<button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Register & Add New</button>	
							<button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
							<button class="btn btn-white btn-cons" type="reset">Cancel</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@include('include.important')
@endsection