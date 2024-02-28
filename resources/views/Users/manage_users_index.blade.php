@extends('layout.layout')
@section('title','Manage Users')
@section('content')
    <div class="row-fluid">
        <div class="span12">
            <div class="grid simple ">
                <a href="{{route('manage_users_create')}}" class="btn btn-primary btn-cons" ><i class="icon-ok"></i>Add New</a>
                <div class="grid-body ">
                    <table class="table dataTable" id="example3" aria-describedby="example3_info">
                        <thead>
                        <tr role="row">
                            <th>S.no</th>
                            <th>Name</th>
                            <th>Cnic</th>
                            <th>Email</th>
                        </tr>
                        </thead>
                        <tbody role="alert" aria-live="polite" aria-relevant="all">
                        <?php $i=1?>
                        @foreach($users as $user)
                            <tbody>
                            <tr>
                                <td> <?php echo  $i?></td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->cnic}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                    <a href="{{ route('manage_users_edit',$user->id) }}" class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>
                                </td>


                            </tr>
                                <?php  $i++ ?>
                            @endforeach
                            @include('sweetalert::alert')
                            </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    </div>

@endsection
