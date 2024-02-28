@extends('layout.layout')
@section('title','Marks Attendance')
@section('content')
      
    <div class="row">
      <div class="col-md-12">
        <div class="grid simple form-grid">
          <div class="grid-body no-border" style="height: 114px !important">
            <br>
            <br>
            <form action="{{route('employee.search')}}" method="post" class="header_search_form clearfix">
             @csrf
            <div class="col-xs-8 col-xs-offset-2">
        <div class="input-group">
                <div class="input-group-btn search-panel">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                      <span id="search_concept">Filter by</span> <span class="caret"></span>
                    </button>
                    
                </div>         
                <input type="search" class="form-control" autocomplete="Off" name="search" placeholder="Name \ ID \ CNIC">
                
            </div>
        </div>
        <span class="input-group-btn">
          <button type="submit" class="btn btn-primary">Search <span class="glyphicon glyphicon-search"></span></button>
                    <!-- <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span></button> -->
                </span>
        </form>
            <!-- <form action="{{route('employee.search')}}" method="post" class="header_search_form clearfix">
               @csrf
    <div class="form-group">
        <label for="inputEmail">Search Employee</label>
        <input type="search" class="form-control" id="inputEmail" name="search" placeholder="ID/Name/CNIC">
    </div>
   
    <button type="submit" class="btn btn-primary">Search</button>
</form> -->
</div>
<br>
</div>

<!-- <div class="grid simple form-grid">
          <div class="grid-body no-border" style="height: 430px !important">
            <br>
            <br>
        <div class="form-row">   
          <div class="form-group col-md-6">
    <label for="exampleInputPassword1">ID</label>
    <input type="text" class="form-control" id="exampleInputPassword1" placeholder="ID" readonly="">
  </div>
  <div class="form-group col-md-6">
    <label for="exampleInputEmail1">Name</label>
    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Name" readonly="">
  </div>
  
</div>
  <div class="form-row">   
    <div class="form-group col-md-6">
    <label for="exampleInputPassword1">Designation</label>
    <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Designation" readonly="">
  </div>
  <div class="form-group col-md-6">
    <label for="exampleInputEmail1">Department</label>
    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Department" readonly="">
  </div>
  </div>
  <hr style="font-size: 5px">
  
 <form action="" method="post" class="header_search_form clearfix">
    @csrf

  <div class="form-group">
    <label for="exampleInputPassword1">Manual Timing</label>
     <input type="datetime-local" name="time_in"  autocomplete="Off" class="form-control" placeholder="Time In">
  </div>
  <button type="submit" class="btn btn-primary">Save</button>

</form> -->


            <!-- <form action="{{route('employee.search')}}" method="post" class="header_search_form clearfix">
               @csrf
    <div class="form-group">
        <label for="inputEmail">Search Employee</label>
        <input type="search" class="form-control" id="inputEmail" name="search" placeholder="ID/Name/CNIC">
    </div>
   
    <button type="submit" class="btn btn-primary">Search</button>
</form> -->
<!-- </div>
<br>
</div> -->



</div>
</div>
@include('include.important')
@endsection