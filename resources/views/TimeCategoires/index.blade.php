
@extends('layout.layout')
@section('title','TimeCategoires')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
              <div class="table-responsive">
                <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">

                              <th>S.NO</th>
                              <th>Title</th>
                              <th>Department</th>
                              <th> In</th>
                              <th> Out</th>
                              <th>Grace Time</th>
                              <th>Action</th>


                        </tr>
                    </thead>

                    <tbody >
                        @foreach($timecategories as $t)

                              <tr>        
                                <td> {{ $loop->iteration }}</td>
                                <td>{{$t->title}} (<span style="color:red">@if($t->time_cat_type==1) Non.Roster @else Roster @endif</span>)
                                <span style="color:red">@if($t->is_night_shift==1) Night Shift @endif</span>
                                </td>
                                <td>{{$t->departments->name}}</td>
                                <td>{{$t->tc_monday_in}}</td>
                                <td>{{$t->tc_monday_out}}</td>
                                <td>{{$t->grace_time}}</td>

                                <form action="{{ route('timecategories.destroy',$t->id) }}" method="post">  
                                  {{ csrf_field() }}
                                  {{ method_field('DELETE') }}     

                                  <td>   
                                    <a href="{{ route('timecategories.edit',$t->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
                                    <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> 
                                    </button> 
                                </td>
                            </form>

                        </tr>

                       @endforeach
                         @include('sweetalert::alert')
                    </tbody>
                </table>
              </div>
              
                <a href="{{route('timecategories.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="fa fa-plus"></i> Add Time Category</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection