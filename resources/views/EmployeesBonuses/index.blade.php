
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
                            <th>Amount</th>
                            <th>satus</th>
                            <th>Date</th>
                            <th>remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                         @foreach($bonuses as $b)                            
                            <tr>
                              <td> {{ $loop->iteration }}</td>                 
                              <td>{{$b->users->name}}</td>
                              <td>{{$b->amount}}</td>
                              
                              @if($b->status==1)
                              <td>Bonus</td>
                              @else
                              <td>Deduction</td>
                              @endif
                              <td>{{$b->date}}</td>  
                              <td>{{$b->remarks}}</td>  
                              <form action="{{ url('employees_bonuses/'.$b->id) }}" method="post"> 
                                {{method_field('DELETE')}}
                                @csrf
                                <td>           
                                   
                                    <a href="{{ route('employees_bonuses.edit',$b->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
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
                <a href="{{route('employees_bonuses.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Bonus</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection