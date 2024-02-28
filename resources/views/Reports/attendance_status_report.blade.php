@extends('layout.layout')
@if(isset($rep_date))
    @section('title','Attendance Status Report ('.date('d M,Y',strtotime($rep_date)).")")
@else
    @section('title','Attendance Status Report ('.date('d M,Y',strtotime(date(NOW()))).")")
@endif
@section('content')

@include('Reports.print_header')
    <style type="text/css">
        .form-actions {background-color:#f6f7f8;border: 0px;margin-bottom: 20px;margin-top: 0px;padding: 19px 20px 9px;}
        .rcorners2 {border-radius: 25px;padding: 20px 10px;}
        .box-wrap{display:flex;gap:20px}
        .box-wrap+.box-wrap{margin-top:20px}
        .box-wrap .box{flex:1}
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="grid simple" style="position: static;">
                <div class="grid-title no-border">
                  <h4>Report <span class="semi-bold">Filters</span></h4>
                  <div class="tools"><a href="javascript:;" class="collapse"></a></div>
                </div>
                <div class="grid-body no-border" style="display: block;padding-bottom: 0;">

                <form class="form-no-horizontal-spacing" action="{{route('report')}}"
                        method="post" id="form-condensed" novalidate="novalidate">
                    @csrf
                    <div class="row form-row">
                        <div class="col-md-6">
                            @if($role_id ==5)
                                <label class="form-label"  style="font-size:15px">Department</label>
                                <select name="department[]" id="source" style="width:100%" tabindex="-1" class="search select2-offscreen" multiple placeholder="All Departments/Select Department">
                                    @foreach($departments as $dept)
                                        <option
                                            @if(!is_null($choosen_departments))
                                                @if(in_array($dept->id,$choosen_departments))
                                                    selected
                                            @endif
                                            @endif
                                            value="{{$dept->id}}">{{$dept->name}}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->any())
                                    <p style="color:red">
                                        {{$errors->first('search','Department field is required')}}
                                    </p>
                                @endif
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"  style="font-size:15px ">Date</label>
                            <div  class="input-append success date col-md-10 col-lg-4 no-padding" style="width:calc(100% - 36px);">
                                <input type="text"
                                    @if($rep_date) value="{{ date("Y/m/d",strtotime($rep_date)) }}" @endif
                                autocomplete="off" name="report_date" placeholder="Report Date" class="form-control">
                                <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                @if($errors->any())
                                    <p style="color:red">
                                        {{$errors->first('date','Date field is required')}}
                                    </p>
                                @endif
                            </div>

                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-md-6">
                            <label class="form-label"  style="font-size:15px ">Status</label>
                            <div class="radio">
                                <input id="all" @if($status=="all") checked @endif type="radio"  name="status" value="all">
                                <label for="all">All</label>
                                <input id="present" @if($status=="present") checked @endif type="radio" name="status" value="present" >
                                <label for="present">Present</label>
                                <input id="absent" @if($status=="absent") checked @endif type="radio" name="status" value="absent">
                                <label for="absent">Absent</label>
                                <input id="late" @if($status=="late") checked @endif type="radio" name="status" value="late">
                                <label for="late">Late</label>
                                <input id="earlyout" @if($status=="earlyout") checked @endif type="radio" name="status" value="earlyout">
                                <label for="earlyout">Early Timeout</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"  style="font-size:15px ">Sort By</label>
                            <div class="radio">
                                <input id="id" checked="" @if($orderby=="id") checked @endif type="radio"  name="orderby" value="id">
                                <label for="id">ID</label>
                                <input id="name" @if($orderby=="name") checked @endif type="radio" name="orderby" value="name" >
                                <label for="name">EmpName</label>
                                <input id="bps" @if($orderby=="bps") checked @endif type="radio" name="orderby" value="bps" >
                                <label for="bps">PayScale</label>

                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                <input id="asc" checked @if($ordertype=="asc") checked @endif type="radio" name="ordertype" value="asc" >
                                <label for="asc">ASC</label>
                                <input id="desc" @if($ordertype=="desc") checked @endif type="radio" name="ordertype" value="desc" >
                                <label for="desc">DESC</label>
                            </div>

                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-md-6">
                            @if($role_id ==5)
                                <label class="form-label"  style="font-size:15px">Time Categories</label>
                                <select name="tcats[]" id="source1" style="width:100%" tabindex="-1" class="search select2-offscreen" multiple placeholder="All Time Categories">
                                    @foreach($time_categories as $tcat)
                                        <option
                                            @if(!is_null($choosen_departments))
                                                @if(in_array($tcat->id,$choosen_departments))
                                                    selected
                                            @endif
                                            @endif
                                            value="{{$tcat->id}}">{{$tcat->title}}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->any())
                                    <p style="color:red">
                                        {{$errors->first('search','Time Category field is required')}}
                                    </p>
                                @endif
                            @endif
                        </div>
                        
                    </div>
                    <div class="form-actions">
                        <div class="pull-left">
                            <button class="btn btn-success btn-cons" type="submit" name="generate_report"><i class="icon-ok"></i>Generate Report</button>
                        </div>
                        @if(count($users)>0)
                            <div class="pull-right">
                                <a class="btn btn-success noPrint" id="print_report"><i class="fa fa-print"> </i> Print Full Report</a>
                            </div>
                        @endif
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    @if(count($users)>0)


        <div class="row">
            <div class="col-md-12">
                <div class="grid simple" style="position: static;">
                    <div class="grid-title no-border">
                    <h4>Report <span class="semi-bold">Summary</span></h4>
                    <div class="tools"><a href="javascript:;" class="collapse"></a></div>
                    </div>
                    <div class="grid-body no-border" style="display: block;">
                        @if(request()->status == 'all')

                            <div class="box-wrap">
                                <div class="box">
                                    <div class="bg-primary text-center rcorners2 ">
                                    <h2 class="semi-bold text-white weather-widget-big-text no-margin p-t-35 p-b-10">{{ $users->count() }}</h2>
                                    <h4 class="bold text-white">EMPLOYEES</h4></div>
                                </div>
                                <div class="box">
                                    <div class="bg-green text-center rcorners2">
                                        <h2 class="semi-bold text-white weather-widget-big-text no-margin p-t-35 p-b-10">
                                            {{ $user_attendance->whereIn('status',['present','late','earlyout'])->count() }}</h2>
                                        <h4 class="bold text-white">PRESENT</h4>
                                    </div>
                                </div>
                                <div class="box">
                                    <div class="bg-red text-center rcorners2">
                                        <h2 class="semi-bold text-white weather-widget-big-text no-margin p-t-35 p-b-10">
                                            {{ $user_attendance->whereIn('status',['late'])->count() }}</h2>
                                        <h4 class="bold text-white">LATE</h4>
                                    </div>
                                </div>

                                <div class="box">
                                    <div class="bg-yellow text-center rcorners2">
                                        <h2 class="semi-bold text-white weather-widget-big-text no-margin p-t-35 p-b-10">
                                        {{ ($users->count()-$total_emp_not_reg_prev_date) -($user_attendance->whereIn('status',['present','late','offday','earlyout'])->count()+$leave_users->count()+$attach_users->count()) }}</h2>
                                        <h4 class="bold text-white">ABSENT</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="box-wrap">
                                <div class="box">
                                    <div class="bg-warning text-center rcorners2">
                                        <h2 class="semi-bold text-warning weather-widget-big-text no-margin p-t-35 p-b-10">
                                        {{ $user_attendance->whereIn('status',['earlyout'])->count() }}</h2>
                                        <h4 class="bold">EARLY OUT</h4>
                                    </div>
                                </div>
                                <div class="box">
                                    <div class="bg-warning text-center rcorners2">
                                        <h2 class="semi-bold text-info weather-widget-big-text no-margin p-t-35 p-b-10">
                                        {{ $user_attendance->whereIn('status',['offday'])->count() }}</h2>
                                        <h4 class="bold">OFF DAY</h4>
                                    </div>
                                </div>
                                <div class="box">
                                    <div class="bg-success text-center rcorners2">
                                        <h2 class="semi-bold text-secondary weather-widget-big-text no-margin p-t-35 p-b-10">
                                            {{ $leave_users->count() }}</h2>
                                        <h4 class="bold">LEAVE</h4>
                                    </div>
                                </div>
                                <div class="box">
                                    <div class="bg-success text-center rcorners2">
                                        <h2 class="semi-bold text-danger weather-widget-big-text no-margin p-t-35 p-b-10">
                                            {{ $sleave_users->count() }}</h2>
                                        <h4 class="bold">SHORT LEAVE</h4>
                                    </div>
                                </div>
                                    <div class="box">
                                    <div class="bg-success text-center rcorners2">
                                        <h2 class="semi-bold text-danger weather-widget-big-text no-margin p-t-35 p-b-10">
                                        {{ $attach_users->count() }}</h2>
                                        <h4 class="bold">ATTACH</h4>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="att_status_report_attendance">
            <div class="col-md-12">
                <div class="grid simple" style="position: static;">
                    <div class="grid-title no-border">
                    <h4>Report <span class="semi-bold">Details</span></h4>
                    <div class="tools"><a href="javascript:;" class="collapse"></a></div>
                    </div>
                    <div class="grid-body no-border" style="display: block;">
                        <div class="table-responsive">
                            <table id="dataexample" class="table dataTable display nowrap" aria-describedby="example3_info" >
                                <!--<table class="table dataTable" id="example" aria-describedby="example3_info">-->
                                <thead>
                                <tr role="row">

                                    <th>S#</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>File #</th>
                                    <th>Gender</th>
                                    <th>Bps</th>
                                    <th>In</th>
                                    <th>Out</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                </tr>
                                </thead>

                                <tbody role="alert" aria-live="polite" aria-relevant="all" id="att_status_report_attendance_body">
                                    @foreach($user_attendance as $key=>$attendance)
                                        <tr
                                            @if($attendance['status'] == "absent")
                                                class="danger"
                                            @elseif($attendance['status'] == "late")
                                                class="warning"
                                            @elseif($attendance['status'] == "present")
                                                class="success"
                                            @elseif($attendance['status'] == "leave")
                                                class="info"
                                            @elseif($attendance['status'] == "attach")
                                                class="info"
                                            @endif
                                        >
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $attendance['user_id'] }}</td>
                                            <td>{{ $attendance['name'] }}</td>
                                            <td>{{ $attendance['department_name'] }}</td>
                                            <td>{{ $attendance['designation'] }}</td>
                                            <td>{{ $attendance['file_number']?:"--" }}</td>
                                            <td>{{ $attendance['gender'] }}</td>
                                            <td>{{ $attendance['bps']?:"--" }}</td>
                                            <td>{{ $attendance['time_in'] }}</td>
                                            <td>{{ $attendance['time_out'] }}</td>
                                            @if(\Session::get('role_id') == 7)
                                                @if($holidays->where('department_id',$attendance['department_id'])->count() >0)
                                                    <td>Holiday :: {{
                                                            $holidays->where('department_id',$attendance['department_id'])->first()->title
                                                        }}
                                                    </td>
                                                @else
                                                    <td>{{ $attendance['status'] }}</td>
                                                @endif
                                                @else
                                                @if($holidays->where('department_name',$attendance['department_name'])->count() >0)
                                                    <td>Holiday :: {{
                                                            $holidays->where('department_name',$attendance['department_name'])->first()->title
                                                        }}
                                                    </td>
                                                @else
                                                    <td>{{ $attendance['status'] }}</td>
                                                @endif
                                            @endif
                                            <td></td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                        <div class="onlyPrint">
                            <div class="table-responsive">
                                <table id="print_att_table" class="table">
                                    <thead>
                                    <tr role="row">

                                        <th>S#</th><th>ID</th><th>Name</th>
                                        <th>Department</th><th>Designation</th>
                                        <th>File #</th><th>Gender</th>
                                        <th>Bps</th><th>In</th><th>Out</th>
                                        <th>Status</th><th>Remarks</th>
                                    </tr>
                                    </thead>
                                    <tbody id="print_att_table_body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @include('include.important')
@endsection

