  @extends('layout.layout')
  @section('title','Update Employee')
  @section('content')
  <div class="row">
   <div class="col-md-12">
    <div class="grid simple form-grid">

     <div class="grid-body no-border">
      <form class="form-no-horizontal-spacing"  method="post" 
      action="{{route('users.update',$user->id)}}" enctype="multipart/form-data" id="form-condensed" novalidate="novalidate">
      @csrf
      {{method_field('Put')}}
      <h4 style="font-family: bold">Basic Information</h4>
      <div class="row column-seperation">
        
       <h4></h4>	
       <br>
       <div class="col-md-6" style="border-right: none">
         <div class="row form-row">
          <div class="col-md-12">
            <label class="form-label"><strong>Name</strong> <span style="color:red">*</span></label>
            <input type="text" autocomplete="off" placeholder="Name" name="name" value="{{$user->name}}" class="form-control" id="txtFullName">
            @if($errors->any())
            <p style="color:red">{{$errors->first('name','Name field is required')}}</p>
            @endif     
          </div>
        </div>
      </div>
    <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
        <div class="col-md-12">
          <label class="form-label"><strong>Father Name</strong></label>
          <input type="text" autocomplete="off" placeholder="Father Name" class="form-control " value="{{$user->father_name}}" name="father_name" id="txtFirstName">
        </div>
      </div>
    </div>
    <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
        <div class="col-md-12">
          <label class="form-label"><strong>CNIC</strong></label>
         <input type="text" value="{{$user->cnic}}" placeholder="CNIC" autocomplete="off" class="form-control" name="cnic"  id="tin">
        </div>
      </div>
    </div>
    <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
        <div class="col-md-12">
          <div style="margin-top:0px;" class="input-append success date no-padding col-md-11">
          <label class="form-label"><strong>DOB</strong></label>
          <input type="text" autocomplete="off" name="dob" value="{{$user->dob}}" placeholder="From Date" class="form-control ">
          <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
        </div>
        </div>
      </div>
    </div>
     <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
        <div class="col-md-12">
          <label class="form-label"><strong>Fixed Salary</strong></label>
         <input type="text" value="{{$user->fixed_salary}}" placeholder="Fixed Salary" autocomplete="off" class="form-control" name="fixed_salary" >
        </div>
      </div>
    </div>
     <div class="row form-row">
                        <div class="col-md-2" >
                         <label>Hourly Rate</label>
                          <input type="checkbox" name="overtime_check" id="overtime_check" value="1">
                       </div>
      </div>
                      <div class="row form-row" id="overtime_show_field" style="display: none">  
                        <div class="col-md-6">
                        <label class="form-label"><strong>Over time</strong></label>
                        <input type="text" autocomplete="off" value="{{$user->hour_rate_salary}}" placeholder="Over time" class="form-control"   name="hour_rate_salary" id="txtFirstName">
                        </div>
                      </div> 
     

    <div class="row form-row">
        <div class="col-md-6" style="border-right: none;">
        <label class="form-label"><strong>Gender</strong></label>
        <div class="radio">
        <input id="male" type="radio" name="gender" value="male" checked="checked">
        <label for="male">Male</label>
        <input id="female" type="radio" name="gender"   value="female">
        <label for="female">Female</label>
        </div>
        </div> 
      </div>
    <h4 style="font-family:bold">Department Information</h4>
    <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
        <div class="col-md-12">
         
            <label class="form-label"><strong>Role</strong> <span style="color:red">*</span></label>
            <div class=" right">
              <i class=""></i>
              <select  autocomplete="off" class="form-control select2" id="cardType" name="role_id" id="role_id" data-init-plugin="select2" required>

                <option value="">Please Select...</option>
                @foreach($role as $r)
                <option name=""  value="{{$r->id}}" <?php echo ($r->id==6) ? 'selected':'' ?>>{{$r->title}}</option>
                @endforeach
              </select>
              @if($errors->any())
              <p style="color:red" id="role_msg"></p>
              @endif
            </div>
        
        </div>
      </div>
    </div>
     <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
        <div class="col-md-12">
        
        <label class="form-label"><strong>Designation</strong> <span style="color:red">*</span></label>
        <div class=" right">
          <i class=""></i>
          
          <select autocomplete="off"  class="form-control select2" id="cardType" name="designation_id" id="" data-init-plugin="select2" required>
            <option value="">Please Select...</option>

            @foreach($designation as $d)
            <option name=""  value="{{$d->id}}" <?php echo ($d->id==$user->designation_id) ? 'selected':'' ?>>{{$d->title}}</option>
            @endforeach
          </select>
          @if($errors->any())
          <p style="color:red">{{$errors->first('designation_id','Designation field is required')}}</p>
          @endif
        </div>
    
        </div>
      </div>
    </div>
     <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
           <div class="col-md-12">
          <label class="form-label"><strong>Branch</strong> <span style="color:red">*</span></label>
          <div class=" right">
            <i class=""></i>
            <select  class="form-control select2" autocomplete="off" id="" name="branch" >
              <option value="">Please Select...</option>

              @foreach($branch as $value)
              <option name="" value="{{$value->branch_id}}" <?php echo ($value->branch_id==$user->branch_id) ? 'selected':'' ?>>{{$value->branch_name}}</option>
              @endforeach
            </select>
            @if($errors->any())
            <p style="color:red">{{$errors->first('branch','Branch field is required')}}</p>
            @endif
          </div>
        </div>
      </div>
      </div>
      <div class="col-md-6" style="border-right: none;">
        <div class="row form-row">
          <div class="col-md-12">
               <label class="form-label"><strong>Files #</strong></label>
              <input type="text" autocomplete="off" placeholder="File Number" class="form-control" value="{{$user->file_number}}"  name="file_number" id="txtPhoneNumber">
          </div>
        </div>
      </div>
        <div class="col-md-6" style="border-right: none;">
        <div class="row form-row">
          <div class="col-md-12">
               <label class="form-label"><strong>Service Scale</strong></label>
          <input type="text" placeholder="Service Scale" class="form-control" name="bps" value="{{$user->bps}}" id="txtPhoneNumber">
          </div>
        </div>
      </div>
           <div class="col-md-6" style="border-right: none;">
        <div class="row form-row">
           <div class="col-md-12">
           <label class="form-label"><strong>Department</strong><span style="color:red"> *</span></label>
       
          <div class=" right">
          <i class=""></i>
          <select  class="form-control select2" autocomplete="off" id="select_by_main_dep" class="select_by_main_dep" name="deparment_id"  data-init-plugin="select2"   required>
          <option value="">Please Select...</option>
          @foreach($department as $d)
          <option name="" value="{{$d->id}}" <?php echo ($d->id==$user->department_id) ? 'selected':'' ?> >{{$d->name}}</option>
          @endforeach
          </select>
          @if($errors->any())
          <p style="color:red">{{$errors->first('deparment_id','Department field is required')}}</p>
          @endif
          </div> 
          </div> 
         
        </div>
      </div>

      <div class="col-md-6" style="border-right: none;">
        <div class="row form-row">
         <div class="col-md-12">
           <label class="form-label"><strong>Employee Transfer to Department</strong><span style="color:red"></span></label>

           <div class=" right">
            <i class=""></i>
            <select  class="form-control select2" autocomplete="off" id="sub_dep_unit" name="sub_dep_unit"  required>
              <option value="">Please Select...</option>
            </select>
          </div> 
        </div> 

      </div>
    </div>
  </div> 

  <div class="row column-seperation">

    </div>  
     <h4 style="font-family: bold">Roster Information</h4>
     <div class="row form-row">
      <div class="col-md-6" style="border-right: none;">
      <label class="form-label"><strong>Attendance Mode</strong></label>
      <div class="col-md-6">
      <div class="radio radio-success">
      <input id="yes" type="radio"  <?php echo (1==$user->flag) ? 'checked':'' ?> class="checkbox_type_roster" name="roster" value="1">
      <label for="yes">Non Roaster</label>
      <input id="no" type="radio"  <?php echo (2==$user->flag) ? 'checked':'' ?> class="checkbox_type_non_roster"  name="roster" value="2"  >
      <label for="no">Roaster</label>
      </div>
      </div>
      </div>
     <div class="col-md-6" style="border-right: none;">
        <div class="row form-row">
           <div class="col-md-12">
          <label class="form-label"><strong>Time Category</strong></label>
          <i class=""></i>
          <select id="" class="col-md-12" autocomplete="off" name="tcat_id">
           @foreach($timecategory as $t)
           <option name="" value="{{$t->id}}" <?php echo ($t->id==$user->department_id) ? 'selected':'' ?>>
              {{$t->title}}
           </option> 
           @endforeach 
          </select>
          </div>   
        </div>
     </div>
     <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
        <div class="col-md-12">
         
            <label class="form-label"><strong>Leave Type</strong> <span style="color:red">*</span></label>
            <div class=" right">
              <i class=""></i>
                <select name="leave_type" id="leave_type" autocomplete="off"  class="select2 form-control">
                 <option>Please Select...</option>

                @foreach($leave as $l)
                <option value="{{$l->id}}">{{$l->title}}</option>
                @endforeach
                </select>
                 <p id="lev_msg" style="color:red"></p> 
            </div>
        </div>
      </div>
    </div>
        <div class="col-md-3" style="border-right: none;">
        <div class="row form-row">
          <div class="col-md-12">
               <label class="form-label"><strong>Leave number</strong></label>
              <input type="text" placeholder="" autocomplete="off" class="form-control" value=""  name="file_number" id="total_leave">
          </div>
        </div>
      </div>
      <div class="col-md-3" style="border-right: none;">
        <div class="row form-row">
          <div class="col-md-12">
                <button class="btn btn-default btn-cons" style="position: relative;top:27px;" name="action" id="leave_config_btn" value="save" type="button">
            <i class="icon-ok"></i>Add</button>
             <!--  <input type="text" placeholder="leave number" class="form-control" value=""  name="file_number" id="total_leave"> -->
          </div>
        </div>
      </div>
       <!-- <div class="col-md-3" style="border-right: none;">
          <div class="row form-row">
            <div class="col-md-12">
            <button class="btn btn-default btn-cons" style="position: relative;top:27px;" name="action" id="leave_config_btn" value="save" type="button">
            <i class="icon-ok"></i>Add</button> 
            </div>
          </div>
       </div> -->
       
           <div class="row-fluid">
             <span style="color:red;position: relative;top:41px;right: 519px;" id="append_exist_leave"></span>
                      <div class="span12">
                          <div class="grid simple" style="border:none;border:1px solid #dddddd21;">

                              <div class="grid-body" style="border:none;border:1px solid #dddddd21;">
                                 <h4 id="emp_name"></h4>
                                  <table class="table dataTable" id="registered_participants" aria-describedby="example3_info">
                                      <thead id="tablehead"  style="background-color: #22262e;">
                                          <tr role="row">
                                                    
                                               
                                                  <th style="">Leave Type</th>
                                                 <th style="">Total Number</th>
                                                  <th style=""></th>

                                          </tr>
                                      </thead>
                                      <tbody role="alert" aria-live="polite" aria-relevant="all" id="leave_configuration_table">
                                                   
                                      </tbody>
                                  </table>
                              </div>
                         
                      </div>
                  </div>
  
    
  </div> 
  <div class="row column-seperation">
      <h4 style="font-family: bold" >Contact Information</h4>
    <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">      
           <div class="col-md-12">
             <label class="form-label"><strong>Moblie # </strong> <span style="color:red">*</span></label>
            <input type="text" placeholder="Modbile Number" autocomplete="off" name="mobile_number" value="{{ $user->mobile_number}}" class="form-control" id="mob">
            <p id="user-text" style="color:red"></p>
             </div> 
        </div>
      </div>

        <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
        
           <div class="col-md-12">
                <label class="form-label"><strong>Moblie # 2</strong></label>
              <input type="text" placeholder="Mobile Number" id="mob2" autocomplete="off"  class="form-control" value="{{$user->mobile_number_2}}" name="mobile_number_2">
              <p id="user-text" style="color:red"></p>
             </div> 
        </div>
      </div>
       <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
        
           <div class="col-md-12">
             <label class="form-label"><strong>Land Line No</strong></label>
             <input type="text" placeholder="Land line number" class="form-control" autocomplete="off" value="" name="{{$user->land_line_number}}"  id="phone"> 
             </div> 
        </div>
      </div>
       <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
           <div class="col-md-12">
          <label class="form-label"><strong>Email</strong></label>
          <input type="email" placeholder="Email" class="form-control" autocomplete="off" value="{{$user->email}}" name="email" id="txtPhoneNumber"> 
             </div> 
        </div>
      </div>
       <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
           <div class="col-md-12">
             <label class="form-label"><strong>Address</strong></label>
                <textarea rowspan="6" class="col-md-12" name="addres">{{$user->address}}</textarea>
            </div> 
        </div>
      </div>    
      <div class="col-md-6" style="border-right: none;">
       <div class="row form-row">
           <div class="col-md-12">
               <label class="form-label"><strong>Image</strong></label>
              <input type="file"  name="image" value="{{$user->image}}" class="form-control">
              <p id="user-text" style="color:red"></p>
            </div> 
        </div>
      </div>  
     
  </div>
  <div>
   <div class="row column-seperation">

