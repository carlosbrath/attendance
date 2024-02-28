@extends('layout.layout')
 @section('title','Employee Information')
 @section('content')
 <div class="row-fluid">
  <div class="col-md-12">
    <div class=" tiles white col-md-12 no-padding">
      <div class="tiles green cover-pic-wrapper">
        <div class="overlayer bottom-right">
          <div class="overlayer-wrapper">
          </div>
        </div>
        <img src="{{url('template_files/theme/assets/img/cover_pic.png')}}" alt="">
      </div>
      <div class="tiles white">
        <div class="row">
          <div class="col-md-3 col-sm-3">
            <?php if($user !=null && $user->image !='' ){ ?>
            <div class="user-profile-pic">
              <img style="width: 100px !important; height: 100px !important;" data-src-retina="{{ asset('emp_images') }}/{{ $user->image }}" data-src="<?php echo asset("photos/$user->image")?>" 
               src="<?php echo asset("photos/$user->image")?>"   alt="">
            </div>
           <?php } 
           else { ?>
           
            <div class="user-profile-pic">
              <img style="width: 100px !important; height: 100px !important;" data-src-retina="" data-src="" 
             src="" alt="">
            </div>
            <?php } ?>
          </div><br>
          <div class="col-md-8 user-description-box  col-sm-8" style="width: 97%;
    height: 100%;
    text-align: center;
    float: right;
    border-radius: 10px 10px 10px 10px;
    border: 2px solid;">
    <div class="row">
          <div class="col-sm-6">
            <div class="row mgbt-xs-0">
              <label class="col-xs-5 control-label">First Name:</label>
              <div class="col-xs-7 controls"><strong>{{ucfirst($user->user_name)}}</strong></div>
              <!-- col-sm-10 --> 
            </div>
          </div>
           <div class="col-sm-6">
            <div class="row mgbt-xs-0">
              <label class="col-xs-5 control-label">DOB:</label>
              <div class="col-xs-7 controls"><strong>{{$user->dob}}</strong></div>
              <!-- col-sm-10 --> 
            </div>
          </div>
          <div class="col-sm-6">
            <div class="row mgbt-xs-0">
              <label class="col-xs-5 control-label">CNIC:</label>
              <div class="col-xs-7 controls"><strong>{{$user->cnic}}</strong></div>
              <!-- col-sm-10 --> 
            </div>
          </div>
          <div class="col-sm-6">
            <div class="row mgbt-xs-0">
              <label class="col-xs-5 control-label">Designations:</label>
              <div class="col-xs-7 controls"><strong>{{ucfirst($user->designation_title)}}</strong></div>
              <!-- col-sm-10 --> 
            </div>
          </div>
          <div class="col-sm-6">
            <div class="row mgbt-xs-0">
              <label class="col-xs-5 control-label">Department:</label>
              <div class="col-xs-7 controls"><strong>{{ucfirst($user->department_name)}}</strong></div>
              <!-- col-sm-10 --> 
            </div>
          </div>
          <div class="col-sm-6">
            <div class="row mgbt-xs-0">
              <label class="col-xs-5 control-label">Service Scale:</label>
              <div class="col-xs-7 controls"><strong>{{$user->bps}}</strong></div>
              <!-- col-sm-10 --> 
            </div>
          </div>
          <div class="col-sm-6">
            <div class="row mgbt-xs-0">
              <label class="col-xs-5 control-label">File Number:</label>
              <div class="col-xs-7 controls"><strong>{{$user->file_number}}</strong></div>
              <!-- col-sm-10 --> 
            </div>
          </div>
          <div class="col-sm-6">
            <div class="row mgbt-xs-0">
              <label class="col-xs-5 control-label">Email:</label>
              <div class="col-xs-7 controls"><strong>{{$user->email}}</strong></div>
              <!-- col-sm-10 --> 
            </div>
          </div>
           <div class="col-sm-6">
            <div class="row mgbt-xs-0">
              <label class="col-xs-5 control-label">Father Name:</label>
              <div class="col-xs-7 controls"><strong>{{ucfirst($user->father_name)}}</strong></div>
              <!-- col-sm-10 --> 
            </div>
          </div>
          <div class="col-sm-6">
            <div class="row mgbt-xs-0">
              <label class="col-xs-5 control-label">Mobile:</label>
              <div class="col-xs-7 controls"><strong>{{$user->mobile_number}}</strong></div>
              <!-- col-sm-10 --> 
            </div>
          </div>
        </div>
      </div>
   </div>                
      <div class="row">  <!-- start row --> 
      <div class="col-md-7 user-description-box  col-sm-7" style="
                          height: 100%;
                          text-align: center;
                          border-radius: 10px 10px 10px 10px;
                        border: 2px solid;">  
                <form method="post" action="{{route('emp.files')}}" enctype="multipart/form-data">         
                  <div class="row form-row"> 
                        {{csrf_field()}} 
                      <div id="emp_file_add_sec">
                          <div class="col-md-12">
                              <div class='d_leaves_row'>
                              
                                      <div class="col-md-2">
                                       
                                          <input type="text" class="form-control" value="" required name="name[]" placeholder=" Name" >
                                      </div>
                                      <div class="col-md-3">
                                          <input type="date"  style="width:100%" name="issue_date[]" placeholder="Issue Date"  >
                                      </div>
                                      <div class="col-md-3">
                                       
                                          <input type="date"   style="width:100%"  name="expire_date[]" placeholder="Expire Date"  >
                                      </div>
                                      
                                      <div class="col-md-2">
                                        <label class="form-label"><strong></strong> <span style="color:red"></span></label>
                                          <input type="file" height="50px" style="border:none;width:93px!important"  id='userfiles' required  name="userfiles[]" placeholder=""  >
                                      </div>
                                       <input type="hidden" name="user_id" value="{{$user->user_id}}">
                                      <div class="col-md-2">
                                          <button class="btn btn-success add_file_row" style="width:70%" type="button" style="margin-top:4px">
                                              <i class="fa fa-plus"></i>
                                          </button>
                                      </div>
                                   
                                </div>
                            </div>  
                        </div>
                    </div>
                    <div class="row form-row"> 
                          <div class="col-md-12">
                             <div class="col-md-2">
                                <button style="min-width:79px!important" class="btn btn-primary btn-cons btn-sm" type="submit">Add</button>
                              </div>
                           </div>
                    </div>                                
                </form>  
            </div>

            <div class="col-md-4 user-description-box  col-sm-4"> 
             @if($user_Files)
              @foreach($user_Files as $key => $file )              
                    <div class="row" style="color:{{checkEmployeeAttachmentExpireValidity($file->issue_date,$file->expire_date )}}">
                        
                        <div class="col-sm-9" style="padding-left:10px">
                           <div style="display:flex; flex-wrap: wrap;">
                              <div class="dd-handle"><input type="checkbox" checked></div>
                              <i class="fa fa-file" style="font-size:15px;padding-left:2px"></i>
                              <p style="margin-bottom:0px;padding-left:6px">{{$file->name}}</p>
                              &nbsp;  &nbsp;
                              <p>{{ \Carbon\Carbon::parse($file['date'])->format('d M, Y h:i a') }}    <p>
                           </div>
                       
                          
                        </div>
                        <div class="col-sm-2">
                        
                         <a href="{{ route('emp_file.download',$file->id) }}"><i class="fa fa-download"></i></a> |
                         <a href="{{ route('emp_file.delete',$file->id) }}"><i class="fa fa-trash"></i></a>
                          <p><small> {{ formatSizeUnits($file['file_size']) ?? '' }}</small></p>
                        </div> 
                        <div class="row">
                        </div>     
                   </div> 
                @endforeach  
              @endif        
             </div>
         </div> <!-- end row -->
        </div>
      </div>
    </div>   
   </div>
  </div>
 
</div>

   

@endsection