@extends('layout.layout')
@section('title','Add Client')
@section('content')
@if(session('message'))
<div class="alert alert-success" style="text-align: center;">{{session('message')}}
</div>
@endif
<style type="text/css">
.form-actions {
background-color: #22262e;
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
        <div class="grid-body ">
            <form class="form-no-horizontal-spacing" method="post" action ="{{route('clients.store')}}" enctype="multipart/form-data" id="form-condensed" novalidate="novalidate">
                {{csrf_field()}}
                <div class="row column-seperation">
                    <div class="col-md-8">
                    <div class="row form-row">
                        <div class="col-md-8">
                            <label class="form-label"><strong>Department Name</strong></label>
                            <input name="name" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="Enter Name">
                            @if($errors->any())
                            <p style="color:red">{{$errors->first('name')}}</p>
                            @endif
                        </div>
                    </div>
              <div class="row form-row">
              <div class="col-md-8">
                  <div class="form-group">
                      <label class="form-label"><strong>Parent Department</strong></label>
                     <div class=" right">
                      <i class=""></i>
                          <select name="department_id" id="department_id"   class="leave_deparments select2 form-control">
                              <option value="0">
                              Choose Parent Department
                              </option>
                              @foreach($departments as $d)
                                <option value="{{$d->id}}">{{$d->name}}
                                </option>
                              @endforeach
                          </select>
                      @if($errors->any())
                      <p style="color:red">
                      {{$errors->first('department_id','Department field is required')}}
                      </p>
                      @endif
                    </div>
                 </div>
               </div>
               </div>

               <div class="row form-row">
                    <div class="col-md-8">
                        <label class="form-label"><strong>Total Employees</strong></label>
                        <input name="total_employees" id="total_employees" value="{{old('total_employees')}}" type="text" class="form-control" placeholder="Total Employees According to Budget Book">
                        @if($errors->any())
                        <p style="color:red">{{$errors->first('total_employees')}}</p>
                        @endif
                    </div>
                </div>

                <div class="row form-row">
                    <div class="col-md-8">
                        <label class="form-label"><strong>Vacant Posts</strong></label>
                        <input name="vacant_posts" id="vacant_posts" value="{{old('vacant_posts')}}" type="text" class="form-control" placeholder="Vacant Posts">
                        @if($errors->any())
                        <p style="color:red">{{$errors->first('vacant_posts')}}</p>
                        @endif
                    </div>
                </div>

                <div class="row form-row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label"><strong>Role</strong><span style="color:red"> *</span></label>
                            <div class=" right">
                                <i class=""></i>
                                <select name="role_id" id="role_id" class="col-md-12">
                                <option value="">Please Select...</option>
                                @foreach($role as $r)
                                <option name=""  value="{{$r->id}}" <?php echo ($r->id==5) ? 'selected':'' ?>>{{$r->title}}</option>
                                @endforeach
                                </select>
                                @if($errors->any())
                                <p style="color:red">{{$errors->first('role_id','Role field is required')}}</p>
                                @endif
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="row form-row">
                  <div class="col-md-8">
                      <div class="form-group">
                          <label class="form-label"><strong>Level</strong></label>

                         <div class=" right">
                          <i class=""></i>
                              <select name="department_level_id" id="department_level_id"   class="select2 form-control">
                                    <option>Please Select</option>
                                  @foreach($levels as $key=>$name)
                                    <option value="{{ $key }}" >{{ $name }}
                                    </option>
                                  @endforeach
                              </select>
                          @if($errors->any())
                          <p style="color:red">
                          {{$errors->first('level','Department level is required')}}
                          </p>
                          @endif
                        </div>
                     </div>
                   </div>
                </div>
                <div class="row form-row">
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="form-label"><strong>Region</strong></label>

                         <div class=" right">
                          <i class=""></i>
                              <select name="region_id" id="region_id"   class="select2 form-control" required>
                                    <option>Please Select</option>
                                  @foreach($regions as $key=>$name)
                                    <option value="{{ $key }}"
                                    >{{ $name }}
                                    </option>
                                  @endforeach
                              </select>
                        </div>
                     </div>
                   </div>
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="form-label"><strong>Zone</strong></label>

                         <div class=" right">
                          <i class=""></i>
                              <select name="zone_id" id="r_zone_id"   class="select2 form-control" required>
                                    <option>Please Select</option>

                              </select>
                        </div>
                     </div>
                   </div>
                  <div class="col-md-2">
                      <div class="form-group">
                          <label class="form-label"><strong>Branch</strong></label>

                         <div class=" right">
                          <i class=""></i>
                              <select name="branch_id" id="z_branch_id"   class="select2 form-control" required>
                                    <option>Please Select</option>

                              </select>
                        </div>
                     </div>
                   </div>
                </div>
                <div class="row form-row">
                    <div class="col-md-8">
                        <label class="form-label"><strong>Email</strong></label>
                        <input name="email" id="form3FirstName" value="{{old('email')}}" type="text" class="form-control" placeholder="Enter Email">
                        @if($errors->any())
                        <p style="color:red">{{$errors->first('email')}}</p>
                        @endif
                    </div>

                </div>
                <div class="row form-row">
                    <div class="col-md-8">
                        <label class="form-label"><strong>Focal Person</strong></label>
                        <input name="focal_person" id="focal_person" value="{{old('focal_person')}}" type="text" class="form-control" placeholder="Enter Focal Person">

                   </div>
                </div>

                <div class="row form-row">
                    <div class="col-md-8">
                        <label class="form-label"><strong>Contact</strong></label>
                        <input name="contact" id="form3FirstName" value="{{old('contact')}}" type="text" class="form-control" placeholder="Enter Contact">
                        @if($errors->any())
                        <p style="color:red">{{$errors->first('contact')}}</p>
                        @endif
                   </div>
                </div><div class="row form-row">
                    <div class="col-md-8">
                        <label class="form-label"><strong>Sort Order</strong></label>
                        <input name="sort_order" id="form3FirstName" value="{{old('sort_order')}}" type="number" class="form-control" placeholder="Enter Sort Order">
                        @if($errors->any())
                        <p style="color:red">{{$errors->first('sort_order')}}</p>
                        @endif
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
