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
                          <li data-target="#step2" class="active">
                            <a> <span class="step">2</span> <span class="title">Department & Service Information</span> </a>
                          </li>
                          <li data-target="#step3" class="">
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
 <h4 class="semi-bold">Step 2 - <span class="light">Service Information</span></h4>
 
<?php
 $id  = Request::segment(3);
?>
 <form method="post" action="{{url('employee/dep_info_store',$id)}}" id="commentForm" novalidate="novalidate">
  {{ method_field('PUT')}}
  {{csrf_field()}}     
  <div class="tab-content transparent">
    <div class="col-md-6">
       <div class="form-group">
         <label class="form-label"><strong>Role</strong><span style="color:red"> *</span></label>
        <div class=" right">
          <i class=""></i>
            <select name="role_id" id="role_id"  style="width: 66%" class="">
                 <option value="">Please Select...</option>
                  @foreach($role as $r)
                  <option name="" autocomplete="off"    value="{{$r->id}}" <?php echo ($r->id==6) ? 'selected':'' ?>>{{$r->title}}</option>
                  @endforeach
             </select>
                @if($errors->any())
                <p style="color:red">{{$errors->first('role_id','Role field is required')}}</p>
                @endif
         </div>
       </div>
  <div class="form-group">
       <label class="form-label"><strong>Designation</strong><span style="color:red"> *</span></label>
      <div class=" right">
        <i class=""></i>
          <select name="designation_id" id="designation_id" autocomplete="off"  style="width: 66%" class="">
              <option value="">Please Select...</option>
              @foreach($designation as $d)
              <option name=""  value="{{$d->id}}" <?php echo ($d->id==$emp->designation_id) ? 'selected' : '' ?>>{{$d->title}}</option>
              @endforeach
           </select>             
          @if($errors->any())
          <p style="color:red">{{$errors->first('designation_id','Designation field is required')}}</p>
          @endif      
       </div>
   </div>
     <div class="form-group">
         <label class="form-label"><strong>Branch</strong><span style="color:red"> *</span></label>
        <div class=" right">
          <i class=""></i>
            <select name="branch" id="branch_id" autocomplete="off"  style="width: 66%" class="">
                <option value="">Please Select...</option>
                @foreach($branch as $value)
                <option name=""  value="{{$value->branch_id}}"  <?php echo ($value->branch_id==$emp->branch_id) ? 'selected' : '' ?>>{{$value->branch_name}}</option>
               @endforeach
             </select>             
            @if($errors->any())
            <p style="color:red">{{$errors->first('branch','Branch field is required')}}</p>
            @endif      
         </div>
     </div>
</div>
 
  <div class="col-md-6">
    <label class="form-label"><strong>Personal Files #</strong></label>
      <input type="text" autocomplete="off" placeholder="File Number" class="form-control" value="{{$emp->file_number}}"  name="file_number" id="personal_file_no">
      <p style="color:red" id="append_file_no_msg"></p>
      <br>
     <label class="form-label"><strong>Service Scale</strong></label>
    <input type="text" autocomplete="off" placeholder="Service Scale" class="form-control" name="bps" value="{{$emp->file_number}}" id="txtPhoneNumber">

  </div>
     
     <div class="col-md-6" style="margin-top: 10px">
        <div class="form-group">
         <label class="form-label"><strong>Contract Type</strong><span style="color:red"> *</span></label>
        <div class=" right">
          <i class=""></i>
            <select name="contract_type" id="contract_type"  class="col-md-12">
                    <option value="">Please Select...</option>    
                   <option value="1" <?php echo (1==$emp->contract_type) ? 'selected' : '' ?>>Deputation</option>
                   <option value="2" <?php echo (2==$emp->contract_type) ? 'selected' : '' ?>>Work Charges</option>
                   <option value="3" <?php echo (3==$emp->contract_type) ? 'selected' : '' ?>>Contract</option>
                   <option value="4" <?php echo (4==$emp->contract_type) ? 'selected' : '' ?>>Regular</option>
             </select>             
            @if($errors->any())
            <p style="color:red">{{$errors->first('contract_type','Contract type field is required')}}</p>
            @endif      
          </div>
        </div>
     </div>
    </div>
    <div class="form-actions" id="department_form_action">
      <div class="pull-right">
        <a class="btn btn-info btn-cons" name="action" value="save_next" href="{{url('employee/basic_info',Request::segment(3))}}"><i class="icon-ok"></i>Back</a>
        <button class="btn btn-danger btn-cons" name="action" value="save" id="dep_form" type="submit"><i class="icon-ok"></i> Save</button>
        <button class="btn btn-success btn-cons" name="action" value="save_next" id="dep_form" type="submit"><i class="icon-ok"></i>
        Save & Next
        </button>
        
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
</div>
<script src="{{url('template_files/theme/assets/plugins/jquery/jquery-1.11.3.min.js')}}" type="text/javascript"></script>
<script type="text/javascript">
</script>
@include('include.important')
@endsection