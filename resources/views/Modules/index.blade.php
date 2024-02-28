
@extends('layout.layout')
@section('title','Module List')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="example3" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">
                            <th>S.No</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                      @foreach($module as $m)
                          <tr>
                          <td>{{ $loop->iteration }}</td>                
                          <td>{{$m->name}}</td>
                          <td>{{$m->route}}</td>

                          <form action="{{ route('modules.destroy',$m->id) }}" method="post"> 
                          {{method_field('DELETE')}}
                          @csrf   
                                    <td>

                          <a href="{{route('modules.edit',$m->id)}}"class="btn btn-primary" style="color: white"><i class="fa fa-edit"></i> </a>


                          <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                          <i class="fa fa-trash"></i> 
                          </button> 
                          <!--    <input type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger" value="Delete"> -->
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