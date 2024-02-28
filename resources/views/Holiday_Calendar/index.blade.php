
@extends('layout.layout')
@section('title','Holidays Calendar List')
@section('content')
<style>
  .customTableCss{
         width:100%!important;
   }
</style>
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">
        <div class="grid-body ">
                    <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#home">Department Wise</a></li>
                                <li><a data-toggle="tab" href="#menu1">Employee Wise</a></li>
                            
                    </ul>
                    <div class="tab-content">
                        <div id="home" class="tab-pane fade in active">
            <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
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
                            <th>Date</th>
                            <th>Date From</th>
                            <th>Date To</th>
                            <th>Days</th>
                            <th>Action </th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                                      
                          <?php $i=1; ?>
                           @foreach($holiday_calendar->where('user_id','=',null) as $h)                
                            <tr>
                              <td>{{ $loop->iteration }}</td>              
                              <td>{{$h->title}} @if($h->is_all_departments==1) (All Departments) @endif</td>
                              <td>{{$h->date}}</td>
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
                </div>
                <div id="menu1" class="tab-pane fade">
                <table class="table dataTable customTableCss" id="dataexample_table1" aria-describedby="example3_info">
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
                            <th>Department Name</th>
                            <th>Emp Name</th>
                            <th>Date</th>
                           
                            <th>Days</th>
                            <th>Action </th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                                      
                          <?php $i=1; ?>
                           @foreach($holiday_calendar->where('user_id','!=',null) as $h)                
                            <tr>
                              <td>{{ $loop->iteration }}</td>              
                              <td>{{$h->title}} </td>
                              <td>{{$h->department->name}} </td>
                              <td>{{$h->user->name}} </td>
                              <td>{{$h->date}}</td>
                           
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
              </div>
            </div>

          
          
                @include('sweetalert::alert')
                <a href="{{route('holidays.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Holiday</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection