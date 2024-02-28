

@extends('layout.layout')
@section('title','Update Leave Request')
@section('content')
 @if(session('msg'))
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
<!-- <div class="alert alert-success" style="text-align: center;">{{session('msg')}}
</div> -->
@endif
<div class="row">
  <div class="col-md-12">
    <div class="grid simple form-grid">

      <div class="grid-body no-border">
        <form class="form-no-horizontal-spacing" method="post" 
              action ="{{route('leaverequests.update',$leaverequest->id)}}" enctype="multipart/form-data"  id="form-condensed" novalidate="novalidate">
                  {{method_field('PUT')}}
                  {{csrf_field()}}
          <div class="row column-seperation">
            <div class="row form-rows">
                <div class="col-md-12">
                    <label class="form-label"><strong>Leave Status</strong></label>
                    <div class="col-md-8">
                <div class="radio">
                <input id="male" type="radio"  name="leave_type" class="short_leve" value="1" <?php echo (1==$leaverequest->leave_type)?'checked':'' ?>>
                <label for="male">Short Leave</label>
                <input id="female" type="radio"  class="leave" name="leave_type" value="2" <?php echo (2==$leaverequest->leave_type)?'checked':'' ?> >
                <label for="female">Leaves</label>
                </div>
                </div>
                   
                </div>
            </div>
           <div class="row form-row">   
            <div class="col-md-8">
              <h4></h4>
          <div class="form-group">
            <label class="form-label"><strong>Department</strong></label>
            <div class=" right">
              <i class=""></i>
            <select name="deparment_id" autocomplete="off" id="leave_deparment_id"  style="width: 66%" class="select2 form-control">
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
        </div>
        <div class="col-md-4">
                <div class="loader" id="preloader" style="display:none;">   
          </div>
        </div> 


          @if(Session::get('role_id')!= 6)
               <div class="row form-row">         
                <div class="col-md-8" >


                  <div class="row">
                    <div class="col-md-8">
                      <br>
                      <label class="form-label"  style="font-size:15px">User <span style="color: red;">*</span></label>
                      <select name="user_id" id="source" style="width:100%" tabindex="-1" class="select2-offscreen employees_list" autocomplete="off">
                        <option value="">Please Select...</option>
                        <!-- <option value=""><b>Please Select...</b></option>
                        @foreach($user as $u)
                        @foreach($u->users as $uu)
                        <option name=""  value="{{$uu->id}}">{{$uu->name}}</option>
                        @endforeach
                        @endforeach-->
                      </select> 
                      <p id="emp_msg" style="color:red"></p>
                     
                    </div>
                  </div>


                </div>
               </div>
              
              
              @endif
              <br>
                <div class="row form-row">          
                    <div class="col-md-8">
                      <div class="row">
                            <div class="col-md-8">
                            <br>
                            <label class="form-label"  style="font-size:15px">Leave Type <span style="color: red;">*</span></label>
                            <select name="leave_fix_var" id="leave_fix_var"   class="leave_deparments select2 form-control" autocomplete="off">
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
                            {{$errors->first('leave_fix_var','Leave type field is required')}}
                            </p>
                            @endif      


                            </div> <div class="col-md-1"></div>
                            <div class="col-md-3" style="display: none">
                            <div class="loader" id="loader" >   
                            </div>
                            </div> 
                      </div>
                    </div>
                </div>  
                <br>
              <div class="row form-row">
                <div class="col-md-8">
                <div class="row">
                <div class="col-md-8">
                  <label class="control-label">Leave<span style="color: red;">*</span></label>
                  <select name="leave" id="leave" autocomplete="off"  class="select2 form-control">
                  </select>
                  <p id="leave_msg" style="color:red;"></p>
    
              </div>
             </div>
            </div>
          </div>
        <div class="row form-row">     
            <div class="col-md-5" style="width: 442px;">
            <label class="form-label"><strong>Total Leave</strong></label>
            <input type="text" class="form-control" autocomplete="off" id="total_leave" disabled="" name="total_leave">      
            <br>
            </div>
            </div>
            <div class="row form-row">
            <div class="col-md-5" style="width: 442px;">
            <label class="form-label"><strong>Avail Leave</strong></label>
            <input type="text" value="" autocomplete="off" class="form-control" disabled="
            " id="avail_leave" name="avail_leave">      
            <br>  
        </div>
        </div>
               <div class="col-md-12">
                <div class="col-md-6">
                   <label class="control-label">From Date <span style="color: red;">*</span></label>                  <div class="input-append success date col-md-10 col-lg-10 no-padding">
                    <input type="text" autocomplete="off" name="date_from" value="{{$leaverequest->from_date}}" placeholder="From Date" class="form-control">
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                    @if($errors->any())
                                  <p style="color:red">{{$errors->first('date_from')}}</p>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="col-md-6">
                   <label class="control-label">To Date <span style="color: red;">*</span></label>
                  <div class="input-append success date col-md-10 col-lg-10 no-padding">
                    <input type="text" autocomplete="off" value="{{$leaverequest->to_date}}" name="date_to" placeholder="To Date" class="form-control">
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                   @if($errors->any())
                                     <p style="color:red">{{$errors->first('date_to')}}</p>
                                  @endif
                  </div>
                </div>
              </div> 
         
         
              <div class="row form-row">
                <div class="col-md-8" id="time_from">
                  <div class="form-group">
                     <label class="control-label">Time From </label>
                    <div class="controls">
                      <div class="input-group transparent clockpicker col-md-8" style="width: 463px">
                        <input type="text" name="time_from" value="{{$leaverequest->time_from}}" autocomplete="off" class="form-control" placeholder="Time From">
                        <span class="input-group-addon ">
                          <i class="fa fa-clock-o"></i>
                        </span>
                    @if($errors->any())
                                     <p style="color:red">{{$errors->first('time_from')}}</p>
                    @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>
                           

              <div class="row form-row">
                <div class="col-md-8" id="time_to">
                  <div class="form-group">
                     <label class="control-label">To Time </label>
                    <div class="controls">
                      <div class="input-group transparent clockpicker col-md-8" style="width: 463px">
                        <input type="text" autocomplete="off" value="{{$leaverequest->time_from}}" class="form-control" name="time_to" placeholder="Time To">
                        <span class="input-group-addon ">
                          <i class="fa fa-clock-o"></i>
                        </span>
                
                      </div>
                    </div>
                  </div>
                </div>

            </div>
            <div class="row form-row">
                <div class="col-md-8">
                    <div class="form-group">
                       <label class="control-label">Attach Application Letter </label>
                      <div class="controls">
                        <div class="input-group transparent  col-md-6">
                          <input type="file" autocomplete="off" class="form-control" name="application_letter" placeholder="Time To">
                        
                  
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row form-row">
                  <div class="col-md-6" style="width: 454px">
                                  <label class="form-label"><strong>Remarks</strong></label>
                   <textarea rowspan="6" placeholder="Remarks here" class="col-md-12" name="remarks"></textarea>                        
                  <br>
                  <div class="clearfix"></div>
                               
                  </div>       
            </div>
          </div>
            <div class="row form-row">
            <div class="col-md-6" style="width: 454px">
                              <label class="form-label"><strong>Custom SMS</strong></label>
               <textarea rowspan="6" placeholder="custom sms here" class="col-md-12" name="custom_sms"></textarea>                        
              <br>
              <div class="clearfix"></div>
                           
            </div>
            </div>
            </div>
          </div>
          <div class="form-actions">
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