
@extends('layout.layout')
@section('title','Devices')
@section('content')
@include('sweetalert::alert')

<div class="row-fluid">
    <div class="col-md-12">
        <div class=""> <!-- Only required for left/right tabs -->
            <ul class="nav nav-tabs">
                @php
                    $count = 0;
                @endphp
                @foreach($statuses as $key=>$status)
                    <li @if($show_tab == $key) class="active" @endif ><a href="#{{$key}}" data-toggle="tab">{{$key}} ({{$status['Total']}})</a></li>
                    @php
                        $count++;
                    @endphp
                @endforeach
            </ul>
            <div class="tab-content">
                @php
                    $count = 0;
                @endphp
                <style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                    }

                    table, th, td {
                        border: 1px solid #c9d1d9;
                        vertical-align: middle !important;
                    }

                    th, td {
                        padding: 8px; /* Optional: Set padding for better spacing */
                    }
                </style>
                @foreach($statuses as $key=>$status)
                    <div class="tab-pane @if($show_tab == $key) active @endif" id="{{$key}}">
                        <div class="grid simple ">
                            <table style="width:100%!important;" class="mygeneraldatatable" id="{{$key}}_devices" >
                                    <thead>
                                        <tr role="row">
                                            <th>S.NO</th>
                                            <th>Serial Number</th>
                                            <th>Department</th>
                                            <th>Total Employees</th>
                                            <th>Active Employees</th>
                                            <th>Focal Person</th>
                                            <th>Contact</th>
                                            @if($key!='Online')
                                                <th>Remarks</th>
                                            @endif
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php $counter=1; @endphp

                                        @foreach($status['devices'] as $d)
                                            @php
                                                 $clientIds =[];
                                                 $other_deps = \App\DeviceDepartment::where('device_id', $d->id)->get();
                                                 if($other_deps->count() > 0){
                                                     $other_deps = $other_deps->pluck('dep_id')->toArray();
                                                     $clientIds = array_merge($clientIds, $other_deps);
                                                     $clientIds = array_unique($clientIds);
                                                 }
                                                 $all_clients = \App\Client::whereIn('id', $clientIds)->get();
                                                 $row_count = $all_clients->count();
                                                 $row_count++;
                                            @endphp
                                            <tr>
                                                <td rowspan="{{$row_count}}" > {{ $counter++ }}</td>
                                                <td rowspan="{{$row_count}}" >
                                                    @if(Session::get('role_id')==1)
                                                        <a href="{{ route('devices.edit',$d->id) }}" >{{$d->title}}</a>
                                                    @else
                                                        {{$d->title}}
                                                    @endif
                                                    <br>
                                                    {{$d->serial_number}}
                                                    <br>
                                                    <small><b>Last Activity:</b> {{$d->lastActivity ?? '--' }}</small>
                                                </td>
                                                @php
                                                    $clients = \App\Client::where('parent_id', optional($d->client)->id)->get();
                                                    $clientIds = $clients->pluck('id')->toArray();
                                                    $clientIds[] = optional($d->client)->id;
                                                    $totalEmployees = \App\Client::whereIn('id', $clientIds)->sum('total_employees');
                                                   $userCount = \App\User::whereIn('department_id', $clientIds)
                                                                ->whereIn('role_id', [6, 7])
                                                                ->where(['status' => 1])
                                                                ->whereExists(function ($query) {
                                                                    $query->select(\DB::raw(1))
                                                                        ->from('attendance')
                                                                        ->whereRaw('attendance.user_id = users.id');
                                                                })
                                                                ->distinct()
                                                                ->count();

                                                @endphp
                                                <td style="color: green;" >{{optional($d->client)->name}}</td>
                                                <td style="color: blue;" >{{$totalEmployees}} </td>
                                                <td style="color: #df7e07;" >{{$userCount}}</td>
                                                <td >{{$d->client?($d->client->focal_person?$d->client->focal_person:'Not Set'):'Not Set'}}</td>
                                                <td >{{$d->client?($d->client->contact?$d->client->contact:'Not Set'):'Not Set'}}</td>

                                                @if($key!='Online')
                                                    <th>{{$d->remarks}}</th>
                                                @endif
                                            </tr>
                                            @foreach($all_clients as $cl)
                                                @php
                                                    $clients = \App\Client::where('parent_id', $cl->id)->get();
                                                    $clientIds = $clients->pluck('id')->toArray();
                                                    $clientIds[] = $cl->id;
                                                    $totalEmployees = \App\Client::whereIn('id', $clientIds)->sum('total_employees');
                                                    $userCount = \App\User::whereIn('department_id', $clientIds)
                                                                ->whereExists(function ($query) {
                                                                    $query->select(\DB::raw(1))
                                                                        ->from('attendance')
                                                                        ->whereRaw('attendance.user_id = users.id');
                                                                })
                                                                ->distinct()
                                                                ->count();
                                                @endphp
                                                <tr>
                                                    <td style="color: green;" >{{$cl->name}}</td>
                                                    <td style="color: blue;">{{$totalEmployees}} </td>
                                                    <td style="color: #df7e07;" >{{$userCount}}</td>
                                                    <td >{{$cl?($cl->focal_person?$cl->focal_person:'Not Set'):'Not Set'}}</td>
                                                    <td >{{$cl?($cl->contact?$cl->contact:'Not Set'):'Not Set'}}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                        </div>
                    </div>
                    @php
                        $count++;
                    @endphp
                @endforeach

            </div>
        </div>
    </div>
</div>

@endsection
