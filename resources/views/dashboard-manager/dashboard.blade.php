@extends('layout.layout')
@section('title','Dashboard')
@section('content')
<?php

    $date=date('Y-m-d');
    if(isset($attendance_data['present'][$date])){
       $daily_present= $attendance_data['present'][$date];

     }else{
          $daily_present=0;
     }
    if(isset($attendance_data['absent'][$date])){
       $daily_absent= $attendance_data['absent'][$date];

     }else{
          $daily_absent=0;
     }

     if(isset($attendance_data['late'][$date])){
       $daily_late= $attendance_data['late'][$date];
     }else{
          $daily_late=0;
     }
     if(isset($attendance_data['leave'][$date])){
       $daily_leave= $attendance_data['leave'][$date];
     }else{
          $daily_leave=0;
     }
     if(isset($attendance_data['attach'][$date])){
       $daily_attach= $attendance_data['attach'][$date];
     }else{
          $daily_attach=0;
     }

     $total_absent= $daily_absent;//$total_departmental_employees-($daily_present+ $daily_leave+ $daily_attach);

      $online = 0;
      $offline = 0;
      foreach($online_offline_devices as $device){
          if($device->online_status == 1){
              $online = $online+1;
          }else{
              $offline = $offline+1;
          }
      }


 ?>
<div class="row">
    <div class="col-md-12">
        <div class="grid simple" style="position: static;">
        <div class="grid-title no-border">
            <h4> Attendance Summary</h4>
            <div class="tools"><a href="javascript:;" class="collapse"></a></div>
            </div>
            <div class="grid-body no-border" style="display: block;">
        <style type="text/css">
          .rcorners2 {border-radius: 25px;padding: 20px 10px;}
          .box-wrap{display:flex;gap:20px}
          .box-wrap+.box-wrap{margin-top:20px}
          .box-wrap .box{flex:1}
          </style>
            <div class="grid-body no-border" style="display: block;">
              <div class="box-wrap">
                  <div class="box">
                      <div class="bg-green text-center rcorners2 ">
                      <h2 class="semi-bold text-white weather-widget-big-text no-margin p-t-35 p-b-10">
                      <a href="{{url('employees-list')}}" class="text-white">{{$total_departmental_employees}}</a></h2>
                      <h4 class="bold text-white">All</h4></div>
                  </div>
                  <div class="box">
                      <div class="bg-blue text-center rcorners2">
                          <h2 class="semi-bold text-white weather-widget-big-text no-margin p-t-35 p-b-10">
                          <a href="{{url('employee/present_employee')}}" class="text-white" > {{$daily_present}}</a></h2>
                          <h4 class="bold text-white">Present</h4>
                      </div>
                  </div>
                  <div class="box">
                      <div class="bg-yellow text-center rcorners2">
                          <h2 class="semi-bold text-white weather-widget-big-text no-margin p-t-35 p-b-10">
                              <a href="{{url('employee/late_employee')}}" class="text-white"> {{$daily_late}}</a></h2>
                          <h4 class="bold text-white">Late</h4>
                      </div>
                  </div>
                  <div class="box">
                      <div class="bg-red text-center rcorners2">
                          <h2 class="semi-bold text-white weather-widget-big-text no-margin p-t-35 p-b-10">
                              <a href="{{url('employee/absent_employee')}}" class="text-white"> {{$daily_absent}}</a></h2>
                          <h4 class="bold text-white">Absent </h4>
                      </div>
                  </div>
                  <div class="box">
                      <div class="bg-primary text-center rcorners2">
                          <h2 class="semi-bold text-white weather-widget-big-text no-margin p-t-35 p-b-10">
                              <a href="{{ route('roasters.index') }}" class="text-white">{{$total_roster_employees}}</a></h2>
                          <h4 class="bold text-white">On Schedule </h4>
                      </div>
                  </div>
              </div>

            </div>
        </div>
    </div>
  </div>

 <div class="row">
    <div class="col-md-12">
        <div class="grid simple" style="position: static;">
        <div class="grid-title no-border">
            <h4> Leave Attach Employees</h4>
            <div class="tools"><a href="javascript:;" class="collapse"></a></div>
            </div>
            <div class="grid-body no-border" style="display: block;">

            <div class="grid-body no-border" style="display: block;">
              <div class="box-wrap">
                  <div class="box">
                      <div class="bg-primary text-center rcorners2 ">
                      <h2 class="semi-bold text-white weather-widget-big-text no-margin p-t-35 p-b-10">
                      <a href="{{url('employee/employee_leave')}}">{{$daily_leave}}</a></h2>
                      <h4 class="bold text-white">Leave</h4></div>
                  </div>
                  <div class="box">
                      <div class="bg-green text-center rcorners2">
                          <h2 class="semi-bold text-white weather-widget-big-text no-margin p-t-35 p-b-10">
                          <a href="{{url('employee/employee_attach')}}" > {{$daily_attach}}</a></h2>
                          <h4 class="bold text-white">Attach</h4>
                      </div>
                  </div>
                  <div class="box">
                      <div class="bg-red text-center rcorners2">
                          <h2 class="semi-bold text-white weather-widget-big-text no-margin p-t-35 p-b-10">
                              <a href="{{url('employee/employee_short_leave')}}"> {{$short_leave_count}}</a></h2>
                          <h4 class="bold text-white">Short Leave</h4>
                      </div>
                  </div>
              </div>

            </div>
        </div>
    </div>
  </div>
<div class="row">
  <div class="col-md-4 col-vlg-4 col-sm-6">
    <div class="tiles green m-b-10">
      <div class="tiles-body">
        <div class="controller">
          <a href="javascript:;" class="reload"></a>
          <a href="javascript:;" class="remove"></a>
        </div>
        <div class="tiles-title text-black"></div>
        <div class="widget-stats">
          <div class="wrapper transparent">
            <a href="{{url('users')}}" style="text-decoration: none;color:white">
            <span class="item-title">All</span> <span class="item-count animate-number semi-bold" data-value="{{$total_departmental_employees}}" data-animation-duration="700"></span></a>
          </div>
        </div>
        <div class="widget-stats">
          <div class="wrapper transparent">
            <!-- {{url('employee/present_employee')}} -->
            <a href="#" style="text-decoration: none;color:white">
            <span class="item-title">Present</span> <span class="item-count animate-number semi-bold" data-value="{{$daily_present}}" data-animation-duration="700"></span></a>
          </div>
        </div>
        <div class="widget-stats">
          <div class="wrapper transparent">
            <!-- {{url('employee/late_employee')}} -->
            <a href="#" style="text-decoration: none;color:white">
            <span class="item-title">Late</span> <span class="item-count animate-number semi-bold" data-value="{{$daily_late}}" data-animation-duration="700"></span></a>
          </div>
        </div>
        <div class="widget-stats">
          <div class="wrapper transparent">
            <!-- {{url('employee/absent_employee')}} -->
            <a href="#" style="text-decoration: none;color:white">
            <span class="item-title">Absent</span> <span class="item-count animate-number semi-bold" data-value="{{$total_absent}}" data-animation-duration="700"></span></a>
          </div>
        </div>
        <div class="widget-stats">
          <div class="wrapper last">
            <a href="{{ route('roasters.index') }}" style="text-decoration: none;color:white">
            <span class="item-title">On Schedule</span> <span class="item-count animate-number semi-bold" data-value="{{$total_roster_employees}}" data-animation-duration="700"></span></a>
          </div>
        </div>
        <div class="progress transparent progress-small no-radius m-t-20" style="width:90%">
          <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="64.8%" style="width: 64.8%;"></div>
        </div>
        <div class="description"> <span class="text-white mini-description ">
          Attendance Summary <span class="blend">Today</span></span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-8">
  <div class="col-md-6 col-vlg-4 col-sm-6">
    <div class="tiles blue m-b-10">
      <div class="tiles-body">
        <div class="controller">
          <a href="javascript:;" class="reload"></a>
          <a href="javascript:;" class="remove"></a>
        </div>

        <div class="widget-stats">
          <div class="wrapper transparent">
            <a href="{{url('employee/employee_leave')}}" style="text-decoration: none;color:white">
            <span class="item-title">Leave</span> <span class="item-count animate-number semi-bold"  data-value="{{$daily_leave}}" animation-duration="700"></span>
          </a>
          </div>
        </div>
        <div class="widget-stats">
          <div class="wrapper transparent">
            <a href="{{url('employee/employee_attach')}}" style="text-decoration: none;color:white">
            <span class="item-title">Attach</span> <span class="item-count animate-number semi-bold" data-value="{{$daily_attach}}" data-animation-duration="700">0</span>
            </a>
          </div>
        </div>
        <div class="widget-stats ">
          <div class="wrapper last">
            <a href="{{url('employee/employee_short_leave')}}" style="text-decoration: none;color:white">
            <span class="item-title">Short Leave</span> <span class="item-count animate-number semi-bold" data-value="{{$short_leave_count}}" data-animation-duration="700"></span>
            </a>
          </div>
        </div>
        <div class="progress transparent progress-small no-radius m-t-20" style="width:90%">
          <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="54%" style="width: 54%;"></div>
        </div>
        <div class="description"> <span class="text-white mini-description ">Leave/Attach Employees <span class="blend">Today</span></span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-vlg-4 col-sm-6">
    <div class="tiles purple m-b-10">
      <div class="tiles-body">
        <div class="controller">
          <a href="javascript:;" class="reload"></a>
          <a href="javascript:;" class="remove"></a>
        </div>
        <div class="widget-stats">
          <a  style="text-decoration: none;color:white">
          <div class="wrapper transparent">
            <span class="item-title">Devices</span> <span class="item-count animate-number semi-bold" data-value="{{ $online+$offline }}" data-animation-duration="700"></span>
          </div>
        </a>


        </div>
        <div class="widget-stats">
          <div class="wrapper transparent">
          <a  style="text-decoration: none;color:white">
            <span class="item-title">Online</span> <span class="item-count animate-number semi-bold" data-value="{{ $online }}" data-animation-duration="700"></span>
          </div>
         </a>
        </div>
        <div class="widget-stats ">
          <div class="wrapper last">
            <a  style="text-decoration: none;color:white">
            <span class="item-title">Offline</span> <span class="item-count animate-number semi-bold" data-value="{{ $offline }}" data-animation-duration="700"></span>
          </a>
          </div>
        </div>
        <div class="progress transparent progress-small no-radius m-t-20" style="width:90%">
          <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="90%" style="width: 90%;"></div>
        </div>
        <div class="description"> <span class="text-white mini-description ">Devices status of <span class="blend"> the department</span></span>
        </div>
      </div>
    </div>
  </div>


</div>
</div>
@if($sub_department_statistics)
<div class="row">
<div class="col-md-12 col-sm-12 col-xs-6  col-lg-12 ">
        <div class="tiles white">
            <div class="row">
                <div class="sales-graph-heading">
                    <div class="col-md-6 col-sm-6">
                        <h4><span class="item-count animate-number semi-bold">Sub Departments Attendance Summary</span> </h4>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <table class="table table-responsive no-more-tables m-t-20 m-l-20 m-b-30">
                <thead >
                <tr>
                    <th style="width:25%">Department/Wing Name</th>
                    <th style="width:15%">Total Employees</th>
                    <th style="width:15%">Present</th>
                    <th style="width:15%">Late</th>
                    <th style="width:15%">Leave</th>
                    <th style="width:15%">Attach</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($sub_department_statistics as $key=>$s_data)
                      <tr>
                        <th>{{ $key }}</th>
                        <th>{{ $s_data['total'] }}</th>
                        <th>{{ $s_data['present'] }}</th>
                        <th>{{ $s_data['late'] }}</th>
                        <th>{{ $s_data['leave'] }}</th>
                        <th>{{ $s_data['attach'] }}</th>
                      </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="row">
  <div class="col-md-6 col-xs-12 col-sm-12 col-lg-12">
    <div class="card">
     <!--  <button id="btn2" class="btn-primary"><i class="icon-long-arrow-right"></i></button> -->
     <div id="averagechart" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
   </div>
 </div>
