
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
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                          <?php $i=1?>
                              @foreach($role as $r)
                              <tbody>
                                <tr>
                                  <td> <?php echo  $i?></td>
                                  <td>{{$r->title}}</td>
                                  <td>{{$r->description}}</td>
                                  <form action="{{ route('roles.destroy',$r->id) }}" method="post">  
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}         
                                    <td>
                                     <a href="{{ url('assign_right',$r->id)}}"class="btn btn-primary" style="color: white" ><i class="fa fa-key"></i> </a>           
                                    <a href="{{ route('roles.edit',$r->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
                                    <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> 
                                    </button> 
                                </td>
                            </form>

                        </tr>
                         <?php  $i++ ?>
                         @endforeach
                        @include('sweetalert::alert')
                    </tbody>
                </table>
             
                <a href="{{route('roles.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Role</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection