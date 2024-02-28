
@extends('layout.layout')
@section('title','Employee Detail List')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
                    <thead>
              <th>ID</th>
              <th>Name</th>
              <th>Role</th>
              @if(Session::get('role_id')==5 || Session::get('role_id')==7)
              <th>Designation</th>
              @endif
              @if(Session::get('role_id')==5 || Session::get('role_id')==7)
              <th>Department</th>
              <th>Bps</th>         
              <th>Cnic</th>
              @else
              <th>Password</th>
              @endif
              <th>Action</th>       
            </tr>
          </thead>
        @if($flag==1)

          <tbody role="alert" aria-live="polite" aria-relevant="all">
            
           @foreach($users as $u)
         
           @if(Session::get('role_id')==5 || Session::get('role_id')==7)
           
           <tr>
            <td>{{$u->id}}</td>                         
            <td>{{$u->name}}</td>         
            <td>{{$u->role_title}}</td>
            <td>{{$u->designations_title}}</td>
            <td>{{$u->dep_name}}</td>
            <td>{{$u->bps}}</td>  
            <td>{{$u->cnic}}</td> 
            <form action="{{route('users.destroy',$u->id) }}" method="post">       {{method_field('delete')}}
              @csrf
              <td>
                   
                    <a href="{{ route('users.inactive_edit',$u->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-key"></i> </a> 
                   <a href="{{ route('users.show',$u->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-eye"></i> </a> 
                  <a href="{{ route('users.edit',$u->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
                  <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                      <i class="fa fa-trash"></i> 
                  </button> 
              </td>
                </form>
             </tr> 

        
          @endif
          
          @endforeach  
        </tbody>

        @else
          <tbody role="alert" aria-live="polite" aria-relevant="all">
           @foreach($users as $u)
           @if($u->departments !='') 
           <tr>
            <td>{{$u->id}}</td>                         
            <td>{{$u->name}}</td>         
            <td>{{$u->role->title}}</td>
            <td>{{@$u->designations->title}}</td> 
            <td>{{$u->departments->name}}</td>
            <td>{{$u->bps}}</td>  
            <td>{{$u->cnic}}</td> 
          
            <form action="{{route('users.destroy',$u->id) }}" method="post">       {{method_field('delete')}}
              @csrf
              <td>
                 <a href="{{ route('users.inactive_edit',$u->id) }}"class="btn btn-primary" style="color: white" data-toggle="tooltip" data-placement="top" title="Inactive" ><i class="fa fa-key"></i> 
                 </a> 
                   <a href="{{ route('users.show',$u->id) }}"class="btn btn-primary" style="color: white" data-toggle="tooltip" data-placement="top" title="View" ><i class="fa fa-eye"></i> </a> 
                  <a href="{{ route('users.edit',$u->id) }}"class="btn btn-primary" style="color: white" data-toggle="tooltip" data-placement="top" title="edit" ><i class="fa fa-edit"></i> </a>                 
                  <button type="submit" data-toggle="tooltip" data-placement="top" title="delete" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                      <i class="fa fa-trash"></i> 
                  </button> 
              </td>
                </form>
             </tr>
             
          @elseif(Session::get('role_id')==1)
          <tr>
            <td>{{$u->id}}</td>               
            <td>{{$u->name}}</td>
            <td>{{$u->role_title}}</td>
            <td>{{$u->password}}</td> 

             <form action="{{route('users.destroy',$u->id) }}" method="post">       {{method_field('delete')}}
              @csrf
              <td>
                  <a href="{{ route('users.edit',$u->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
                  <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                      <i class="fa fa-trash"></i> 
                  </button> 
               </td>
                </form>
          </tr>
          @endif
          @endforeach  
        </tbody>

        @endif 
         
        
    </table>
    
          @include('sweetalert::alert')
      </br>    
           <a href="{{url('employee/basic_info')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Employee</a> 
            </div>
        </div>
    </div>
</div>
</div>
</div>  

@endsection