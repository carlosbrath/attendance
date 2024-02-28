       @extends('layout.layout')
       @section('title','Add Leave')
        @section('content')
        @if(session('message'))
         <div class="alert alert-success" style="text-align: center;">
            {{session('message')}}
         </div>
        @endif
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
        				<form class="form-no-horizontal-spacing" method="post" action ="{{route('leaves.store')}}" id="form-condensed" novalidate="novalidate">
        					 {{csrf_field()}}
        			<div class="row column-seperation">
        <!-- 				<div class="col-md-8">
                  <div class="form-group">
                          <label class="form-label"><strong>Department</strong></label>
                      <div class=" right">
                          <i class=""></i>
                          <select name="deparment_id" id="department_id"  style="width: 66%" class="leave_deparments select2 form-control">
                              <option value="">
                              Please Select...
                              </option>
                              @foreach($department as $d)
                                <option name=""  value="{{$d->id}}">{{$d->name}}
                                </option>
                              @endforeach
                           </select>
                            @if($errors->any())
                            <p style="color:red">
                              {{$errors->first('deparment_id','Department field is required')}}
                            </p>
                            @endif   			
                       </div>
                  </div> --> 
                   <div class="form-group">
                          <label class="form-label"><strong>Leave Type</strong></label>
                      <div class=" right">
                          <i class=""></i>
                          <select name="leave_type" id=""  style="width: 66%" class="leave_deparments select2 form-control">
                              <option value="">
                              Please Select...
                              </option>
                                <option value="2">
                                 Variables Leave
                              </option>
                               <option value="1">
                                 Fixed Leave
                              </option>
                             
                           </select>
                            @if($errors->any())
                            <p style="color:red">
                              {{$errors->first('leave_type','Leave type field is required')}}
                            </p>
                            @endif      
                            
                       </div>
                  </div> 

                  <div class="row form-row">
                    <div class="col-md-8">
                        <label class="form-label">
                          <strong>Title</strong>
                        </label>
                        <input name="type" autocomplete="off" id="form3FirstName" type="text" class="form-control" placeholder="Enter type">
                        @if($errors->any())
                          <p style="color:red">{{$errors->first('type','Type field is required')}}</p>
                        @endif
                    </div>
                  </div>
                <div class="col-md-8">
                 <label>Color</label> 
                  <input name="color" id="form3FirstName" type="color" class="form-control" placeholder="Enter title">
                </div>
                <div class="row form-row">
                  <div class="col-md-8">
                      <label class="form-label"><strong>Description</strong></label>
                      <textarea rowspan="6" class="col-md-12" name="desc">
                        {{old('desc')}} 
                      </textarea>      									
                      <br>
                      <div class="clearfix"></div>
                  </div>
                </div>
        			</div>
        		</div>
        	</div>
       </div>
  		<div class="form-actions">
  				<div class="pull-left">
  					<button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
  					<button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Register & AddNew</button>
  					<button class="btn btn-white btn-cons" type="reset">Cancel</button>
  				</div>
  			</div>
  		</form>
  	</div>
  </div>
</div>
</div>
@endsection