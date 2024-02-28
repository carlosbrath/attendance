    @extends('layout.layout')
    @section('title','Add Department')
    @section('content')
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
  @if(session('message'))
  <div class="alert alert-danger" style="text-align: center;">{{session('message')}}
  </div>
  @endif       
    <div class="row">
    	<div class="col-md-12">
    		<div class="grid simple form-grid">
    			<div class="grid-body no-border">
    				<form class="form-no-horizontal-spacing" method="post" action ="{{route('departments.store')}}" id="form-condensed" novalidate="novalidate">
    					@csrf
    					<div class="row column-seperation">
                <div class="col-md-12">
                  <label class="form-label"><strong></strong></label>
                  <div class="radio">
                        <input id="unit" type="radio" name="check_account" value="1" checked="checked">
                        <label for="unit">Unit</label>
                        @if($check_subaccount->sub_account!=2)
                        <input id="sub_account" type="radio" name="check_account" value="2">
                        <label for="sub_account">Sub Account</label>
                        @endif
                   </div>
              </div>

    						<div class="col-md-8">
    							<div class="row form-row">
    								<div class="col-md-8">
    									<label><strong>Name</strong></label>
    									<input value="{{old('name')}}" autocomplete="off" name="name" id="form3FirstName" type="text" class="form-control" placeholder="Enter Department Name">
    							       @if($errors->any())
                              <p style="color:red">{{$errors->first('name')}}</p>
                        @endif
    								</div>
    							</div>
                  <div class="row form-row" id="department_email" style="display: none;">
                      <div class="col-md-8">
                                  <label class="form-label"><strong>Email</strong></label>
                                  <input name="email" autocomplete="off" id="form3FirstName"  type="text" class="form-control" placeholder="Enter Email">
                      @if($errors->any())
                          <p style="color:red">{{$errors->first('email')}}</p>
                      @endif     
                       </div>
                   </div>              
    							<div class="row form-row">
    								<div class="col-md-8">
    									<div class="col-md-12">
    										<label><strong>Description</strong></label>
    									 <textarea id="text-editor" name="description" autocomplete="off" class="form-control" rows="10" placeholder="Enter text ..." style="width: 100%; height: 100px;">{{old('description')}}
    									</textarea>
    										<!--<input name="form3LastName" id="form3LastName" type="text" class="form-control" placeholder="Last Name">-->
    									</div>

    								</div>
                 </div>  
               
<!--
<div class="col-md-7">
<input type="text" placeholder="Date of Birth" class="form-control" id="form3DateOfBirth" name="form3DateOfBirth">
</div>-->
<!--<div class="row form-row">
<div class="col-md-12">
<input name="form3Occupation" id="form3Occupation" type="text" class="form-control" placeholder="Occupation">
</div>
</div>
<div class="row form-row">
<div class="col-md-8">
<div class="radio">
<input id="male" type="radio" name="gender" value="male" checked="checked">
<label for="male">Male</label>
<input id="female" type="radio" name="gender" value="female">
<label for="female">Female</label>
</div>-->
</div>
</div>
<!--<div class="row form-row">
<div class="col-md-12">
<input name="form3Email" id="form3Email" type="text" class="form-control" placeholder="email@address.com">
</div>
</div>-->
</div>
<!--
<div class="col-md-6">
<h4>Postal Information</h4>
<div class="row form-row">
<div class="col-md-12">
<input name="form3Address" id="form3Address" type="text" class="form-control" placeholder="Address">
</div>
</div>
<div class="row form-row">
<div class="col-md-6">
<input name="form3City" id="form3City" type="text" class="form-control" placeholder="City">
</div>
<div class="col-md-6">
<input name="form3State" id="form3State" type="text" class="form-control" placeholder="State">
</div>
</div>
<div class="row form-row">
<div class="col-md-8">
<input name="form3Country" id="form3Country" type="text" class="form-control" placeholder="Country">
</div>
<div class="col-md-4">
<input name="form3PostalCode" id="form3PostalCode" type="text" class="form-control" placeholder="Postal Code">
</div>
</div>
<div class="row form-row">
<div class="col-md-4">
<input name="form3TeleCode" id="form3TeleCode" type="text" class="form-control" placeholder="+94">
</div>
<div class="col-md-8">
<input name="form3TeleNo" id="form3TeleNo" type="text" class="form-control" placeholder="Phone Number">
</div>
</div>
<div class="row small-text">
<p class="col-md-12">
NOTE - Facts to be considered, Simply remove or edit this as for what you desire. Disabled font Color and size
</p>
</div>
</div>------>
</div>
<div class="form-actions">

	<div class="pull-left">
        <button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
		<button class="btn btn-success btn-cons" name="action" value="save_and_close" ="save_and_close" type="submit"><i class="icon-ok"></i> Register & AddNew</button>
        <button class="btn btn-white btn-cons" type="button">Cancel</button>
	</div>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection