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
    </style>

    <div class="row full_screen">
        <div class="col-sm-5">
            <div class="row">
                <div class="col-sm-6 card_container">
                    <a href="http://137.59.195.228:8080/ajk_attendance/allemployees?ai_query=Secretaries" target="_blank">
                        <div class="small_card card autoheight"  style="flex-direction: row">
                            <img src="{{ asset('assets/img/sectories.png') }}" height="50" width="50" style="margin-right: 10px;" />
                            <span class="text-title m-l-5">Secretaries</span>
                        </div>
                    </a>
                </div>

                <div class="col-sm-6 card_container">
                    <a href="http://137.59.195.228:8080/ajk_attendance/allemployees?ai_query=Commissioners" target="_blank">
                        <div class="small_card card autoheight" style="flex-direction: row">
                            <img src="{{ asset('assets/img/commissnors.png') }}" height="50" width="50" style="margin-right: 10px;" />
                            <span class="text-title m-l-5">Commissioners</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="row">
                <div class="col-sm-4 card_container">
                    <a href="http://137.59.195.228:8080/ajk_attendance/allemployees?ai_query=DCs" target="_blank">
                        <div class="small_card card autoheight" style="flex-direction: row">
                            <img src="{{ asset('assets/img/dc.png') }}" height="50" width="50" style="margin-right: 10px;" />
                            <span class="text-title m-l-5">DCs</span>
                        </div>
                    </a>
                </div>

                <div class="col-sm-4 card_container">
                    <a href="http://137.59.195.228:8080/ajk_attendance/allemployees?ai_query=DIGPs" target="_blank">
                        <div class="small_card card autoheight" style="flex-direction: row">
                            <img src="{{ asset('assets/img/digs.png') }}" height="50" width="50" style="margin-right: 10px;" />
                            <span class="text-title m-l-5">DIGs</span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-4 card_container">
                    <a href="http://137.59.195.228:8080/ajk_attendance/allemployees?ai_query=SSPs" target="_blank">
                        <div class="small_card card autoheight" style="flex-direction: row">
                            <img src="{{ asset('assets/img/ssps.png') }}" height="50" width="50" style="margin-right: 10px;" />
                            <span class="text-title m-l-5">SSPs</span>
                        </div>
                    </a>
                </div>
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
                <div class="col-sm-2 card_container">
                    <a href="http://137.59.195.228:8080/ajk_attendance/allemployees?bps=22" target="_blank">
                        <div class="small_card card autoheight" style="flex-direction: row">
                            <img src="{{ asset('assets/img/founder.png') }}" height="50" width="50" style="margin-right: 10px;" />
                            <span class="text-hint m-l-5"><b>BPS-22</b></span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-2 card_container">
                    <a href="http://137.59.195.228:8080/ajk_attendance/allemployees?bps=>=:21" target="_blank">
                        <div class="small_card card autoheight" style="flex-direction: row">
                            <img src="{{ asset('assets/img/founder.png') }}" height="50" width="50" style="margin-right: 10px;" />
                            <span class="text-hint m-l-5"><b>BPS-21 & Above</b></span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-2 card_container">
                    <a href="http://137.59.195.228:8080/ajk_attendance/allemployees?bps=>=:20" target="_blank">
                        <div class="small_card card autoheight" style="flex-direction: row">
                            <img src="{{ asset('assets/img/manager.png') }}" height="50" width="50" style="margin-right: 10px;" />
                            <span class="text-hint m-l-5"><b>BPS-20 & Above</b></span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-2 card_container">
                    <a href="http://137.59.195.228:8080/ajk_attendance/allemployees?bps=>=:19" target="_blank">
                        <div class="small_card card autoheight" style="flex-direction: row">
                            <img src="{{ asset('assets/img/manager.png') }}" height="50" width="50" style="margin-right: 10px;" />
                            <span class="text-hint m-l-5"><b>BPS-19 & Above</b></span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-2 card_container">
                    <a href="http://137.59.195.228:8080/ajk_attendance/allemployees?bps=>=:18" target="_blank">
                        <div class="small_card card autoheight" style="flex-direction: row">
                            <img src="{{ asset('assets/img/team.png') }}" height="50" width="50" style="margin-right: 10px;" />
                            <span class="text-hint m-l-5"><b>BPS-18 & Above</b></span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-2 card_container">
                    <a href="http://137.59.195.228:8080/ajk_attendance/allemployees?bps=>=:17" target="_blank">
                        <div class="small_card card autoheight" style="flex-direction: row">
                            <img src="{{ asset('assets/img/team.png') }}" height="50" width="50" style="margin-rightmargin-right: 10px;" />
                            <span class="text-hint m-l-5"> <b>BPS-17 & Above</b></span>
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



@endsection
