
@extends('layout.layout')
@section('title','Devices')
@section('content')
@include('sweetalert::alert')

<div class="row-fluid">
    <div class="span12">

        <h2 id="offlineDevices"  style="text-align:center">Offline Devices</h2>
        <div class="grid simple ">
            <div class="grid-body ">
                @php
                    $devices_activity = [];
                    foreach($devices_stats as $ds){
                        $devices_activity[$ds['serialno']] = $ds['lastactivity'];
                    }
                @endphp
                <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">
                            <th>S.NO</th>
                            <th>Title</th>
                            <th>Serial Number</th>
                            @if(Session::get('role_id')==1)<th>Action</th>@endif    
                        </tr>
                    </thead>
                    
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                        @php $counter=1; @endphp
                         @foreach($devices as $d)
                            @php
                                $last_activity = "0";
                                if(array_key_exists($d->serial_number,$devices_activity)){
                                    $last_activity = $devices_activity[$d->serial_number];
                                }

                                $minutes_to_add = 10;
                                if($last_activity!="0"){
                                    $time = new DateTime($last_activity);
                                    $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));

                                    $stamp = $time->format('Y-m-d H:i');                  
                                    if($stamp>date('Y-m-d H:i')){
                                        continue;                                   
                                    }         
                                }
                             
                             @endphp
                            
                            <tr>
                                 <td> {{ $counter++ }}</td>
                                 <td>
                                        @if(Session::get('role_id')==1)
                                            <a href="{{ route('devices.edit',$d->id) }}" >{{$d->title}}</a>
                                        @else
                                            {{$d->title}}
                                        @endif
                                        <br><small><b>Department:</b> {{$d->client?$d->client->name:''}}</small>
                                 </td>
                                 <td>{{$d->serial_number}}<br><small>@php echo ($last_activity==0)?'<span class="badge badge-warning">Not Active</span>':'<b>Last Activity:</b> '.$last_activity; @endphp</small></td>  
                                @if(Session::get('role_id')==1)
                                    <td> 
                                        <form action="{{route('devices.destroy',$d->id) }}" method="post">       {{method_field('delete')}}
                                            @csrf              
                                            <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                                                <i class="fa fa-trash"></i> 
                                            </button> 
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <h2 id="offlineDevices" style="text-align:center">Online Devices</h2>

        <div class="grid simple ">
            <div class="grid-body ">

                <table class="table dataTable" id="dataexample1" aria-describedby="example4_info">
                    <thead>
                        <tr role="row">
                            <th>S.NO</th>
                            <th>Title</th>
                            <th>Serial Number</th>
                            @if(Session::get('role_id')==1)<th>Action</th>@endif            
                        </tr>
                    </thead>
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                         @php $counter=1; @endphp
                         @foreach($devices as $d)
                             @if(array_key_exists($d->serial_number,$devices_activity))
                                @php
                                    $last_activity = "0";
                                    $last_activity = $devices_activity[$d->serial_number];

                                    $minutes_to_add = 10;
                                    $time = new DateTime($last_activity);
                                    $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));

                                    $stamp = $time->format('Y-m-d H:i');                  
                                    if($stamp>date('Y-m-d H:i')){
                                                                           
                                    }
                                    else
                                    {
                                       continue; 
                                    }   
                                 
                                 @endphp
                                <tr> 
                                    <td> {{ $counter++ }}</td>                 
                                    <td>
                                        @if(Session::get('role_id')==1)
                                            <a href="{{ route('devices.edit',$d->id) }}" >{{$d->title}}</a>
                                        @else
                                            {{$d->title}}
                                        @endif
                                        <br><small><b>Department:</b> {{$d->client?$d->client->name:''}}</small>
                                    </td>
                                    <td>{{$d->serial_number}}<br><small><b>Last Activity:</b> {{$devices_activity[$d->serial_number]}}</small></td>
                                    <td>
                                        <form action="{{route('devices.destroy',$d->id) }}" method="post">       
                                            {{method_field('delete')}}
                                            @csrf
                                            <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
                                                <i class="fa fa-trash"></i> 
                                            </button> 
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection