
@extends('layout.layout')
@section('title','Leaves')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple">
            <div class="grid-body ">
                <table class="table dataTable" id="example3" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Leave Type</th>
                            <th>Title</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                        <?php $i=1?>
                        @foreach($leave as $l)            
                        <tr>
                         <td> <?php echo  $i?></td>
                         <td>{{@$l->departments->name}}</td>
                         @if($l->leave_type==1)
                         <td>Fixed</td>
                         @else
                         <td>Variable</td>
                         @endif
                       
                         <td>{{$l->title}}</td>
                            <form action="{{ route('leaves.destroy',$l->id)}}" method="post">  
                                  {{ csrf_field() }}
                                  {{ method_field('DELETE') }}         
                                  <td> 
                                    <a href="{{ route('leaves.show', $l->id)}}" class="btn btn-success" style="color: white"><i class="fa fa-eye"></i></a>
                                    <a href="{{ route('leaves.edit',$l->id)}}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
                                    <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> 
                                </button> 
                                </td>
                            </form>
                        </tr>                   
                     <?php $i++ ?>
                     @endforeach
                   </tbody>
                </table>
            @include('sweetalert::alert')
            <a href="{{route('leaves.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>
             Add Leave</a>
            </div>
         </div>
      </div>
  </div>
 </div>
</div>  
@endsection