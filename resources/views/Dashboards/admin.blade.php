@extends('layout.layout')
@section('title','State Dashboard')
@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="{{url('select2totree/select2totree.css')}}" rel="stylesheet" type="text/css" />
<style>
      .ai_adv{position: absolute;top: 0;left: 1px;background: none!important;}
    .ai_adv_det{max-height: 90vh;overflow-y: auto;position: absolute;top: 40px;left: 4px;right: 4px;background: rgb(255, 255, 255);border: 1px solid rgb(221, 221, 221);z-index: 20000;padding: 10px;border-radius: 8px;}
    ul.ai_sub_filters{padding: 0; list-style: none;columns: 2;-webkit-columns: 2;-moz-columns: 2;}
    ul.ai_sub_filters + ul.ai_sub_filters{margin-top: 16px;border-top: 1px dashed #ccc;padding-top: 16px;}
    ul.ai_sub_filters li+li{margin-top:8px}
</style>
<div class="col-md-12 radius card_shadow white dataTables_filter m-b-5"  style="background:#fff">
  {{ csrf_field() }}
  <div class="p-t-10 p-b-10 p-l-5 p-r-5">
    <div class="row bg-white clearfix">
      <div class="col-md-5 mb-3 main ai_query-container">
        <form action="{{route('allemployees')}}" class="" method="get">
          {{ csrf_field() }}
          
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
        </form>
      </div>
      <form action="" class="col-md-7" method="get">
        {{ csrf_field() }}
        <div class="row">
          <div class="col-md-6 m-y-2">
            <select name="department_id" id="all_departments" class="form-control" placeholder="Select Department">
            </select>
          </div>
          <div class="col-md-4 m-y-2 jquicss">
            <input type="hidden" name="allregions" value="{{$data['selected_dmgs'] ?? 'all'}}">
            <input type="date" name="date" class="form-control mydateformat" value="{{ $data['c_date'] }}" data-date="" data-date-format="DD-MM-YYYY" />
          </div>
          <div class="col-md-2 m-y-2 text-center">
            <button type="submit" class="btn btn-primary btn-success" title="Search" name="submit" value="Filter" style="margin-right:15px"><i class="fa fa-search"></i></button>
            <button class="btn btn-primary " type="button" title="Regional Dashboard"  data-toggle="collapse" data-target="#collapseDMGs" aria-expanded="false" aria-controls="collapseDMGs"><i class="fa fa-sitemap"> </i></button>
          </div>
        </div>
      </form>

      <div class="col-sm-12 m-t-10  collapse" id="collapseDMGs">@include('include.dmg_list')</div>
    </div>
  </div>
</div>

@php

$present_male = @$data['present']->male;
$roster_employees = @$data['roster_employees'];
$present_female = @$data['present']->female;

$present = $present_male + $present_female ;

$present_g = @$data['present']->g_male + @$data['present']->g_female;
$present_ng = @$data['present']->ng_male + @$data['present']->ng_female;
$present_g_male = @$data['present']->g_male;
$present_g_female = @$data['present']->g_female;
$present_ng_male = @$data['present']->ng_male;
$present_ng_female = @$data['present']->ng_female;
$percent_present = 0
@endphp

@php
$percent_present = ($data['reg_employees']['total']>0)?ceil(($present / $data['reg_employees']['total']) * 100):0;
@endphp

@php
$absent_male = @$data['absent']->male;
$absent_female = @$data['absent']->female;
$absent = @$data['absent']->total;
$shift_not_started = @$data['absent']->shift_not_started ;
$absent_g = @$data['absent']->g_male + @$data['absent']->g_female;
$absent_ng = @$data['absent']->ng_male + @$data['absent']->ng_female;
$absent_g_male = @$data['absent']->g_male;
$absent_g_female = @$data['absent']->g_female;
$absent_ng_male = @$data['absent']->ng_male;
$absent_ng_female = @$data['absent']->ng_female;
$percent_absent = 0

@endphp

@php
$percent_absent = ($data['reg_employees']['total']>0)?ceil(($absent/$data['reg_employees']['total'])*100):0;
@endphp


<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="row no_gutter">

  <div class="col-md-3 p-2 card_main">
    <div class="card b1 card_shadow">
      <a href="{{ route('dw-employees',['date'=>$data['c_date'],'department_id'=>$data['department_id'],'selected_dmgs'=>$data['selected_dmgs']]) }}" target="_blank">
        <p class="text_heading">Total Employees</p>
        <p class="text_count">{{ number_format($data['total_employees'],0) }}</p>
      </a>
    </div>
  </div>
  <div class="col-md-9">
    <div class="row no_gutter">
      <div class="col-md-3 p-2 card_main">
        <div class="card  card_shadow">
          <div style="display: flex">
            <div>
              <a href="{{ route('dw-registered',['department_id'=>$data['department_id'],'selected_dmgs'=>$data['selected_dmgs']]) }}" target="_blank">
                <p class="text_heading">Biometric Registered</p>
                <p class="text_count">{{ number_format($data['reg_employees']['total'],0) }}</p>
              </a>
              <div style="display: flex ; flex-direction:row">
                @if(isset($data['reg_employees']['childs']))
                @foreach($data['reg_employees']['childs'] as $key=>$value)
                <span class="text-white sub"><span class="mytooltip" data-toggle="tooltip" data-original-title="{{$key}}">{{$value}}</span></span>
                @endforeach
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card card_shadow">
          <div style="display: flex">
            <div>
              <a href="{{ route('dw-unregistered',['department_id'=>$data['department_id'],'selected_dmgs'=>$data['selected_dmgs']]) }}" target="_blank">
                <p class="text_heading">Field Staff</p>
                <p class="text_count">{{ number_format($data['unreg_employees']['total'],0) }}</p>
              </a>

              <div style="display: flex ; flex-direction:row">
                @if(isset($data['unreg_employees']['childs']))
                @foreach($data['unreg_employees']['childs'] as $key=>$value)
                <span class="text-white sub"><span class="mytooltip" data-toggle="tooltip" data-original-title="{{$key}}">{{$value}}</span></span>
                @endforeach
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card b11 card_shadow">
          <p class="text_heading">Vacant Posts</p>
          <p class="text_count">{{ number_format($data['total_vacant_posts'],0) }}</p>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card b11 card_shadow">
          <p class="text_heading">Unaccounted</p>
          <p class="text_count">{{ number_format(($data['total_employees'] - ($data['total_vacant_posts']+$data['reg_employees']['total']+$data['unreg_employees']['total'])),0) }}</p>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 no_gutter card_main">
    <div class="card_round card_shadow card_chart d-flex align-center">
      <div id="myChart" style="height:320px;"></div>
    </div>
  </div>
  <div class="col-md-9 no_gutter">
    <div class="row no_gutter">
      <div class="col-md-3 p-2 card_main">
        <div class="card b3 c_present card_shadow">
          <a href="{{ route('dw-present', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
            <p class="text_heading">Present</p>
            <p class="text_count">{{ number_format($present,0) }}</p>
          </a>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card b4 c_absent card_shadow">
          <a href="{{ route('dw-absent', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
            <p class="text_heading">Absent</p>
            <p class="text_count">{{ number_format($absent,0) }}</p>
          </a>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card b5 c_late card_shadow">
          <a href="{{ route('dw-late', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
            <p class="text_heading">Late</p>
            <p class="text_count">{{ @number_format($data['late'],0)}}</p>
          </a>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card b7 c_leave card_shadow">
          <a href="{{ route('dw-leave', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
            <p class="text_heading">Leave</p>
            <p class="text_count">{{ @number_format($data['leave'],0) }}</p>
          </a>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card b8 card_shadow">
          <a href="{{ route('dw-os-employees', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
            <p class="text_heading">On Schedule</p>
            <p class="text_count">
              {{ @number_format($data['roster_employees'],0) }}
            </p>
          </a>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card b9 card_shadow">
          <a href="{{ route('dw-offdays', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
            <p class="text_heading">Off Day</p>
            <p class="text_count">{{ @number_format($data['offdays'],0) }}</p>
          </a>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card b10 card_shadow">
          <a href="{{ route('dw-sna-employees', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
            <p class="text_heading">Shift Not Started</p>
            <p class="text_count">{{ number_format($shift_not_started,0) }}</p>
          </a>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card b10 card_shadow">
          <a href="{{ route('dw-attach', ['date' => $data['c_date'], 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
            <p class="text_heading">On-Duty</p>
            <p class="text_count">{{ @number_format($data['attach'],0) }}</p></span>
          </a>
        </div>
      </div>


      <div class="col-md-3 p-2 card_main">
        <div class="card b12 card_shadow">
          <a href="{{ route('devices.index', ['department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
            <p class="text_heading">Total Devices</p>
            <p class="text_count">{{ number_format($data['devices']['Online']+$data['devices']['Offline']+$data['devices']['Inactive'],0)  }}</p>
          </a>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card b13 card_shadow">
          <a href="{{ route('devices.index', [ 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs']]) }}" target="_blank">
            <p class="text_heading">Online</p>
            <p class="text_count">{{ number_format($data['devices']['Online'],0) }}</p>
          </a>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card b14 card_shadow">
          <a href="{{ route('devices.index', ['department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs'],'show_tab'=>'Offline']) }}" target="_blank">
            <p class="text_heading">Offline</p>
            <p class="text_count">{{ number_format($data['devices']['Offline'],0) }}</p>
          </a>
        </div>
      </div>
      <div class="col-md-3 p-2 card_main">
        <div class="card b13 card_shadow">
          <a href="{{ route('devices.index', [ 'department_id' => $data['department_id'], 'selected_dmgs' => $data['selected_dmgs'],'show_tab'=>'Inactive']) }}" target="_blank">
            <p class="text_heading">In Active</p>
            <p class="text_count">{{ number_format($data['devices']['Inactive'],0) }}</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="tabbable m-t-20">
  <ul class="nav nav-tabs center_tabs">
    <li class="active"><a href="#subdepstab" data-toggle="tab" aria-expanded="true">Sub-Departments</a></li>
    <li><a href="#subdmgstab" data-toggle="tab" aria-expanded="false">Sub-Divisions</a></li>
    <li><a href="#externaldeps" data-toggle="tab" aria-expanded="false">External-Departments</a></li>
  </ul>
  <div class="tab-content tiles card_shadow white">
    <div class="tab-pane active" id="subdepstab">
      <div class="sub_deps_loading ajax_loading"><i class="glyphicon glyphicon-refresh fa-2x spin"></i></div>
      <div class="sub_deps_table table-responsive" style="display:none">
        <table class="table  m-t-20 m-l-0 m-b-30 dept_table">
          <thead>
            <tr>
              <th class="width_5"> S.No </th>
              <th class="width_25"  style="text-align:left!important"> Sub-department </th>
              <th class="width_5"> Total </th>
              <th class="width_5"> Bio.Reg. </th>
              <th class="width_5"> FieldStaff </th>
              <th class="width_5"> Vacant </th>
              <th class="width_5"> Unaccounted </th>
              <th class="width_5"> Present </th>
              <th class="width_5"> Absent </th>
              <th class="width_5"> Late </th>
              <th class="width_5"> Leave </th>
              <th class="width_5"> On-Duty </th>
              <th class="width_5"> Off Days </th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
    <div class="tab-pane " id="subdmgstab">
      <div class="sub_dmgs_loading ajax_loading"><i class="glyphicon glyphicon-refresh fa-2x spin"></i></div>
      <div class="sub_dmgs_table table-responsive" style="display:none">
        <table class="table  m-t-20 m-l-0 m-b-30 dept_table">
          <thead>
            <tr>
              <th class="width_5"> S.No </th>
              <th class="width_25"  style="text-align:left!important"> Sub-department </th>
              <th class="width_5"> Total </th>
              <th class="width_5"> Bio.Reg. </th>
              <th class="width_5"> FieldStaff </th>
              <th class="width_5"> Vacant </th>
              <th class="width_5"> UnAcc. </th>
              <th class="width_5"> Present </th>
              <th class="width_5"> Absent </th>
              <th class="width_5"> Late </th>
              <th class="width_5"> Leave </th>
              <th class="width_5"> On-Duty </th>
              <th class="width_5"> Off Days </th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
    <div class="tab-pane " id="externaldeps">
      <div class="externaldeps_loading ajax_loading"><i class="glyphicon glyphicon-refresh fa-2x spin"></i></div>
      <div class="externaldeps_table table-responsive" style="display:none">
        <table class="table  m-t-20 m-l-0 m-b-30 dept_table">
          <thead>
            <tr>
              <th class="width_5"> S.No </th>
              <th class="width_25"  style="text-align:left!important"> department </th>
              <th class="width_5"> Total </th>
              <th class="width_5"> Bio.Reg. </th>
              <th class="width_5"> UnAcc. </th>
              <th class="width_5"> Present </th>
              <th class="width_5"> Absent </th>
              <th class="width_5"> Late </th>
              <th class="width_5"> Leave </th>
              <th class="width_5"> On-Duty </th>
              <th class="width_5"> OffDays </th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="row m-t-10 m-b-10">
  <div class="col-md-6 col-vlg-6 m-b-10 ">
    <div class="tiles card_shadow white p-t-10 p-b-5 p-l-20 p-r-20 table-responsive">
      <h3 class="text-center p-t-10" style="font-weight: bold">Present</h3>
      <table class=" table m-t-10 m-l-0 dept_table">
        <thead>
          <tr>
            <th style="width:0%"> </th>
            <th style="width:40%"> </th>
            <th style="width:20%"><i class="fa fa-male fa-3x"> </i> </th>
            <th style="width:20%"><i class="fa fa-female fa-3x"> </i> </th>
            <th style="width:20%"> Total </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td></td>
            <td class="bold" style="text-align:left!important"><a>Gazzetted Staff</a></td>
            <td><span class="muted bold ">{{  number_format($present_g_male,0) }}</td>
            <td><span class="muted bold ">{{  number_format($present_g_female,0) }}</td>
            <td><span class="muted bold">{{  number_format($present_g,0) }}</td>
          </tr>
          <tr>
            <td></td>
            <td class="bold" style="text-align:left!important"><a>Non Gazzetted Staff</a></td>
            <td>{{  number_format($present_ng_male,0) }}</td>
            <td>{{  number_format($present_ng_female,0) }}</td>
            <td>{{  number_format($present_ng,0) }}</td>
          </tr>
          <tr>
            <td></td>
            <td class="bold" style="text-align:left!important"><a>Total</a></td>
            <td>{{  number_format($present_g_male +  $present_ng_male,0)  }}</td>
            <td>{{  number_format($present_g_female + $present_ng_female,0)  }}</td>
            <td>{{  number_format($present_g + $present_ng,0) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="col-md-6 col-vlg-6 m-b-10 ">
    <div class="tiles card_shadow white p-t-10 p-b-5 p-l-20 p-r-20 table-responsive">
      <h3 class="text-center p-t-10" style="font-weight: bold">Absent</h3>
      <table class=" table m-t-10 m-l-0 dept_table" style="border-collapse: separate; border-spacing: 0 15px">
        <thead style="">
          <tr>
            <th style="width:0%"> </th>
            <th style="width:40%"> </th>
            <th style="width:20%"><i class="fa fa-male fa-3x"> </i> </th>
            <th style="width:20%"><i class="fa fa-female fa-3x"> </i> </th>
            <th style="width:20%"> Total </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td></td>
            <td style="text-align:left!important"><a>Gazzetted Staff</a></td>
            <td  class="bold" ><span class="muted bold ">{{  number_format($absent_g_male,0) }}</td>
            <td  class="bold" ><span class="muted bold ">{{  number_format($absent_g_female,0) }}</td>
            <td  class="bold" ><span class="muted bold">{{  number_format($absent_g,0)  }}</td>
          </tr>
          <tr>
            <td></td>
            <td  style="text-align:left!important"><a>Non Gazzetted Staff</a></td>
            <td  class="bold" >{{  number_format($absent_ng_male,0) }}</td>
            <td  class="bold" > {{  number_format($absent_ng_female,0) }}</td>
            <td  class="bold" >{{  number_format($absent_ng,0)  }}</td>
          </tr>
          <tr>
            <td></td>
            <td  style="text-align:left!important"><a>Total</a></td>
            <td  class="bold" >{{  number_format($absent_g_male + $absent_ng_male,0)  }}</td>
            <td  class="bold" >{{  number_format($absent_g_female +  $absent_ng_female,0)  }}</td>
            <td  class="bold" >{{  number_format($absent_g + $absent_ng,0) }}</td>

          </tr>
        </tbody>
        </table>
    </div>
  </div>
</div>


<script type="text/javascript">
    var v_total = "{{ @$data['reg_employees']['total'] }}";
    var v_present = "{{$present}}";
    var v_absent = "{{$absent}}";
    var v_late = "{{ @$data['late'] }}";
    var v_leave = "{{ @$data['leave'] }}";
    var v_schedule = "{{ @$data['roster_employees'] }}";
    var v_tour = "{{ @$data['attach'] }}"
    var v_shift = "{{ $shift_not_started }}";
    var v_off = "{{  @$data['$offdays'] }}";

    var xValues = [
      "Present",
      "Absent",
      "Leave",
      "On Schedule",
      "On-Duty",
      "Shift Not started",
      "Off Days"
    ];
    var yValues = [v_present, v_absent, v_leave, v_schedule, v_tour, v_shift, v_off];

    var per_present = "{{$percent_present}}"
    var per_absent = "{{$percent_absent}}"
    var per_late = ((Number(v_late) / Number(v_total)) * 100).toFixed(2)
    var per_pleave = ((Number(v_leave) / Number(v_total)) * 100).toFixed(2)

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
        ['On-Schedule', parseFloat(v_schedule)],
        ['On-Duty', parseFloat(v_tour)],
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
        width: 350,
        height: 350,

        // title: 'Total Employees',


        slices: {
          0: {
            color: '#56CB74'
          },
          1: {
            color: '#EE2A38'
          },
          2: {
            color: '#1C90FF'
          },
          3: {
            color: '#144681'
          },
          5: {
            color: '#144681'
          },
          6: {
            color: '#144681'
          },
          7: {
            color: '#144681'
          },

        },
      };

      var chart = new google.visualization.PieChart(document.getElementById('myChart'));

      chart.draw(data, options);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{url('select2totree/select2totree.js')}}" type="text/javascript"></script>
<script>
  
  var all_depts_data = @php echo json_encode($data['all_departments']);@endphp;
    //console.log(all_depts_data);

    $("#all_departments").select2ToTree({
      treeData: {
        dataArr: all_depts_data
      },
      maximumSelectionLength: 3
    });
    $("#all_departments").val("@php echo (isset($data['department_id']))?$data['department_id']:'all';@endphp").trigger('change');
    
  

</script>


<script>
  $(function() {
    loadsubdeps();
    loadsubdmgs();
    loadexternaldeps();
  });

  function loadsubdeps() {
    $.ajax({
      method: "GET",
      url: "{{route('get-sub-deps-stats')}}",
      data: {
        department_id: "@php echo (isset($data['department_id']))?$data['department_id']:'all';@endphp",
        selected_dmgs: "@php echo (isset($data['selected_dmgs']))?$data['selected_dmgs']:'all';@endphp",
        date: "{{ $data['c_date'] }}"
      }

    }).done(function(data) {
      if (data == 'no-more-deps') {
        $('#subdepstab').html('<center>No more sub-departments found.</center>');
      } else {
        $('#subdepstab .sub_deps_table table tbody').html(data);

        $('#subdepstab .sub_deps_loading').hide();
        $('#subdepstab .sub_deps_table').show();
        $('#subdepstab .sub_deps_table table').DataTable({
          fixedColumns: true,
          dom: 'Bfrtip',
          lengthMenu: [
            [10, 20, 50, -1],
            ['10 rows', '20 rows', '50 rows', 'Show all']
          ],

          buttons: [
            'pageLength',
            'copy', 'csv', 'excel', 'pdf',
            {
              extend: 'print',
              exportOptions: {
                columns: [0, ':visible']
              },
              customize: function(win) {
                $(win.document.body)
                  .css('text-align', 'center')
                  .css('font-size', '10pt')
                  .prepend(
                    '<img src="" style="position:absolute; top:0; left:0;" />'
                  );

                $(win.document.body).find('table')
                  .addClass('compact')
                  .css('font-size', 'inherit');
              }
            }
          ],

        });
      }
    }).fail(function() {
      $('#subdepstab').html('<center>Something went wrong, please reload the page.</center>');
    });
  }

  function loadsubdmgs() {
    $.ajax({
      method: "GET",
      url: "{{route('get-sub-dmgs-stats')}}",
      data: {
        department_id: "@php echo (isset($data['department_id']))?$data['department_id']:'all';@endphp",
        selected_dmgs: "@php echo (isset($data['selected_dmgs']))?$data['selected_dmgs']:'all';@endphp",
        date: "{{ $data['c_date'] }}"
      }

    }).done(function(data) {
      if (data == 'no-more-divisions') {
        $('#subdmgstab').html('<center>No more sub-divisions found.</center>');
      } else {
        $('#subdmgstab .sub_dmgs_table table tbody').html(data);

        $('#subdmgstab .sub_dmgs_loading').hide();
        $('#subdmgstab .sub_dmgs_table').show();
        $('#subdmgstab .sub_dmgs_table table').DataTable({
          fixedColumns: true,
          dom: 'Bfrtip',
          lengthMenu: [
            [10, 20, 50, -1],
            ['10 rows', '20 rows', '50 rows', 'Show all']
          ],

          buttons: [
            'pageLength',
            'copy', 'csv', 'excel', 'pdf',
            {
              extend: 'print',
              exportOptions: {
                columns: [0, ':visible']
              },
              customize: function(win) {
                $(win.document.body)
                  .css('text-align', 'center')
                  .css('font-size', '10pt')
                  .prepend(
                    '<img src="" style="position:absolute; top:0; left:0;" />'
                  );

                $(win.document.body).find('table')
                  .addClass('compact')
                  .css('font-size', 'inherit');
              }
            }
          ],

        });
      }

    }).fail(function() {
      $('#subdmgstab').html('<center>Something went wrong, please reload the page.</center>');
    });
  }
  function loadexternaldeps() {
    $.ajax({
      method: "GET",
      url: "{{route('get-external-deps-stats')}}",
      data: {
        date: "{{ $data['c_date'] }}"
      }

    }).done(function(data) {
      $('#externaldeps .externaldeps_table table tbody').html(data);

      $('#externaldeps .externaldeps_loading').hide();
      $('#externaldeps .externaldeps_table').show();
      $('#externaldeps .externaldeps_table table').DataTable({
        fixedColumns: true,
        dom: 'Bfrtip',
        lengthMenu: [
          [10, 20, 50, -1],
          ['10 rows', '20 rows', '50 rows', 'Show all']
        ],

        buttons: [
          'pageLength',
          'copy', 'csv', 'excel', 'pdf',
          {
            extend: 'print',
            exportOptions: {
              columns: [0, ':visible']
            },
            customize: function(win) {
              $(win.document.body)
                .css('text-align', 'center')
                .css('font-size', '10pt')
                .prepend(
                  '<img src="" style="position:absolute; top:0; left:0;" />'
                );

              $(win.document.body).find('table')
                .addClass('compact')
                .css('font-size', 'inherit');
            }
          }
        ],

      });
      

    }).fail(function() {
      $('#subdmgstab').html('<center>Something went wrong, please reload the page.</center>');
    });
  }
</script>

<script>
    $(document).ready(function() {

        $('.ai_adv').on('click',function(){
            $('.ai_adv i').toggleClass('fa-caret-down');
            $('.ai_adv i').toggleClass('fa-caret-up');

            $(".ai_adv_det").toggle();

        });
    });
</script>


@endsection