<!-- <div class="row form-row">
 
	<div class="col-md-6">
    <label class="form-label"><strong>Files #</strong></label>
    <input type="text" placeholder="File Number" class="form-control" value="{{$user->file_number}}"  name="file_number" id="txtPhoneNumber">
    <br>
    <label class="form-label"><strong>Service Scale</strong></label>
    <input type="text" placeholder="Service Scale" class="form-control" name="bps" value="{{$user->bps}}" id="txtPhoneNumber">
  </div>
  <div class="col-md-6">
    <label class="form-label"><strong>Attendance Mode</strong></label>
    <div class="col-md-6">
      <div class="radio radio-success">
        <input id="yes" type="radio"  <?php echo (1==$user->flag) ? 'checked':'' ?> class="checkbox_type_roster" name="roster" value="1">
        <label for="yes">Non Roaster</label>
        <input id="no" type="radio"  <?php echo (2==$user->flag) ? 'checked':'' ?> class="checkbox_type_non_roster"  name="roster" value="2"  >
        <label for="no">Roaster</label>
      </div>
    </div>
  </div>
  

</div> -->
<!--  <div class="row form-row">   
      <div class="col-md-6">
        <label class="form-label"><strong>Gender</strong></label>
        <div class="radio">

          <input id="male" type="radio" name="gender" value="male" checked="checked">
          <label for="male">Male</label>
          <input id="female" type="radio" name="gender"   value="female">
          <label for="female">Female</label>
        </div>
      </div>
    
  </div> -->

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
<script src="{{url('css/new_css/css/js/jquery-3.3.1.js')}}" type="text/javascript"></script> 
<script type="text/javascript">

 $("#leave_config_btn").click(function() {
   if($('#leave_type').val()=="" || $('#total_leave').val()=="" ){
       alert('leave type and total leave field is required');
   }
   else{
   var leave_type=$('#leave_type').val();
   // var department_id= $('#department_select').val();
   var total_leave=$('#total_leave').val();
    $('#append_exist_leave').text("");
     $("#emp_name").text("");
   response="";  
   var url = "{{ route('employee.leave_config',Request::segment(2)) }}";
    jQuery.ajax({
    url:url,
    type: 'POST',
    dataType: 'json',          
    data: {
      "_token": "{{ csrf_token() }}",                        
      "leave_type":leave_type,
       "total_leave":total_leave,
       // "department_id":department_id 
      },
       success:function(response){
            
             if(response.flag==1){

               $('#append_exist_leave').append(response.leave_name+' is already exist !')
                $("#emp_name").append("<span><b>Employee:</b> "+response.emp_leave_config[0].id+" - "+response.emp_leave_config[0].name+"</span>")


             }else{
                  $("#emp_name").append("<span><b>Employee:</b> "+response[0].id+" - "+response[0].name+"</span>")
                    $.each(response, function (i,value) {
                    $("table tbody").append("<tr style='background-color:#d8e0f1e3'><td>"+value.title+"</td><td>"+value.total_leaves+"</td><td><span  class='new_participant_form' ><label style='display:none;' id='configuration_id'>"+value.emp_leave_config_id+"</label><i class='fa fa-trash'></i></span></td></tr>");
                    $('#total_lev_msg').text("");
                    $('#total_leave').val("");
                    }); 
             }
     

       }



   });
   } 
  });

  $('document').ready(function(){
  
  var url = "{{ route('employee.leave_config_detail',Request::segment(2)) }}";
  jQuery.ajax({
    url:url,
    type: 'POST',
    dataType: 'json',          
    data: {
      "_token": "{{ csrf_token() }}",                        
       // "department_id":department_id 
     },
      success:function(response){
          $.each(response, function (i,value) {
          $("table tbody").append("<tr style='background-color:#d8e0f1e3'><td>"+value.title+"</td><td>"+value.total_leaves+"</td><td><span  class='new_participant_form' ><label style='display:none;' id='configuration_id'>"+value.emp_leave_config_id+"</label><i class='fa fa-trash'></i></span></td></tr>");
           $('#total_lev_msg').text("");
           $('#total_leave').val("");
          

          }); 
       
      }   
  });
  });
</script>
</div>
@include('include.important')
@endsection     