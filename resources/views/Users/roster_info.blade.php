       @extends('layout.layout')
       @section('title','Add Employee')
       @section('content')
       @if(session('message'))
       <div class="alert alert-success" style="text-align: center;">{{session('message')}}
       </div>
       @endif

      
<div class="row">
  <div class="col-md-12">
    <div class="grid simple form-grid">
      <div class="grid-title no-border">                    
      </div>
      <div class="grid-body no-border">
       <div class="col-md-12">
        <div class="grid simple transparent">

          <div class="grid-body ">
            <div class="row">
              <div id="rootwizard" class="col-md-12">
                <div class="form-wizard-steps">
                  <ul class="wizard-steps form-wizard">
                    <li class="" data-target="#step1">
                      <a> <span class="step">1</span> <span class="title">Basic information</span> </a>
                    </li>
                    <li data-target="#step2" class="">
                      <a> <span class="step">2</span> <span class="title">Department & Service Information</span> </a>
                    </li>
                    <li data-target="#step3" class="active">
                      <a> <span class="step">3</span> <span class="title">Time & Roster Information</span> </a>
                    </li>
                    <li data-target="#step4" class="">
                      <a> <span class="step">4</span> <span class="title">Contact Information</span> </a>
                    </li>

                  </ul>
                  <div class="clearfix"></div>
                </div>
            <div class="tab-content transparent">

                  <br>
                  <br>
                        <h4 class="semi-bold">Step 3 - <span class="light">Time & Roster Information</span></h4>
                        <br>
 
                        <form method="post" action="{{url('employee/roster_store', Request::segment(3))}}" id="commentForm" novalidate="novalidate">
                        {{ method_field('PUT')}}
                        {{csrf_field()}}    
                        <div class="row form-row">
                            <div class="col-md-12">
                                <label class="form-label"><strong>Attendance Mode</strong></label>
                                <div class="col-md-8">
                                    <div class="radio radio-success">
                                        <input id="yes" type="radio" checked="" class="checkbox_type_roster" name="roster" value="1">
                                        <label for="yes">Non Roaster</label>
                                        <input id="no" type="radio"  class="checkbox_type_non_roster"  name="roster" value="2"  >
                                        <label for="no">Roaster</label>
                                    </div>
                                </div>
                              
                            </div>
                        </div>          
                        <div class="row form-row"> 
                          <div class="col-md-6">
                            <div class="form-group">
                               <label class="form-label"><strong>Department</strong><span style="color:red"> *</span></label>
                            <div class=" right">
                                <i class=""></i>
                                  <select name="deparment_id" autocomplete="off" id="department_select"  style="width: 66%" class="employee_deparments select2 form-control">
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
                       <div class="col-md-6" id="time_category">
                          <div class="form-group">
                                 <label class="form-label"><strong>Time Category</strong></label>
                              <div class=" right">
                                  <i class=""></i>
                                    <select name="tcat_id" style="width: 66%" id="user_timecategory" class=select2 form-control">
                                        <option value="">
                                         Please Select...
                                        </option>
                                        @foreach($timecategory as $t)
                                         <option name=""  value="{{$t->id}}">{{$t->title}}
                                        </option>
                                         @endforeach
                                     </select>
                                     @if($errors->any())                                     
                                     <p style="color:red">
                                    {{$errors->first('tcat_id','Time Category field is required')}}
                                  </p>
                                  @endif   
                               </div>
                            </div> 
                        
                    </div>
                </div>
                 <div class="row form-row" id="emp_leave_config_panel">
                        <div class="col-md-4">
                            <div class="form-group">
                              <label class="form-label"><strong>Leave Type</strong><span style="color:red"> *</span></label>

                              <div class=" right">
                              <i class=""></i>
                              <select name="leave_type" id="leave_type"   class="select2 form-control">
                                <option value="">Please Select...</option>

                                @foreach($leave as $l)
                                <option value="{{$l->id}}">{{$l->title}}</option>
                                @endforeach
                              </select>
                              </div>
                               @if($errors->any())
                              <p style="color:red">
                                    {{$errors->first('leave_type','Leave field is required')}}
                                  </p>
                              @endif 
                            </div> 
                           </div>
                              <div class="col-md-5">
                                <label class="form-label"><strong>Total Leave</strong></label>
                                   <input id="total_leave" value="{{old('leave_number')}}" name="leave_number" type="number" >
                                    <button class="btn btn-default btn-cons" name="action" id="leave_config_btn" value="save" type="button">
                                      <i class="icon-ok"></i>Add</button>   
                                      <p id="total_lev_msg" style="color:red;"></p>    
                              </div>
                               <!--  @if($errors->any()) 
                                     <span style="color:red;position: relative;top:68px;right: 445px;">
                                    {{$errors->first('leave_number','Leave field is required')}}
                                  </span>
                              @endif  -->
                              <div class="col-md-3">
                                                    
                              </div>
                  </div>
                 <span style="margin-left:300px;font-size:30px" id="append_exist_leave"></span>
                   <div class="row-fluid">
                      <div class="span12">
                          <div class="grid simple ">

                              <div class="grid-body ">
                                 <h4 id="emp_name"></h4>
                                  <table class="table dataTable" id="registered_participants" aria-describedby="example3_info">
                                      <thead id="tablehead"  style="background-color: #22262e;display: none;">
                                          <tr role="row">
                                                    
                                               
                                                  <th style="color: white">Leave Type</th>
                                                 <th style="color: white">Total Number</th>
                                                  <th style="color: white"></th>

                                          </tr>
                                      </thead>
                                      <tbody role="alert" aria-live="polite" aria-relevant="all" id="leave_configuration_table">
                                                   
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                  </div>

                              
             </div>

          </div>      
                  <div class="col-md-4">
                            <div class="loader" id="preloader" style="display: none" >
                            </div>  
                  </div>  
                <div class="form-actions" id="roster_form_action">
                <div class="pull-right">
                    <a class="btn btn-info btn-cons" name="action" value="save_next" href="{{url('employee/dep_info_create',Request::segment(3))}}"><i class="icon-ok"></i>Back</a>
                  <button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
                  <button class="btn btn-success btn-cons" name="action" value="save_next" type="submit"><i class="icon-ok"></i>Save & Next</button>
                </div>
              </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<script src="{{url('css/new_css/css/js/jquery-3.3.1.js')}}" type="text/javascript"></script> 
<script type="text/javascript">
 $("#leave_config_btn").click(function() {
  if($('#total_leave').val()=="" || $('#leave').val()=="" ){
     
        // $('#total_lev_msg').append("Total leave field is required");
        // $('#roster_form_action').hide();
   }else{

  $("#emp_name").text("");
   $('#append_exist_leave').text("");
   var leave_type=$('#leave_type').val();
   // var department_id= $('#department_select').val();
   var total_leave=$('#total_leave').val();
   response="";  
   var url = "{{ route('employee.leave_config',Request::segment(3)) }}";
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
      $('#total_leave').val(response.total_leave);
   
     if(response.flag==1){
      // alert(response.leave_name);
      $('#append_exist_leave').append(response.leave_name+' is already exist !')
       $("#emp_name").append("<span><b>Employee:</b> "+response.emp_leave_config[0].id+" - "+response.emp_leave_config[0].name+"</span>")
      $("#tablehead").show();
       $("table tbody").text("");
      $.each(response.emp_leave_config, function (i,value) {
          $("table tbody").append("<tr style='background-color:#d8e0f1e3'><td>"+value.title+"</td><td>"+value.total_leaves+"</td><td><span  class='new_participant_form' ><label style='display:none;' id='configuration_id'>"+value.emp_leave_config_id+"</label><i class='fa fa-trash'></i></span></td></tr>");
           $('#total_lev_msg').text("");
         
      }); 

     }else{
      $("#emp_name").append("<span><b>Employee:</b> "+response[0].id+" - "+response[0].name+"</span>")
      $("#tablehead").show();
       $("table tbody").text("");
      $.each(response, function (i,value) {
          $("table tbody").append("<tr style='background-color:#d8e0f1e3'><td>"+value.title+"</td><td>"+value.total_leaves+"</td><td><span  class='new_participant_form' ><label style='display:none;' id='configuration_id'>"+value.emp_leave_config_id+"</label><i class='fa fa-trash'></i></span></td></tr>");
           $('#total_lev_msg').text("");
          

          }); 
        }
      
       


      } 
     });
     }      
  });
</script>
<script type="text/javascript">
$('#registered_participants').on('click', '.new_participant_form', function() {
      $("table tbody").text("");
     var id = $(this).children('#configuration_id').text();
     var url = "{{ route('employee.leave_config_delete',Request::segment(3)) }}";  
    var result = confirm("Are your want to delete total number of leave?");
   
if (result) {    
  jQuery.ajax({
    url:url,
    type: 'POST',
    dataType: 'json',          
    data: {
      "_token": "{{ csrf_token() }}",                        
       "id":id 
     },
    success:function(response){ 
      $.each(response, function (i,value) {
        $("table tbody").append("<tr style='background-color:#d8e0f1e3'><td>"+value.title+"</td><td>"+value.total_leaves+"</td><td><span class='new_participant_form'><label style='display:none;' id='configuration_id'>"+value.emp_leave_config_id+"</label><i class='fa fa-trash'></i></span></td></tr>");
         });
          $('#append_exist_leave').text(""); 
     } 
  }); 
}// end if(result)    
 });
</script>
</div>
@include('include.important')
@endsection