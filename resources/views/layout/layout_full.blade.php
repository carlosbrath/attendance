@include('include.header')
<body class="">
    <style type="text/css">
        #preloader  {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #000c;
            z-index: 9999;
            height: 100%;
        } 

        #status  {
            width: 200px;
            height: 200px;
            position: absolute;
            left: 50%;
            top: 55%;
            background-image: url('{{ asset("loading.gif") }}');
            background-repeat: no-repeat;
            background-position: center;
            margin: -100px 0 0 -100px;
            border-radius: 99px;
            background-size: cover;
        }

        .width_80{
            width:80% !important;
        }

        .width_30{
            width:30% !important;
        }

        .width_20{
            width:20% !important;
        }

        .width_10{
            width:10% !important;
        }
    </style>

<style type="text/css">
        .pagination {
            float: right;
        }

        .table_wrapper {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .tiles {
            border-radius: 15px
        }
    </style>

    <style>
        .user-card {
            background-color: #ffffff;
            padding: 26px;
            border: 1px solid #dddddd;
            color: #6f7b8a;
            margin-top: 50px !important;
        }

        .user-card-top {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            flex-wrap: nowrap;
            margin-bottom: 10px
        }

        .user-img {
            margin-top: -60px;
            width: 100px;
            height: 100px;
            border: 4px solid white;
            border-radius: 9999px;
            object-fit: cover;
        }

        .text-active {
            font-size: 16px;
            font-weight: bold;
            color: #00C851;
        }

        .text-inactive {
          font-size: 16px;
          font-weight: bold;
          color: #FF4444;
      }

        .text-bps {
            font-size: 16px;
            font-weight: bold;
        }

        .text-username {
            font-size: 20px;
            font-weight: bold;
            color: #12124e;
            text-align: center;
        }

        .td-1 {
            width: 40%;
            padding-top: 20px;
            text-align: right;
        }

        .td-2 {
            width: 60%;

            font-weight: bold;
            padding-top: 20px;
            padding-left: 4px;
            color: black;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100px;
        }

        .table-user {
            margin-top: 10px;
            width: 100%;
            border-top: 1px solid #0002;
        }

        .box-pad {
            padding: 10px !important;
        }

        .class_20 {
            width: 20%;
        }

        .class_10 {
            width: 20%;
        }
    </style>

    <style type="text/css">

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

        .select2-selection {
            border: 1px solid #0003 !important;
            background-color: transparent !important;
            border-radius: 8px !important;
            background-image: linear-gradient(to bottom, transparent 0%, transparent 100%) !important;
            height: 35px !important;
            align-items: center  !important;
    display: flex  !important;
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

        .c_present{
            background-color: #00C851 !important;
        }
        .c_absent{
            background-color: #FF4444 !important;
        }
        .c_late{
            background-color: #F3A305 !important;
        }

        .full_bg {
        background-color: #12124E !important;
    }
    
        .table-responsive {
            border: none !important;
        }

     
    

    </style>
    <div id="preloader">
        <div id="status"></div>
    </div>
    @include('include.nabar_header')

    <div class="page-container row-fluid">
        @include('include.page_sidebar')
      
        <div class="page-content">
            <div class="clearfix"></div>
            <div class="content sm-gutter full_bg">
             
                @yield('content')
            </div>
        </div>
    </div>
    @include('include.footer')
</body>

</html>