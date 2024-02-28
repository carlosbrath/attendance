
@extends('layout.layout')
@section('title','Inactive Employee')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="example3" aria-describedby="example3_info">
                    <thead>
                         <th>ID</th>
              <th>Name</th>
              <th>Role</th>
              <th>Designation</th>
              <th>Department</th>
                 
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