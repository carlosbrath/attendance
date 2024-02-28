
@extends('layout.layout')
@section('title','Districts')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">           
                              <th>S.NO</th>
                              <th>District Name</th>       
                              <th>Address</th> 
                              <th>Action</th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                     @foreach($zone as $z)
                       @if(Session::get('role_id')==5)    
                               <tr>
                                <td> {{ $loop->iteration }}</td>                                 
                                <td>{{$z->zone_name}}</td>
                                <td>{{$z->address_line_1}}</td>    
                                <form action="{{route('zones.destroy',$z->zone_id) }}" method="post">       {{method_field('delete')}}
                                  @csrf                                
                                  <td>
                                    
                                    <a href="{{ route('zones.edit', $z->zone_id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
                                    <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> 
                                    </button> 
                                </td>
                            </form>

                        </tr>
                           @elseif(Session::get('role_id')==1)
                              <tr>
                                <td> {{ $loop->iteration }}</td>                
                                <td>{{$z->zone_name}}</td>
                                <td>{{$z->zone_desc}}</td>
                                <td>{{$z->address_line1}}</td>
                                <td>{{$z->address_line2}}</td> 

                                <form action="{{route('zones.destroy',$z->zone_id) }}" method="post">       {{method_field('delete')}}
                                  @csrf                             
                                 <td>                                
                                    <a href="{{ route('zones.edit',$z->zone_id)}}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
                                    <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> 
                                    </button> 
                                </td>
                            </form>

                        </tr>             
                        @endif
                       @endforeach    
                    </tbody>
                </table>
                @include('sweetalert::alert')
                <a href="{{route('zones.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Zone</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection