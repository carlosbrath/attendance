@extends('layout.layout')
@section('title','Add Time Category')
@section('content')
 @if(session('msg'))


  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->

@endif


 <div class="container" style="width: 100%;">
  <ul class="nav nav-tabs">
   
 
    <li class="active"><a data-toggle="tab" href="#home">Non Roster</a></li>
    <li style="display: none"><a data-toggle="tab" href="#menu1">Roster</a></li>  
  </ul>

  <div class="tab-content">

    <div id="home" class="tab-pane fade in active">
     <form class="form-no-horizontal-spacing" method="post" 
         action ="{{route('timecategories.update',$timecategories->id)}}"  id="form-condensed" novalidate="novalidate">
        {{ method_field('PUT')}}
        {{csrf_field()}}
      <h3>Non Roster</h3>
      <div class="row">
        <div class="col-md-12">
            <div class="col-md-6">    
                <label class="form-label">Title<span style="color:red"> *</span></label>
                 <?php if($timecategories->time_cat_type=="1"){
                                   $non_roster_title=$timecategories->title;
                                   $non_rost_department_id=$timecategories->department_id;
                                   $grace_time=$timecategories->grace_time;
                                   $monday_in=$timecategories->tc_monday_in;
                                   $monday_out=$timecategories->tc_monday_out;
                                   $tuesday_in=$timecategories->tc_tuesday_in;
                                   $tuesday_out=$timecategories->tc_tuesday_out;
                                   $wednesday_in=$timecategories->tc_wednesday_in;
                                   $wednesday_out=$timecategories->tc_wednesday_out;
                                   $thursday_in=$timecategories->tc_thursday_in;
                                   $thursday_out=$timecategories->tc_thursday_out;
                                   $friday_in=$timecategories->tc_friday_in;
                                   $friday_out=$timecategories->tc_friday_out;
                                   $satureday_in=$timecategories->tc_saturday_in;
                                   $satureday_out=$timecategories->tc_saturday_out;
                                   $sunday_in=$timecategories->tc_sunday_in;
                                   $sunday_out=$timecategories->tc_sunday_out;
                                   
                           }
                           else{
                              $non_roster_title='';
                              $non_rost_department_id='';
                              $grace_time='';
                              $monday_in='';
                              $monday_out='';
                              $tuesday_in='';
                              $tuesday_out='';
                              $wednesday_in='';
                              $wednesday_out='';
                              $thursday_in='';
                              $thursday_out='';
                              $friday_in='';
                              $friday_out='';
                              $satureday_in='';
                              $satureday_out='';
                              $sunday_in='';
                              $sunday_out='';

                           }
                           ?>
                <div class="col-md-12">
                    <input name="title" autocomplete="off" id="form3FirstName" value="{{$non_roster_title}}" type="text" class="form-control" placeholder="Enter title">
                    @if($errors->any())
                    <p style="color:red">{{$errors->first('title')}}</p>
                    @endif
                </div>                        
  
        <div class="col-md-12">
             <div class="form-group">
                <label class="form-label">Grace Time<span style="color:red"> *</span></label>
                <div class="controls">
                    <div class="input-group transparent"><!-- clockpicker -->
                        <input type="text" name="grace_time" value="{{$grace_time}}"  autocomplete="Off" class="form-control" placeholder="Grace Time" readOnly>
                        <span class="input-group-addon ">
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
                        <input id="checkbox1" autocomplete="off" @if($timecategories->is_night_shift==1) checked @endif name="is_night_shift" type="checkbox" value="1" >
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
                            <input type="text" style="width: 100px;" name="tc_monday_in" value="{{$monday_in}}"   autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>
                        </div> 
                    </div>
              
            </div>
            <div class="col-md-5" style="margin-top: -7px;">
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" class="form-control" value="{{$monday_out}}"  autocomplete="Off" name="tc_monday_out" placeholder="Time Out">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>

                        </div>
                   
                </div>
            </div>
            </div>
            <div class="row" style="margin-bottom: 14px;">
            <div class="col-md-2" style="width: 80px;">
                          
                                    <label >Tues</label>

                        </div>
            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" name="tc_tuesday_in"  value="{{$tuesday_in}}"  autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>
                        </div>
                </div>
            </div>
            <div class="col-md-5" style="margin-top: -7px;">
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" class="form-control"   value="{{$tuesday_out}}"  autocomplete="Off" name="tc_tuesday_out" placeholder="Time Out">
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
                            <input type="text" style="width: 100px;" name="tc_wednesday_in" value="{{$wednesday_in}}"  autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>
                
                        </div>
                    
                </div>
            </div>
            <div class="col-md-5" style="margin-top: -7px;">
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" class="form-control" value="{{$wednesday_out}}"   autocomplete="Off" name="tc_wednesday_out" placeholder="Time Out">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>

                        </div>       
                </div>
            </div>
            </div>
             
         
            <div class="row" style="margin-bottom: 14px;">
            <div class="col-md-2" style="width: 80px;">
                            
                                    <label>Thurs</label>
                                
                        </div>
            <div class="col-md-5" style="margin-top: -7px;width: 140px;">     
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" name="tc_thursday_in" value="{{$thursday_in}}"   autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>
                
                        </div>
                    
                </div>
            </div>
            <div class="col-md-5" style="margin-top: -7px;">
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" class="form-control" value="{{$thursday_out}}"   autocomplete="Off" name="tc_thursday_out" placeholder="Time Out">
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
                            <input type="text" style="width: 100px;" name="tc_friday_in" value="{{$friday_in}}"   autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>
                
                        </div>     
                </div>
            </div>
            <div class="col-md-5" style="margin-top: -7px;">
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" class="form-control" value="{{$friday_out}}"  autocomplete="Off" name="tc_friday_out" placeholder="Time Out">
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
                            <input type="text" style="width: 100px;" name="tc_saturday_in" value="{{$satureday_in}}" autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                            </span>
                
                        </div>
                    
                </div>
            </div>
            <div class="col-md-5" style="margin-top: -7px;">
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" class="form-control" value="{{$satureday_out}}"  autocomplete="Off" name="tc_saturday_out" placeholder="Time Out">
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
                            <input type="text" style="width: 100px;" name="tc_sunday_in" value="{{$sunday_in}}"  autocomplete="Off" class="form-control" placeholder="Time In">
                            <span class="input-group-addon ">
                                <i class="fa fa-clock-o"></i>
                        </div>     
                </div>
            </div>
            <div class="col-md-5" style="margin-top: -7px;">
                    <div class="controls">
                        <div class="input-group transparent clockpicker col-md-8">
                            <input type="text" style="width: 100px;" class="form-control"  value="{{$sunday_out}}"    autocomplete="Off" name="tc_sunday_out" placeholder="Time Out">
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
   
    <button class="btn btn-success btn-cons" name="action" value="" type="submit"><i class="icon-ok"></i>Update</button> 
   
</div>

</div>
</form>
</div>
    <div id="menu1" class="tab-pane fade">
      <form class="form-no-horizontal-spacing" method="post" 
            action ="{{route('timecategories.update',$timecategories->id)}}"  id="form-condensed" novalidate="novalidate">
            {{ method_field('PUT')}}
            {{csrf_field()}}
      <h3>Roster</h3>
      <div class="row">
           <div class="col-md-12">

             <div class="col-md-6">
                  <div class="col-md-12">
                        <label class="form-label">Title<span style="color:red"> *</span></label>
                           <?php if($timecategories->time_cat_type=="2"){
                                   $title=$timecategories->title;
                                   $department_id=$timecategories->department_id;
                                   
                           }else{
                                    $title="";
                                    $department_id="";
                                 }
                                 ?>
                            <input name="roster_title" id="form3FirstName" value="{{$title}}" type="text" class="form-control" placeholder="Enter title">
                       @if($errors->any())
                          <p style="color:red">{{$errors->first('roster_title')}}</p>
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
               
                    <button class="btn btn-success btn-cons" name="action" value="" type="submit"><i class="icon-ok"></i>Update</button> 
           </div>
       </form>
      </div>
    </div>
@include('include.important')
@endsection