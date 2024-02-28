       @extends('layout.layout')
       @section('title','Password Setting')
       @section('content')
       @if(session('message'))
       <div class="alert alert-success" style="text-align: center;">{{session('message')}}
       </div>
       @endif
       <style type="text/css">
       .form-actions {
              background-color:#f6f7f8;
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
@if (\Session::has('success'))
<div class="alert alert-success" style="background:#c3e7dd !important;color:green">
        <ul style="list-style: none;">
            <li style="list-style: none;"> {!! \Session::get('success') !!} </li>
        </ul>
    </div>
@endif    
<div class="row">
      <div class="col-md-12">
            <div class="grid simple form-grid">
                  <div class="grid-body no-border">
                        <form class="form-no-horizontal-spacing" method="post"  action ="{{route('employee.profile_update', Session::get('user_id'))}}"" id="form-condensed" novalidate="novalidate">
                              {{ method_field('PUT') }}
                              {{csrf_field()}}
                              <br>
                              <div class="row column-seperation">
                                    <div class="col-md-8">
                                          <div class="row form-row">
                                                <div class="col-md-8">            
                                                      <label class="form-label"><strong>New Password</strong><span style="color:red"> *</span></label>
                                                      <input autocomplete="off" name="new_password" type="password" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="New Password">
                                                      @if($errors->any())
                                                      <p style="color:red">{{$errors->first('new_password')}}</p>
                                                      @endif
                                               </div>

                                        </div>
                                    <div class="row form-row">
                                     <div class="col-md-8">
                                        <label class="form-label"><strong>Confirm Password</strong><span style="color:red"> *</span></label>
                                          <input type="password" autocomplete="off" name="confirm_password" id="form3FirstName" value="" type="text" class="form-control" placeholder="Confirm Password">   
                                           @if($errors->any())
                                            <p style="color:red">{{$errors->first('confirm_password')}}</p>
                                            @endif  									
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
                <button class="btn btn-danger btn-cons"  value="save" type="submit"><i class="icon-ok"></i> Save</button>
              
         </div>
  </div>
</form>
</div>
</div>
</div>
</div>
@endsection