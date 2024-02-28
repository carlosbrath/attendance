
@extends('layout.layout')
@section('title','Sub Department')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                <table class="table dataTable" id="example3" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">           
                              
                               <th>S.no</th>
                               <th>Name</th>
                               <th>Email</th>
                               <th>Contact</th>
                               <th>Image</th>
                               <th>Action</th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                     <?php $i=1?>
                            @foreach($sub_departments as $c)
                            <tr>
                              <td> <?php echo  $i?></td>          
                              <td>{{$c->name}}</td>
                              <td>{{$c->email}}</td>
                              <td>{{$c->contact}}</td>
                              @if($c->logo!='')
                              <td>
                               <img width="80px" height="80px" height="" src ="<?php echo asset("photos/$c->logo")?>" /></td>
                              @else
                              <td><img width="80px" height="80px" height="" src = "http://localhost/timenex-v2/storage/app/photos/logo.png" /></td>
                              @endif
                              <form action="{{ route('clients.destroy',$c->id) }}" method="post">       
                                {{method_field('DELETE')}}
                                {{csrf_field()}}                    
                                <td> 
                                    <a href="{{ route('clients.edit',$c->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>                 
                                    <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> 
                                    </button> 
                                </td>
                            </form>

                        </tr>
                       <?php  $i++ ?>
                       @endforeach    
                    </tbody>
                </table>
                @include('sweetalert::alert')
                <a href="{{route('clients.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Client</a>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection