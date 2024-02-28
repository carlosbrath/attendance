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
                    <li data-target="#step3" class="">
                      <a> <span class="step">3</span> <span class="title">Time & Roster Information</span> </a>
                    </li>
                    <li data-target="#step4" class="active">
                      <a> <span class="step">4</span> <span class="title">Contact Information</span> </a>
                    </li>

                  </ul>
                  <div class="clearfix"></div>
                </div>
                <div class="tab-content transparent">

                  <br>
                  <br>

                  <h4 class="semi-bold">Step 4 - <span class="light">Contact Information</span></h4>
                  <br>
                  <form method="post" action="{{url('employee/contact_store',Request::segment(3))}}" enctype="multipart/form-data" id="commentForm" novalidate="novalidate">
                    {{ method_field('PUT')}}
                      {{csrf_field()}} 
                      
                    <div class="row form-row">
                      <div class="col-md-6">
                                                  
                        <label class="form-label"><strong>Moblie # </strong></label>
                        <input type="text" placeholder="Modbile Number" autocomplete="off" name="mobile_number" value="{{$emp->mobile_number}}" class="form-control" id="mob">
                        <p id="user-text" style="color:red"></p>
                        
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Emergency Cell # </strong></label>
                         <input type="text" placeholder="Mobile Number" id="mob2" autocomplete="off"  class="form-control" value="{{$emp->mob_number_2}}" name="mobile_number_2">
                        <p id="user-text" style="color:red"></p>

                      </div>
                    </div>  
                    <div class="row form-row">
                      <div class="col-md-6">
                          <label class="form-label"><strong>Land Line No</strong></label>
                      <input type="text" placeholder="Land line number" class="form-control" autocomplete="off" value="{{$emp->land_line_number}}" name="land_line_number"  id="phone"> 
                         
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Email</strong></label>
                        <input type="email" placeholder="Email"  class="form-control" autocomplete="off" value="{{$emp->email}}" name="email" id="txtPhoneNumber"> 
                        @if($errors->any())
                        <p style="color:red">{{$errors->first('email')}}</p>
                        @endif 
                      </div>
                    </div>
                    <div class="row form-row">                    
                     <div class="col-md-6">
                                <label class="form-label"><strong>Address</strong><span style="color:red"> *</span></label>
                                <input type="text" placeholder="Address" class="form-control" autocomplete="off" value="{{$emp->address}}" name="address" id="txtPhoneNumber">
                      @if($errors->any())
                       <p style="color:red">{{$errors->first('address')}}</p>
                      @endif 
                     </div>
                  </div>
               <div class="form-actions">
                <div class="pull-right">
                  <a class="btn btn-info btn-cons"  href="{{url('employee/roster_create',Request::segment(3))}}"><i class="icon-ok"></i>Back</a>
               <!--    <button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i>Save</button> -->
                  <button class="btn btn-success btn-cons" name="action" value="finish" type="submit"><i class="icon-ok"></i>Save & Finish</button>
                  <!-- <button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Register &amp; AddNew</button>
                  <button class="btn btn-white btn-cons" type="reset">Cancel</button> -->
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