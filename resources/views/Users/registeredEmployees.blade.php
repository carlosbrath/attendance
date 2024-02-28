@extends('layout.layout')
@section('title', 'Rehistered Employee per Date')
@section('content')

    <link href="{{url('assets/plugins/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet" type="text/css" />
    <link href=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css " rel="stylesheet">
    <link href="{{url('select2totree/select2totree.css')}}" rel="stylesheet" type="text/css" />
    <div class="col-md-12 radius card_shadow white  m-b-5"  style="background:#fff">
        <div class="p-t-10 p-b-10 p-l-5 p-r-5">
            <div class="row bg-white clearfix">
                <form action="{{route('reg_employees')}}" class="col-md-7" method="get">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-4 m-y-2 jquicss">
                            <input type="date" name="date_from" class="form-control " value="{{$startDate}}"   required/>
                        </div>
                        <div class="col-md-4 m-y-2 jquicss">
                            <input type="date" name="date_to" class="form-control " value="{{$endDate}}"   required/>
                        </div>
                        <div class="col-md-2 m-y-2 text-center">
                            <button type="submit" class="btn btn-primary btn-success" title="Search" name="submit" value="Filter" style="margin-right:15px"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>

                <div class="col-sm-12 m-t-10  collapse" id="collapseDMGs">@include('include.dmg_list')</div>
            </div>
        </div>
    </div>
    <div class="grid simple ">
        <div class="row tableView tiles card_shadow white p-t-20 p-b-20 p-l-20 p-r-20 m-t-10 m-b-10 ">
            <div class="col-md-12 col-vlg-12 m-b-10 ">
                <h4 style="color: #0A246A; font-weight: bold">Date wise registered Employees</h4> <em>(Last one month)</em>
            </div>
            <div class="col-md-6 col-vlg-6 m-b-10">
                <table class="table no-more-tables m-l-0 m-b-30 dept_table" style="border-collapse: separate; border-spacing: 0 15px">
                    <thead>
                    <th>Date</th>
                    <th>Total</th>
                    </thead>
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                    @foreach ($users_by_date as $u)
                        <tr>
                            <td style="font-weight: bold;">
                                <a href="{{ route('allemployees', ['reg_date' => $u->date]) }}">
                                    {{ Carbon\Carbon::parse($u->date)->format('d-m-Y') }}
                                </a>
                            </td>
                            <td style="color: blue">
                                <a href="{{ route('allemployees', ['reg_date' => $u->date]) }}">
                                    {{ $u->count }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-6 col-vlg-6 m-b-10 noPrint">
                <canvas id="userChart" width="400" height="200"></canvas>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment"></script>


    <script>
        var ctx = document.getElementById('userChart').getContext('2d');

        var dates = {!! json_encode($users_by_date->pluck('date')) !!};
        var counts = {!! json_encode($users_by_date->pluck('count')) !!};

        // Reverse the order of dates
        dates.reverse();

        var userChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates.map(date => moment(date, 'YYYY-MM-DD').format('DD-MM-YYYY')),
                datasets: [{
                    label: 'Total Registered Users',
                    data: counts.reverse(), // Reverse the order of counts to match the reversed dates
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: false
                }]
            },
            options: {
                scales: {
                    x: [{
                        type: 'time',
                        time: {
                            unit: 'day',
                            displayFormats: {
                                day: 'DD-MM-YYYY'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }],
                    y: {
                        title: {
                            display: true,
                            text: 'Total Registered Users'
                        }
                    }
                }
            }
        });
    </script>
    @include('include.important')
@endsection
