
@extends('layout.layout')
@section('title','Roles')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="example3" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">
                            <th>S.no</th>
                            <th>Title</th>
                            <th>No of Departments</th>
                            <th>No of Regions</th>
                            <th>No of Zones</th>
                            <th>No of Branches</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                          <?php $i=1?>
                              @foreach($records as $r)
                              
                                <tr>
                                  <td> <?php echo  $i?></td>
                                  <td>{{$r->title}}</td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>

                                    {{ csrf_field() }}      
                                    <td>        
                                    <a href="{{ route('jurs.edit',$r->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
                                </td>

                        </tr>
                         <?php  $i++ ?>
                         @endforeach
                        @include('sweetalert::alert')
                    </tbody>
                </table>
             
                <a href="{{route('jurs.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add New</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection