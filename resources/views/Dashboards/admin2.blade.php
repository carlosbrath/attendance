@extends('layout.layout')
@section('title', 'State Dashboard')
@section('content')
<form action="" class="col-md-12 tiles card_shadow white p-t-20 p-b-20 p-l-20 p-r-20 p-l-20  dataTables_filter  m-b-5" method="post">
    {{ csrf_field() }}
    <div class="row bg-white">
        <div class="col-md-12"></div>

        <div class="col-md-3 mb-2">
            <select name="allregions" id="allregions" class="select2 form-control">

            </select>
        </div>

        <div class="col-md-3 mb-2">
            <select name="department_id" id="all_departments" class="select2 form-control">

            </select>
        </div>
        <div class="col-md-3 mb-2">
            <input type="date" name="date" class="form-control" value="{{ $data['c_date'] }}" />
        </div>
        <div class="col-md-3 mb-2">
            <button type="submit" class="btn btn-primary btn-success" name="submit" value="Filter"> Filter</button>
            </input>
</form>
</div>
</div>

@php
@$data['reg_employees'] += @$external_departments_stats['total'];

$present_male = @$data['present']['male'] + @$external_departments_stats['present'];
$roster_employees = @$data['roster_employees'];
$present_female = @$data['present']['female'];

$present = $present_male + $present_female;

$present_g = @$data['present']['g_male'] + @$data['present']['g_female'];
$present_ng = @$data['present']['ng_male'] + @$data['present']['ng_female'];
$present_g_male = @$data['present']['g_male'];
$present_g_female = @$data['present']['g_female'];
$present_ng_male = @$data['present']['ng_male'];
$present_ng_female = @$data['present']['ng_female'];
$percent_present = 0;
@endphp

@php
$percent_present = $data['reg_employees'] > 0 ? ceil(($present / $data['reg_employees']) * 100) : 0;
@endphp

@php
$absent_male = @$data['absent']['male'] + @$external_departments_stats['absent'];
$absent_female = @$data['absent']['female'];
$absent = @$data['absent']['total'] + @$external_departments_stats['absent'];
$shift_not_started = @$data['absent']['shift_not_started'] + @$external_departments_stats['shift_not_started'];
$absent_g = @$data['absent']['g_male'] + @$data['absent']['g_female'];
$absent_ng = @$data['absent']['ng_male'] + @$data['absent']['ng_female'];
$absent_g_male = @$data['absent']['g_male'];
$absent_g_female = @$data['absent']['g_female'];
$absent_ng_male = @$data['absent']['ng_male'];
$absent_ng_female = @$data['absent']['ng_female'];
$percent_absent = 0;

@endphp

@php
$percent_absent = $data['reg_employees'] > 0 ? ceil(($absent / $data['reg_employees']) * 100) : 0;
@endphp


<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<style>
    .card {
        height: 85px;
        background-size: contain;
        margin: 0px;
        padding: 0px;
        display: flex;
        background-color: #144681;
        justify-content: center;
        padding-left: 20px;
        flex-direction: column;
        background-repeat: no-repeat;
        border-radius: 10px;
        background-position: right;
    }

    .b1 {
        background-image: url("{{ asset('assets/img/dash/a.png') }}");
        /* background-color: #0c27c0; */
    }

    .b2 {
        background-image: url("{{ asset('assets/img/dash/b.png') }}");
        /* background-color: #0c7fc0; */
    }

    .b-off {
        background-color: #a007a0;
    }

    .b3 {
        background-image: url("{{ asset('assets/img/dash/c.png') }}");
        background-color: #00c851;
        /* background-image: url("{{ asset('assets/img/b3.png') }}"); */
    }

    .bleave {
        background-image: url("{{ asset('assets/img/dash/c.png') }}");
        background-color: dodgerblue;
        /* background-image: url("{{ asset('assets/img/b3.png') }}"); */
    }

    .b4 {
        background-image: url("{{ asset('assets/img/dash/d.png') }}");
        background-color: #ff4444;
        /* background-image:url("{{ asset('assets/img/b4.png') }}"); */
    }

    .b5 {
        background-image: url("{{ asset('assets/img/dash/e.png') }}");
        background-color: #f3a406;
        /* background-image:url("{{ asset('assets/img/b5.png') }}"); */
    }

    .b6 {
        background-image: url("{{ asset('assets/img/dash/f.png') }}");
        /* background-image:url("{{ asset('assets/img/b6.png') }}"); */
    }

    .b7 {
        background-image: url("{{ asset('assets/img/dash/g.png') }}");
        /* background-image:url("{{ asset('assets/img/b7.png') }}"); */
    }

    .b8 {
        background-image: url("{{ asset('assets/img/dash/h.png') }}");
        /* background-image:url("{{ asset('assets/img/b8.png') }}"); */
    }

    .b9 {
        background-image: url("{{ asset('assets/img/dash/i.png') }}");
        /* background-image:url("{{ asset('assets/img/b9.png') }}"); */
    }

    .b10 {
        background-image: url("{{ asset('assets/img/dash/j.png') }}");
        /* background-image:url("{{ asset('assets/img/b10.png') }}"); */
    }

    .b11 {
        background-image: url("{{ asset('assets/img/dash/k.png') }}");
        /* background-color: #0c7fc0; */
    }

    .b12,
    .b13,
    .b14,
    .b15 {
        background-color: #26718e;
    }

    .no_gutter {
        padding: 0px;
        margin: 0px;
    }

    .text_heading {
        font-size: 18px;
        margin: 0px;
        color: white;
    }

    .text_count {
        font-size: 30px;
        color: white;
        margin: 0px;
        line-height: 50px;
        font-weight: bold;
    }

    .card_round {
        flex: 1;
        border-radius: 15px;
        padding-top: 20px;
        padding-bottom: 20px;
        background-color: white;
        /* box-shadow: 2px 2px 4px 2px #0000001c; */
    }

    .card_main {
        padding: 5px;
    }

    .card_shadow {
        box-shadow: -5px 4px 10px 0px #0000001c;
    }



    .absolute-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .text-center {
        text-align: center;
    }

    .text-percent {
        font-size: 1.3em;
        font-weight: bold;
        margin-bottom: 0px;
    }


    .align-center {
        align-items: center;
    }

    .card_chart {

        justify-content: space-between;
        display: flex;
        flex-direction: column;
        padding: 0px
    }
</style>

<style type="text/css">
    .dept_table tr {
        background-color: #F8F8F8 !important;
        border-width: 0px;
        box-shadow: 0px 2px 4px 1px rgb(0 0 0 / 10%);
        border-radius: 10px;


    }

    .dept_table td,
    .dept_table th {
        padding: 10px 0px !important;
    }

    .dept_table td {
        vertical-align: middle !important;
        text-align: center;
    }



    .dept_table thead tr {
        background-color: transparent !important;
        box-shadow: 0px 4px 8px 0px rgba(0, 0, 0, 0);
    }

    .dept_table thead th {
        font-size: 11px !important;
        font-weight: bold;
        color: #0f0f0f !important;
        text-align: center;
    }

    .dept_table td {
        border-top: 0px !important
    }

    t_p {
        color: "

    }

    .dept_table td:first-child {
        border-radius: 10px 0px 0px 10px !important;
        padding-left: 10px !important;
    }


    .dept_table td:last-child {
        border-radius: 0px 10px 10px 0px !important;
    }

    .dept_table td span {
        font-size: 16px;
        font-weight: bold;
        color: #0f0f0f !important;
    }

    .dept_table td a {
        font-size: 16px;
        font-weight: bold;
        color: #0f0f0f;
    }

    .dept_table td:nth-child(2) {
        text-align: start !important;
    }

    .dept_table td:nth-child(2) a {
        text-align: start !important;
        font-size: 14px;
    }

    .dept_table th:nth-child(2) {
        text-align: start !important;
    }

    .width_10 {
        width: 8% !important
    }

    .dt-button {
        border: 1px solid #0003 !important;
        background-color: transparent !important;
        border-radius: 8px !important;
        background-image: linear-gradient(to bottom, transparent 0%, transparent 100%) !important;
    }

    .dataTables_filter input , .dataTables_filter select  {
        border: 1px solid #0003 !important;
        background-color: transparent !important;
        border-radius: 8px !important;
        background-image: linear-gradient(to bottom, transparent 0%, transparent 100%) !important;
        height: 35px !important;
    }

    .t-present {
        color: #00C851 !important;
    }

    .t-absent {
        color: #FF4444 !important;
    }

    .t-late {
        color: #F3A305 !important;
    }

    .tiles {
        border-radius: 15px
    }
