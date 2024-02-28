       @extends('layout.layout')
       @section('title','Add Tehsil')
        @section('content')
           @if(session('message'))
               <div class="alert alert-success" style="text-align: center;">{{session('message')}}
                </div>
             @endif
          <style type="text/css">
          .form-actions {
          background-color: #f6f7f8;
          border: 0px;
          margin-bottom: 20px;
          margin-top: 0px;
          padding: 19px 20px 9px;
          }
          .grid {
          clear: both;
          margin-top: 0px;
          margin-bottom: 0px;
          padding: 0px;
          }
          </style>
                       
        <div class="row">
            <div class="col-md-12">
                <div class="grid simple form-grid">
                    <div class="grid-body no-border">
                        <form class="form-no-horizontal-spacing" method="post" action="{{route('branchs.store')}}"   id="form-condensed" novalidate="novalidate" enctype="multipart/form-data">
                             @csrf
                            <div class="row column-seperation">
                                <div class="col-md-8">
                                  <div class="row form-row">
                                    <div class="form-group">
                                      <label class="form-label"><strong>District</strong><span style="color:red"> *</span></label>
                                      <div class=" right">
                                      <i class=""></i>
                                      <select name="zone_name" id="zone_id"  style="width: 66%" class="employee_deparments select2 form-control">
                                      <option value="">
                                      Please Select...
                                      </option>
                                       @foreach($zone as $zone_id=>$zone_name) {
                                                <option  value="{{$zone_id}}">{{$zone_name}}</option>
                                                @endforeach
                                              </select>
                                              @if($errors->any())
                                              <p style="color:red">
                                                {{$errors->first('zone_name')}}
                                              </p>
                                        @endif
                                    </div>
                                  </div> 
                                    <!-- <div class="form-group">
                                      <label class="form-label"><strong>Zone Name <span style="color: red"> * </span></strong></label>
                                      <div class=" right">
                                        <i class=""></i>
                                        <select name="zone_name"  style="width: 66%" class="select2 form-control">
                                          <option value="">
                                             Please Select...
                                         </option>                        
                                          @foreach($zone as $zone_id=>$zone_name) {
                                                <option  value="{{$zone_id}}">{{$zone_name}}</option>
                                                @endforeach
                                              </select>
                                              @if($errors->any())
                                              <p style="color:red">
                                                {{$errors->first('zone_name')}}
                                              </p>
                                        @endif

                                 </div>
                             </div> -->

                         </div>
                                    <div class="row form-row">
                                        <div class="col-md-8">
                                            <label class="form-label"><strong>Tehsil Name <span style="color: red"> * </span> </strong></label>
                                            <input value="{{old('branch_name')}}" name="branch_name"  id="form3FirstName" autocomplete="off"  type="text" class="form-control" placeholder="Enter Branch">
                                        @if($errors->any())
                                        <p style="color:red">
                                                {{$errors->first('branch_name')}}
                                        </p>
                                        @endif
                                        </div>

                                    </div>
                                    <div class="row form-row">
                                        <div class="col-md-8">
                                           <label class="form-label"><strong>Description </strong></label>
                                             <textarea rowspan="6" class="col-md-12" value="{{old('branch_name')}}" name="branch_desc">{{old('branch_name')}} </textarea> 
                                                         
                                            <br>
                                            <div class="clearfix"></div>
                                       
                                        </div>
                                       
                                    </div>
                                    <div class="row form-row">
                                        <div class="col-md-8">
                                          <label class="form-label"><strong>Address 1 <span style="color: red"> * </span> </strong></label>
                                        <textarea rowspan="6" class="col-md-12" name="address1">{{old('address1')}}</textarea>
                                        @if($errors->any())
                                        <p style="color:red">
                                        {{$errors->first('address1')}}
                                        </p>
                                        @endif                                       
                                            <br>
                                            <div class="clearfix"></div>
                                       
                                        </div>
                                       
                                    </div>
                                     <div class="row form-row">
                                        <div class="col-md-8">
                                          <label class="form-label"><strong>Address 2</strong></label>
                                             <textarea rowspan="6" class="col-md-12" name="address2">
                                                {{old('address2')}}
                                              </textarea>                                        
                                            <br>
                                            <div class="clearfix"></div>
                                        </div>
                                       
                                    </div>

                                        </div>
                                    </div>

                                </div>

                    </div>
                   <div class="form-actions">

                        <div class="pull-left">
                            <button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
                            <button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Register & AddNew</button>
                            <button class="btn btn-white btn-cons" type="reset">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection