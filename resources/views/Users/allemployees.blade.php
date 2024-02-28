@extends('layout.layout')
@section('title', 'Employee Detail List')
@section('content')

<link href="{{url('assets/plugins/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet" type="text/css" />
<link href=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css " rel="stylesheet">
<link href="{{url('select2totree/select2totree.css')}}" rel="stylesheet" type="text/css" />

<style>
    .pagination{margin-right:20px}
    .pagination>li{padding:0}
    .tab-list{float:right;margin:20px 0}
    .tab-list .btn{padding:6px 10px}
    .stats_att{font-size:16px!important;font-weight:700}
    .text-hint{font-size:13px;font-weight:400;color:#000!important;margin:0!important;padding:0!important;text-align:left!important}
    .select2-container{width:100%!important}#radioBtn .notActive{color:#3276b1;background-color:#fff}
    .dataTables_filter .main .ai_query-container{position:relative;display:inline-block}
    .dataTables_filter .main .ai_query{border:2px double transparent!important;background-image:linear-gradient(white,#fff),linear-gradient(to right,red,orange)!important;background-origin:border-box;background-clip:padding-box,border-box;margin-bottom:0!important}
    .ai_adv{position: absolute;top: 0;left: 1px;background: none!important;}
    .ai_adv_det{max-height: 90vh;overflow-y: auto;position: absolute;top: 40px;left: 4px;right: 4px;background: rgb(255, 255, 255);border: 1px solid rgb(221, 221, 221);z-index: 20000;padding: 10px;border-radius: 8px;}
    ul.ai_sub_filters{padding: 0; list-style: none;columns: 2;-webkit-columns: 2;-moz-columns: 2;}
    ul.ai_sub_filters + ul.ai_sub_filters{margin-top: 16px;border-top: 1px dashed #ccc;padding-top: 16px;}
    ul.ai_sub_filters li+li{margin-top:8px}
</style>
<div class="row noPrint">
    <form action="{{ route('allemployees') }}" method="GET" class="col-md-12 radius card_shadow dataTables_filter m-b-5" style="background:#fff">
        {{ csrf_field() }}
        <div class="p-t-10 p-b-10 p-l-5 p-r-5">
            <div class="form-row clearfix">
                <div class="col-md-4 mb-3 main ai_query-container ">
                    <span class="ai_adv btn"><i class="fa fa-caret-down"></i></span>
                    <input type="text" name="ai_query" id="ai_query" value="{{ request('ai_query') }}" class="form-control ai_query" placeholder="Search with AI Query String" style="padding-left: 20px !important;padding-right: 20px !important;">
                    <button type="submit" class="btn" style="position: absolute;top: 0;right: 1px;background: none;" name="submit" value="Filter"><i class="fa fa-search"> </i></button>
                    <div class="ai_adv_det" style="display:none;">
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="margin:0;">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingGeneral" style="border-radius: 8px;">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseGeneral" aria-expanded="true" aria-controls="collapseGeneral">
                                        General
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseGeneral" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingGeneral">
                                    <div class="panel-body" style="padding: 10px 0;">
                                        <ul class="ai_sub_filters">
                                            <li><a href="{{ route('allemployees')}}?ai_query=CS">Chief Secretary (CS)</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=ACS">Additional Chief Secretaries (ACS)</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=Secretaries">Secretaries</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=SpecialSecretaries">Special Secretaries</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=additionalSecretaries">Additional Secretaries</a></li>
                                        </ul>
                                        <ul class="ai_sub_filters">
                                            <li><a href="{{ route('allemployees')}}?ai_query=smbr">SMBR</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=memebrsBOR">Members Board of Revenue</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=Commissioners">Commissioners</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=DCs">Deputy Commissioners (DC)</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=ADCs">Additional Deputy Commissioners (ADC)</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=ACs">Assistant Commissioners (AC)</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=EACs">Extra Assistant Commissioners (EAC)</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=AACs">Additional Assistant Commissioners (AAC)</a></li>
                                        </ul>
                                        <ul class="ai_sub_filters">
                                            <li><a href="{{ route('allemployees')}}?ai_query=IG">Inspector General Police (IG)</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=DIGPs">Deputy Inspector Generals Police (DIGP)</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=SSPs">Senior Superintendents Police (SSP)</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=SPs">Superintendents Police (SP)</a></li>
                                        </ul>
                                        <ul class="ai_sub_filters">
                                            <li><a href="{{ route('allemployees')}}?ai_query=DHOs">District Health Officers (DHO)</a></li>
                                            <li><a href="{{ route('allemployees')}}?ai_query=MSs">Medical Superintendents (MS)</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingBPS" style="border-radius: 8px;">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseBPS" aria-expanded="false" aria-controls="collapseBPS">
                                        Basic Pay Scale (BPS)
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseBPS" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingBPS">
                                    <div class="panel-body" style="padding: 10px 0;">

                                        <ul  class="ai_sub_filters">
                                            <li><a href="{{ route('allemployees')}}?bps=22">BPS 22</a></li>
                                            <li><a href="{{ route('allemployees')}}?bps=>=:21">BPS 21 and above</a></li>
                                            <li><a href="{{ route('allemployees')}}?bps=>=:20">BPS 20 and above</a></li>
                                            <li><a href="{{ route('allemployees')}}?bps=>=:19">BPS 19 and above</a></li>
                                            <li><a href="{{ route('allemployees')}}?bps=>=:18">BPS 18 and above</a></li>
                                            <li><a href="{{ route('allemployees')}}?bps=>=:17">BPS 17 and above</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingDep" style="border-radius: 8px;">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseDep" aria-expanded="false" aria-controls="collapseDep">
                                        Departments
                                    </a>
                                </h4>
                                </div>
                                <div id="collapseDep" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingDep">
                                <div class="panel-body" style="padding: 10px 0;">

                                    <ul  class="ai_sub_filters">
                                        <li><a href="{{ route('allemployees')}}?department_id=2751">Secretariats</a></li>
                                        <li><a href="{{ route('allemployees')}}?department_id=2334">Police</a></li>
                                        <li><a href="{{ route('allemployees')}}?department_id=2464">Board of Revenue</a></li>
                                        <li><a href="{{ route('allemployees')}}?department_id=1972">Health</a></li>
                                        <li><a href="{{ route('allemployees')}}?department_id=2780">Directorates / Others</a></li>
                                    </ul>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <select name="department_id" id="all_departments" class="select2 form-control">
                        <!-- Add your options here -->
                    </select>
                </div>
                <div class="col-md-3 mb-3 jquicss">
                    <input type="text" name="allregions_input" id="allregions_input" value="{{request('allregions_input')}}" class="form-control" placeholder="Enter Region, District or Tehsil">
                    <input type="hidden" name="allregions" id="allregions" value="{{request('allregions')}}">
                </div>
                <div class="col-md-1 m-y-2 text-center">
                <button type="submit" class="btn btn-primary btn-success pull-left" title="Search" name="submit" value="Filter" style="margin-right:15px"><i class="fa fa-search"></i></button>
                    <button class="btn btn-primary pull-right" type="button" title="Advance Filters" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-sliders"> </i></button>
                </div>
            </div>


            <div class="form-row clearfix m-t-10 collapse" id="collapseExample" >

                <div class="col-md-3 mb-3">
                    <input type="text" name="emp_id" id="emp_id" value="{{ request('emp_id') }}" class="form-control" placeholder="Employee ID">
                </div>
                <div class="col-md-3 mb-3">
                    <input type="text" name="name" id="name" value="{{ request('name') }}" class="form-control" placeholder="Name..">
                </div>
                <div class="col-md-3 mb-3">
                    <input type="text" name="cnic" id="cnic" value="{{ request('cnic') }}" class="form-control" placeholder="CNIC with dashes">
                </div>
                <div class="col-md-3 mb-3">
                    <input type="text" name="designation" id="designation" value="{{ request('designation') }}" class="form-control" placeholder="Designation">
                </div>
                <div class="col-md-3 mb-3">
                    <input type="text" name="bps" id="bps" value="{{ request('bps') }}" class="form-control" placeholder="BPS(19, >:20, <:15, <=:15, >=:18)">
                </div>

                <div class="col-md-3 mb-3">
                    <select name="status" class=" form-control">
                        <option selected value="all">Status</option>
                        <option value="1" @if(request('status') == "1") selected @endif>Active</option>
                        <option value="2" @if(request('status') == "2") selected @endif>In Active</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <select name="field_staff" class=" form-control">
                        <option selected value="all">Field Staff</option>
                        <option value="1"  @if(request('field_staff') == "1") selected @endif>Yes</option>
                        <option value="0"  @if(request('field_staff') == "0") selected @endif>No</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <select name="emp_type" class="= form-control">
                        <option selected value="all">Employment Type</option>
                        <option value="Regular"  @if(request('emp_type') == 'Regular') selected @endif>Regular</option>
                        <option value="Adhoc"  @if(request('emp_type') == 'Adhoc') selected @endif>Adhoc</option>
                        <option value="Contract"  @if(request('emp_type') == 'Contract') selected @endif>Contract</option>
                        <option value="Contingency"  @if(request('emp_type') == 'Contingency') selected @endif>Contingency</option>
                        <option value="Temporary"  @if(request('emp_type') == 'Temporary') selected @endif>Temporary</option>
                        <option value="Attach"  @if(request('emp_type') == 'Attach') selected @endif>Attach</option>
                        <option value="NotSet"  @if(request('emp_type') == 'NotSet') selected @endif>NotSet</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <input type="date" name="reg_date" id="reg_date" value="{{ request('reg_date') }}" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <select name="attendance_type" class="= form-control">
                        <option selected value="all">Check Attendance</option>
                        <option value="Full"  @if(request('attendance_type') == 'Full') selected @endif>Full Record</option>
                        <option value="Single"  @if(request('attendance_type') == 'Single') selected @endif>Single Day</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">

                </div>
                <div class="col-md-12 mb-3 text-center clear-fix">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"> </i> Search</button>
                </div>
            </div>



        </div>
    </form>
</div>

@if (count($data['users']) > 0)
<div class="row-fluid">
    <div class="span12 ">
        <div class="row tiles card_shadow white p-l-20 p-r-20 m-t-10 m-b-10 dataTables_filter noPrint">
            <div class="col-md-4" style="margin-top:20px">
                <span style='font-weight:bold; font-size:20px; color:#12124E'>Total Employees:
                    {{ $data['users']->total() }}
                </span>

            </div>
            <div class="col-md-8">
                <div class="btn-group tab-list" role="group" aria-label="View Toggle">
                    <button type="button" class="btn btn-primary" id="cardViewBtn" style="margin-right: 2px;" title="view as grid"><i class="fa fa-th"></i></button>
                    <button type="button" class="btn btn-primary" id="tableViewBtn" title="view as list"><i class="fa fa-list"></i></button>
                </div>

{{--              {{ $data['users']->appends(['page' => $data['users']->currentPage()])->links() }}--}}
                {{ $data['users']->appends(request()->query())->links() }}




            </div>
        </div>


        <div class="grid simple ">
            <div class="row tableView tiles card_shadow white p-t-20 p-b-20 p-l-20 p-r-20 m-t-10 m-b-10 dataTables_filter">
                <div class="col-md-12 col-vlg-12 m-b-10 ">
                    <div class="">
                        <table class="table no-more-tables m-l-0 m-b-30 dept_table" style="border-collapse: separate; border-spacing: 0 15px">
                            @php
                                $attendance_type = request()->query('attendance_type');
                            @endphp
                            <thead>
                                <th>S.No</th>
                                <th style="text-align: left;">Name/Designation/Department</th>
                                <th style="text-align: left;">ID/BPS/CNIC</th>
                                @if($attendance_type == 'Single')
                                    <th colspan="6" class="text-center"> Status</th>
                                @else
                                    <th>Working Days</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Late</th>
                                    <th>Leave</th>
                                    <th>OffDays</th>
                                @endif

                                </tr>
                            </thead>
                            <tbody role="alert" aria-live="polite" aria-relevant="all">
                                @foreach ($data['users'] as $index => $u)

                                <tr>

                                    <td style="width: 5% !important;" class="{{ $u->status == 1 ? 'active_td' : 'in_active_td' }}">
                                        {{ $data['users']->appends(request()->query())->firstItem() + $index }}
                                    </td>
                                    @php
                                    $from_date = date('Y-m-') . '01';
                                    $to_date = date('Y-m-d');
                                    $user_id = $u->id;
                                    @endphp
                                    <td style="width: 30% !important;" class="width_20">
                                        <a href="{{route('employee_daily_report', $u->id) }}">
                                            <div style="display: flex">
                                                <div>
                                                    @if ($u->image)
                                                    <img width="50px" class="image_round" src="{{ asset('emp_images') }}/{{ $u->image }}" onerror="this.onerror=null; this.src='{{ asset('assets/img/avatar.png') }}';" />
                                                    @else
                                                    <img class="image_round" width="50px" src="{{ asset('assets/img/avatar.png') }}" />
                                                    @endif
                                                </div>
                                                <div class="m-l-5" style="display: flex;flex-direction: column;align-items: flex-start;">
                                                    <p style="margin:0px !important">{{ $u->name }} </p>
                                                    <p class="text-hint"><b>Designation: </b>{{ $u->designations ? $u->designations->title : '--' }} </p>
                                                    <p class="text-hint"><b>Department: </b>{{ $u->departments ? $u->departments->name : '--' }} </p>
                                                </div>
                                            </div>
                                        </a>
                                    </td>

                                    <td style="width: 23% !important;">
                                        <div style="display: flex;flex-direction: column;align-items: flex-start;">
                                            <p class="text-hint"><strong>ID : </strong>{{ $u->id }}</p>
                                            <p class="text-hint"><strong>BPS : </strong>{{ $u->bps ?: '-' }} </p>
                                            <p class="text-hint"><strong>CNIC : </strong>{{ $u->cnic ?? '--' }} </p>
                                        </div>
                                    </td>

                                    @php
                                            $first_attendance = $u->reg_date;
                                            $record_exists = App\Attendance::where('user_id', $u->id)->first();
                                            $monthStartDate = date('Y-m-1');
                                            if($first_attendance > $monthStartDate){
                                                $monthStartDate = $first_attendance;
                                            }
                                            elseif($attendance_type == 'Single'){
                                                $monthStartDate = date('Y-m-d');
                                            }
                                            else{
                                                $monthStartDate = $monthStartDate;
                                            }
                                            $monthEndDate = date('Y-m-d');
                                            try {
                                                $attendance_stats = App\Http\Controllers\UsersController::employeeYearlyReportCopy($monthStartDate, $monthEndDate, $u->id);

                                            } catch (\Exception $e) {
                                                continue; // This will continue to the next iteration of the loop
                                            }

                                            // dd($attendance_stats);
                                            if($attendance_stats){
                                            $attendance_stats = $attendance_stats->getData();
                                            }
                                    @endphp
                                    @if($u->field_staff == 1)
                                        <td colspan="6" class="text-center">Field Staff</td>
                                    @elseif(!($record_exists))
                                        <td colspan="6" class="text-center">No Record Found /
                                          <span class="text-primary">Registered on: {{ \Carbon\Carbon::parse($u->reg_date)->format('d-m-Y') }}</span>
                                        </td>
                                    @elseif($attendance_type == 'Single')
                                        @php
                                            $today = now()->format('Y-m-d');
                                            $attendance_today = \DB::table('attendance')
                                                        ->where(\DB::raw('DATE(datetime)'), $today)
                                                        ->where('user_id', $u->id)
                                                        ->first();

                                            $attach_user = \App\AttachEmployee::where('user_id', $user_id)
                                                            ->where('from_date', '<=', $today)
                                                            ->where('to_date', '>=', $today)
                                                            ->first();
                                        @endphp
                                        @if($attendance_stats['total_present'] ==1 && $attendance_stats['total_late'] < 1)
                                            <td colspan="6" class="text-center text-primary">
                                                <b style="color: green">Present /
                                                    {{ optional($attendance_today)->datetime ? \Carbon\Carbon::parse($attendance_today->att_checktime)->format('m-d-Y H:i:s') : '' }}
                                                </b> </td>
                                        @elseif($attendance_stats['total_late'] == 1)
                                            <td colspan="6" class="text-center text-warning">
                                                <b> Late /  {{ \Carbon\Carbon::parse($attendance_today->att_checktime)->format('m-d-Y H:i:s') }}
                                                </b>
                                            </td
                                        @elseif($attendance_stats['total_absent'] == 1)
                                            <td colspan="6" class="text-center "> <b style="color: red">Absent</b> </td
                                        @elseif($attach_user)
                                            <td colspan="6" class="text-center "> <b style="color: blue">Attached</b> </td
                                        @endif
                                    @else
                                        <td style="width: 7% !important;" class="t-working stats_att">
                                            {{$attendance_stats? $attendance_stats['total_present'] + $attendance_stats['total_leave']
                                                + $attendance_stats['total_absent']  + $attendance_stats['total_late'] : ''}}
                                        </td>
                                        <td style="width: 7% !important;" class="t-present stats_att">{{$attendance_stats? $attendance_stats['total_present'] + $attendance_stats['total_late'] :''}}</td>
                                        <td style="width: 7% !important;" class="t-absent stats_att">{{$attendance_stats? $attendance_stats['total_absent'] : ''}}</td>
                                        <td style="width: 7% !important;" class="t-late stats_att">{{$attendance_stats? $attendance_stats['total_late'] : ''}}</td>
                                        <td style="width: 7% !important;" class="stats_att text-black">{{$attendance_stats? $attendance_stats['total_leave'] : ''}}</td>
                                        <td style="width: 7% !important;" class="stats_att text-black">{{$attendance_stats? $attendance_stats['total_day_off'] : ''}}</td>
<!--
                                        <td style="width: 7% !important;" class="stats_att text-black">{{$attendance_stats? $attendance_stats['attachs']->count() : ''}}</td>
-->
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @include('sweetalert::alert')
                    </div>
                </div>
            </div>

            <div class="row CardView" style="display: none;">
                @foreach ($data['users'] as $u)
                <div class="col-sm-4 box-pad">
                    <div class="user-card tiles card_shadow white p-t-20 p-b-20 p-l-20 p-r-20 grid-body all_emps">
                        <div class="user-card-top">

                            @if ($u->status == 1 )
                            <span class="text-active">Active</span>
                            @else
                            <span class="text-inactive">In Active</span>
                            @endif

                            @if ($u->image)
                            <img class="user-img" src="{{ asset('emp_images') }}/{{ $u->image }}" onerror="this.onerror=null; this.src='{{ asset('assets/img/avatar.png') }}';" />
                            @else
                            <img class="user-img" src="{{ asset('assets/img/avatar.png') }}">
                            @endif
                            <span class="text-bps">BPS-{{ $u->bps ?: '--' }}</span>
                        </div>
                        <p class="text-username">
                            <a href="{{ route('employee_report2') }}/?user_id={{ $u->id }}">
                                {{ $u->name }}
                        </p>
                        </a>
                        </span>
                        <p class="text-center">{{ $u->designations ? $u->designations->title : '--' }}</span>
                        <table class="p-20 table-user">
                            <tr>
                                <td class="td-1">Department :</td>
                                <td class="td-2">{{ $u->departments ? $u->departments->name : '--' }}</td>
                            </tr>
                            <tr>
                                <td class="td-1">CNIC: </td>
                                <td class="td-2"> {{ $u->cnic ?? '--' }}</td>
                            </tr>

                        </table>

                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

<div>
</div>
@else
<div class="row">
    <div class="span12">
        <div class="grid simple ">
            <div class="col-md-12">
                <h4 align="center"> No record found..</h4>
            </div>
        </div>
    </div>

</div>
@endif
</div>
<!-- <script src="{{ url('css/new_css/css/js/jquery-3.3.1.js') }}" type="text/javascript"></script> -->
<script src=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js "></script>

<script src="{{ url('select2totree/select2totree.js') }}" type="text/javascript"></script>
<script src="{{url('public/assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js')}}" type="text/javascript"></script>

<script>
    var dmg_autocomplete = @php echo json_encode($data['dmg_autocomplete']) @endphp;
    var dmg_autocomplete_keys = @php echo json_encode($data['dmg_autocomplete_keys']) @endphp;

    $( "#allregions_input" ).autocomplete({
        source: dmg_autocomplete,
        change: function( event, ui ) {
            if(ui.item){
                $('#allregions').val(dmg_autocomplete_keys[ui.item.label]);
            }
            else
            {
                $('#allregions').val('');
                $("#allregions_input").val('');
            }
        },
    });

</script>

<script>
    var all_depts_data = @php echo json_encode($data['all_departments']);
    @endphp;

    $("#all_departments").select2ToTree({
        treeData: {
            dataArr: all_depts_data
        },
        maximumSelectionLength: 3
    });
    $("#all_departments").val("@php echo (isset($data['department_id']))?$data['department_id']:'all';@endphp").trigger('change');


</script>

<script>
    $(document).ready(function() {

        $('#cardViewBtn').on('click', function() {
            $('.tableView').hide();
            $('.CardView').show();
        });

        $('#tableViewBtn').on('click', function() {
            $('.CardView').hide();
            $('.tableView').show();
        });

        $('.ai_adv').on('click',function(){
            $('.ai_adv i').toggleClass('fa-caret-down');
            $('.ai_adv i').toggleClass('fa-caret-up');

            $(".ai_adv_det").toggle();

        });
    });
</script>

<script>
    $('#radioBtn a').on('click', function() {
        var sel = $(this).data('title');
        var tog = $(this).data('toggle');
        $('#' + tog).prop('value', sel);

        $('a[data-toggle="' + tog + '"]').not('[data-title="' + sel + '"]').removeClass('active').addClass('notActive');
        $('a[data-toggle="' + tog + '"][data-title="' + sel + '"]').removeClass('notActive').addClass('active');
    })
</script>

@include('include.important')
@endsection
