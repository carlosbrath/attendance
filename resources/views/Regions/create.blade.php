       @extends('layout.layout')
       @section('title','Add Region')
       @section('content')
       @if(session('message'))
       <div class="alert alert-success" style="text-align: center;">{{session('message')}}
       </div>
       @endif
       <div class="row">
         <div class="col-md-12">
          <div class="grid simple form-grid">
           <div class="grid-body no-border">
            <form class="form-no-horizontal-spacing" method="post" action="{{route('regions.store')}}"  id="form-condensed" novalidate="novalidate">
              {{csrf_field()}}
              <div class="row column-seperation">
                <div class="col-md-8">
                 <div class="row form-row">
                  <div class="col-md-8">
                   <label class="form-label"><strong>Region Name <span style="color:red"> * </span></strong></label>
                   <input name="region_name"  autocomplete="off" id="form3FirstName" value="{{old('region_name')}}" type="text" class="form-control" placeholder="Enter Region">
                   @if($errors->any())
                   <p style="color:red">
                    {{$errors->first('region_name')}}
                  </p>
                  @endif
                </div>

              </div>
              <div class="row form-row">
                <div class="col-md-8">
                  <label class="form-label"><strong>Region Description</strong></label>
                  <textarea rowspan="6" class="col-md-12" name="region_desc">{{old('region_desc')}} </textarea> 

                  <br>
                  <div class="clearfix"></div>

                </div>

              </div>
              <div class="row form-row">
                <div class="col-md-8">
                  <label class="form-label"><strong>Address 1 <span style="color:red"> * </span></strong></label>
                  <textarea rowspan="6" class="col-md-12" name="address1">{{old('address1')}} </textarea> 
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
                  <textarea rowspan="6" class="col-md-12" name="address2">{{old('address2')}} </textarea>                                        
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