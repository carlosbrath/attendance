
@extends('layout.layout')
@section('title','Add Time Category')
@section('content')
 @if(session('message'))
<div class="alert alert-success" style="text-align: center;">{{session('message')}}
</div>
@endif
<div class="row">
	<div class="col-md-12">
		<div class="grid simple form-grid">

			<div class="grid-body no-border">
				<form class="form-no-horizontal-spacing" method="post" 
				      action ="{{route('timecategories.store')}}"  id="form-condensed" novalidate="novalidate">
					@csrf

					<div class="row column-seperation">
						<div class="col-md-6">
							<h4></h4>
							<div class="row form-row">
								<label class="form-label">Title</label>
								<div class="col-md-6">
									<input name="title" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="Enter title">
								@if($errors->any())
                                  <p style="color:red">{{$errors->first('title')}}</p>
                                @endif
								</div>
        					</div>
							
							<div class="row form-row">
								<div class="col-md-6">
								  <label class="form-label">Department</label>
									<select name="deparment_id" id="form3Gender"  class="select2 form-control">
										<option value="">Please Select</option>	  
				                          @foreach($department as $d)
				                                  <option name=""  value="{{$d->id}}">{{$d->name}}</option>
				                          @endforeach
				                    </select>             
                               @if($errors->any())
                               <p style="color:red">
                                	{{$errors->first('deparment_id','Department field is required')}}
                               </p>
                               @endif							
								</div>
							</div>
							<div class="row form-row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="form-label">Time In</label>
										<div class="controls">
											<div class="input-group transparent clockpicker col-md-6">
												<input type="text" name="time_in" autocomplete="Off" class="form-control" placeholder="Time In">
												<span class="input-group-addon ">
													<i class="fa fa-clock-o"></i>
												</span>
								    @if($errors->any())
	                                   <p style="color:red">{{$errors->first('time_in')}}</p>
	                                @endif
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row form-row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="form-label">Time Out</label>
										<div class="controls">
											<div class="input-group transparent clockpicker col-md-6">
												<input type="text" class="form-control" autocomplete="Off" name="time_out" placeholder="Time Out">
												<span class="input-group-addon ">
													<i class="fa fa-clock-o"></i>
												</span>
								
											</div>
										</div>
									</div>
								</div>
							</div>
							<br>
							<div class="row form-row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="form-label">Grace Time</label>
										<div class="controls">
											<div class="input-group transparent clockpicker col-md-6">
												<input type="text" name="grace_time" autocomplete="Off" class="form-control" placeholder="Grace Time">
												<span class="input-group-addon ">
													<i class="fa fa-clock-o"></i>
												</span>
								    @if($errors->any())
	                                   <p style="color:red">{{$errors->first('grace_time')}}</p>
	                                @endif
											</div>
										</div>
									</div>
								</div>
							</div>
                           	<div class="row form-row">
								<div class="col-md-6">
								 <label>Color</label>	
	    						<input name="color" id="form3FirstName" type="color" class="form-control" placeholder="Enter title">
								@if($errors->any())
                                  <p style="color:red">{{$errors->first('color')}}</p>
                                @endif
								</div>
        					</div>

        					<div class="row">
								<div class="col-md-6">
									<label>Type</label>
									<br>
									<div class="radio">
										<input id="male" type="radio" name="time_cat_type" value="1" checked="checked">
										<label for="male">Non Roster</label>
										<input id="female" type="radio" name="time_cat_type" value="2">
										<label for="female">Roster</label>
									</div>
								</div>
							</div>
							<br>
							<div class="col-md-4">
								<div class="row-fluid">
									<div class="checkbox check-primary">
										<input id="checkbox3" type="checkbox" value="1" name="night_shift">
										<label for="checkbox3">Night Shift</label>
									</div>
								</div>
								<div class="row-fluid">
									<div class="checkbox check-info">
										<input id="checkbox4" type="checkbox" value="3" name="day_off">
										<label for="checkbox4">Day Off</label>
									</div>
								</div>
							</div>						
						</div>
						<br>
						<div class="col-md-6">
							<h4></h4>
							<div class="col-md-12">
								<div class="grid simple">
									<div class="grid-title no-border">
										<!-- <div class="tools">
											<a href="javascript:;" class="collapse"></a>
											<a href="#grid-config" data-toggle="modal" class="config"></a>
											<a href="javascript:;" class="reload"></a>
											<a href="javascript:;" class="remove"></a>
										</div> -->
									</div>
									<div class="grid-body no-border">
										<div class="row">
											<div class="col-md-4">
												<div class="row-fluid">
													<div class="checkbox check-default">
														<input id="checkbox1" type="checkbox" value="1">
														<label for="checkbox1">Monday</label>
													</div>
												</div>
												<div class="row-fluid">
													<div class="checkbox check-success">
														<input id="checkbox2" type="checkbox" value="1" checked="checked">
														<label for="checkbox2">Tuesday</label>
													</div>
												</div>
												<div class="row-fluid">
													<div class="checkbox check-warning">
														<input id="checkbox5" type="checkbox" value="1" checked="checked">
														<label for="checkbox5">Wnesday</label>
													</div>
												</div>
												<div class="row-fluid">
													<div class="checkbox check-danger">
														<input id="checkbox6" type="checkbox" value="1" checked="checked">
														<label for="checkbox6">Thursday</label>
													</div>
												</div>
												<div class="row-fluid">
													<div class="checkbox check-danger">
														<input id="checkbox7" type="checkbox" value="1" checked="checked">
														<label for="checkbox7">Friday</label>
													</div>
												</div>
											</div>
										</div>
										<div class="form-actions">
											<div class="pull-left">
												<button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Register &amp; Add New</button>	
												<button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
												<button class="btn btn-white btn-cons" type="reset">Cancel</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@include('include.important')
@endsection