</div>
<div class="row" style="margin-top:10px">
    <!-- <div class="col-md-6 col-sm-6 col-xs-6  col-lg-6">
    <div class="card">
    <button id="btn2" class="btn-primary"><i class="icon-long-arrow-right"></i></button>
     <div id="gendergraph" style="min-width: 310px; height: 400px; margin: 0 auto">
     </div>
   </div>

 </div> -->

   <div class="col-md-6 col-sm-6 col-xs-6  col-lg-6">
    <div class="card">
     <div id="pie"></div>
    </div>
   </div>
    <div class="col-md-6 col-sm-6 col-xs-6  col-lg-6 ">

        <div class="tiles white">
            <div class="row">
                <div class="sales-graph-heading">
                    <div class="col-md-3 col-sm-3">
                        <h5 class="no-margin">Online</h5>
                        <h4><span class="item-count animate-number semi-bold" >{{ $online }}</span> </h4>
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <p class="semi-bold">Offline</p>
                        <h4><span class="item-count animate-number semi-bold" >{{ $offline }}</span></h4>
                    </div>
                    <div class="col-md-4 col-sm-3">
                        <p class="semi-bold">Total</p>
                        <h4><span class="item-count animate-number semi-bold" >{{ $online+$offline }}</span></h4>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <h5 class="semi-bold m-t-30 m-l-30">Devices</h5>
            <table class="table table-responsive no-more-tables m-t-20 m-l-20 m-b-30">
                <thead style="display:none">
                <tr>
                    <th style="width:9%">Project Name</th>
                    <th style="width:22%">Description</th>
                    <th style="width:22%">Description</th>
                    <th style="width:6%">Price</th>
                    <th style="width:1%"> </th>
                </tr>
                </thead>
                <tbody>
                @foreach($online_offline_devices as $device)
                <tr>
                    <td class="v-align-middle bold text-success">{{ $loop->iteration }}</td>
                    <td class="v-align-middle"><span class="muted">{{ $device->description }}</span> </td>
                    <td class="v-align-middle"><span class="muted">{{ $device->serialno }}</span> </td>
                    @if($device->online_status==1)
                    <td><span class="badge badge-success"> Online</span></td>
                    @else
                        <td><span class="badge badge-danger"> Offline</span></td>
                    @endif
                    <td class="v-align-middle"></td>
                </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>

  </div>
</div>
                  <script src="{{url('design/graph/highcharts.js')}}"></script>
                  <script src="{{url('design/graph/data.js')}}"></script>
                  <script src="{{url('design/graph/drilldown.js')}}"></script>
                  <!-- <script type="text/javascript" src="{{url('design/charts/highcharts.js')}}"></script>       -->
                  <!-- <script src="https://code.highcharts.com/highcharts.js"></script> -->
                  <script src="https://code.highcharts.com/modules/exporting.js"></script>
                  <!-- <script src="https://code.highcharts.com/modules/export-data.js"></script> -->
                  <script src="{{url('design/js/jquery-1.8.2.min.js')}}"></script>
                  <script type="text/javascript">
                    $(document).ready(function(){
                      var day_1={{$absent_weekday1}};
                      var day_2={{$absent_weekday2}};
                      var day_3={{$absent_weekday3}};
                      var day_4={{$absent_weekday4}};
                      var day_5={{$absent_weekday5}};
                      var day_6={{$absent_weekday6}};


                      var pre_day_1={{$pre_weekday1}};

                      var pre_day_2={{$pre_weekday2}};
                      var pre_day_3={{$pre_weekday3}};
                      var pre_day_4={{$pre_weekday4}};
                      var pre_day_5={{$pre_weekday5}};
                      var pre_day_6={{$pre_weekday6}};

                      var late_day_1={{$late_weekday1}};
                      var late_day_2={{$late_weekday2}};
                      var late_day_3={{$late_weekday3}};
                      var late_day_4={{$late_weekday4}};
                      var late_day_5={{$late_weekday5}};
                      var late_day_6={{$late_weekday6}};


                      var attach_day_1={{$attach_weekday1}};
                      var attach_day_2={{$attach_weekday2}};
                      var attach_day_3={{$attach_weekday3}};
                      var attach_day_4={{$attach_weekday4}};;
                      var attach_day_5={{$attach_weekday5}};
                      var attach_day_6={{$attach_weekday6}};

                      var leave_day_1={{$leave_weekday1}};
                      var leave_day_2={{$leave_weekday2}};
                      var leave_day_3={{$leave_weekday3}};
                      var leave_day_4={{$leave_weekday4}};
                      var leave_day_5={{$leave_weekday5}};
                      var leave_day_6={{$leave_weekday6}};

                      var total_absent_in_week=12;
                      var total_late_in_week=20;
                      var total_present_in_week = 20 + 30;
                      Highcharts.chart('averagechart', {
                        title: {
                          text: 'Weekly Attendance Status'
                        },
                        xAxis: {
                          categories: ['Monday', 'Tuesday', 'Wednesday','Thursday','Friday','Saturday']
                        },
                        labels: {
                          items: [{
                            html: 'This Week',
                            style: {
                              left: '50px',
                              top: '18px',
                              color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                            }
                          }]
                        },
                        series: [{
                          type: 'column',
                          name: 'Present',
                          data: [pre_day_1,pre_day_2,pre_day_3,pre_day_4,pre_day_5,pre_day_6]
                        },
                        {
                          type: 'column',
                          name: 'Absent',
                          data: [day_1, day_2,day_3,day_4,day_5,day_6]
                        },
                        {
                          type: 'column',
                          name: 'Late',
                          data: [late_day_1,late_day_2,late_day_3,late_day_4,late_day_5,late_day_6]
                        },
                        {
                          type: 'column',
                          name: 'Leave',
                          data: [leave_day_1, leave_day_2,leave_day_3,leave_day_4,leave_day_5,leave_day_6]
                        }, {
                          type: 'column',
                          name: 'Attach',
                          data: [attach_day_1, attach_day_2,attach_day_3,attach_day_4,attach_day_5,attach_day_6]
                        }, {
                          type: 'spline',
                          name: 'Average',
                          data: [3, 2.67, 3, 6.33, 3.33],
                          marker: {
                            lineWidth: 2,
                            lineColor: Highcharts.getOptions().colors[3],
                            fillColor: 'white'
                          }
                        }, {
                          type: 'pie',
                          name: 'Weekly Status',
                          data: [{
                            name: 'Late',
                            y:total_late_in_week,
                            color: Highcharts.getOptions().colors[2] // Jane's color
                          }, {
                            name: 'Absent',
                            y: total_absent_in_week,
                            color: Highcharts.getOptions().colors[1] // John's color
                          }, {
                            name: 'Present',
                            y: total_present_in_week,
                            color: Highcharts.getOptions().colors[0] // Joe's color
                          }],
                          center: [100, 80],
                          size: 100,
                          showInLegend: false,
                          dataLabels: {
                            enabled: false
                          }
                        }]
                      });
                    });
                  </script>
                  <script type="text/javascript">
                    var total_attach ={{$daily_attach}};
                    var total_leaves={{$daily_leave}};;
                    var present={{$daily_present}};

                    var absent={{$total_absent}};
                    var late={{$daily_late}};

                    // Create the chart
                    Highcharts.chart('pie', {
                      chart: {
                        type: 'pie'
                      },
                      title: {
                        text: 'Daily Wise Attendance'
                      },
                      plotOptions: {
                        series: {
                          dataLabels: {
                            enabled: true,
                            format: '{point.name}: {point.y:.1f}'
                          }
                        }
                      },

                      tooltip: {
                        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b> of total<br/>'
                      },

                      series: [
                      {
                        name: "Attendance",
                        colorByPoint: true,
                        data: [
                        {
                          name: "Present",
                          y: present,
                          drilldown: "Present",

                        },
                        {
                          name: "Absent",
                          y: absent,
                          drilldown: "Absent"
                        },
                        {
                          name: "Leave",
                          y: total_leaves,
                          drilldown: "Leave"
                        },
                        {
                          name: "Attach",
                          y: total_attach,
                          drilldown: "Attach"
                        },
                        {
                          name: "Late",
                          y: late,
                          drilldown: "Late"
                        },

                        ]
                      }
                      ],
                      drilldown: {
                        series: [
                        {
                          name: "Present",
                          id: "Present",
                          data: [{
                            y: present,
                            color: '#9CE7F9'
                          },
                          ]
                        },
                        {
                          name: "Absent",
                          id: "Absent",
                          data: [{
                            y: absent,
                            color: 'black'
                          },
                          ]
                        },
                        {
                          name: "Leave",
                          id: "Leave",
                          data: [{
                            y:total_leaves ,
                            color: '#C9FF93'
                          },
                          ]
                        },
                        {
                          name: "Attach",
                          id: "Attach",
                          data: [{
                            y: total_attach,
                            color: 'orange'
                          },
                          ]
                        },
                        {
                          name: "Late",
                          id: "Late",
                          data: [{
                            y: late,
                            color: 'blue'
                          },
                          ]
                        },


                        ]
                      }
                    });
                  </script>


                  @endsection
