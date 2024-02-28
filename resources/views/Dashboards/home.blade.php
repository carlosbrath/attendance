@extends('layout.layout')
@section('title', 'State Dashboard')
@section('content')

<link href="{{url('assets/plugins/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet" type="text/css" />
<link href=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css " rel="stylesheet">
<link href="{{url('select2totree/select2totree.css')}}" rel="stylesheet" type="text/css" />
 
<style>
    .full_screen {width: 100%;height: 100%;background-color: #12124E !important;padding: 30px;border-radius: 15px;}
    .card {padding: 10px;background-color: white;border-radius: 15px;}
    .card:hover {animation: scaleAnimation 0.5s ease-in-out infinite;}
    .card_container {padding: 10px !important;}
    .text-title {color: #12124E;font-size: 18px;font-weight: bold;text-align: center;margin: 0 !important;}
    .text-hint {color: #12124E;font-size: 14px;font-weight: 300;margin: 0 !important;}
    .text-desc {color: #12124E;font-size: 17px;}
    .heading {font-size: 32px;color: #12124E;margin: 0px;line-height: 50px;font-weight: bold;}
    .title-main {font-size: 50px;color: white;font-weight: bold;line-height: normal;}
    .padding {padding: 20px !important;}
    .padding_10 {padding: 10px !important;}
    .small_card {display: flex;align-items: center;padding: 10px;border-radius: 10px;}
    @keyframes scaleAnimation {
        0% {transform: scale(1);}
        50% {transform: scale(1.03);}
        100% {transform: scale(1);}
    }
    .container-clock {position: relative;margin-top: 10px;}
    .container-clock .circle {position: absolute;border-radius: 50%;animation: 2s ease-in infinite alternate;}
    .container-clock .circle:nth-child(1) {width: 50px;height: 50px;top: 20px;left: -10px;background: linear-gradient(#3f5efb, #fc466b);animation-name: move-up;}
    @keyframes move-up {to {transform: translateY(-30px);}}
    .container-clock .circle:nth-child(2) {width: 50px;height: 50px;background: linear-gradient(#833ab4, #fd1d1d, #fcb045);bottom: 0;left: 100px;animation-name: move-down;}
    @keyframes move-down {to {transform: translateY(30px);}}
    .clock {width: 150px;height: 150px;display: flex;align-items: center;justify-content: center;border: 2px solid rgba(255, 255, 255, 0.1);border-radius: 50%;backdrop-filter: blur(15px);background: rgba(255, 255, 255, 0.05);box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);}
    .clock-img img {width: 100%;height: 100%;}
    .clock::before {content: "";position: absolute;width: 15px;height: 15px;background: #fff;border-radius: 50%;z-index: 15;}
    .clock .hour,.clock .minute,.clock .second {position: absolute;}
    .clock .hour,.clock .hour .hr {width: 60px;height: 60px;}
    .clock .minute,.clock .minute .min {width: 90px;height: 90px;}
    .clock .second,.clock .second .sec {width: 130px;height: 130px;}
    .hr,.min,.sec {display: flex;justify-content: center;position: absolute;border-radius: 50%;}
    .hr::before {content: "";position: absolute;width: 4px;height: 40px;background-color: #f00;border-radius: 10px;z-index: 10;}
    .min::before {content: "";position: absolute;width: 4px;height: 60px;background-color: #0f0;z-index: 11;border-radius: 8px;}
    .sec::before {content: "";position: absolute;width: 2px;height: 80px;background-color: #fff;z-index: 12;border-radius: 4px;}
    .dataTables_filter .main .ai_query-container {position: relative;display: inline-block;}
    .dataTables_filter .main .ai_query {border: double 2px transparent !important;background-image: linear-gradient(white, white), linear-gradient(to right, red, orange) !important;background-origin: border-box;background-clip: padding-box, border-box;}
    .autocomplete {position: relative;display: inline-block;}
    .autocomplete-items {position: absolute;border: 1px solid #d4d4d4;border-bottom: none;border-top: none;z-index: 99;top: 100%;left: 0;right: 0;}
    .autocomplete-items div {padding: 10px;cursor: pointer;background-color: #fff;border-bottom: 1px solid #d4d4d4;}
    .autocomplete-items div:hover {background-color: #e9e9e9;}
    .autocomplete-active {background-color: DodgerBlue !important;color: #ffffff;}
   
    .btn-dis,.btn-all,.btn-div {background-color: #e9e9e9;}
    .ai_adv{position: absolute;top: 0;left: 1px;background: none!important;}
    .ai_adv_det{max-height: 90vh;overflow-y: auto;position: absolute;top: 40px;left: 4px;right: 4px;background: rgb(255, 255, 255);border: 1px solid rgb(221, 221, 221);z-index: 20000;padding: 10px;border-radius: 8px;}
    ul.ai_sub_filters{padding: 0; list-style: none;columns: 2;-webkit-columns: 2;-moz-columns: 2;}
    ul.ai_sub_filters + ul.ai_sub_filters{margin-top: 16px;border-top: 1px dashed #ccc;padding-top: 16px;}
    ul.ai_sub_filters li+li{margin-top:8px}
</style>

<div class="row full_screen">
    <div class="col-md-12 radius card_shadow dataTables_filter m-b-5"  style="background:#fff">
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
                <form action="{{route('super-admin')}}" class="col-md-7" method="get">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6 m-y-2">
                            <select name="department_id" id="all_departments" class="form-control" placeholder="Select Department">
                            </select>
                        </div>
                        <div class="col-md-4 m-y-2 jquicss">
                                <input type="text" name="allregions_input" id="allregions_input" class="form-control" placeholder="Enter Region, District or Tehsil">
                                <input type="hidden" name="allregions" id="allregions" value='all'>
                        </div>
                        <div class="col-md-2 m-y-2 text-center">
                            <button type="submit" class="btn btn-primary btn-success" title="Search" name="submit" value="Filter" style="margin-right:15px"><i class="fa fa-search"></i></button>
                            <button class="btn btn-primary " type="button" title="AJK Devisions"  data-toggle="collapse" data-target="#collapseDMGs" aria-expanded="false" aria-controls="collapseDMGs"><i class="fa fa-sitemap"> </i></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="form-row clearfix  collapse" id="collapseDMGs">@include('include.dmg_list')</div>
        </div>
    </div>



    <div class="col-sm-6 padding">
        <p class="title-main">
            AJ&K Biometric Attendance System
        </p>
        <p class="text-hint text-white">
            AJ&K Biometric Attendance System uses face recognition for precise time tracking, ensuring accurate and transparent Government employees presence monitoring.
        </p>
        <div class="container-clock">
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="clock">
                <div class="clock-img">
                    <img src="{{asset('assets/img/clock.png')}}" alt="clock" />
                </div>
                <div class="hour">
                    <span class="hr" id="hr"></span>
                </div>
                <div class="minute">
                    <span class="min" id="min"></span>
                </div>
                <div class="second">
                    <span class="sec" id="sec"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-12 card_container card_shadow">
                <a href="{{route('super-admin')}}" target="_blank">
                    <div class="card autoheight">
                        <div class="row p-l-5 p-r-5">
                            <div class="col-sm-8 padding_10">
                                <p class="heading">All AJ&K</p>
                                <p class="text-hint">AJ&K Biometric Attendance System streamlines workforce management, leveraging facial recognition for precise time tracking.</p>
                            </div>
                            <div class="col-sm-4">
                                <img src="{{ asset('assets/img/full_map.png') }}" height="100%" width="100%" />
                            </div>
                        </div>
                    </div>
            </div>
        </div>

        <div class="col-sm-4 card_container">
            <a href="{{route('super-admin')}}?allregions=reg_1" target="_blank">
                <div class="card autoheight">
                    <img src="{{ asset('assets/img/mzdz_map.png') }}" height="100%" width="100%" />
                    <p class="text-title">Muzaffarabad</p>
                    <p class="text-hint text-center">Division</p>

                </div>
            </a>
        </div>

        <div class="col-sm-4 card_container">
            <a href="{{route('super-admin')}}?allregions=reg_3" target="_blank">
                <div class="card autoheight">
                    <img src="{{ asset('assets/img/hav_map.png') }}" height="100%" width="100%" />
                    <p class="text-title">Poonch</p>
                    <p class="text-hint text-center">Division</p>

                </div>
            </a>
        </div>

        <div class="col-sm-4 card_container">
            <a href="{{route('super-admin')}}?allregions=reg_2" target="_blank">
                <div class="card autoheight">
                    <img src="{{ asset('assets/img/mir_map.png') }}" height="100%" width="100%" />
                    <p class="text-title">Mirpur</p>
                    <p class="text-hint text-center">Division</p>
                </div>
            </a>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-3 card_container">
                <a href="{{route('super-admin')}}?department_id=2751" target="_blank">
                    <div class="small_card card autoheight">
                        <img src="{{ asset('assets/img/sec.png') }}" height="50" width="50" style="padding: 10px;" />
                        <span class="text-title m-l-5">Secretariat</span>
                    </div>
                </a>
            </div>

            <div class="col-sm-3 card_container">
                <a href="{{route('super-admin')}}?department_id=2761" target="_blank">
                    <div class="small_card card autoheight">
                        <img src="{{ asset('assets/img/adm.png') }}" height="50" width="50" style="padding: 10px;" />
                        <span class="text-title m-l-5">Administration</span>
                    </div>
                </a>
            </div>

            <div class="col-sm-3 card_container">
                <a href="{{route('super-admin')}}?department_id=2780" target="_blank">
                    <div class="small_card card autoheight">
                        <img src="{{ asset('assets/img/dir.png') }}" height="50" width="50" style="padding: 10px;" />
                        <span class="text-title m-l-5">Directorate & Others</span>
                    </div>
            </div>

            <div class="col-sm-3 card_container">
                <a href="{{route('super-admin')}}?department_id=1972" target="_blank">
                    <div class="small_card card autoheight">
                        <img src="{{ asset('assets/img/hel.png') }}" height="50" width="50" style="padding: 10px;" />
                        <span class="text-title m-l-5">Health</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
</div>

<script src=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js "></script>
<script src="{{url('select2totree/select2totree.js')}}" type="text/javascript"></script>
<script src="{{url('js/clock.js')}}"></script>

<script src="{{url('public/assets/plugins/jquery-ui/jquery-ui.min.js')}}" type="text/javascript"></script>

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
  //(function ($) {
  
  var all_depts_data = @php echo json_encode($data['all_departments']);@endphp;
  //console.log(all_depts_data);

  $("#all_departments").select2ToTree({
    treeData: {
      dataArr: all_depts_data
    },
    maximumSelectionLength: 3
  });
  $("#all_departments").val("@php echo (isset($data['department_id']))?$data['department_id']:'all';@endphp").trigger('change');

  //});
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