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
                    <li class="active" data-target="#step1">
                      <a> <span class="step">1</span> <span class="title">Basic information</span> </a>
                    </li>
                    <li data-target="#step2" class="">
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

                  <h4 class="semi-bold">Step 1 - <span class="light">Basic Information</span></h4>
                  <br>
                  <form method="post" action="{{route('employee.basic_info_store')}}" enctype="multipart/form-data" id="commentForm" novalidate="novalidate">
                    @csrf
                    <div class="row form-row">
                      <div class="col-md-6">
                                   
                        <label class="form-label"><strong>ID</strong> <span style="color:red">*</span></label>

                       <?php if($user==''){
                      
                          $value_id=  'value=""';
                          $value_name=  'value=""';
                          $value_fname=  'value=""';
                          $value_cnic=  'value=""';
                           $value_dob=  'value=""';
                           $value_fixed_salary=  'value=""';
                           $value_hours_rate_salary=  'value=""';

                       }
                       else{
                             $value_id= 'value="'.$user->id.'"';
                              $value_name= 'value="'.$user->name.'"';
                               $value_fname='value="'.$user->father_name.'"';
                               $value_cnic=  'value="'.$user->cnic.'"';
                                $value_dob=  'value="'.$user->dob.'"';
                                 $value_fixed_salary=   'value="'.$user->fixed_salary.'"';
                                 $value_hours_rate_salary=   'value="'.$user->hour_rate_salary.'"';


                       }

                       ?>

                        <input type="text" autocomplete="off" placeholder="ID" name="id" <?php echo $value_id ?> class="form-control" id="id">
                        <p id="user-text" style="color:red"></p>
                        @if($errors->any())
                        <p style="color:red">{{$errors->first('id','ID field is required')}}</p>
                        @endif     
                      </div>
                      <div class="col-md-6">
                         <label class="form-label">
                          <strong>Images</strong>
                         </label>
                          <input type="file" autocomplete="off"  name="image" class="form-control">
                         <p id="user-text" style="color:red"></p>
                      </div>
                    </div>  
                    <div class="row form-row">
                      <div class="col-md-6">
                        <label class="form-label"><strong>Name</strong> <span style="color:red">*</span></label>
                        <input type="text" autocomplete="off" placeholder="Name" name="name" <?php echo $value_name ?> class="form-control" id="txtFullName">
                        @if($errors->any())
                        <p style="color:red">{{$errors->first('name','Name field is required')}}</p>
                        @endif     
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Father Name</strong></label>
                        <input type="text" autocomplete="off" placeholder="Father Name" class="form-control"  <?php echo $value_fname ?> name="father_name" id="txtFirstName">

                      </div>
                    </div>
                     <div class="row form-row">
                      <div class="col-md-6">
                        <label class="form-label"><strong>Fixed Salary</strong></label>
                        <input type="text" autocomplete="off" placeholder="Fixed Salary" name="fixed_salary" <?php echo $value_fixed_salary ?> class="form-control" id="txtFullName">
                          
                      </div>
                      
                      <div class="col-md-6">
                        <label class="form-label"><strong>Salary Hour Rate</strong></label>
                        <input type="text" autocomplete="off" placeholder="Hour Rate" class="form-control"  <?php echo $value_hours_rate_salary ?> name="salary_hour_rate" id="txtFirstName">

                      </div>
                    </div>
                
                    <div class="row form-row">
                     <div class="col-md-6">
                       <label class="form-label"><strong>CNIC</strong></label>
                       <input type="text" autocomplete="off" placeholder="CNIC"  <?php echo $value_cnic ?>  class="form-control" name="cnic"  id="tin">
                     </div>
                   
                     <div class="col-md-6">
                       <div style="margin-top:8px;width:490px"  class="input-append success date no-padding">
                        <label class="form-label"><strong>DOB <span style="color:red">*</span>&nbsp;&nbsp; <span id="append_age" style="margin-bottom:2px;font-size:20px" class=" badge badge-disable"></span></strong></label>
                        <input type="hidden" id="age_hidden" value="" name="age">
                        <input type="text"   autocomplete="off" name="dob" <?php echo $value_dob ?> id="date_of_birth"    placeholder="From Date" class="form-control">
                        @if($errors->any())
                        <p style="color:red">{{$errors->first('dob','Dob field is required')}}</p>
                        @endif 
                        <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                      </div>
                    
                      <!--  <span  style="font-size: 20px;color:white;background-color: #22262e"></span> -->
                    </div>

                  </div>
                  @if(!is_null($user))
                    <input type="hidden" value="{{ $user->id }}" name="found_user_id">
                  @endif
                  <div class="row form-row">
                  
                    <div class="col-md-6" >
                      <div class="form-group">
                          <label class="form-label"><strong>Religion</strong></label>
                        <div class=" right">
                            <i class=""></i>
                            <select name="religion" id="religion"  class="col-md-12">
                              <option value="">Please Select...</option> 
                              <option value="">Islam</option>
                              <option value="">Hindu</option>
                              <option value="">Christian</option>
                              <option value="">Others</option>      
                            </select>             
                            @if($errors->any())
                            <p style="color:red"><!-- {{$errors->first('contract_type','Contract type field is required')}} --></p>
                            @endif      
                        </div>
                      </div>
                    </div>
                  <div class="col-md-6">
                    <label class="form-label"><strong>Gender</strong></label>
                  <div class="radio">
                      <?php if($user==''){ ?>
                        <input id="male" type="radio"  name="gender" value="male"   checked="checked" >
                        <label for="male">Male</label>

                        <input id="female" type="radio" name="gender"   value="female">
                        <label for="female">Female</label>
                      <?php } else {  ?>

                       <input id="male" type="radio" name="gender" value="male"  <?php echo ($user->gender=='male') ? 'checked':'' ?> >
                        <label for="male">Male</label>

                        <input id="female" type="radio" name="gender" <?php echo ($user->gender=='female') ? 'checked':'' ?>    value="female" >
                        <label for="female">Female</label>

                   <?php  } ?>
                  </div>
              </div>
         </div>
         <br>
           <div class="row form-row">
                      <div class="col-md-6">
                        <label class="form-label"><strong>Fixed Salary</strong></label>
                        <input type="text" autocomplete="off" placeholder="Fixed Salary" name="fixed_salary" <?php echo $value_fixed_salary ?> class="form-control" id="txtFullName">
                          
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
                        <input type="text" autocomplete="off" placeholder="Over time" class="form-control"  <?php echo $value_hours_rate_salary ?> name="salary_hour_rate" id="txtFirstName">
                        </div>
                      </div>  
            
             <!--    <div class="row form-row">
                   <div class="col-md-6">
                    <label class="form-label"><strong>Religion</strong></label>
                    <div class="radio">
                    <?php if($user==''){ ?>
                      <input id="muslim" type="radio" name="religion" value="male"   checked="checked" >
                      <label for="muslim">Muslim</label>
                      <input id="nonmuslim" type="radio" name="religion"  value="female">
                      <label for="nonmuslim">Non Muslim</label>
                    <?php } else {  ?>
                     <input id="muslim" type="radio" name="religion" value="male" >
                      <label for="muslim">Muslim</label>
                      <input id="nonmuslim" type="radio" name="religion" value="female" >
                      <label for="nonmuslim">Non Muslime</label>
                   <?php  } ?>
                    </div>
                  </div>
                </div> -->
          
               <div class="form-actions">
                <div class="pull-right">
                  <button class="btn btn-danger btn-cons" id="basic_action_save" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
                   <button class="btn btn-success btn-cons" id="basic_action_save_next" name="action" value="save_next" type="submit"><i class="icon-ok"></i>Save & Next</button>
                </div>
              </div>
                <!--  <button class="btn btn-success" type="submit" name="action" value="save_and_close" >Register & AddNew</button> -->

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

@include('include.important')
@endsection