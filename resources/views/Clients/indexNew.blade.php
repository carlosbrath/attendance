
@extends('layout.layout')
@section('title','Clients')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
                @php if(count($client)>0){ @endphp
                <table class="table" id="dataexample" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">           
                              
                               <th>S.no</th>
                               <th>Name</th>
                               <th>Parent</th>
                               <th>Email</th>
                               <th>Contact</th>
                               <th>Action</th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                     <?php $i=1?>
                            @foreach($client as $c)
                            <tr>
                              <td> <?php echo  $i?></td>          
                              <td><a href="/clients-new?parent_id={{$c->id}}">{{$c->name}}</a></td>          
                              <td>{{$c->parent?$c->parent->name:'Parent Department'}}</td>
                              <td>{{$c->email}}</td>
                              <td>{{$c->contact}}</td>
                              <form action="{{ route('clients.destroy',$c->id) }}" method="post">       
                                {{method_field('DELETE')}}
                                {{csrf_field()}}                    
                                <td style="padding-right:0px !important;"> 
                                    <a href="{{ route('clients.edit',$c->id) }}"class="btn btn-primary" style="color: white;font-size:11px" ><i class="fa fa-edit"></i> </a>                 
                                    {{--<button style="font-size:11px" type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> 
                                    </button> --}}
                                </td>
                            </form>

                        </tr>
                       <?php  $i++ ?>
                       @endforeach    
                    </tbody>
                </table>
                @php }else{ @endphp
                    <center>No record found.</center>
                @php } @endphp
                <div class="form-actions">
                    @php if($parent_id>0){ @endphp
                    <a href="/clients-new?parent_id={{$prev_parent_id}}"  class="btn btn-secondary btn-cons"  type="button"><i class="icon-ok"></i>Go Back</a>
                    @php } @endphp
                    <a href="{{route('clients.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Client</a>
                    @include('sweetalert::alert')
                </div>
            </div>

        </div>
    </div>
</div>
</div>
</div>  

@endsection