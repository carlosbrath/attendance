 @extends('layout.layout')
 @section('title','Dashboard')
 @section('breadcumb_list','Dashboard')
 @section('content')
    <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
  @include('include.breadcrumb')
     <div class="page-content container-fluid">
                <!-- ============================================================== -->
                <!-- Card Group  -->
                <!-- ============================================================== -->
                <div class="card-group">
                    <div class="card p-2 p-lg-3">
                        <div class="p-lg-3 p-2">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-circle btn-danger text-white btn-lg" href="javascript:void(0)">
                                <i class="ti-clipboard"></i>
                            </button>
                                <div class="ml-4" style="width: 38%;">
                                    <h4 class="font-light">Employees</h4>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="40"></div>
                                    </div>
                                </div>
                                <div class="ml-auto">
                                    <h2 class="display-7 mb-0">{{$user}}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card p-2 p-lg-3">
                        <div class="p-lg-3 p-2">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-circle btn-cyan text-white btn-lg" href="javascript:void(0)">
                                <i class="ti-wallet"></i>
                            </button>
                                <div class="ml-4" style="width: 38%;">
                                    <h4 class="font-light">Department</h4>
                                    <div class="progress">
                                        <div class="progress-bar bg-cyan" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="40"></div>
                                    </div>
                                </div>
                                <div class="ml-auto">
                                    <h2 class="display-7 mb-0">{{$department}}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card p-2 p-lg-3">
                        <div class="p-lg-3 p-2">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-circle btn-warning text-white btn-lg" href="javascript:void(0)">
                                <i class="fas fa-dollar-sign"></i>
                            </button>
                                <div class="ml-4" style="width: 38%;">
                                    <h4 class="font-light">Designation</h4>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="40"></div>
                                    </div>
                                </div>
                                <div class="ml-auto">
                                    <h2 class="display-7 mb-0">{{$designation}}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- Products yearly sales, Weather Cards Section  -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="card">
                         <!--  <button id="btn2" class="btn-primary"><i class="icon-long-arrow-right"></i></button> -->
                        <div id="averagechart" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="card">
                           <div id="pie" style="min-width: 310px; max-width: 600px; height: 400px; margin: 0 auto"></div>
                           
                        </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="card">
                         <!--  <button id="btn2" class="btn-primary"><i class="icon-long-arrow-right"></i></button> -->
                      <div id="gendergraph" style="min-width: 310px; height: 400px; margin: 0 auto">
                        </div>
                    </div>
                  
                </div>
                 
               <div class="col-md-6 col-lg-6">
                        <div class="card">
                          <div id="leavestatus" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                           
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
    var day_1={{$first_day_of_weekly_graph_absent}};
   var day_2={{$second_day_of_weekly_graph_absent}};
   var day_3={{$third_day_of_weekly_graph_absent}};
   var day_4={{$fourth_day_of_weekly_graph_absent}};
   var day_5={{$fifth_day_of_weekly_graph_absent}};
   var day_6={{$sixth_day_of_weekly_graph_absent}};


   var pre_day_1={{$first_day_of_weekly_graph[0]->present}};
   var pre_day_2={{$second_day_of_weekly_graph[0]->present}};
   var pre_day_3={{$third_day_of_weekly_graph[0]->present}};
   var pre_day_4={{$fourth_day_of_weekly_graph[0]->present}};
   var pre_day_5={{$fifth_day_of_weekly_graph[0]->present}};
   var pre_day_6={{$sixth_day_of_weekly_graph[0]->present}};

   var late_day_1={{$first_day_of_weekly_graph[0]->late}};
   var late_day_2={{$second_day_of_weekly_graph[0]->late}};
   var late_day_3={{$third_day_of_weekly_graph[0]->late}};
   var late_day_4={{$fourth_day_of_weekly_graph[0]->late}};
   var late_day_5={{$fifth_day_of_weekly_graph[0]->late}};
   var late_day_6={{$sixth_day_of_weekly_graph[0]->late}};
   
  
   var attach_day_1=0;
   var attach_day_2=0;
   var attach_day_3=0;
   var attach_day_4=0;
   var attach_day_5=0;
   var attach_day_6=0;

   var leave_day_1={{$first_day_of_weekly_graph_leave[0]->leave_title}};
   var leave_day_2={{$second_day_of_weekly_graph_leave[0]->leave_title}};
   var leave_day_3={{$third_day_of_weekly_graph_leave[0]->leave_title}};
   var leave_day_4={{$fourth_day_of_weekly_graph_leave[0]->leave_title}};
   var leave_day_5={{$fifth_day_of_weekly_graph_leave[0]->leave_title}};
   var leave_day_6={{$sixth_day_of_weekly_graph_leave[0]->leave_title}};
   
   var total_absent_in_week={{$total_absent_in_week}};
   var total_late_in_week={{$total_late_in_week}};
   var total_present_in_week = {{$total_present_in_week}};
   Highcharts.chart('averagechart', {
    title: {
      text: 'Weekly Attendance Status'
    },
    xAxis: {
      categories: ['Monday', 'Tuesday', 'Weddnesday','Thursday','Friday','Saturday']
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
  var total_attach=0;
  var total_leaves={{$daily_leave_count[0]->leave_title}};  
  var present={{$daily_present_count[0]->present}};
  var absent={{$daily_absent_count}};
  var late={{$daily_late_count[0]->late}};
  var attach_emp={{$attach_employee}};
  // Create the chart
Highcharts.chart('pie', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'Attendance Status Day Wise'
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
<script type="text/javascript">
  
  Highcharts.chart('leavestatus', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Leaves in Departments'
    },
    xAxis: {
        categories:

      'pda'

    },
    
    yAxis: {
        min: 0,
        title: {
            text: 'leaves'
        }
    },
    tooltip: {
        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
        shared: true
    },
    plotOptions: {
        column: {
            stacking: 'percent'
        }
    },
    series: [{
        name: 'Sick Leave',
        data: [5, 3, 4, 7, 2]
    }, {
        name: 'Casual Leave',
        data: [2, 2, 3, 2, 1]
    }, {
        name: 'EX-Pakistan Leave',
        data: [3, 4, 4, 2, 5]
    }]
});

</script>

<?php 

$depts=json_encode($gender_data->pluck('dept_names'));
$male=json_encode($gender_data->pluck('male'));
$female=json_encode($gender_data->pluck('female'));
  

  ?>
<script type="text/javascript">
  Highcharts.chart('gendergraph', {

    chart: {
        type: 'column'
    },

    title: {
        text: 'Total Gender in Departments'
    },

      xAxis: {
        categories:   <?php echo $depts ?> 
    },

    yAxis: {
        allowDecimals: false,
        min: 0,
        title: {
            text: 'Gender in Departments'
        }
    },

    tooltip: {
        formatter: function () {
            return '<b>' + this.x + '</b><br/>' +
                this.series.name + ': ' + this.y + '<br/>' +
                'Total: ' + this.point.stackTotal;
        }
    },

    plotOptions: {
        column: {
            stacking: 'normal'
        }
    },

    series: [{
        name: 'Male',
        data:   <?php echo $male ?> ,
        stack: 'male'
    }, {
        name: 'Female',
        data:   <?php echo $female ?> ,
        stack: 'male'
    }]
});
</script>
     @endsection        