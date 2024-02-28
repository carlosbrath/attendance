
@extends('layout.layout')
@section('title','Employee Bonuses/Deduction')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">
                            <th>S.No</th>
                            <th>User</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                         @foreach($ded_day_off as $d_dayoff)                            
                            <tr>
                              <td> {{ $loop->iteration }}</td>                 
                              <td>{{ $d_dayoff->users?$d_dayoff->users->name:'' }}</td>   
                              <td>{{$d_dayoff->off_day_date}}</td>  
                              <form action="{{ url('day_off/'.$d_dayoff->id) }}" method="post"> 
                                {{method_field('DELETE')}}
                                @csrf
                                <td>           
                                    <!-- <a href="{{ route('day_off.edit',$d_dayoff->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                  -->
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
                <a href="{{route('day_off.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Day Off</a>
            </div>
        </div>
    </div>
</div>
</div>
</div>  

@endsection