
@extends('layout.layout')
@section('title','Attach Employees')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">
                            <th>S.no</th>
                            <th>User Id</th>
                            <th>User</th>
                            <th>From</th>
                            <th>To</th>
                            <th>From Time</th>
                            <th>To Time</th>
                            <th>Application</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
         
                     @foreach($attachemployees as $a)
                     
                        <tr>
                          <td>{{$loop->iteration}}</td> 
                          <td>{{@$a->user->id}}</td>
                          <td>{{@$a->user->name}}</td>
                          <td>{{$a->from_date}}</td>
                          <td>{{$a->to_date}}</td>
                          <td>{{$a->time_from}}</td>
                          <td>{{$a->time_to}}</td>

                            @if($a->application_letter!="")
                            <td>
                            <div class="user-profile-pic" style="margin-top: 15px; width:40%;">
                            <img src="<?php echo asset('photos/'.$a->application_letter)?>" width="150px" height="68px">
                            </div>
                            </td>
                            @else
                            <td>
                                --
                            </td>
                            @endif
                            <td>{{ $a->remarks?$a->remarks:"--" }}</td> 
                            <form action="{{route('attachemployees.destroy',$a->id) }}" method="post">       {{method_field('delete')}}
                                @csrf   
                                <td>            
                                    
                                    <a href="{{ route('attachemployees.show',$a->id) }}"class="btn btn-success" style="color: white" ><i class="fa fa-eye"></i> </a> 
                                    <a href="{{ route('attachemployees.edit',$a->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                  
                                    <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> 
                                    </button> 
                                </td>
                            </form>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @include('sweetalert::alert')
                <a href="{{route('attachemployees.create')}}" class="btn btn-primary btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i>Add Attach Employee</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection