
@extends('layout.layout')
@section('title','Inactive Employee')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">
                                  
                               <th>ID</th>
                               <th>Name</th>
                               <th>Role</th>
                               <th>Designation</th>
                               <th>Department</th>
                               <th>Action</th>
                        </tr>
                    </thead>
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                      @foreach($users as $u)     
                      <tr>
                      <td>{{$u->id}}</td> 
                      <td>{{$u->name}}</td>                        
                      <td>{{$u->role_title}}</td>         
                      <td>{{$u->designation_title}}</td>
                      <td>{{$u->department_name}}</td> 
                      <form action="{{route('users.destroy',$u->id) }}" method="post">       {{method_field('delete')}}
                      @csrf
                     <td>
                        <a data-toggle="tooltip" data-placement="top" title="Inactive" href="{{ route('active_employee',$u->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-check"></i> </a> 
                        <button data-toggle="tooltip" data-placement="top" title="delete"  type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                        <i class="fa fa-trash"></i> 
                        </button>
                        </td>
                        </form>
                      </tr>     
                      @endforeach  
                    </tbody>
                  </table>
               <div>   
            <div>
          <div>   
          <div>             

</div>
</div>  

@endsection