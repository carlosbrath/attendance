
@extends('layout.layout')
@section('title','Add Hoilday Calendar')
@section('content')
<!--  @if(session('msg'))
<div class="alert alert-success" style="text-align: center;">{{session('msg')}}
</div>
@endif -->
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
<div class="row">
	<div class="col-md-12">
		<div class="grid simple form-grid">

			<div class="grid-body no-border">
				<form class="form-no-horizontal-spacing" method="post" 
				      action ="{{route('holidays.store')}}"  id="form-condensed" novalidate="novalidate">
					@csrf
					<div class="row column-seperation">
						<div class="col-md-6">
							<h4></h4>
							<div class="row form-row">
								<label class="form-label">Title</label>
								<div class="col-md-12">
									<input name="title" autocomplete="off" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="Enter title">
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
								<input id="male" type="radio" name="holiday_info" class="Depart" value="department" checked="checked">
								<label for="male">Department</label>
								<input id="female" type="radio"  class="user" name="holiday_info" value="user">
								<label for="female">User</label>
								</div>
								</div>
                            </div>
                           </div>
						   <div class="row form-row">
								<div class="col-md-12" id="dp">
								  <label class="form-label">Department</label>
									<select name="deparment_id" id="department_id"  class="select2 form-control">
										<option value="all">All Departments </option>
										@foreach($department as $d)
										<option value="{{$d->id}}">{{$d->name}}</option>
										@endforeach
							         </select>
							@if($errors->any())
       						  <p style="color:red">{{$errors->first('deparment_id','Department Field is Required')}}</p>
       					    @endif             
						     </div>
						 </div> 
						<div class="row form-row">
						 <div class="col-md-12" id="usr" style="display: none;">
						  <label class="form-label">User</label>
						  <!-- id="source1"  class="select2" -->
							<select multiple name="user_id[]"   class="form-control hol_cal_users">
								<!-- <option value="">Please Select </option>
								@foreach($department_emp as $u)
								   @foreach($u->users as $user)
								    	<option value="{{$user->id}}">{{$user->name}}</option>	
								    @endforeach    
								@endforeach -->
		                    </select>
		                    @if($errors->any())
       						  <p style="color:red">{{$errors->first('user_id','User Field is Required')}}</p>
       						@endif             
					     </div>
					   </div>					
							<div class="row form-row">
								<div class="col-md-12">
									 <label class="control-label">From Date <span style="color: red;">*</span></label>				
									 <div  class="input-append success date col-md-11 col-lg-11 no-padding">
										<input type="text" autocomplete="off" name="date_from" placeholder="From Date" class="form-control">
										<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
										@if($errors->any())
										<p style="color:red">{{$errors->first('date_from')}}</p>
										@endif
									</div>
								</div>
							</div>
							<div class="row form-row">
								<div class="col-md-12" >
									 <label class="control-label">To Date <span style="color: red;">*</span></label>
									<div class="input-append success date col-md-11 col-lg-11 no-padding" >
										<input type="text" autocomplete="off" name="date_to" placeholder="To Date" class="form-control">
										<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
								        @if($errors->any())
										<p style="color:red">{{$errors->first('date_to')}}</p>
										@endif
									</div>
								</div>
							</div>
							<div class="row form-row">
    								
    							<div class="col-md-12">
    										<label><strong>Description</strong></label>
    									 <textarea id="" name="description" class="form-control" rows="10" placeholder="Enter text ..." style="width: 100%; height: 100px;">
    									</textarea>
    							</div>
                            </div>		
						</div>			
					</div>
					<div class="form-actions">
					<div class="pull-left">
							
						<button class="btn btn-danger btn-cons"  type="submit"><i class="icon-ok"></i> Save</button>
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