@extends('layout.layout')
@if(Session::get('role_id')==1)
    @section('title','Create Account')
@else
    @section('title','Add Employee')
@endif
@section('content')
@if(Session::get('role_id')==1)
    @if(session('message'))
        <div class="alert alert-success" style="text-align: center;">{{session('message')}}</div>
    @endif
    <div class="row">   
        <div class="col-md-12">
            <div class="grid simple form-grid">
                <div class="grid-body no-border">
                    <form class="form-no-horizontal-spacing" method="post" action ="{{route('users.store')}}" enctype="multipart/form-data" id="form-condensed" novalidate="novalidate">
                        {{csrf_field()}}
                        <div class="row column-seperation">
                            <div class="col-md-8">
                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>ID</strong></label>
                                        <input name="id" id="id" value="{{old('id')}}" type="text" class="form-control" placeholder="ID">
                                        @if($errors->any())
                                            <p style="color:red">{{$errors->first('id')}}</p>
                                        @endif 
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Name</strong><span style="color:red">*</span></label>
                                        <input name="name" id="form3FirstName" value="{{old('name')}}" type="text" class="form-control" placeholder="Enter Name">
                                        @if($errors->any())
                                            <p style="color:red">{{$errors->first('name')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <div style="margin-top:8px;width:490px"  class="input-append success date no-padding">
                                            <label class="form-label"><strong>DOB <span style="color:red">*</span>&nbsp;&nbsp; <span id="append_age" style="margin-bottom:2px;font-size:20px" class=" badge badge-disable"></span></strong></label>
                                            <input type="text"  autocomplete="off" name="dob" <?php echo $value_dob ?> id="date_of_birth"    placeholder="From Date" class="form-control">
                                            @if($errors->any())
                                                <p style="color:red">{{$errors->first('dob','Dob field is required')}}</p>
                                            @endif 
                                            <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Email</strong><span style="color:red">*</span></label>
                                        <input name="email" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="Enter Email">
                                        @if($errors->any())
                                            <p style="color:red">{{$errors->first('email')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="form-label"><strong>Department</strong><span style="color:red">*</span></label>
                                            <div class=" right">
                                                <i class=""></i>
                                                <select style="width: 66%" class="form-control select2" id="department_select" name="deparment_id" data-init-plugin="select2" required>
                                                    <option value="">Please Select...</option>
                                                    @foreach($department as $d)
                                                        <option name="" value="{{$d->id}}">{{$d->name}}</option>
                                                    @endforeach
                                                </select>
                                                @if($errors->any())
                                                <p style="color:red">{{$errors->first('deparment_id','Department field is required')}}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <select style="width: 66%" class="form-control select2" id="cardType" name="role_id" data-init-plugin="select2" required>
                                            <option value="">Please Select...</option>
                                            @foreach($role as $r)
                                                <option name=""  value="{{$r->id}}" <?php echo ($r->id==5) ? 'selected':'' ?>>{{$r->title}}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->any())
                                        <p style="color:red">{{$errors->first('role_id','Role field Is required')}}</p>
                                        @endif                                          
                                        <br>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="pull-left">
                                <button class="btn btn-success btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
                                <button class="btn btn-white btn-cons" type="reset">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-md-12">
            <div class="grid simple form-grid">
                <div class="grid-title no-border"></div>
                <div class="grid-body no-border">
                    <div class="col-md-12">
                        <div class="grid simple transparent">
                            <div class="grid-body ">
                                <div class="row">
                                    <div id="rootwizard" class="col-md-12">
                                        <div class="form-wizard-steps">
                                            <ul class="wizard-steps form-wizard">
                                                <li class="active" data-target="#step1">
                                                <a href="#tab1" data-toggle="tab" aria-expanded="true"> <span class="step">1</span> <span class="title">Basic information</span> </a>
                                                </li>
                                                <li data-target="#step2" class="">
                                                <a href="#tab2" data-toggle="tab"> <span class="step">2</span> <span class="title">Department & Service Information</span> </a>
                                                </li>
                                                <li data-target="#step3" class="">
                                                <a href="#tab3" data-toggle="tab"> <span class="step">3</span> <span class="title">Time & Roster Information</span> </a>
                                                </li>
                                                <li data-target="#step4" class="">
                                                <a href="#tab4" data-toggle="tab"> <span class="step">4</span> <span class="title">Contact Information</span> </a>
                                                </li>
                                            </ul>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="tab-content transparent">
                                        <br><br>
                                        @if($page=='roster_info')
                                            @include('Users.roster_info') 
                                        @elseif($page=='department_info')
                                            @include('Users.department_info')
                                        @elseif($page=='roster_info3')
                                            @include('Users.contact_info')
                                        @elseif($page=='')
                                            @include('Users.basic_info')
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@include('include.important')
@endsection