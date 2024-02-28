
@extends('layout.layout')
@section('title','Holidays Calendar List')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="example3"  aria-describedby="example3_info">
                  <div class="row">
                     <div class="col-md-6">
                      <div class="toolbar"></div></div>
                      <div class="col-md-6">
                     </div>
                  </div>
                    <thead>
                        <tr role="row">
                            <th>S.No</th>
                            <th>Title</th>
                            <th>Date From</th>
                            <th>Date To</th>
                            <th>Days</th>
                            <th>Action </th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                                      
                          <?php $i=1; ?>
                           @foreach($holiday_calendar as $h)                
                            <tr>
                              <td>{{ $loop->iteration }}</td>              
                              <td>{{$h->title}}</td>
                              <td>{{$h->date_from}}</td>
                              <td>{{$h->date_to}}</td>
                              <td>{{$h->days}}</td>
                              <form action="{{route('holidays.destroy',$h->id) }}" method="post">       
                                {{method_field('delete')}}
                                 @csrf
                               <td>                               
                                  <a href="{{ route('holidays.edit',$h->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i></a>                 
                                    <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> 
                                    </button> 
                               </td>
                            </form>
                        </tr>
                       <?php $i++;?>
                      @endforeach
                    </tbody>
                </table>
                @include('sweetalert::alert')
                <a href="{{route('holidays.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Holiday</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection