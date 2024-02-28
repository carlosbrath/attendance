       @extends('layout.layout')
       @section('title','Update Region')
        @section('content')
           @if(session('message'))
               <div class="alert alert-success" style="text-align: center;">{{session('message')}}
                </div>
             @endif
        <div class="row">
            <div class="col-md-12">
                <div class="grid simple form-grid">
                    <div class="grid-body no-border">
                        <form class="form-no-horizontal-spacing" method="post" action="{{route('regions.update',$region->region_id)}}""  id="form-condensed" novalidate="novalidate">
                            {{method_field('PUT')}}
                           {{csrf_field()}}
                            <div class="row column-seperation">
                                <div class="col-md-8">
                                    <div class="row form-row">
                                        <div class="col-md-8">
                                            <label class="form-label"><strong>Region Name</strong></label>
                                            <input name="region_name"  id="form3FirstName"  value="{{$region->region_name}}"" type="text" class="form-control" placeholder="Enter Region">
                                         @if($errors->any())
                                          <p style="color:red">
                                            {{$errors->first('region_name')}}
                                         </p>
                                         @endif
                                        </div>

                                    </div>
                                    <div class="row form-row">
                                        <div class="col-md-8">
                                          <label class="form-label"><strong>Description</strong></label>
                                             <textarea rowspan="6" class="col-md-12" name="region_desc">{{$region->region_desc}}</textarea> 
                                                                            
                                            <br>
                                            <div class="clearfix"></div>
                                       
                                        </div>
                                       
                                    </div>
                                    <div class="row form-row">
                                        <div class="col-md-8">
                                          <label class="form-label"><strong>Address 1</strong></label>
                                             <textarea rowspan="6" class="col-md-12" name="address_line_1">  {{$region->address_line_1}}</textarea> 
                                                                              
                                            <br>
                                            <div class="clearfix"></div>
                                       
                                        </div>
                                       
                                    </div>
                                     <div class="row form-row">
                                        <div class="col-md-8">
                                          <label class="form-label"><strong>Address 2</strong></label>
                                             <textarea rowspan="6" class="col-md-12" name="address_line_1"> {{$region->address_line_2}}</textarea>                                        
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
                            <button class="btn btn-success btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Update</button>
                           
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection