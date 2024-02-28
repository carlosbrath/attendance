<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <?php
      //$dpermtent_id=Session::get('dep_id');
      //$client_image= App\Client::where('id',$dpermtent_id)->first();
    ?>
    @if(Session::get('role_id')==1)
    <title>{{ $page_title ?? 'No Page Title Choosen' }}</title>
    @else
    <title><?php echo  Session::get('name'); ?></title>
    @endif
    

<link rel="apple-touch-icon" sizes="180x180" href="{{asset('images/icons/apple-touch-icon.png')}}">
<link rel="icon" type="image/png" sizes="32x32" href="{{asset('images/icons/favicon-32x32.png')}}">
<link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/icons/favicon-16x16.png')}}">
<link rel="manifest" href="{{asset('images/icons/site.webmanifest')}}">
<link rel="mask-icon" href="{{asset('images/icons/safari-pinned-tab.svg')}}" color="#5bbad5">
<link rel="shortcut icon" href="{{asset('images/icons/favicon.ico')}}">
<meta name="apple-mobile-web-app-title" content="AJK Boimetric Attendance">
<meta name="application-name" content="AJK Boimetric Attendance">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="msapplication-config" content="{{asset('images/icons/browserconfig.xml')}}">
<meta name="theme-color" content="#ffffff">



    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <!-- <script src="{{url('template_files/theme/cdn-cgi/apps/head/8jwJmQl7fEk_9sdV6OByoscERU8.js')}}"></script> -->
    <link href="{{url('template_files/theme/assets/plugins/font-awesome/css/font-awesome.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('template_files/theme/assets/plugins/bootstrap-tag/bootstrap-tagsinput.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('template_files/theme/assets/plugins/dropzone/css/dropzone.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('template_files/theme/assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('template_files/theme/assets/plugins/bootstrap-datepicker/css/datepicker.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('template_files/theme/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('template_files/theme/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('template_files/theme/assets/plugins/ios-switch/ios7-switch.css')}}" rel="stylesheet" type="text/css" media="screen">
    <link href="{{url('template_files/theme/assets/plugins/bootstrap-select2/select2.css')}}" rel="stylesheet" type="text/css" media="screen" />
    <link href="{{url('css/new_css/css/dataTables.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{url('css/new_css/css/jquery_dataTables.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('template_files/theme/assets/plugins/jquery-datatable/css/jquery.dataTables.css')}}" rel="stylesheet" type="text/css" />
    <!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">-->

    <link href="{{url('template_files/theme/assets/plugins/datatables-responsive/css/datatables.responsive.css')}}" rel="stylesheet" type="text/css" media="screen" />
    <link href="{{url('template_files/theme/assets/plugins/boostrap-clockpicker/bootstrap-clockpicker.min.css')}}" rel="stylesheet" type="text/css" media="screen" />
    <link href="{{url('template_files/theme/assets/plugins/pace/pace-theme-flash.css')}}" rel="stylesheet" type="text/css" media="screen" />
    <link href="{{url('template_files/theme/assets/plugins/bootstrapv3/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('template_files/theme/assets/plugins/bootstrapv3/css/bootstrap-theme.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- <link href="{{url('template_files/theme/webarch/css/fontawesome.css')}}" rel="stylesheet"> -->
    <link href="{{url('template_files/theme/assets/plugins/animate.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('template_files/theme/assets/plugins/jquery-scrollbar/jquery.scrollbar.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('template_files/theme/webarch/css/webarch.css')}}" rel="stylesheet" type="text/css" />

    <link href="{{url('assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.css')}}" rel="stylesheet" type="text/css" />

    <style type="text/css">
        @media print {
            .noPrint {display:none; }
            a[href]:after {content: none !important;}
            .page-content{margin-left: 0 !important;}
        }
        @media screen {.onlyPrint {display: none;}}
        @font-face {font-family: 'Material Icons';font-style: normal;font-weight: 400;src: url('{{url('/template_files/theme/webarch/css/fontsawesom.woff2 ')}}') format('woff2');}
        .highcharts-button, .highcharts-legend, .highcharts-container > svg > text {display: none;}

        .dept_table{ border-collapse: separate!important; border-spacing: 0 15px!important}
        .dept_table tbody tr {background-color: #F8F8F8 !important;border-width: 0px;box-shadow: 0px 2px 4px 1px rgb(0 0 0 / 10%);border-radius: 10px;}
        .dept_table td,.dept_table th {padding: 8px 4px !important;vertical-align: middle !important;text-align: center;border-top: 0px !important;}
        .dept_table thead th {font-size: 12px !important;color: #0f0f0f !important;text-transform: capitalize;}
        .dept_table tbody td {font-size: 16px;}
        .dept_table td a {color: #0f0f0f}

        /*http://localhost/timenexC/public/template_files/theme/webarch/css/fontsawesom.woff2 */
        .material-icons,.table_wrapper,.td-2{white-space:nowrap}
        .material-icons{font-family:'Material Icons';font-weight:400;font-style:normal;font-size:24px;line-height:1;letter-spacing:normal;text-transform:none;display:inline-block;word-wrap:normal;direction:ltr;-webkit-font-feature-settings:'liga';-webkit-font-smoothing:antialiased}
        .text-active,.text-bps,.text-inactive{font-size:16px;font-weight:700}
        .form-actions{background-color:#f6f7f8;border:0;margin-bottom:20px;margin-top:0;padding:19px 20px 9px}
        .grid{clear:both;margin-top:0;margin-bottom:0;padding:0}
        .loader{border:16px solid #f3f3f3;border-radius:50%;border-top:16px solid #3498db;width:120px;height:120px;-webkit-animation:2s linear infinite spin;animation:2s linear infinite spin}
        .table.dataTable.no-footer{border-bottom:1px solid #f1ecec}
        @-webkit-keyframes spin{0%{-webkit-transform:rotate(0)}100%{-webkit-transform:rotate(360deg)}}
        @keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}
        #preloader{position:fixed;top:0;left:0;right:0;bottom:0;background-color:#000c;z-index:9999;height:100%}
        #status{width:200px;height:200px;position:absolute;left:50%;top:55%;background-image:url('{{ asset("loading.gif") }}');background-repeat:no-repeat;background-position:center;margin:-100px 0 0 -100px;border-radius:99px;background-size:cover}

        .pagination{float:right}
        .table_wrapper{display:block;overflow-x:auto}
        .user-card{background-color:#fff;padding:26px;border:1px solid #ddd;color:#6f7b8a;margin-top:50px!important}
        .td-1,.td-2{padding-top:20px}
        .user-card-top{display:flex;flex-direction:row;justify-content:space-between;flex-wrap:nowrap;margin-bottom:10px}
        .user-img{margin-top:-60px;width:100px;height:100px;border:4px solid #fff;border-radius:9999px;object-fit:cover}
        .text-active{color:#00c851}
        .text-inactive{color:#f44}
        .active_td{border-left:10px solid #00c851}
        .in_active_td{border-left:10px solid #f44}
        .text-username{font-size:20px;font-weight:700;color:#12124e;text-align:center}
        .td-1{width:40%;text-align:right}
        .td-2{width:60%;font-weight:700;padding-left:4px;color:#000;overflow:hidden;text-overflow:ellipsis;max-width:100px}
        .table-user{margin-top:10px;width:100%;border-top:1px solid #0002}
        .dataTables_filter input,.dataTables_filter select,.dt-button{border:1px solid #0003!important;background-color:transparent!important;background-image:linear-gradient(to bottom,transparent 0,transparent 100%)!important}
        .box-pad{padding:10px!important}
        .no_gutter{padding:0;margin:0}
        .width_5{width:5%!important}
        .width_10{width:10%!important}
        .width_20{width:20%!important}
        .width_25{width:25%!important}
        .width_30{width:30%!important}
        .width_40{width:40%!important}
        .width_50{width:50%!important}
        .width_60{width:60%!important}
        .width_70{width:70%!important}
        .width_75{width:75%!important}
        .width_80{width:80%!important}
        .width_90{width:90%!important}
        .width_100{width:100%!important}

        .dt-button{border-radius:8px!important}
        .dataTables_filter input,.dataTables_filter select{border-radius:8px!important;height:35px!important}
        .select2-selection{border:1px solid #0003!important;background-color:transparent!important;border-radius:8px!important; background-image:linear-gradient(to bottom,transparent 0,transparent 100%)!important;height:35px!important;align-items:center!important;display:flex!important}
        .t-present{color:#00c851!important}
        .t-absent{color:#f44!important}
        .t-late{color:#f3a305!important}
        .t-leave{color:dodgerblue!important}
        .c_present{background-color:#00c851!important}
        .c_absent{background-color:#f44!important}
        .c_late{background-color:#f3a305!important}
        .c_leave{background-color:dodgerblue!important}
        input.mydateformat{position:relative!important;width:100%!important;color:#fff!important}
        input.mydateformat:before{position:absolute;top:0;bottom:0;left:16px;content:attr(data-date);display:inline-block;color:#000}
        input.mydateformat::-webkit-clear-button,input.mydateformat::-webkit-datetime-edit,input.mydateformat::-webkit-inner-spin-button{display:none}
        input.mydateformat::-webkit-calendar-picker-indicator{position:absolute;top:10px;right:4px;color:#000;opacity:1}
        @media (max-width:767px){
        .m-y-2{margin-bottom:5px;margin-top:5px}
        }

        .ajax_loading,.center_tabs,.text-center{text-align:center}
        .card{height:100px;background-size:contain;margin:0;padding:10px;display:flex;background-color:#144681;justify-content:center;flex-direction:column;background-repeat:no-repeat;border-radius:10px;background-position:right}
        .card.autoheight{height:auto!important;}
        .b1{background-image:url("{{ asset('assets/img/dash/a.png') }}")}
        .b2{background-image:url("{{ asset('assets/img/dash/b.png') }}")}
        .b3{background-image:url("{{ asset('assets/img/dash/c.png') }}")}
        .b4{background-image:url("{{ asset('assets/img/dash/d.png') }}");}
        .b5{background-image:url("{{ asset('assets/img/dash/e.png') }}");}
        .b6{background-image:url("{{ asset('assets/img/dash/f.png') }}")}
        .b7{background-image:url("{{ asset('assets/img/dash/g.png') }}")}
        .b8{background-image:url("{{ asset('assets/img/dash/h.png') }}")}
        .b9{background-image:url("{{ asset('assets/img/dash/i.png') }}")}
        .b10{background-image:url("{{ asset('assets/img/dash/j.png') }}")}
        .b11{background-image:url("{{ asset('assets/img/dash/k.png') }}")}
        .b12,.b13,.b14,.b15{background-color:#26718e}


        .text_heading{font-size:18px;margin:0;color:#fff}
        .text_count{font-size:30px;color:#fff;margin:0;line-height:normal;font-weight:700}
        .sub+.sub::before{content:'+';margin:0 2px;display:inline-block}
        .card_round{flex:1;border-radius:15px;padding-top:20px;padding-bottom:20px;background-color:#fff}

        .card_main{padding:5px}
        .card_shadow{box-shadow:-5px 4px 10px 0 #0000001c}

        .align-center{align-items:center}
        .card_chart{justify-content:space-between;display:flex;flex-direction:column;padding:0}
        .dt-button{border:1px solid #0003!important;border-radius:8px!important;background-color:transparent!important;background-image:linear-gradient(to bottom,transparent 0,transparent 100%)!important}
        .tiles{border-radius:15px;overflow:hidden}
        .radius{border-radius:15px;}


        .center_tabs{background-color:transparent}
        .center_tabs li{display:inline-block;float:none}
        .center_tabs li a{font-weight:700;font-size:20px}
        .center_tabs li.active a{color:#1e90ff;border-radius:15px 15px 0 0}
        .center_tabs li a:hover,.center_tabs:hover li.active a{border-radius:15px 15px 0 0!important}
        .spin{animation:1s linear infinite spin}@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}

        .ad_filters{display:none}
        .dataTables_filter input,.dataTables_filter select{background-color:transparent!important;background-image:linear-gradient(to bottom,transparent 0,transparent 100%)!important}
        .dataTables_filter input,.dataTables_filter select{border:1px solid #0003!important;border-radius:8px!important;height:35px!important}
        .dataTables_filter .main .ai_query-container{position:relative;display:inline-block}
        .dataTables_filter .main .ai_query{border:2px double transparent!important;background-image:linear-gradient(white,#fff),linear-gradient(to right,red,orange)!important;background-origin:border-box;background-clip:padding-box,border-box}

        .btn-all,.btn-dis,.btn-div{background-color:#e9e9e9}

    </style>

    <script src="{{url('css/new_css/css/js/jquery-3.3.1.js')}}" type="text/javascript"></script>
  </head>
