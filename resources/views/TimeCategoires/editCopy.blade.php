
@extends('layout.layout')
@section('title','Update Time Category')
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
                      action ="{{route('timecategories.update',$timecategories->id)}}"  id="form-condensed" novalidate="novalidate">
                    {{ method_field('PUT')}}
                    {{csrf_field()}}

                    <div class="row column-seperation">
                        <div class="col-md-6">
                            <h4></h4>
                            
                            <div class="row form-row">
                                <div class="col-md-6">
                                    <label class="form-label">Department</label>
                                    <select name="deparment_id" id="tcat_edit_department"   class="select2 form-control">
                                        <option value="">Please Select</option>        
                                          @foreach($department as $d)
                                           <option name=""  value="{{$d->id}}" <?php echo ($timecategories->department_id == $d->id ) ? 'Selected':'' ?> >{{$d->name}}</option>
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
                                                <input type="text" name="time_in" autocomplete="Off"  value="{{$timecategories->time_in}}" class="form-control" placeholder="Time In">
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
                                        <label class="form-label">Grace Time</label>
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-6">
                                                <input type="text" name="grace_time" autocomplete="Off"  value="{{$timecategories->grace_time}}" class="form-control" placeholder="Grace Time">
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
                                    
                                    <input name="color" id="form3FirstName" type="color" class="form-control" placeholder="Enter title">
                                @if($errors->any())
                                  <p style="color:red">{{$errors->first('color')}}</p>
                                @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4></h4>
                            <div class="row form-row">
                                <div class="col-md-6">
                                <label class="form-label">Type</label>
                                <select name="time_cat_type" id="edit_roster_type" class="select2 form-control">
                                        <option value="">Please Select...</option>                             
                                        <option name=""  value="1" <?php echo(1==$timecategories->time_cat_type)? 'selected':'' ?> >Non Roster</option>
                                        <option name=""  value="2" <?php echo(2==$timecategories->time_cat_type)? 'selected':''?>>Roster</option>
                                 </select>     
                            @if($errors->any())
                             <p style="color:red">
                                {{$errors->first('time_cat_type','Department field is required')}}
                            </p>
                            @endif
                           
                            
                                </div>
                            </div>
                    
                            <div class="row form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Time Out</label>
                                        <div class="controls">
                                            <div class="input-group transparent clockpicker col-md-6">
                                                <input type="text" class="form-control" autocomplete="Off" value="{{$timecategories->time_out}}" name="time_out" placeholder="Time Out">
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

                                <div class="col-md-6">
                                    <label class="form-label">Title</label>
                                    <input name="title" id="form3FirstName" value="{{$timecategories->title}}" type="text" class="form-control" placeholder="Enter title">
                                @if($errors->any())
                                  <p style="color:red">{{$errors->first('title')}}</p>
                                @endif
                                </div>
                            </div>
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
                                        <label for="checkbox4">Day Off </label>
                                    </div>
                                </div>
                            </div>  
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="pull-left">
                            <button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Update</button>  
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('include.important')
@endsection