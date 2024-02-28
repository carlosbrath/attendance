@extends('layout.layout')
@section('title', 'Employee Yearly Report')
@section('content')
@php
$last_mon = 0;
@endphp
<style>
    .user_card {
        height: 205px;
        background-size: contain;
        margin: 0px;
        padding: 0px;
        display: flex;
        background-color: white;
        justify-content: center;
        padding-left: 20px;
        flex-direction: column;
        background-repeat: no-repeat;
        border-radius: 10px;
        background-position: right;
        color: black;
    }

    .small-t {
        font-size: 11px !important;
        font-weight: 500 !important;
    }
    .dept_table2 tr {
          background-color: #F8F8F8 !important;
          border-width: 0px;
          border-radius: 0px;
      }

      .dept_table2 td,
      .dept_table2 th {
          padding: 10px 0px !important;
      }

      .dept_table2 td {
          vertical-align: middle !important;
          text-align: center;
      }



      .dept_table2 thead tr {
          background-color: transparent !important;
          box-shadow: 0px 4px 8px 0px rgba(0, 0, 0, 0);
      }

      .dept_table2 thead th {
          font-size: 11px !important;
          font-weight: bold;
          color: #0f0f0f !important;
          text-align: center;
      }

      .dept_table2 td {
          border-top: 0px !important
      }



      .dept_table2 td span {
          font-size: 13px;
          font-weight: bold;
          color: #0f0f0f !important;
      }

      .image_round {
          border-radius: 99px
      }

      .dept_table2 td a.bold {
          font-size: 16px;
          font-weight: bold;
          color: #0f0f0f;
      }

      .dept_table2 td:last-child {
        border-radius: 0px !important;
      }

     .clicked {
        background-color: #E5E9EC; /* Set your desired background color */
        border: 1px solid #E5E9EC; /* Set your desired border style and color */
    }


</style>
<div class="mt-3">
    @php  $user = \App\User::find($user_information->id); @endphp
    @if($user_information)
    <div class="user_card mb-3">
        <div class="card-body bg-gradient-info ">
            <div class="row m-t-11">
                <div class="col-md-1" style="padding-top: 10px">
                    @if ($user->image)
                        <img style="border: 1px solid black; border-radius: 50%; width: 100px; height: auto;"  src="{{ asset('emp_images') }}/{{ $user->image }}" onerror="this.onerror=null; this.src='{{ asset('assets/img/avatar.png') }}';" />
                    @else
                        <img style="border: 1px solid black; border-radius: 50%; width: 100px; height: auto;"  src="{{ asset('assets/img/avatar.png') }}" />
                    @endif
                </div>
                <div class="col-md-11"  style="padding-left: 20px; padding-right: 20px">
                    <div style="display: flex; justify-content: space-between; padding-right: 15px">
                        <h3 style="color:black;"><b>{{ $user_information->user_name }}</b></h3>
                        <h3 style="color: black"><b>User Id: {{$user_information->id}}</b></h3>
                    </div>
                    <hr width="100%" style="margin-top: -10px">
                    <p style="margin-top: -15px"><b>Department:</b> {{ $user_information->name }}</p>
                    <p style="margin-top: -10px"><b>Designation:</b> {{ $user_information->title }}</p>
                    <p style="margin-top: -10px"><b>CNIC:</b> {{ $user->cnic }}</p>
                    <p style="margin-top: -10px"><b>BPS:</b> {{ $user->bps }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($user->field_staff == 1)
            <div class="m-t-20">
                <h4 class="bg-primary text-white p-t-10 p-b-10 p-l-10 p-r-10 text-center ">
                    Field Staff
                </h4>
            </div>
    @else
        @if($monthlyYearlyAttendance)
        @foreach ($monthlyYearlyAttendance as $year => $monthlyData)
        @if (!empty($monthlyData))
        <div class="row tableView white m-t-20 dataTables_filter" style="margin-top: 2px">
            <div class="card-body col-sm-12 yearlyrow" data-year="{{ $year }}" onclick="toggleMonthlyReport('{{ $year }}')">
                <table class="table no-more-tables m-l-0 dept_table2" style="margin:0px">

                    <tbody>
                        <tr id="yearlyTotals{{ $year }}">
                            <td style="width: 25%; text-align:start;padding-left:40px !important; border-radius:0px !important "><a class="bold" style="16px;font-weight:bolder">{{ $year }}</a><br><span class="small-t"  style="color: #757474 !important;">Year</span></td>
                            <td style="width: 14% !important; text-align:center !important ;padding-left:00px !important"></td>
                            <td style="width: 14%;"></td>
                            <td style="width: 14%;"></td>
                            <td style="width: 14%;"></td>
                            <td style="width: 14%"></td>
                            <td style="width: 5%; border-radius:0px !important;">
                            <i class="fa fa-chevron-circle-right closed" style="{{ $year !== now()->year ? 'display: block;' : 'display: none;' }} ; font-size: 1.5em; color:#1c90ff "> </i>

                            <i class="fa fa-chevron-circle-down open" style="{{ $year === now()->year ? 'display: block;' : 'display: none;' }} ; font-size: 1.5em; color:#1c90ff"> </i>

                        </td>
                        </tr>
                    </tbody>
                </table>
                <!-- <span id="yearlyTotals{{ $year }}" class="float-right text-secondary" style="float: inline-end"></span> -->
            </div>

            <div class="col-sm-12 m-b-10" id="monthlyReport{{ $year }}" style="{{ $year === now()->year ? 'display: block;' : 'display: none;' }} ;">
                <div class="" style="background-color: rgb(255, 255, 255); padding: 10px 20px;">
                    <div class="card-body">
                        <!-- <div class="card-body" id="monthlyReport{{ $year }}" style="{{ $year === now()->year ? 'display: block;' : 'display: none;' }}"> -->
                        <table class="table no-more-tables m-l-0 dept_table2" style="border-collapse: separate; border-spacing: 0 0px;border-radius: 10px; overflow: hidden;border-spacing: 1px;">
                            <tbody>
                            @foreach ($monthlyData as $key => $viewData)
                                    @php
                                        $formattedMonth = $key;
                                    @endphp

                                    @if (isset($monthlyData[$formattedMonth]))
                                        @php
                                            $viewData = $monthlyData[$formattedMonth];

                                            // Check if $viewData is an instance of Illuminate\View\View
                                              if ($viewData instanceof Illuminate\View\View) {
                                                $viewData = $viewData->getData();
                                            } elseif (is_array($viewData)) {
                                                $viewData = $viewData[$formattedMonth]->getData();
                                            } else {
                                                $viewData = [];
                                            }

                                            // Extract other necessary information
                                            $monthName = date("F", strtotime("2022-" . $formattedMonth . "-01"));
                                        @endphp

                                            <!-- Your existing code for displaying data -->
                                        <tr style="box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.1); border-radius:0px !important " >
                                            <td style="width: 25%; text-align:start;padding-left:40px !important ; border-radius:0px !important ">
                                                <a class="bold" id="monthStats{{ $formattedMonth . $year }}" onclick="toggleDailyStats('{{ $formattedMonth }}', '{{ $year }}', this)">
                                                    {{ $monthName}}
                                                </a>
                                            </td>
                                            <td style="width: 14% !important; text-align:center !important ;padding-left:0px !important"><a class="t-present bold " >{{ $viewData['total_present'] + $viewData['total_late'] ?? 'N/A' }}</a></td>
                                            <td style="width: 14%;"><a class="t-absent bold " >{{ $viewData['total_absent'] ?? 'N/A' }}</a></td>
                                            <td style="width: 14%;"><a class="t-late bold " >{{ $viewData['total_late'] ?? 'N/A' }}</a></td>
                                            <td style="width: 14%;"><a class="t-leave bold " >{{ $viewData['total_leave'] ?? 'N/A' }}</a></td>
                                            <td style="width: 14%;"><a class="t-offDay bold " >{{ $viewData['total_day_off'] ?? 'N/A' }}</a></td>
                                            <td style="width: 5%; border-radius:0px !important ; border-radius:0px !important ">
                                                <a class="bold" id="monthStats{{ $formattedMonth . $year }}" onclick="toggleDailyStats('{{ $formattedMonth }}', '{{ $year }}', this)">
                                                    <i class="fa fa-chevron-circle-right closed" style="color:#1c90ff "> </i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr id="dailyStats{{ $formattedMonth . $year }}" style="display:{{ $year === now()->year && $formattedMonth=== now()->month ? 'table-row' : 'none' }};box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.1); border-radius:0px !important ">
                                            <td colspan="7">
                                                @php
                                                    $data = is_array($viewData) ? $viewData : $viewData->toArray();
                                                    $from_date = $data['from_date'];
                                                    $to_date = $data['to_date'];
                                                    $to_date = $to_date->format('Y-m-d');
                                                    $last_mon = $formattedMonth . $year;
                                                    $url = "http://137.59.195.228:8080/ajk_attendance/employee_report2/?user_id={$user_information->id}&from_date={$from_date}&to_date={$to_date}";
                                                @endphp
                                                <div data-url="{{$url}}" class="reloadMe" id="div_dailyStats{{ $formattedMonth . $year }}">
                                                    <!-- Your existing code (e.g., iframe, etc.) -->
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endforeach
        @else
        <div class="m-t-20">
            <h4 class="bg-primary text-white p-t-10 p-b-10 p-l-10 p-r-10 text-center">
                No Record Found
            </h4>
        </div>
        @endif
    @endif

</div>

<script src="{{url('public/assets/plugins/jquery-1.10.1.min.js')}}" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        // Iterate through each div with class "reloadMe"
        $(".reloadMe").each(function() {
            // Get the data-url attribute value
            var url = $(this).data("url");

            // Make an AJAX request to fetch content
            $.ajax({
            url: url,
            method: "GET",
            dataType: "html",
            success: function(data) {
                // Update the content of the current div with fetched data
                $(this).html(data);
            }.bind(this), // Ensure 'this' refers to the current div inside the success callback
            error: function(xhr, status, error) {
                console.error("Error fetching data:", status, error);
            }
            });
        });
    });
</script>
<script>
    function toggleMonthlyReport(year) {
        const monthlyReport = document.getElementById('monthlyReport' + year);
        const yearElement = document.getElementById('yearlyTotals' + year);

        // Check if the clicked year is the current year
        const currentYear = new Date().getFullYear();
        const isCurrentYear = year === currentYear;

        // Hide monthly reports for all other years if the clicked year is not the current year
        if (!isCurrentYear) {
            const allMonthlyReports = document.querySelectorAll('.monthly-report');
            allMonthlyReports.forEach(report => {
                if (report.id !== 'monthlyReport' + year) {
                    report.style.display = 'none';
                }
            });
        }


        const show = yearElement.querySelectorAll('.open');
        const hide = yearElement.querySelectorAll('.closed');

        monthlyReport.style.display = (monthlyReport.style.display === 'none' || !monthlyReport.style.display) ? 'block' : 'none';

        if (monthlyReport.style.display === 'none') {
            show[0].style.display = 'none'
            hide[0].style.display = 'block'
         } else {
            show[0].style.display = 'block'
            hide[0].style.display = 'none'
        };

    }

    function calculateYearlyTotals(year) {
        const totalsElement = document.getElementById('yearlyTotals' + year);
        let totalLeaves = 0;
        let totalPresent = 0;
        let totalLate = 0;
        let totalAbsent = 0;
        let totalOffDays = 0;

        const rows = document.querySelectorAll('#monthlyReport' + year + ' tbody tr');

        rows.forEach(row => {
            const presentCell = row.cells[1];
            const absentCell = row.cells[2];
            const lateCell = row.cells[3];
            const leaveCell = row.cells[4];
            const offDayCell = row.cells[5];

            // Check if the cells are defined before trying to read their textContent
            if (leaveCell && presentCell && lateCell && absentCell && offDayCell) {
                totalLeaves += parseInt(leaveCell.textContent) || 0;
                totalLate += parseInt(lateCell.textContent) || 0;
                totalPresent += parseInt(presentCell.textContent) || 0 + parseInt(lateCell.textContent) || 0;
                totalAbsent += parseInt(absentCell.textContent) || 0;
                totalOffDays += parseInt(offDayCell.textContent) || 0;

            } else {
                console.error('One or more cells are undefined in the current row:', row);
            }
        });

        // const totalsContainer = document.createElement('div');

        const monthlyReport = document.getElementById('monthlyReport' + year);
        var tdElements = totalsElement.querySelectorAll('td');

        tdElements[1].innerHTML = `<a class="t-present bold">${totalPresent}</a><br><span class="small-t" style="color: #757474 !important;">Present</span></td>`;
        tdElements[2].innerHTML = `<a class="t-absent bold">${totalAbsent}</a><br><span class="small-t"  style="color: #757474 !important;">Absent</span></td>`;
        tdElements[3].innerHTML = `<a class="t-late bold">${totalLate}</a><br><span class="small-t"  style="color: #757474 !important;">Late</span></td>`;
        tdElements[4].innerHTML = `<a class="t-leave bold">${totalLeaves}</a><br><span class="small-t"  style="color: #757474 !important;">Leave</span></td>`;
        tdElements[5].innerHTML = `<a class="bold">${totalOffDays}</a><br><span class="small-t"  style="color: #757474 !important;">Off Day</span></td>`;
    }


    function toggleDailyStats(month, year, clickedElement) {

        // Show all rows in the current tbody
        $(clickedElement).parents('tbody').find('tr td.all_rows_show').closest('tr').show();

        const dailyStats = document.getElementById('dailyStats' + month + year);
        const monthlyReport = document.getElementById('monthStats' + month + year);

        if (dailyStats) {
            dailyStats.style.display = (dailyStats.style.display === 'none' || !dailyStats.style.display) ? 'table-row' : 'none';
        }

        const show = monthlyReport.querySelectorAll('.open');
        const hide = monthlyReport.querySelectorAll('.closed');

        if (dailyStats.style.display === 'none') {
            show[0].style.display = 'none';
            hide[0].style.display = 'block';
        } else {
            show[0].style.display = 'block';
            hide[0].style.display = 'none';
        }

        var iconElement = element.querySelector('i');
        if (iconElement.classList.contains('closed')) {
            iconElement.classList.remove('closed');
            iconElement.classList.add('opened'); // Add your desired class name
        } else {
            iconElement.classList.remove('opened');
            iconElement.classList.add('closed');
        }

        var iframe = dailyStats.querySelector('iframe');
        resizeIframe(iframe);
    }



    document.addEventListener('DOMContentLoaded', function() {
        var yearlyRows = document.querySelectorAll('.yearlyrow');
        yearlyRows.forEach(function(row) {
            calculateYearlyTotals(row.getAttribute('data-year'));
        });

        const last_mon = document.getElementById("monthStats{{$last_mon}}");
        last_mon.scrollIntoView();
    });

    function resizeIframe(iframe) {
        iframe.height = iframe.contentWindow.document.body.scrollHeight + "px";
    }
</script>




@endsection
