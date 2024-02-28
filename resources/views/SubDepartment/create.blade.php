       @extends('layout.layout')
       @section('title','Sub Deparmtent')
       @section('content')
      
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
       @if(session('message_for_supdepartment'))
       <div class="alert alert-success" style="text-align: center;">{{session('message_for_supdepartment')}}
       </div>
       @endif     
       <div class="row">
        <div class="col-md-12">
            <div class="grid simple form-grid">
                <div class="grid-body no-border">
                    <form class="form-no-horizontal-spacing" method="post" action ="{{route('sub_departments.store')}}" enctype="multipart/form-data" id="form-condensed" novalidate="novalidate">
                        {{csrf_field()}}
                        <div class="row column-seperation">
                            <div class="col-md-8">

                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Name</strong></label>
                                        <input name="title" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="Enter Name">
                                        @if($errors->any())
                                        <p style="color:red">{{$errors->first('title')}}</p>
                                        @endif
                                    </div>

                                </div>
                                  <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Email</strong></label>
                                        <input name="email" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="Enter Email">
                                        @if($errors->any())
                                        <p style="color:red">{{$errors->first('email')}}</p>
                                        @endif
                                    </div>

                                </div>
                                  <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Contact</strong></label>
                                        <input name="contact" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="Enter Contact">
                                        @if($errors->any())
                                        <p style="color:red">{{$errors->first('contact')}}</p>
                                        @endif
                                   </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Logo</strong></label>
                                       <input type="file" name="logo">                                          
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
                        <button class="btn btn-white btn-cons" type="reset">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
       </div>
   </div>
</div>
@endsection