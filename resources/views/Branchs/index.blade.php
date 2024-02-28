
@extends('layout.layout')
@section('title','Tehsils')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">           
                              <th>S.NO</th>
                              <th>Tehsil Name</th>       
                              <th>Address</th> 
                              <th>Action</th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                      @foreach($branch as $b)

                                <tr>
                                 <td> {{ $loop->iteration }}</td>                                  
                                 <td>{{$b->branch_name}}</td>       
                                 <td>{{$b->address_line_1}}</td>           
                                 <form action="{{route('branchs.destroy',$b->branch_id) }}" method="post">       {{method_field('delete')}}
                                  @csrf   
                                  <td>    
                                    <a href="{{ route('branchs.edit', $b->branch_id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
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
                <a href="{{route('branchs.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Branch</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection