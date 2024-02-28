
@extends('layout.layout')
@section('title','Regions')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">
                                               
                             <th>S.NO</th>
                             <th>Region Name</th>
                             <th>Address</th>
                             <th>Action</th>
             
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                       @foreach($regions as $r)
                           @if(Session::get('role_id')==5)
                           <tr> 
                            <td>{{$loop->iteration}}</td>                                  
                            <td>{{$r->region_name}}</td>
                            <td>{{$r->address_line_1}}</td>
                         <form action="{{route('regions.destroy',$r->region_id) }}" method="post">       {{method_field('delete')}}
                              @csrf
                              
                              <td> 
                                    
                                    <a href="{{ route('regions.edit',$r->region_id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
                                    <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> 
                                    </button> 
                                </td>
                            </form>

                        </tr>
                          @elseif(Session::get('role_id')==1)
                          <tr>
                           <td>{{$loop->iteration}}</td>                 
                           <td>{{$r->region_name}}</td>
                           
                           <td>{{$r->address_line1}}</td>
                            <form action="{{route('regions.destroy',$r->region_id) }}" method="post">       {{method_field('delete')}}
                              @csrf
                              
                              <td> 
                                   
                                    <a href="{{ route('regions.edit',$r->region_id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
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
                <a href="{{route('regions.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Region</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection