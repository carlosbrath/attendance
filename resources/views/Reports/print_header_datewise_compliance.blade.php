
<style type="text/css">
    .no-border tr th{
        border:none !important;
    }
    .no-border tr td{
        border: none !important;
    }

    tr p{
        margin: 2px !important;
    }

    table.bold td, table.bold td {
        font-weight: bold;
    }

    ol.bold li, ul.bold li{
        font-weight:bold;
    }

    .td-page-break {page-break-after: always;}
    @media print {
        .td-page-break {page-break-after: always;}
    }

    .table_no_border tr th, .table_no_border tr td{
        border:none !important;
    }
    .report_header{
        border-bottom: 2px dotted red;
    }
</style>

<div class="report_header borderb onlyPrint">

    <div class="header_logo" style="width: 20%; float: left;">
        @php 
            $logo = App\Client::where('id',Session::get('dep_id'))->first()->logo;
            $logo_path= asset('photos/'.$logo);
        @endphp
        <img src="{{ $logo_path }}" alt="" width="80px">
    </div>

    <div class="header_desc" style="width: 50%; float: left">
        <h4 class="text-center">Attendance Status Report</h4>
        <h5 class="text-center">{{ Session::get('name') }}</h5>
        <h6 class="text-center">{{ $choosen_department_name?$choosen_department_name:""  }}</h6>
        <h6 class="text-center">Report Dated: {{ date("d/m/Y",strtotime(Request::get('from_date'))) }} to {{ date("d/m/Y",strtotime(Request::get('to_date'))) }}</h6>
        <h6 class="text-center">Printed on: <?php echo date("d,M Y h:m:s:A") ?></h6>

    </div>
    <div style="clear: both;"></div>

    <br/>
    <br/>
</div>