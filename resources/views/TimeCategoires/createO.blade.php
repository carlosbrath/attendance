@extends('layout.layout')
@section('title','Add Time Category')
@section('content')

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
                                <label class="form-label">Title<span style="color:red"> *</span></label>
                                <div class="col-md-8">
                                    <input name="title" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="Enter title">
                               @if($errors->any())
                                  <p style="color:red">{{$errors->first('title')}}</p>
                                @endif
                                </div>
                            </div>                            
                            <div class="row form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Time In<span style="color:red"> *</span></label>
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" name="time_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                            </div>
                                     @if($errors->any())
                                       <p style="color:red">{{$errors->first('time_in')}}</p>
                                    @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-row">
                                
                            </div>
                            <br>
                            <div class="row form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Grace Time<span style="color:red"> *</span></label>
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" name="grace_time"  autocomplete="Off" class="form-control" placeholder="Grace Time">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                            </div>
                                    @if($errors->any())
                                       <p style="color:red">{{$errors->first('grace_time')}}</p>
                                    @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="row">
                                <div class="col-md-6">
                                    <label>Type</label>
                                    <br>
                                    <div class="radio">
                                        <input id="male" type="radio" name="time_cat_type"  value="2" checked="checked">
                                        <label for="male">Roster</label>
                                        <input id="female" type="radio" name="time_cat_type" value="1">
                                        <label for="female">Non Roster</label>
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
                                        <div class="row form-row">
                                            <div class="col-md-8" style="margin-top: -40px;">
                                                <div class="form-group">
                                                    <label class="form-label"><strong>Department</strong><span style="color:red"> *</span></label>
                                                    <div class=" right">
                                                        <i class=""></i>
                                                        <select name="deparment_id" id="department_id"   class="leave_deparments select2 form-control">
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
                                                </div> 
                                             </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Time Out<span style="color:red"> *</span></label>
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" class="form-control"  autocomplete="Off" name="time_out" placeholder="Time Out">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                
                                            </div>
                                    @if($errors->any())
                                    <p style="color:red">
                                    {{$errors->first('time_out')}}
                                    </p>
                                    @endif  
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-md-6">
                                 <label>Color</label>   
                                <input name="color" id="form3FirstName" type="color"  class="form-control" placeholder="Enter title">
                                @if($errors->any())
                                  <p style="color:red">{{$errors->first('color')}}</p>
                                @endif

                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 14px;">
                                <div class="col-md-2" style="width: 80px;">
                                                <div class="row-fluid">
                                                    <div class="checkbox check-default">
                                                        <input id="checkbox1" name="tc_monday" type="checkbox" value="1" checked="checked">
                                                        <label for="checkbox1">Monday</label>
                                                    </div>
                                                </div>
                                            </div>
                            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" name="tc_monday_in"   autocomplete="Off" class="form-control" placeholder="Time In">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                     
                                            </div>                                     
                                        </div>
                                </div>
                                <div class="col-md-5" style="margin-top: -7px;">
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" class="form-control"  autocomplete="Off" name="tc_monday_out" placeholder="Time Out">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                
                                            </div>
                                       
                                    </div>
                                </div>
                            </div>



                            <div class="row" style="margin-bottom: 14px;">
                                <div class="col-md-2" style="width: 80px;">
                                               <div class="row-fluid">
                                                    <div class="checkbox check-success">
                                                        <input id="checkbox2" name="tc_tuesday" type="checkbox" value="1" checked="checked">
                                                        <label for="checkbox2">Tuesday</label>
                                                    </div>
                                                </div>
                                            </div>
                            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" name="tc_tuesday_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                                                <span class="input-group-addon ">
                                             
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                     @if($errors->any())
                                       <p style="color:red">{{$errors->first('tuesday_time_in')}}</p>
                                    @endif
                                            </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-5" style="margin-top: -7px;">
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" class="form-control"  autocomplete="Off" name="tc_tuesday_out" placeholder="Time Out">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                
                                            </div>
                                       
                                    </div>
                                </div>
                            </div>


                             <div class="row" style="margin-bottom: 14px;">
                                <div class="col-md-2" style="width: 80px;">
                                               <div class="checkbox check-warning">
                                                        <input id="checkbox5" name="tc_wednesday" type="checkbox" value="1" checked="checked">
                                                        <label for="checkbox5">Wednesday</label>
                                                    </div>
                                            </div>
                            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" name="tc_wednesday_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                     @if($errors->any())
                                       <p style="color:red">{{$errors->first('wnesday_time_in')}}</p>
                                    @endif
                                            </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-5" style="margin-top: -7px;">
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" class="form-control"  autocomplete="Off" name="tc_wednesday_out" placeholder="Time Out">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                
                                            </div>
                                       
                                    </div>
                                </div>
                            </div>



                             <div class="row" style="margin-bottom: 14px;">
                                <div class="col-md-2" style="width: 80px;">
                                                <div class="checkbox check-danger">
                                                        <input id="checkbox6" name="tc_thursday" type="checkbox" value="1" checked="checked">
                                                        <label for="checkbox6">Thursday</label>
                                                    </div>
                                            </div>
                            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" name="tc_thursday_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                     @if($errors->any())
                                       <p style="color:red">{{$errors->first('thursday_time_in')}}</p>
                                    @endif
                                            </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-5" style="margin-top: -7px;">
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" class="form-control"  autocomplete="Off" name="tc_thursday_out" placeholder="Time Out">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                
                                            </div>
                                       
                                    </div>
                                </div>
                            </div>


                             <div class="row" style="margin-bottom: 14px;">
                                <div class="col-md-2" style="width: 80px;">
                                                <div class="checkbox check-danger">
                                                        <input id="checkbox7" name="tc_friday" type="checkbox" value="1" checked="checked">
                                                        <label for="checkbox7">Friday</label>
                                                    </div>
                                            </div>
                            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" name="tc_friday_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                     @if($errors->any())
                                       <p style="color:red">{{$errors->first('friday_time_in')}}</p>
                                    @endif
                                            </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-5" style="margin-top: -7px;">
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" class="form-control"  autocomplete="Off" name="tc_friday_out" placeholder="Time Out">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                
                                            </div>
                                       
                                    </div>
                                </div>
                            </div>


                             <div class="row" style="margin-bottom: 14px;">
                                <div class="col-md-2" style="width: 80px;">
                                                 <div class="checkbox check-info">
                                                        <input id="checkbox9" name="tc_saturday" type="checkbox" value="1" checked="checked">
                                                        <label for="checkbox9">Saturday</label>
                                                    </div>
                                            </div>
                            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" name="tc_satureday_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                     @if($errors->any())
                                       <p style="color:red">{{$errors->first('saturday_time_in')}}</p>
                                    @endif
                                            </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-5" style="margin-top: -7px;">
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" class="form-control"  autocomplete="Off" name="tc_satureday_out" placeholder="Time Out">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                
                                            </div>
                                       
                                    </div>
                                </div>
                            </div>


                             <div class="row" style="margin-bottom: 14px;">
                                <div class="col-md-2" style="width: 80px;">
                                                <div class="checkbox check-primary">
                                                        <input id="checkbox8" name="tc_sunday" type="checkbox" value="1" checked="checked">
                                                        <label for="checkbox8">Sunday</label>
                                                    </div>
                                            </div>
                            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" name="tc_sunday_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                     @if($errors->any())
                                       <p style="color:red">{{$errors->first('sunday_time_in')}}</p>
                                    @endif
                                            </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-5" style="margin-top: -7px;">
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-8">
                                                <input type="text" style="width: 100px;" class="form-control"  autocomplete="Off" name="tc_sunday_out" placeholder="Time Out">
                                                <span class="input-group-addon ">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                
                                            </div>
                                       
                                    </div>
                                </div>
                            </div>
                  </div><br>
                                       <div>
                                            
                                                
                                                <button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
                                                 <button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Register</button> 
                                                <button class="btn btn-white btn-cons" type="reset">Cancel</button>
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