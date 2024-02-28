       @extends('layout.layout')
       @section('title','Update Designation')
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
                    <form class="form-no-horizontal-spacing"
                           method="post" 
                            action="{{route('deductions.update',$deduction->id)}}"
                           id="form-condensed" novalidate="novalidate">
    
                           {{method_field('PUT')}}
                           {{csrf_field()}}
                       
                        <div class="row column-seperation">
                            <div class="col-md-8">

                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Title</strong></label>
                                        <input name="title" autocomplete="off" id="form3FirstName" value="{{$deduction->title}}" type="text" class="form-control" placeholder="Enter Title">
                                        @if($errors->any())
                                        <p style="color:red">{{$errors->first('title')}}</p>
                                        @endif
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