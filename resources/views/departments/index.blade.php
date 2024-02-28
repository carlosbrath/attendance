
@extends('layout.layout')
@section('title','Departments')
@section('content')
 @if(session('error'))
 <div class="alert alert-danger" style="text-align: center;">{{session('error')}}
 </div>
 @endif
 @if(session('success'))
 <div class="alert alert-success" style="text-align: center;">{{session('success')}}
 </div>
 @endif
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">
                                  
                               <th>S.no</th>
                               <th>Department Name</th>
                               <th>Type</th>
                               <th>Description</th>
                               <th>Bulk Time Category Status</th>
                               <th>Action</th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                            @foreach($departments as $c)  
                             <tr>   
                            
                              <td> {{ $loop->iteration }}</td>         
                              <td>{{$c->name}}</td>
                            @if($c->sub_account==2)
                             <td>Sub Account</td>
                            @else
                             <td>Unit</td> 
                            @endif
                            <td>{{$c->description}}</td>
                            <td>
                                {{$c->TimeCategory?$c->TimeCategory->title:'Not Assigned' }}
                            </td>
                              <form action="{{ route('departments.destroy',$c->id) }}" method="post">       
                                {{method_field('DELETE')}}
                                {{csrf_field()}}                    
                                <td>   
                                    <a href="{{ route('departments.edit',$c->id) }}"class="btn btn-primary" style="color: white;font-size:13px" ><i class="fa fa-edit"></i> </a> 
                                    @if($c->sub_account==1)                
                                    <a class="btn btn-info" tooltip="Bulk Time Categories Update" 
                                        data-toggle="modal" data-target="#{{ $c->id}}"
                                    style="color: white;font-size:13px" ><i class="fa fa-clock-o"></i> </a>  
                                    @endif               
                                    <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" style="font-size:13px" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> 
                                    </button> 
                                </td>
                            </form>

                        </tr>                   
                        @endforeach
                    </tbody>
                </table>
                @include('sweetalert::alert')
                <a href="{{route('departments.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Department</a>
            </div>

        </div>
    </div>
@foreach($departments as $dp)
<form method="post" action="{{ route('dept.bulk_tc_update') }}">
<div class="modal fade" id="{{ $dp->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" value="{{ $dp->id }}">
        {{ csrf_field() }}
        <p class="alert alert-warning">This action will update the time category of the whole department and will not be undone, please be careful</p>
        {{ Form::select('tcat_id',[''=>'Choose a Time Category to Update']+$timecategories,NULL,['class'=>'form-control','required'=>'required']) }}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </div>
  </div>
</div>
</form>
@endforeach    
</div>
</div>
</div>  

@endsection