</style>


<div class="row no_gutter m-t-20">
    <div class="col-md-3 no_gutter card_main">
        <div class="card_round card_shadow card_chart d-flex align-center">
            <div id="myChart" style="height:370px;padding:20px 0"></div>
        </div>


    </div>
    <div class="col-md-9 no_gutter">
        <div class="row no_gutter">
            <div class="col-md-4 p-2 card_main">

                <div class="card b1 card_shadow">
                    <p class="text_heading">Total Employees</p>
                    <p class="text_count">{{ number_format($data['total_employees'], 0) }}</p>
                </div>
            </div>
            <div class="col-md-4 p-2 card_main">
                <div class="card b2 card_shadow">
                    <p class="text_heading">Biometric Registration</p>
                    <a href="{{ route('dw-employees', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
                        <p class="text_count">{{ number_format($data['reg_employees'], 0) }}</p>
                    </a>
                </div>
            </div>
            <div class="col-md-4 p-2 card_main">
                <div class="card b11 card_shadow">
                    <p class="text_heading">Unaccounted</p>
                    <p class="text_count">
                        @php
                        $unreg = $data['total_employees'] - $data['reg_employees'];
                        @endphp
                        {{ number_format($unreg, 0) }}
                    </p>
                </div>
            </div>
            <div class="col-md-3 p-2 card_main">
                <div class="card b3 card_shadow">
                    <p class="text_heading">Present</p>
                    <a href="{{ route('dw-present', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
                        <p class="text_count">{{ $present }}</p>
                    </a>
                </div>
            </div>
            <div class="col-md-3 p-2 card_main">
                <div class="card b4 card_shadow">
                    <p class="text_heading">Absent</p>
                    <a href="{{ route('dw-absent', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
                        <p class="text_count">{{ $absent }}</p>
                    </a>
                </div>
            </div>
            <div class="col-md-3 p-2 card_main">
                <div class="card b5 card_shadow">
                    <p class="text_heading">Late</p>
                    <a href="{{ route('dw-late', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
                        <p class="text_count">{{ @$data['late'] + @$external_departments_stats['late'] }}</p>
                    </a>
                </div>
            </div>
            <div class="col-md-3 p-2 card_main">
                <a href="{{ route('dw-leave', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
                    <div class="card bleave card_shadow">
                        <p class="text_heading">Leave</p>
                        <p class="text_count">{{ @$data['leave'] + @$external_departments_stats['onleave'] }}</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3 p-2 card_main">
                <div class="card b8 card_shadow">
                    <p class="text_heading">On Schedule</p>
                    <a href="{{ route('dw-os-employees', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
                        <p class="text_count">
                            {{ @$data['roster_employees'] + @$external_departments_stats['onschedule'] }}
                        </p>
                    </a>
                </div>
            </div>
            <div class="col-md-3 p-2 card_main">
                <a href="{{ route('dw-offdays', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
                    <div class="card b9 card_shadow">
                        <p class="text_heading">Off Days</p>
                        <p class="text_count">{{ @$data['offdays'] }}</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 p-2 card_main">
                <div class="card b10 card_shadow">
                    <p class="text_heading">Shift Not Started</p>
                    <a href="{{ route('dw-sna-employees', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
                        <p class="text_count">{{ $shift_not_started }}</p>
                    </a>
                </div>
            </div>

            <div class="col-md-3 p-2 card_main">
                <div class="card b10 card_shadow">
                    <p class="text_heading">Attach/Tour</p>
                    <a href="{{ route('dw-attach', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
                        <p class="text_count">{{ @$data['attach'] }}</p></span>
                    </a>
                </div>
            </div>


            <div class="col-md-3 p-2 card_main">
                <div class="card b12 card_shadow">
                    <p class="text_heading">Total Devices</p>
                    <a href="{{ route('devices.index', ['department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
                        <p class="text_count">{{ $data['devices']['Online']+$data['devices']['Offline']+$data['devices']['Inactive']  }}</p>
                    </a>
                </div>
            </div>
            <div class="col-md-3 p-2 card_main">
                <div class="card b13 card_shadow">
                    <p class="text_heading">Online</p>
                    <a href="{{ route('devices.index', [ 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
                        <p class="text_count">{{  $data['devices']['Online'] }}</p>
                    </a>
                </div>
            </div>
            <div class="col-md-3 p-2 card_main">
                <div class="card b14 card_shadow">
                    <p class="text_heading">Offline</p>
                    <a href="{{ route('devices.index', ['department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs'],'show_tab'=>'Offline']) }}" target="_blank">
                        <p class="text_count">{{  $data['devices']['Offline'] }}</p>
                    </a>
                </div>
            </div>
            <div class="col-md-3 p-2 card_main">
                <div class="card b13 card_shadow">
                    <p class="text_heading">In Active</p>
                    <a href="{{ route('devices.index', [ 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs'],'show_tab'=>'Inactive']) }}" target="_blank">
                        <p class="text_count">{{  $data['devices']['Inactive'] }}</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- tabular form itb -->

<div class="row p-t-10">
    <div class="col-md-12 col-vlg-12 m-b-10 ">
        <div class="tiles card_shadow white p-t-20 p-b-20 p-l-20 p-r-20">
            <table class="table no-more-tables m-t-20 m-l-0 m-b-30 dept_table" id="dataexample" style="border-collapse: separate; border-spacing: 0 15px">
                <thead style="">
                    <tr>
                        <th class="width_10"></th>
                        <th class="width_20">Department</th>
                        <th class="width_10">Total</th>
                        <th class="width_10">Biometric Registration</th>
                        <th class="width_10">Unaccounted</th>
                        <th class="width_10">Present</th>
                        <th class="width_10">Absent</th>
                        <th class="width_10">Late</th>
                        <th class="width_10">Leave</th>
                        <th class="width_10">Attach</th>
                        <th class="width_10">Off Days</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    @foreach ($sub_departments_stats as $key => $department)
                    <tr>
                        <td> <img width="50" height="50" src="https://ajk.gov.pk/wp-content/uploads/2022/10/AzadKashmirSeal.png" class="attachment-full size-full wp-image-159" alt=""></td>
                        <td class="v-align-middle bold text-success">
                            <a href="{{ route('dw-employees', ['date' => $data['c_date'], 'department_id' => $key, 'selected_dmgs' => $data['selected_dmgs']]) }}">
                                {{ $department['name'] }}
                            </a>
                        </td>
                        <td class="v-align-middle"><span class="muted">

                                {{ $department['stats']['total_employees'] }}
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                <a href="{{ route('dw-employees', ['date' => $data['c_date'], 'department_id' => $key, 'list' => 'yes', 'selected_dmgs' => $data['selected_dmgs']]) }}">
                                    {{ $department['stats']['reg_employees'] }}
                                </a>
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                {{ $department['stats']['total_employees'] - $department['stats']['reg_employees'] }}
                            </span>
                        </td>

                        <td class="v-align-middle "><span class="muted">
                                <a class="t-present" href="{{ route('dw-present', ['date' => $data['c_date'], 'department_id' => $key, 'list' => 'yes', 'selected_dmgs' => $data['selected_dmgs']]) }}">
                                    {{ $department['stats']['present']->male + $department['stats']['present']->female }}
                                </a>
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                <a class="t-absent" href="{{ route('dw-absent', ['date' => $data['c_date'], 'department_id' => $key, 'list' => 'yes', 'selected_dmgs' => $data['selected_dmgs']]) }}">
                                    {{ $department['stats']['absent']->male + $department['stats']['absent']->female }}
                                </a>
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                <a class="t-late" href="{{ route('dw-late', ['date' => $data['c_date'], 'department_id' => $key, 'list' => 'yes', 'selected_dmgs' => $data['selected_dmgs']]) }}">
                                    {{ $department['stats']['late'] }}
                                </a>
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                <a href="{{ route('dw-leave', ['date' => $data['c_date'], 'department_id' => $key, 'list' => 'yes', 'selected_dmgs' => $data['selected_dmgs']]) }}">
                                    {{ $department['stats']['leave'] }}
                                </a>
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                <a href="{{ route('dw-attach', ['date' => $data['c_date'], 'department_id' => $key, 'list' => 'yes', 'selected_dmgs' => $data['selected_dmgs']]) }}">
                                    {{ $department['stats']['attach'] }}
                                </a>
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                <a href="{{ route('dw-offdays', ['date' => $data['c_date'], 'department_id' => $key, 'list' => 'yes', 'selected_dmgs' => $data['selected_dmgs']]) }}">
                                    {{ $department['stats']['offdays'] }}
                                </a>
                            </span>
                        </td>
                    </tr>
                    <?php $i++; ?>
                    @endforeach

                    @if($external_departments)
                    @foreach ($external_departments as $key => $department)
                    <tr>
                        <td> <img width="50" height="50" src="https://ajk.gov.pk/wp-content/uploads/2022/10/AzadKashmirSeal.png" class="attachment-full size-full wp-image-159" alt=""></td>

                        <td class="v-align-middle bold text-success">
                            <a href="#">
                                {{ $key }}
                            </a>
                        </td>
                        <td class="v-align-middle"><span class="muted">

                                -
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                <a href="#">
                                    {{ $department['total'] }}
                                </a>
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                -
                            </span>
                        </td>

                        <td class="v-align-middle"><span class="muted">
                                <a class="t-present" href="#">
                                    {{ $department['present'] }}
                                </a>
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                <a class="t-absent" href="#">
                                    {{ $department['absent'] }}
                                </a>
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                <a class="t-late" href="#">
                                    {{ $department['late'] }}
                                </a>
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                <a href="#">
                                    {{ $department['onleave'] }}
                                </a>
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                <a href="#">
                                    {{ $department['onattach'] }}
                                </a>
                            </span>
                        </td>
                        <td class="v-align-middle"><span class="muted">
                                <a href="#">
                                    -
                                </a>
                            </span>
                        </td>
                    </tr>
                    <?php $i++; ?>
                    @endforeach
                    @endif
                </tbody>
            </table>

        </div>
    </div>
</div>






<div class="row mt-3">
    <div class="col-md-6 col-vlg-6 m-b-10 ">
        <div class="tiles card_shadow white p-t-10 p-b-5 p-l-20 p-r-20">
            <h3 class="text-center p-t-10" style="font-weight: bold"">Present</h3>
                <table class=" table no-more-tables m-t-10 m-l-0 dept_table" id="dataexample" style="border-collapse: separate; border-spacing: 0 15px">
                <thead style="">
                    <tr>
                        <th style="width:0%"></th>
                        <th style="width:40%"></th>
                        <th style="width:20%"><i class="fa fa-male fa-3x"> </i> </th>
                        <th style="width:20%"><i class="fa fa-female fa-3x"> </i> </th>
                        <th style="width:20%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td class="v-align-middle bold text-success"><a>Gazzetted Staff</a></td>
                        <td><span class="muted bold ">{{ $present_g_male }} </span> </td>
                        <td><span class="muted bold ">{{ $present_g_female }} </span> </td>
                        <td><span class="muted bold">{{ $present_g }} </span> </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="v-align-middle bold text-success"><a>Non Gazzetted Staff</a></td>
                        <td><span class="muted bold text-info">{{ $present_ng_male }} </span> </td>
                        <td><span class="muted bold text-info">{{ $present_ng_female }} </span> </td>
                        <td><span class="muted bold text-info">{{ $present_ng }}</span> </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="v-align-middle bold text-success"><a>Total</a></td>
                        <td><span class="muted bold text-info">{{ $present_g_male +  $present_ng_male  }} </span> </td>
                        <td><span class="muted bold text-info">{{ $present_g_female + $present_ng_female  }} </span> </td>
                        <td><span class="muted bold text-info">{{ $present_g + $present_ng }}</span> </td>
                    </tr>
                </tbody>
                </table>

        </div>
    </div>

    <div class="col-md-6 col-vlg-6 m-b-10 ">
        <div class="tiles card_shadow white p-t-10 p-b-5 p-l-20 p-r-20">
            <h3 class="text-center p-t-10" style="font-weight: bold"">Absent</h3>
                <table class=" table no-more-tables m-t-10 m-l-0 dept_table" id="dataexample" style="border-collapse: separate; border-spacing: 0 15px">
                <thead style="">
                    <tr>
                        <th style="width:0%"></th>
                        <th style="width:40%"></th>
                        <th style="width:20%"><i class="fa fa-male fa-3x"> </i> </th>
                        <th style="width:20%"><i class="fa fa-female fa-3x"> </i> </th>
                        <th style="width:20%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td class="v-align-middle bold text-success"><a>Gazzetted Staff</a></td>
                        <td><span class="muted bold ">{{ $absent_g_male }} </span> </td>
                        <td><span class="muted bold ">{{ $absent_g_female }} </span> </td>
                        <td><span class="muted bold">{{ $absent_g  }} </span> </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="v-align-middle bold text-success"><a>Non Gazzetted Staff</a></td>
                        <td><span class="muted bold text-info">{{ $absent_ng_male }} </span> </td>
                        <td><span class="muted bold text-info"> {{ $absent_ng_female }} </span> </td>
                        <td><span class="muted bold text-info">{{ $absent_ng  }}</span> </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="v-align-middle bold text-success"><a>Total</a></td>
                        <td><span class="muted bold text-info">{{ $absent_g_male + $absent_ng_male  }}</span> </td>
                        <td><span class="muted bold text-info">{{ $absent_g_female +  $absent_ng_female  }} </span> </td>
                        <td><span class="muted bold text-info">{{$absent_g + $absent_ng }} </span> </td>

                    </tr>
                </tbody>
                </table>

        </div>
    </div>
</div>





<script src="{{ url('css/new_css/css/js/jquery-3.3.1.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function() {

        $('#level_id,.db_region_id,.db_zone_id,.db_branch_id').change(function() {
            getDepartments();
        });


        function getDepartments() {
            $('#preloader').show();
            var level_id = $('#level_id').val();
            var region_id = $('.db_region_id').val();
            var zone_id = $('.db_zone_id').val();
            var branch_id = $('.db_branch_id').val();

            var CSRF_TOKEN = "{{ csrf_token() }}";
            $.ajax({
                url: "{{route('get_depts_by_params')}}",
                type: 'POST',
                data: {
                    _token: CSRF_TOKEN,
                    'level_id': level_id,
                    'region_id': region_id,
                    'zone_id': zone_id,
                    'branch_id': branch_id,
                },
                success: function(data) {
                    $("#department_id>option[value!='all']").attr('disabled', 'disabled');
                    $.each($.parseJSON(data), function(index, element) {
                        $("#department_id>option[value='" + element + "']").attr('disabled',
                            false);
                        console.log(element);
                    });
                    //$("#department_id>option[value!='1972']").attr('disabled','disabled');
                    /*var options = "<select class='form-control select2' value='all' name='department_id'><option value='all'>All</option>"+data+"</select>";
                    $('.departments_block').html(options);*/
                    $('#preloader').hide();
                }
            });
        }


    });
    var v_total = "{{ @$data['reg_employees'] }}";
    var v_present = "{{ $present }}";
    var v_absent = "{{ $absent }}";
    var v_late = "{{ @$data['late'] }}";
    var v_leave = "{{ @$data['leave'] }}";
    var v_schedule = "{{ @$data['roster_employees'] }}";
    var v_tour = 0
    var v_shift = "{{ $shift_not_started }}";

    var xValues = [
        "Present",
        "Absent",
        "Leave",
        "On Schedule",
        "On Tour",
        "Shift Not started",
    ];
    var yValues = [v_present, v_absent, v_leave, v_schedule, v_tour, v_shift];

    var x1Values = "Present";
    var bar1Colors = ["#00CCF4", "#00CCF444"];

    var x2Values = "Absent";
    var bar2Colors = ["#EE2A38", "#EE2A3844"];

    var x3Values = "Late";
    var bar3Colors = ["#FEBD63", "#FEBD6344"];

    var x4Values = "Leave";
    var bar4Colors = ["#CF79F4", "#CF79F444"];

    var per_present = "{{ $percent_present }}"
    var per_absent = "{{ $percent_absent }}"
    var per_late = ((Number(v_late) / Number(v_total)) * 100).toFixed(2)
    var per_pleave = ((Number(v_leave) / Number(v_total)) * 100).toFixed(2)

    var barColors = [
        "#56CB74",
        "#EE2A38",
        "#F9896B",
        "#FEBD63",
        "#CF79F4",
        "#F9896B",
    ];



    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

        var data = google.visualization.arrayToDataTable([
            ['Employees', 'Percentage'],
            ['Present', parseFloat(v_present)],
            ['Absent', parseFloat(v_absent)],
            ['Leave', parseFloat(v_leave)],
            ['On Schedule', parseFloat(v_schedule)],
            ['On Tour', parseFloat(v_tour)],
            ['Shift Not started', parseFloat(v_shift)]
        ]);


        var options = {
            legend: {
                position: 'top',
                maxLines: 4,
                textStyle: {
                    fontSize: 14
                }
            },
            backgroundColor: 'transparent',
            is3D: true,
            width: 400,
            height: 300,

            // title: 'Total Employees',


            slices: {
                0: {
                    color: '#56CB74'
                },
                1: {
                    color: '#EE2A38'
                },
                2: {
                    color: '#F9896B'
                },
                3: {
                    color: '#FEBD63'
                },
                4: {
                    color: '#CF79F4'
                },
                5: {
                    color: '#144681'
                },
                6: {
                    color: '#144681'
                },

            },
        };

        var chart = new google.visualization.PieChart(document.getElementById('myChart'));

        chart.draw(data, options);
    }
</script>
<script src=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js "></script>
<link href=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css " rel="stylesheet">

<link href="{{ url('select2totree/select2totree.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ url('select2totree/select2totree.js') }}" type="text/javascript"></script>
<script>
    //(function ($) {
    var mydata = @php echo json_encode($data['allregions']);
    @endphp;

    $("#allregions").select2ToTree({
        treeData: {
            dataArr: mydata
        },
        maximumSelectionLength: 3
    });
    $("#allregions").val("@php echo (isset($data['selected_dmgs']))?$data['selected_dmgs']:'all';@endphp").trigger('change');

    var all_depts_data = @php echo json_encode($data['all_departments']);
    @endphp;

    $("#all_departments").select2ToTree({
        treeData: {
            dataArr: all_depts_data
        },
        maximumSelectionLength: 3
    });
    $("#all_departments").val("@php echo (isset($data['department_id']))?$data['department_id']:'all';@endphp").trigger('change');

    //});
</script>

@endsection