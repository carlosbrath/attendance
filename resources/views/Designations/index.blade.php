
@extends('layout.layout')
@section('title','Designations')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">
                            <th>S.No</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Created at</th>
                            <th>Updated at</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                         @foreach($designation as $d)                            
                            <tr>
                              <td> {{ $loop->iteration }}</td>                 
                              <td>{{$d->title}}</td>
                              <td>{{$d->description}}</td>
                              <td>{{$d->created_at}}</td>
                              <td>{{$d->updated_at}}</td>
                              <form action="{{ url('designations/'.$d->id) }}" method="post"> 
                                {{method_field('DELETE')}}
                                @csrf
                              
                                  <td>           
                                    <a href="{{ route('designations.edit',$d->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
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
                <a href="{{route('designations.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Designation</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection