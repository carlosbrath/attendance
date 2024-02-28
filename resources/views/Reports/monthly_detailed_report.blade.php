@extends('layout.layout')
@section('title',' Monthly Attendance Report')
@section('content')
@include('Reports.print_header_monthly_detailed_report')
<style type="text/css">
        .form-actions {
        background-color: #f6f7f8;;
        border: 0px;
        margin-bottom: 20px;
        margin-top: 0px;
        padding: 19px 20px 9px;
        }
        .grid {
        clear: both;
        margin-top: 0px;
        margin-bottom: 0px;
        padding: 0px;
        }
       .monthly_repor span{
          font-size:10px;
        }

        #monthlyreport th{
          font-size: 8px !important;
        }
        #monthlyreport td{
          font-size: 8px !important;
          padding-left:0px !important;
          padding-right:0px !important; 
         
          padding-bottom:0px !important; 
          width: 1px !important;
        }
         #monthlyreport span{
          font-size: 8px !important;
        }



        .table-striped > tbody > tr:nth-child(odd) > td, 
.table-striped > tbody > tr:nth-child(odd) > th {
  white-space: nowrap;
}
.portal-drivers {
  overflow: auto;
}

}

        

</style>
<div class="row" id="form_content">
  <div class="col-md-12">
    <div class="grid simple form-grid">
      <div class="grid-body no-border">
        <form class="form-no-horizontal-spacing" action="{{route('monthly_det_rep')}}" 
             method="post" id="form-condensed" novalidate="novalidate">
          @csrf
          <div class="row column-seperation">
            <div class="col-md-12">
              <h4></h4>
                <div class="row form-row">
                     
                  <div class="col-md-8">
                      <h4></h4>
                      <div class="form-group">
                        <label class="form-label"><strong>Departments</strong></label>
                        <div class=" right">
                        <i class=""></i>
                        <select name="department_id" id="department_id" style="width:367px"  class="select2 form-control">
                         <option  value="all">All</option>
                        @foreach($department as $key=>$value)
                        <option @if($key == $choosen_department_id) selected @endif  value="{{$key}}">{{$value}}</option>
                        @endforeach
                        
                        </select>
                        @if($errors->any())
                        <p style="color:red">{{$errors->first('deparment_id','Department field is required')}}
                        </p>
                        @endif

                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label"><strong>OR Specify IDs Range</strong></label>
                        <div class=" right">
                        <i class=""></i>
                        <input type="text" placeholder="ID From" name="id_from" />
                        <input type="text" placeholder="ID To" name="id_to" />
                        </div>
                      </div>
                  </div>
                 <div class="col-md-12">
                  <label class="form-label"  style="font-size:15px ">From Date</label>
                  <div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
                    <input type="text" autocomplete="off" value="{{ date('Y/m/01') }}" name="from_date" placeholder="From Date" class="form-control">
                     
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                    
                  </div>
                
                </div>

                <div class="col-md-12">
                  <label class="form-label"  style="font-size:15px ">To Date</label>
                  <div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
                    <input type="text" autocomplete="off" value="{{ date('Y/m/d') }}" name="to_date" placeholder="From Date" class="form-control">
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                  </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label"  style="font-size:15px ">Report Type</label>
                        <div class="radio">
                            <input id="detailed" @if($report_type=="detailed") checked @endif  type="radio" name="report_type" value="detailed" >
                            <label for="detailed">Detailed</label>
                            <input id="summary" @if($report_type=="summary") checked @endif type="radio"  name="report_type" value="summary">
                            <label for="summary">Summary</label>
        
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label"  style="font-size:15px ">Status</label>
                        <div class="radio">
                            <input id="all" @if($status=="all") checked @endif type="radio"  name="status" value="all">
                            <label for="all">All</label>
                            <input id="present" @if($status=="present") checked @endif type="radio" name="status" value="present" >
                            <label for="present">Present</label>
                            <input id="absent" @if($status=="absent") checked @endif type="radio" name="status" value="absent">
                            <label for="absent">Absent</label>
                            <input id="late" @if($status=="late") checked @endif type="radio" name="status" value="late">
                            <label for="late">Late</label>
                        </div>
                    </div>
                </div>
              </div>
              <div class="form-actions">
                <button class="btn btn-success" name="search" type="submit">Generate Report</button>
              </div>
              </form>
            </div>
          </div>
         
      </div>
    </div>
  </div>
</div>
<span style="clear: both;"></span>

<div class="row monthlyreport" style="margin-top: 29px;">

 @if(session('message'))
 <div class="alert alert-success" style="text-align: center;">{{session('message')}}
 </div>
 @endif
 @if(session('error'))
 <div class="alert alert-danger" style="text-align: center;">{{session('error')}}
 </div>
 @endif


<h4><b></b></h4>
<div class="pull-right">
      <button class="btn-cogs" style="margin-top:2px" id="monthly_report_print" ><i class="icon-ok"></i>Print</button>
      <button class="btn-cogs" style="margin-top:2px" onclick="tableToExcel('monthlyreport', 'Monthly Attendance')" value="Export to Excel" ><i class="icon-ok"></i>Export to Excel</button>                
</div>
  @if(isset($users))
  <div class="col-md-12">
    @if($report_type == "detailed")
    <div class="grid simple ">

      <div class="grid-body table-responsive">
       <!--  <div class="col-md-12"><button id="btn_print" class="btn btn-success">Print</button></div> -->
       <div class="table-responsive">

        <table id="monthlyreport"  class="monthly_repor table dataTable display nowrap table-responsive table-bordered portal-drivers" aria-describedby="example3_info" style="width:100%">
            <thead>
               <tr>
                <th width="4%">User ID</th>
                <th width="8%">Name</th>
                <th width="3%">IN/OUT</th>
                @php
                    $begin = new DateTime($date_from);
                    $end   = new DateTime($date_to);

                    for($i = $begin; $i <= $end; $i->modify('+1 day')){
                        echo "<th>".$i->format("d")."</th>";
                    }
                @endphp
                <th width="3%">Present</th>
                <th width="3%">OFF Day</th>
                <th width="3%">Late</th>
                <th width="3%">Absent</th>
                <th width="3%">Leave</th>
                <th width="3%">Attach</th>
              </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                  <tr>
                      <th rowspan="3">{{ $user->id }}</th>
                      <th rowspan="3">{{ $user->name }}</th>
                  </tr>
                  <tr>
                      <th>IN</th>
                      @php
                          $begin = new DateTime($date_from);
                          $end   = new DateTime($date_to);
                          $present_total = 0;
                          $absent_total = 0;
                          $leave_total = 0;
                          $off_total = 0;
                          $attach_total = 0;
                          $late_total = 0;
                          for($i = $begin; $i <= $end; $i->modify('+1 day')){
                            $array_index = $user->id."-".date("ymd",strtotime($i->format('Y-m-d')));
                            $att = $attendance[$array_index];
                            $holiday = $holidays->where('holiday_date',$i->format('Y-m-d'))->where('department_id',$user->department_id);
                            if($att['status'] == "present"){
                              if($status == "present" || $status == "all"){
                                if(count($holiday)>0){
                                  echo "<td>".date("H:i:s",strtotime($att['in']))."-".$holiday->first()->title."</td>";
                                }else{
                                  if($att['manual'] == 1){
                                    echo "<td>".date("H:i:s",strtotime($att['in']))." <span style='color:red'>*</span></td>";
                                  }else{
                                    echo "<td>".date("H:i:s",strtotime($att['in']))."</td>";
                                  }
                                }
                                $present_total = $present_total + 1;
                              }else{
                                echo "<td>--</td>";
                              }
                            }elseif($att['status'] == "leave"){

                              if($status == "leave" || $status == "all"){
                                echo "<td>L</td>";
                                $leave_total = $leave_total + 1;
                              }else{
                                echo "<td>--</td>";
                              }
                            }elseif($att['status'] == "late"){
                              if($status == "late" || $status == "all"){
                                if($att['manual'] == 1){
                                  echo "<td>".date("H:i:s",strtotime($att['in']))." <span style='color:red'>*</span></td>";
                                }else{
                                  echo "<td style='background-color: red'>".date("H:i:s",strtotime($att['in']));
                                }
                                $late_total = $late_total + 1;
                              }else{
                                echo "<td>--</td>";
                              }
                            }elseif($att['status'] == "attach"){
                              if($status == "attach" || $status == "all"){
                                echo "<td>Att</td>";
                                $attach_total = $attach_total + 1;
                              }else{
                                echo "<td>--</td>"; 
                              }
                            }elseif($att['status'] == "sd-attach"){
                              if($status == "sd-attach" || $status == "all"){
                                echo "<td>S-Att</td>";
                                $attach_total = $attach_total + 1;
                              }else{
                                echo "<td>--</td>"; 
                              }
                            }elseif($att['status'] == "offday"){
                                echo "<td>OFF</td>";
                                $off_total = $off_total + 1;
                            }elseif($att['status'] == "absent"){

                              if($status == "absent" || $status == "all"){
                              if(count($holiday)>0){
                                  echo "<td>".$holiday->first()->title."</td>";
                              }else{
                                echo "<td>A</td>";
                                $absent_total = $absent_total + 1;
                              }
                            }else{
                              echo "<td>--</td>";
                            }
                            } 
                          }
                      @endphp
                      <th rowspan="2">{{ $present_total }}</th>
                      <th rowspan="2">{{ $off_total }}</th>
                      <th rowspan="2">{{ $late_total }}</th>
                      <th rowspan="2">{{ $absent_total }}</th>
                      <th rowspan="2">{{ $leave_total }}</th>
                      <th rowspan="2">{{ $attach_total }}</th>
                  </tr>
                  <tr>
                      <th>OUT</th>
                      @php
                          $begin = new DateTime($date_from);
                          $end   = new DateTime($date_to);

                          for($i = $begin; $i <= $end; $i->modify('+1 day')){
                            $array_index = $user->id."-".date("ymd",strtotime($i->format('Y-m-d')));
                            $att = $attendance[$array_index];
                            $holiday = $holidays->where('holiday_date',$i->format('Y-m-d'))->where('department_id',$user->department_id);

                            if($att['status'] == "present"){
                              if($status == "present" || $status == "all"){
                                echo "<td>".date("H:i:s",strtotime($att['out']))."</td>";
                              }else{
                                echo "<td>--</td>";
                              }
                            }elseif($att['status'] == "late"){
                              if($status == "late" || $status == "all"){
                                echo "<td>".date("H:i:s",strtotime($att['out']))."</td>";
                              }else{
                                 echo "<td>--</td>";
                              }
                            }elseif($att['status'] == "leave"){
                              if($status == "late" || $status == "all"){
                                echo "<td>L</td>";
                              }else{
                                echo "<td>--</td>";
                              }
                            }elseif($att['status'] == "attach"){
                              if($status == "late" || $status == "all"){
                                echo "<td>Att</td>";
                              }else{
                                echo "<td>--</td>";
                              }
                            }elseif($att['status'] == "sd-attach"){
                              if($status == "late" || $status == "all"){
                                echo "<td>S-Att</td>";
                              }else{
                                echo "<td>--</td>";
                              }
                            }elseif($att['status'] == "offday"){
                                echo "<td>OFF</td>";
                            }elseif($att['status'] == "absent"){

                              if($status == "late" || $status == "all"){
                              if(count($holiday)>0){
                                  echo "<td>".$holiday->first()->title."</td>";
                              }else{
                                  echo "<td>A</td>";
                              }
                            }else{
                              echo "<td>--</td>";
                            }
                            }
                          }
                      @endphp
                  </tr>
                @endforeach
            </tbody>
         </table>
        </div>
        </div>
      </div>
    @else

    <div class="grid simple ">
      <div class="grid-body table-responsive">
       <!--  <div class="col-md-12"><button id="btn_print" class="btn btn-success">Print</button></div> -->
       <div class="table-responsive">
        <table id="monthlyreport"  class="monthly_repor table dataTable display nowrap table-responsive table-bordered portal-drivers" aria-describedby="example3_info" style="width:100%">
            <thead>
               <tr>
                <th width="4%">User ID</th>
                <th width="8%">Name</th>
                <th width="3%">Present</th>
                <th width="3%">OFF Day</th>
                <th width="3%">Late</th>
                <th width="3%">Absent</th>
                <th width="3%">Leave</th>
                <th width="3%">Attach</th>
              </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                  <tr>
                      <th >{{ $user->id }}</th>
                      <th >{{ $user->name }}</th>
                      @php
                          $begin = new DateTime($date_from);
                          $end   = new DateTime($date_to);
                          $present_total = 0;
                          $absent_total = 0;
                          $leave_total = 0;
                          $off_total = 0;
                          $attach_total = 0;
                          $late_total = 0;
                          for($i = $begin; $i <= $end; $i->modify('+1 day')){
                            $array_index = $user->id."-".date("ymd",strtotime($i->format('Y-m-d')));
                            $att = $attendance[$array_index];
                            $holiday = $holidays->where('holiday_date',$i->format('Y-m-d'))->where('department_id',$user->department_id);
                            if($att['status'] == "present"){
                              if($status == "present" || $status == "all"){
                                $present_total = $present_total + 1;
                              }
                            }elseif($att['status'] == "leave"){

                              if($status == "leave" || $status == "all"){
                                $leave_total = $leave_total + 1;
                              }
                            }elseif($att['status'] == "late"){
                              if($status == "late" || $status == "all"){
                                $late_total = $late_total + 1;
                              }
                            }elseif($att['status'] == "sd-attach"){
                              if($status == "sd-attach" || $status == "all"){
                                $attach_total = $attach_total + 1;
                              }
                            }elseif($att['status'] == "attach"){
                              if($status == "attach" || $status == "all"){
                                $attach_total = $attach_total + 1;
                              }
                            }elseif($att['status'] == "offday"){
                                $off_total = $off_total + 1;
                            }elseif($att['status'] == "absent"){

                              if($status == "absent" || $status == "all"){
                              if(count($holiday)>0){
                                  
                              }else{
                                $absent_total = $absent_total + 1;
                              }
                            }
                            } 
                          }
                      @endphp
                      <th >{{ $present_total }}</th>
                      <th >{{ $off_total }}</th>
                      <th >{{ $late_total }}</th>
                      <th >{{ $absent_total }}</th>
                      <th >{{ $leave_total }}</th>
                      <th >{{ $attach_total }}</th>
                  </tr>
                @endforeach
            </tbody>
         </table>
        </div>
        </div>
      </div>
    @endif
     </div>
    </div>
   </div>
   @endif
</div>         
@include('include.important')
@endsection

<script type="text/javascript">
var tableToExcel = (function() {
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
  return function(table, name) {
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    window.location.href = uri + base64(format(template, ctx))
  }
})()
</script>