       @extends('layout.layout')
       @section('title','Client Account Update')
       @section('content')
       @if(session('message'))
       <div class="alert alert-success" style="text-align: center;">{{session('message')}}
       </div>
       @endif
        <style type="text/css">
        .form-actions {
        background-color:#f6f7f8;;
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
                <div class="grid-title">Edit Client</div>
                <div class="grid-body no-border">
                    <form class="form-no-horizontal-spacing" method="post" action ="{{route('client_creadential_update',$user->id)}}" enctype="multipart/form-data" id="form-condensed" novalidate="novalidate">

                        {{method_field('PUT')}}
                        {{csrf_field()}}
                        <div class="row column-seperation">
                            <div class="col-md-8">

                                  <div class="row form-row">
                                    <div class="col-md-8">
                                   <select class="form-control" name="jurisdiction_id">
                                    <option value="0">No-Jurisdiction</option>
                                    @foreach($jurs as $key=>$j)
                                        <option value="{{ $key }}">{{ $j }}</option>
                                    @endforeach
                                  </select>                                       
                                    </div>
                                  </div>
                                  <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Email</strong></label>
                                        <input name="email" id="form3FirstName" value="{{$user->email}}" type="text" class="form-control" placeholder="Enter Email">
                                        @if($errors->any())
                                        <p style="color:red">{{$errors->first('email')}}</p>
                                        @endif
                                    </div>

                                </div>
                                  <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Password</strong></label>
                                        <input name="password" id="form3FirstName"  value="" type="text" class="form-control" placeholder="Enter Password">
                                        @if($errors->any())
                                        <p style="color:red">{{$errors->first('password')}}</p>
                                        @endif
                                   </div>
                                </div>                              
                            </div>
                        </div>

                    </div>

                </div>
                <div class="form-actions">

                    <div class="pull-left">
                        <button class="btn btn-success btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i>Update</button>
                    </div>
                </div>
            </form>
        </div>
       </div>
   </div>
</div>
@endsection