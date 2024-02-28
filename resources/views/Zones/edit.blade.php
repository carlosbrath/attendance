       @extends('layout.layout')
       @section('title','Update District')
        @section('content')
           @if(session('message'))
               <div class="alert alert-success" style="text-align: center;">{{session('message')}}
                </div>
             @endif
        <div class="row">
            <div class="col-md-12">
                <div class="grid simple form-grid">
                    <div class="grid-body no-border">
                        <form class="form-no-horizontal-spacing" method="post" 
                                action = "{{route('zones.update',$zone->zone_id)}}"  id="form-condensed" novalidate="novalidate">
                             {{ method_field('PUT')}}
                             {{ csrf_field() }}

                <div class="row column-seperation">
                    <div class="col-md-8">
                      <div class="row form-row">
                        <div class="form-group">
                          <label class="form-label"><strong>Region</strong></label>
                          <div class=" right">
                            <i class=""></i>
                            <select name="region_name"  style="width: 66%" class="select2 form-control">
                              <option value="">
                               Please Select...
                             </option>                        

                             @foreach($region as $region_id=>$region_name) {
                             <option name="" value="{{$region_id}}" <?php echo ($zone->region_id==$region_id)?'selected':'' ?>>{{$region_name}}</option>
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
                                            <label class="form-label"><strong>District Name</strong></label>
                                            <input  name="zone_name" autocomplete="off" value="{{$zone->zone_name}}"  id="form3FirstName"  type="text" class="form-control" placeholder="Enter Zone">
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
                                             <textarea rowspan="6" class="col-md-12" name="zones_desc">{{old('zone_desc')}} </textarea> 
                                                                            
                                            <br>
                                            <div class="clearfix"></div>
                                       
                                        </div>
                                       
                                    </div>
                                    <div class="row form-row">
                                        <div class="col-md-8">
                                          <label class="form-label"><strong>Address 1</strong></label>
                                             <textarea rowspan="6" class="col-md-12" name="address1">{{old('address1')}} </textarea>                                       
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
                            <button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Update</button>
                        
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection