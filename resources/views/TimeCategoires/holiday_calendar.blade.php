
@extends('layout.layout')
@section('title','Add Hoilday Calendar')
@section('content')
 @if(session('msg'))
<div class="alert alert-success" style="text-align: center;">{{session('msg')}}
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
        					<div class="row form-rows">
                           	<div class="col-md-12">
                                <label class="form-label"><strong>Status</strong></label>
                                <div class="col-md-8">
								<div class="radio">
								<input id="male" type="radio" name="gender" class="Depart" value="male" checked="checked">
								<label for="male">Departmentt</label>
								<input id="female" type="radio"  class="user" name="gender" value="female">
								<label for="female">User</label>
								</div>
								</div>
                               
                            </div>
                           </div>
							
							<div class="row form-row">
								<div class="col-md-6" id="dp">
								  <label class="form-label">Department</label>
									<select name="deparment_id" id="form3Gender"  class="select2 form-control">
										<option>Please Select </option>
										<option>IT</option>			
                              <option>Demo</option>	
				                    </select>             

								</div></div>
								<div class="row form-row">
								<div class="col-md-6" id="usr">
								  <label class="form-label">User</label>
									<select name="deparment_id" id="form3Gender"  class="select2 form-control">
										<option>Please Select </option>
										<option>Saleem</option>			
                              <option>Saeed</option>	
				                    </select>             

								</div>
								
							</div>
							
							<div class="row form-row">
								<div class="col-md-12" style="width:474px">
									 <label class="control-label">From Date <span style="color: red;">*</span></label>									<div class="input-append success date col-md-12 col-lg-12 no-padding">
										<input type="text" autocomplete="off" name="date_from" placeholder="From Date" class="form-control">
										<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
								   
									</div>
								</div>
							</div>
							<div class="row form-row">
								<div class="col-md-12" style="width:474px;">
									 <label class="control-label">To Date <span style="color: red;">*</span></label>
									<div class="input-append success date col-md-12 col-lg-12 no-padding">
										<input type="text" autocomplete="off" name="date_to" placeholder="To Date" class="form-control">
										<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
								   
									</div>
								</div>
							</div>
							<div class="row form-row">
    								
    									<div class="col-md-8">
    										<label><strong>Description</strong></label>
    									 <textarea id="" name="description" class="form-control" rows="10" placeholder="Enter text ..." style="width: 100%; height: 100px;">
    									</textarea>
    										<!--<input name="form3LastName" id="form3LastName" type="text" class="form-control" placeholder="Last Name">-->
    									</div>
<!--
<div class="col-md-7">
<input type="text" placeholder="Date of Birth" class="form-control" id="form3DateOfBirth" name="form3DateOfBirth">
</div>-->
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
				</form>
			</div>
		</div>
	</div>
</div>
@include('include.important')
@endsection