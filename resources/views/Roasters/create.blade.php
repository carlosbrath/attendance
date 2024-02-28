
@extends('layout.layout')
@section('title','Add Roster')
@section('content')
         
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
        <div class="row">
          <div class="col-md-12">
            <div class="grid simple form-grid">
              <div class="grid-title no-border">
                <div class="tools">
                  <a href="javascript:;" class="collapse"></a>
                  <a href="#grid-config" data-toggle="modal" class="config"></a>
                  <a href="javascript:;" class="reload"></a>
                  <a href="javascript:;" class="remove"></a>
                </div>
              </div>
              <div class="grid-body no-border">
                <form class="form-no-horizontal-spacing" action ="{{route('roasters.store')}}" method="post"  id="form-condensed" novalidate="novalidate">
                  {{csrf_field()}}
                  <div class="row column-seperation">
                    <div class="col-md-8">
                         <div class="form-group">
                              <label class="form-label"><strong>Department</strong><span style="color:red"> *</span></label>
                            <div class=" right">
                              <i class=""></i>
                                  <select name="deparment_id" id="department_id"  style="width: 66%" class="select2 form-control">
                                    <option value="">
                                    Please Select...
                                    </option>
                                    @foreach($department as $d)
                                    <option name=""  value="{{$d->id}}">{{$d->name}}</option>
                                    @endforeach
                              </select>
                           @if($errors->any())
                             <p style="color:red">{{$errors->first('deparment_id','Department field is required')}}
                             </p>
                            @endif
                           </div>
                        </div> 
                        <div class="col-md-8" style="margin-bottom:10px">
                                <label>Month <span style="color:red"> *</span></label>
                                <input type="text" id="datepicker" class="form-control" autocomplete="off" name="month" autocomplete="off" placeholder="for the month of">
                          @if($errors->any())
                           <p style="color:red">{{$errors->first('month','Month is Required')}}
                           </p>
                          @endif
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
@include('include.important')
@endsection