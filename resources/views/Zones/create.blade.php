       @extends('layout.layout')
       @section('title','Add District')
       @section('content')
       @if(session('message'))
       <div class="alert alert-success" style="text-align: center;">{{session('message')}}
       </div>
       @endif
       <div class="row">
        <div class="col-md-12">
          <div class="grid simple form-grid">
            <div class="grid-body no-border">
              <form class="form-no-horizontal-spacing" method="post" action="{{route('zones.store')}}"  id="form-condensed" novalidate="novalidate">
               @csrf
               <div class="row column-seperation">
                <div class="col-md-8">
                  <div class="row form-row">
                     <div class="form-group">
                               <label class="form-label"><strong>Region</strong><span style="color:red"> *</span></label>
                            <div class=" right">
                                <i class=""></i>
                                  <select name="region_name"   style="width: 66%" class="region select2 form-control">
                                      <option value="">
                                      Please Select...
                                      </option>
                                    @foreach($region as $region_id=>$region_name) {
                                    <option name="" value="{{$region_id}}">{{$region_name}}</option>
                                    @endforeach
                                    </select>
                                    @if($errors->any())
                                    <p style="color:red">{{$errors->first('region_name','Region field is required')}}</p>
                                    @endif  
                             </div>
                    </div> 
                 </div>
                 <div class="row form-row">
                  <div class="col-md-8">
                    <label class="form-label"><strong>District Name <span style="color:red"> * </span> </strong></label>
                    <input value="{{old('zone_name')}}" autocomplete="off" name="zone_name"  id="form3FirstName"  type="text" class="form-control" placeholder="Enter Branch">
                    @if($errors->any())
                    <p style="color:red">
                      {{$errors->first('zone_name')}}
                    </p>
                    @endif
                  </div>
                </div>
                <div class="row form-row">
                  <div class="col-md-8">
                   <label class="form-label"><strong>Description</strong></label>
                   <textarea rowspan="6" class="col-md-12"  name="zone_desc" >{{old('zone_desc')}}  </textarea> 

                   <br>
                   <div class="clearfix"></div>

                 </div>   
               </div>
               <div class="row form-row">
                <div class="col-md-8">
                  <label class="form-label"><strong>Address 1 <span style="color:red"> * </span> </strong></label>
                  <textarea rowspan="6" class="col-md-12" name="address1" >{{old('address1')}}</textarea>
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