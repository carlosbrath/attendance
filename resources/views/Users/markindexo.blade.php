@extends('layout.layout')
@section('title','Marks Attendance List')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">
             <div>
                <a href="{{route('marks_attendance')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Serach Employee Attendance</a> 
             </div>
            <div class="grid-body ">
                <table class="table dataTable" id="example3" aria-describedby="example3_info">
                    <thead>
              <th>ID</th>
              <th>Name</th>
               <th>CNIC</th>
              <th>Disgnation</th>
              <th>Department</th>
              <th>Action</th>       
            </tr>
          </thead>
       
         
          <tbody role="alert" aria-live="polite" aria-relevant="all">
           @foreach($empm as $u)
           <tr>
            <td class="">{{$u->id}}</td>                         
            <td class="">{{$u->emp_name}}</td>
            <td class="">{{$u->cnic}}</td> 
            <td class="">{{$u->title}}</td>
            <td class="">{{$u->dep_name}}</td>        
         <form action="{{route('users.destroy',$u->id) }}" method="post">       {{method_field('delete')}}
              @csrf
              <td>
                <a href="{{route('mark.attendance',$u->id)}}"  class="btn btn-success" title="Mark Attendance"  style="color: white" ><i class="fa fa-check">MarkAttendance</i> </a>
                 
              </td>
                </form>
             </tr>
            @endforeach
        </tbody>
    </table>
    
         
      </br>    
            
            </div>
        </div>
    </div>
</div>
</div>
</div>  
@endsection