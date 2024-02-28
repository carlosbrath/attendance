@extends('layout.layout')
@section('title','Add Time Category')
@section('content')
 <div class="container" style="width: 100%;">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">Non Roster</a></li>
    <li><a data-toggle="tab" href="#menu1">Roster</a></li>
  </ul>

  <div class="tab-content">

    <div id="home" class="tab-pane fade in active">
     <form class="form-no-horizontal-spacing" method="post" 
         action ="{{route('timecategories.store')}}"  id="form-condensed" novalidate="novalidate">
         @csrf
      <h3>Non Roster</h3>
      <div class="row">
        <div class="col-md-12">
            <div class="col-md-6">    
                <label class="form-label">Title<span style="color:red"> *</span></label>
                <div class="col-md-12">
                    <input name="title" autocomplete="off" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="Enter title">
                    @if($errors->any())
                    <p style="color:red">{{$errors->first('title')}}</p>
                    @endif
                </div>                        
       
        <div class="col-md-12">
             <div class="form-group">
                <label class="form-label">Grace Time<span style="color:red"> *</span></label>
                <div class="controls">
                    <div class="input-group transparent"><!-- clockpicker -->
                        <input type="text" autocomplete="off" name="grace_time"  class="form-control" placeholder="Grace Time" value="00:35" readOnly>
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </span>
                    </div>
            @if($errors->any())
               <p style="color:red">{{$errors->first('grace_time')}}</p>
            @endif
            </div>
          </div>
           <div class="col-md-2" style="width: 80px;">
                            <div class="row-fluid">
                                <div class="checkbox check-default">
                                    <input id="checkbox1" autocomplete="off" name="night_shift" type="checkbox" value="1" >
                                    <label for="checkbox1">Night Shift</label>
                                </div>
                            </div>
                        </div>    
        </div>
     </div>
         <div class="col-md-6">
               <div class="row">
            <div class="grid-body no-border">
            <div class="row form-row">
                      

            </div>

            <div class="row" style="margin-bottom: 14px;">
               <div class="col-md-2" style="width: 80px;">                 
                               
                                    <label>Mon</label>
                               
                           
                        </div>    
            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8" style="margin-left: 0px;">
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
                          
                                    <label>Tues</label>
                             
                        </div>
            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" name="tc_tuesday_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>
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
                           
                                  
                                    <label>Wed</label>
                                
                        </div>
            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" name="tc_wednesday_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>
                
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
                          
                                    <label >Thurs</label>
                               
                        </div>
            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" name="tc_thursday_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>
                
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
                          
                                   
                                    <label>Fri</label>
                               
                        </div>
            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" name="tc_friday_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>
                
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
                             
                                   
                                    <label>Sat</label>
                              
                        </div>
            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" name="tc_saturday_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>
                
                        </div>
                    
                </div>
            </div>
            <div class="col-md-5" style="margin-top: -7px;">
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" class="form-control"  autocomplete="Off" name="tc_saturday_out" placeholder="Time Out">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>

                        </div>       
                </div>
            </div>
            </div>
            
            <div class="row" style="margin-bottom: 14px;">
            <div class="col-md-2" style="width: 80px;">
                            
                                    
                                    <label>Sun</label>
                                
                        </div>
            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" name="tc_sunday_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
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
             

            </div>

             <br>
            </div>
     </div>
     <input id="" name="time_cat_type" type="hidden" value="1" checked="checked">
     </div>


  <div>
    <button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
    <button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Register</button> 
    <button class="btn btn-white btn-cons" type="reset">Cancel</button>
</div>

</div>
</form>
</div>
    <div id="menu1" class="tab-pane fade">
      <form class="form-no-horizontal-spacing" method="post" 
            action ="{{route('timecategories.store')}}"  id="form-condensed" novalidate="novalidate">
            @csrf
      <h3>Roster</h3>
      <div class="row">
           <div class="col-md-12">

             <div class="col-md-6">
                  <div class="col-md-12">
                        <label class="form-label">Title<span style="color:red"> *</span></label>
                      
                            <input name="roster_title" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="Enter title">
                       @if($errors->any())
                          <p style="color:red">{{$errors->first('roster_title')}}</p>
                        @endif
                   </div>
                <div class="col-md-12">
                      <label class="form-label">Department</label>
                        <select name="roster_department_id" id="roster_department_id"   class="select2 form-control">
                             <option value="">Please Select</option>   
                             
                              @foreach($department as $d)
                                      <option name=""  value="{{$d->id}}">{{$d->name}}</option>
                              @endforeach
                        </select>             
                   @if($errors->any())
                   <p style="color:red">
                        {{$errors->first('roster_department_id','Department field is required')}}
                   </p>
                   @endif                   
                </div>



                <div class="col-md-12">
                    <label>Color</label>   
                        <input name="color" id="form3FirstName" type="color"  class="form-control" placeholder="Enter title">
                             
                 </div>

                  <input id="" name="time_cat_type" type="hidden" value="2" checked="checked">
             
                 </div>
                </div> 
             </div>
             <br>
             <br>
               <button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
                    <button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Register</button> 
                <button class="btn btn-white btn-cons" type="reset">Cancel</button>  
           </div>
       </form>
      </div>
    </div>

@include('include.important')
@endsection