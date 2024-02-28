
@extends('layout.layout')
@section('title','Reset Password')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">           
                               <th>S.no</th>
                               <th>User</th>
                               <th>Role</th>
                               <th>Department</th>
                               <th>Parent Department</th>
                               <th>Email</th>
                               <th>Action</th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                     <?php $i=1?>
                          @foreach($client_creadential as $c)
                            <tr>
                              <td><?php echo  $i?></td>
                              <td>{{$c->name}}</td>
                              <td>{{$c->role}}</td>
                              <td>{{$c->client_name }}</td>
                              <td>{{$c->parent_dept }}</td>

                              <td>{{$c->email}}</td>
                                                  
                              <td> 
                                    <a href="{{ route('client.reset',$c->user_id) }}" class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
                                  
                                </td>
                           
                             

                        </tr>
                        
                       <?php  $i++ ?>

                       @endforeach
                      
                    </tbody>
                </table>
                @include('sweetalert::alert')
               
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection