
@extends('layout.layout')
@section('title','Deductions')
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                         @foreach($allowance as $all)                            
                                <tr>
                                    <td> {{ $loop->iteration }}</td>                 
                                    <td>{{$all->title}}</td>
                                    <form action="{{ url('allowances/'.$all->id) }}" method="post"> 
                                        {{method_field('DELETE')}}
                                        @csrf
                                    
                                        <td>           
                                            <a href="{{ route('allowances.edit',$all->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
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
                <a href="{{route('allowances.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Allowance</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection