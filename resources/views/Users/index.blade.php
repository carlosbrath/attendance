
@extends('layout.layout')
@section('title','Employee Detail List')
@section('content')
<style type="text/css">
.pagination{
    float: right;
}

 .table_wrapper{
    display: block;
    overflow-x: auto;
    white-space: nowrap;
}


</style>
    <div class="row">
        <div class="col-md-12">
            <div class="grid simple form-grid">

                <div class="grid-body no-border">
                    <form class="form-no-horizontal-spacing" action="{{route('users')}}"
                          method="post" id="form-condensed" novalidate="novalidate">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <h4>Search Employees</h4>
                                <div class="row form-row">
                                    <div class="col-md-10">
                                        <label class="form-label"  style="font-size:15px">Department</label>
                                        <select name="department[]" id="source" style="width:100%" tabindex="-1" class="search select2-offscreen" multiple placeholder="All Departments/Select Department">
                                            @foreach($departments as $dept)
                                                <option 
                                                  @if(in_array($dept->id,$choosen_departments))
                                                    selected
                                                  @endif
                                                  value="{{$dept->id}}">{{$dept->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-10">
                                        <label class="form-label"  style="font-size:15px">Designation</label>
                                        <select name="designation[]" id="source1" style="width:100%" tabindex="-1" class="search select2-offscreen" multiple placeholder="All Designations/Select Designation">
                                            @foreach($designations as $des)
                                                <option 
                                                  @if(in_array($des->id,$choosen_designations))
                                                    selected
                                                  @endif
                                                  value="{{$des->id}}">{{$des->title}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-3">
                                        <label class="form-label"  style="font-size:15px">ID</label>
                                        <input type="text" placeholder="Employee ID" value="{{ request()->get('id') }}" class="form-control" name="id" />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label"  style="font-size:15px">Name</label>
                                        <input type="text" placeholder="Employee Name" value="{{ request()->get('name') }}" class="form-control" name="name" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"  style="font-size:15px">Father Name</label>
                                        <input type="text" placeholder="Father Name" value="{{ request()->get('father_name') }}" class="form-control" name="father_name" />
                                    </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="row form-row">
                                  <h4>&nbsp;</h4>
                                    <div class="col-md-5">
                                        <label class="form-label"  style="font-size:15px">CNIC</label>
                                        <input type="text" placeholder="11111-1111111-1" value="{{ request()->get('cnic') }}" class="form-control" name="cnic" />
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label"  style="font-size:15px">File Number</label>
                                        <input type="text" placeholder="File #" value="{{ request()->get('file_number') }}" class="form-control" name="file_number" />
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-5">
                                        <label class="form-label"  style="font-size:15px">Gender</label>
                                        <select name="gender" class="form-control">
                                          <option>All</option>
                                          <option @if(request()->get('gender') == 'male') selected @endif value="male">Male</option>
                                          <option @if(request()->get('gender') == 'female') selected @endif value="female">Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label"  style="font-size:15px">Service Scale</label>
                                        <input type="text" placeholder="Service Scale" value="{{ request()->get('bps') }}" class="form-control" name="bps" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="pull-left">
                                <button name="search" class="btn btn-success btn-cons" type="submit"><i class="icon-ok"></i>Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br>
@if(count($users)>0)
<a class="btn btn-success noPrint" style="float: right" id="employees_print"><i class="fa fa-print"> </i> Print</a>
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">
            <div class="grid-body all_emps">
                @include('Reports.print_header_employees')
                <table id="" class="table_wrapper table dataTable display nowrap " aria-describedby="example3_info">
                    <thead>
                      <th>S#</th>
                      <th>ID</th>
                      <th>Photo</th>
                      <th>Name</th>
                      <th>F.Name</th>
                      <th>CNIC Expiry</th>
                      <th class="noPrint">Role</th>
                      @if(Session::get('role_id')==5 || Session::get('role_id')==7)
                      <th>Designation</th>
                      @endif
                      @if(Session::get('role_id')==5 || Session::get('role_id')==7)
                      <th>Department</th>
                      <th>Service Scale</th>
                      @else
                      <th class="noPrint">Password</th>
                      @endif
                      <th class="noPrint">Action</th>
                    </tr>
                  </thead>
          <tbody role="alert" aria-live="polite" aria-relevant="all">
           @foreach($users as $u)
           <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$u->id}}</td>
            <td><img width="50px" src="{{ asset('emp_images') }}/{{ $u->image }}" /></td>
            <td>{{$u->name}}</td>
            <td>{{$u->father_name?:"--"}}</td>
            <td>{{checkCnicValidityExpiry($u->cnic_issue_date,$u->cnic_expiry_date)}}</td>
            <td class="noPrint">{{$u->role?$u->role->title:"--"}}</td>
            <td>{{$u->designations?$u->designations->title:"--"}}</td>
            <td>{{$u->departments->name?:"--"}}</td>
            <td>{{$u->bps?:'--'}}</td>
            @if($u->role && $u->role->id==6)
            <form  action="{{route('users.destroy',$u->id) }}" method="post">       {{method_field('delete')}}
              @csrf
              <td class="noPrint">

                    <a href="{{ route('users.inactive_edit',$u->id) }}" class="btn btn-primary" style="color: white" data-toggle="tooltip" data-placement="top" title="InActive Employee"><i class="fa fa-key"></i> </a>
                   <a href="{{ route('users.show',$u->id) }}" class="btn btn-primary" style="color: white"  data-toggle="tooltip" data-placement="top" title="View Employee"><i class="fa fa-eye"></i> </a>
                  <a href="{{ route('empreg.step1',$u->id) }}" class="btn btn-primary" style="color: white"  data-toggle="tooltip" data-placement="top" title="Edit Employee"><i class="fa fa-edit"></i> </a>
                 {{-- <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger"  data-toggle="tooltip" data-placement="top"  title="Delete Employee">
                      <i class="fa fa-trash"></i>
                  </button> --}}
              </td>
                </form>
            @endif
             </tr>
          @endforeach
        </tbody>
    </table>
          @include('sweetalert::alert')
      </br>
           <a href="{{ route('empreg.step1') }}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Employee</a>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@endif
    @include('include.important')
@endsection
