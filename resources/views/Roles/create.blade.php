
       @extends('layout.layout')
       @section('title','Add Role')
       @section('content')
       @if(session('message'))
       <div class="alert alert-success" style="text-align: center;">{{session('message')}}
       </div>
       @endif
       <div class="row">
        <div class="col-md-12">
            <div class="grid simple form-grid">
                <div class="grid-body no-border">
                    <form class="form-no-horizontal-spacing" method="post"  action ="{{route('roles.store')}}" id="form-condensed" novalidate="novalidate">
                          {{csrf_field()}}
                        <div class="row column-seperation">
                            <div class="col-md-8">

                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Title</strong></label>
                                        <input name="title" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="Enter Title">
                                        @if($errors->any())
                                         <p style="color:red">{{$errors->first('title')}}</p>
                                       @endif
                                    </div>

                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Description</strong></label>
                                        <textarea rowspan="6" class="col-md-12" name="desc">{{old('description')}}</textarea>                                          
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