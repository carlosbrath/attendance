@extends('layout.layout')
@section('title',' Monthly Status Report')
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
        td 
        {
          vertical-align: bottom;
          text-align: center;
        }

        td span 
        {
          -ms-writing-mode: tb-rl;
          -webkit-writing-mode: vertical-rl;
          writing-mode: vertical-rl;
          transform: rotate(180deg);
          white-space: nowrap;
          text-align: center;
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
        <form class="form-no-horizontal-spacing" action="{{route('monthly_payroll_report')}}" 
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
              </div>
              <div class="form-actions">
                <button class="btn btn-success" name="search" value="search" type="submit">Generate Report</button>
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
  <div class="col-md-12">
    <div class="grid simple ">

      <div class="grid-body table-responsive">
       <!--  <div class="col-md-12"><button id="btn_print" class="btn btn-success">Print</button></div> -->
       <div class="table-responsive">

        <table id="monthlyreport"  class="monthly_repor table dataTable display nowrap table-responsive table-bordered portal-drivers" aria-describedby="example3_info" style="width:100%">
          @php 
              $interval_days =  (date_diff(date_create($date_to),date_create($date_from))->d)+11;
          @endphp
            <thead>
               <tr>
                <th width="4%" class="text-center">User ID</th>
                <th width="8%" class="text-center">Name</th>
                @php
                    $begin = new DateTime($date_from);
                    $end   = new DateTime($date_to);
                    for($i = $begin; $i <= $end; $i->modify('+1 day')){
                        echo "<th>".$i->format("d")."</th>";
                    }
                @endphp
                <th width="3%">Salary</th>
                <th width="3%">Total Pay</th>
                <th width="3%">OT</th>
              </tr>
            </thead>
            <tbody>
                @foreach($daytime_attendance as $dt_attendance)
                <tr>
                  <td   class="text-center">{{ $dt_attendance->user_id }}</td>
                  <td   class="text-center">
                            {{ $dt_attendance->name }} <br/>
                            <b>{{ $dt_attendance->designation }}</b> <br/>
                            @php 
                              if($dt_attendance->is_night_shift==1) 
                                echo "(Night Shift)";
                              else 
                                echo "(Day Shift)";
                            @endphp
                  </td>
                  @php
                    $begin = new DateTime($date_from);
                    $end   = new DateTime($date_to);
                    $is_night_shift = $dt_attendance->is_night_shift;

                    $present = 0;
                    $total_pay = 0;
                    $overtime_pay = 0;
                    $attach_pay = 0;
                    $leave_pay = 0;
                    $absent = 0;
                    $leave = 0;
                    $attach = 0;
                    $holiday = 0;
                    $offday = 0;
                    $late = 0;
                    $user_trxs_arr = json_decode($dt_attendance->att_transactions);

                    for($i = $begin; $i <= $end; $i->modify('+1 day')){
                      $today = $i->format("Y-m-d");
                      $yesterday = date('Y-m-d',strtotime($today . "-1 days"));

                      $tcat_day_check = "tc_".strtolower(date('l', strtotime($today)))."_in";
                      $tcat_day_out_check = "tc_".strtolower(date('l', strtotime($today)))."_out";
                      if($tcat_day_check == 'tc_saturday_in'){
                          $tcat_day_check = "tc_satureday_in";
                      }
                      if($tcat_day_check == 'tc_thursday_in'){
                          $tcat_day_check = "tc_thursday_in";
                      }

                      if($tcat_day_out_check == 'tc_saturday_out'){
                          $tcat_day_out_check = "tc_satureday_out";
                      }
                      if($tcat_day_out_check == 'tc_thursday_out'){
                          $tcat_day_out_check = "tc_thursday_out";
                      }


                      $tcat_yesterday_check = "tc_".strtolower(date('l', strtotime($yesterday)))."_in";
                      if($tcat_yesterday_check == 'tc_saturday_in'){
                          $tcat_yesterday_check = "tc_satureday_in";
                      }
                      if($tcat_yesterday_check == 'tc_thursday_in'){
                          $tcat_yesterday_check = "tc_thursday_in";
                      }

                      $total_trxs = 0;
                      if(!is_null($user_trxs_arr) && array_key_exists($today,$user_trxs_arr)){
                        $total_trxs = count($user_trxs_arr->$today);
                        $transactions = $user_trxs_arr->$today;
                      }else{
                        $transactions = [];
                      }
                      $yesterday_total_trxs = 0;
                      if($is_night_shift == 1){
                        if(!is_null($user_trxs_arr) && array_key_exists($yesterday,$user_trxs_arr)){
                          $yesterday_total_trxs = count($user_trxs_arr->$yesterday);
                          $yesterday_transactions = $user_trxs_arr->$yesterday;
                        }
                      }
                      if($is_night_shift == 1){

                        if($yesterday_total_trxs == 0){
                          //check if the user is on off day or not
                          if($dt_attendance->$tcat_yesterday_check == ""){
                            $offday+=1;
                            echo "<td  class='info text-center'><span class='text-center'>OFFDAY</span></td>";  
                          }else{
                            //check in leaves
                            $today_leaves = $leaves->where('user_id',$dt_attendance->user_id)->where('leave_date',$yesterday);
                            $today_attachs = $attachs->where('user_id',$dt_attendance->user_id)->where('attach_date',$yesterday);
                            if(count($today_leaves)>0){
                              $leave+=1;
                              echo "<td class='text-center' >L</td>";    
                            }else if(count($today_attachs)>0){
                              $attach+=1;


                            }else{
                              $absent+=1;
                              echo "<td  class='danger text-center' >A</td>";  
                            }
                            
                          }
                        }else{
                          $tcat_timein = $yesterday." ".$dt_attendance->$tcat_day_check;
                          $tcat_timeout = $today." ".$dt_attendance->$tcat_day_out_check;
                          $tcat_timein = $yesterday." ".$dt_attendance->$tcat_yesterday_check;
                          $tcat_gracetime = date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime($tcat_timein)));
                          echo "<td class='text-center'>";
                          $present+=1;
                          echo "IN:".date("H:i:s",strtotime(count($transactions)-1))."<br/>";  
                      
                          if($yesterday_total_trxs>0){
                            if($yesterday_total_trxs == 1){
                              echo "OUT:MC <br/>";
                            }else{
                              echo "OUT:".date("H:i:s",strtotime($yesterday_transactions[0]));
                            }
                          }
                          echo "</td>";
                          
                        }
                      }else{
                          $tcat_timein = $yesterday." ".$dt_attendance->$tcat_day_check;
                          $tcat_timeout = $today." ".$dt_attendance->$tcat_day_out_check;
                          $today_leaves = $leaves->where('user_id',$dt_attendance->user_id)->where('leave_date',$today);
                          $today_attachs = $attachs->where('user_id',$dt_attendance->user_id)->where('attach_date',$today);

                          if(count($today_attachs)){
                            $today_attachs = collect($today_attachs);
                            $attach_data = $today_attachs->first();
                            if($attach_data->time_from !="" || $attach_data->time_to !=""){
                              //means its short attach
                              $time_from = $today." ".$attach_data->time_from;
                              $time_to   = $today." ".$attach_data->time_to;                              
                              $daily_hours = (strtotime($tcat_timeout) - strtotime($tcat_timein))/(60*60);
                              $attach_hours = (strtotime($time_to) - strtotime($time_from))/(60*60);
                              
                              $month=date("n",strtotime($today));  
                              $year=date("Y",strtotime($today));  
                              $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);                              
                              $fix_salary = $dt_attendance->fixed_salary;
                              $attach_per_hour = ($fix_salary/($days))/$daily_hours;
                              $attach_pay = $attach_per_hour*$attach_hours;
                            }else{
                              //its full day attach hence calculate full day pay                              
                              $daily_hours = (strtotime($tcat_timeout) - strtotime($tcat_timein))/(60*60);
                              $month=date("n",strtotime($today));  
                              $year=date("Y",strtotime($today));  
                              $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);                              
                              $fix_salary = $dt_attendance->fixed_salary;
                              $attach_pay = $fix_salary/($days);
                            }
                          }
                          if(count($today_leaves)){
                            $today_leaves = collect($today_leaves);
                            $leaves_data = $today_leaves->first();
                            if($leaves_data->time_from !="" || $leaves_data->time_to !=""){
                              //means its short leave
                              $time_from = $today." ".$leaves_data->time_from;
                              $time_to   = $today." ".$leaves_data->time_to;                              
                              $daily_hours = (strtotime($tcat_timeout) - strtotime($tcat_timein))/(60*60);
                              $leave_hours = (strtotime($time_to) - strtotime($time_from))/(60*60);
                              
                              $month=date("n",strtotime($today));  
                              $year=date("Y",strtotime($today));  
                              $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);                              
                              $fix_salary = $dt_attendance->fixed_salary;
                              $leave_per_hour = ($fix_salary/($days))/$daily_hours;
                              $leave_pay = $leave_per_hour*$leave_per_hour;
                            }else{
                              //its full day attach hence calculate full day pay                              
                              $daily_hours = (strtotime($tcat_timeout) - strtotime($tcat_timein))/(60*60);
                              $month=date("n",strtotime($today));  
                              $year=date("Y",strtotime($today));  
                              $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);                              
                              $fix_salary = $dt_attendance->fixed_salary;
                              $leave_pay = $fix_salary/($days);

                            }
                          }

                        if($total_trxs == 0){
                          $tcat_timein = $yesterday." ".$dt_attendance->$tcat_day_check;
                          $tcat_timeout = $today." ".$dt_attendance->$tcat_day_out_check;                          
                          //check if the user is on off day or not
                          if($dt_attendance->$tcat_day_check == ""){
                            $offday+=1;
                            echo "<td  class='info text-center'><span class='text-center'>OFFDAY</span></td>";
                            //but there is possibilty for leave/attach  
                          }else{
                            //check in leaves
                            $today_leaves = $leaves->where('user_id',$dt_attendance->user_id)->where('leave_date',$today);
                            $today_attachs = $attachs->where('user_id',$dt_attendance->user_id)->where('attach_date',$today);
                            $holidays_filtered = $holidays->where('holiday_date',$today)->where('department_id',$dt_attendance->user_department_id);
                            
                            if(count($today_leaves)>0){
                              $leave+=1;
                              echo "<td  class='info text-center'><span>LEAVE<br/>";                              
                              if($attach_pay){
                                echo "A:".round($attach_pay,0)."<br/>";
                                $total_pay+=round($attach_pay,0);
                              }                 
                              if($leave_pay){
                                echo "L:".round($leave_pay,0);
                                $total_pay+=round($leave_pay,0);
                              }  
                            echo "</span></td>";
    
                            }else if(count($today_attachs)>0){
                              $attach+=1;
                              echo "<td class='text-center'>";
                              echo "ATT<br/>"; 
                                  if(count($transactions)>0){
                                    $out = date("H:i",strtotime($transactions[count($transactions)-1]));                                    
                                  }else{
                                    $out = "--";
                                  }

                                  echo "O:".$out;
                                   $month=date("n",strtotime($today));  
                                   $year=date("Y",strtotime($today));  
                                   $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                                   $daily_hours = (strtotime($tcat_timeout) - strtotime($tcat_timein))/(60*60);
                                   $hour_rate_salary = $dt_attendance->hour_rate_salary;                               
                                   $daily_pay = $daily_hours*$hour_rate_salary;
                                   $pay= $daily_pay;
                                   $pay = $pay+$attach_pay;
                                   $total_pay += $pay;
                                   echo "<br/>P:".round($pay);

                                   //now check for bonus
                                   if($emp_bonus){
                                      $check_bonus=$emp_bonus->where('date',$i->format('Y-m-d'))->where('user_id',$dt_attendance->user_id);
                                      if(count($check_bonus)>0){
                                        $bonus = $check_bonus->first()->amount;
                                        $total_pay += $bonus;
                                        echo "<br/> B:".$bonus;
                                      }

                                   }
                                   //now check for deduction
                                   if($emp_deduction){
                                      $check_deduction=$emp_deduction->where('date',$i->format('Y-m-d'))->where('user_id',$dt_attendance->user_id);
                                      if(count($check_deduction)>0){
                                        $deduction = $check_deduction->first()->amount;
                                        $total_pay = $total_pay-$deduction;
                                        echo "<br/> D:".$deduction;
                                      }

                                   }
                                
                              echo "</td>";  
                            }else if(count($holidays_filtered)>0){
                              $holiday+=1;
                              $daily_hours = (strtotime($tcat_timeout) - strtotime($tcat_timein))/(60*60);
                              $month=date("n",strtotime($today));  
                              $year=date("Y",strtotime($today));  
                              $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                              $daily_hours = (strtotime($tcat_timeout) - strtotime($tcat_timein))/(60*60);                               
                              $fix_salary = $dt_attendance->fixed_salary;
                              $daily_salary = $fix_salary/($days);
                              echo "<td class='info text-center'><span>".collect($holidays_filtered)->first()->title."<br/>(".round($daily_salary,0).")</span></td>";
                              $total_pay += $daily_salary;    
                            }else{
                              $absent+=1;
                              echo "<td  class='danger text-center' ><span>ABSENT</span></td>";  
                            }
                            
                          }
                        }else{
                          $tcat_timein = $today." ".$dt_attendance->$tcat_day_check;
                          $tcat_timeout = $today." ".$dt_attendance->$tcat_day_out_check;
                          $tcat_gracetime = date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime($tcat_timein)));                              
                          echo "<td class='text-center'>";
                          $present+=1;
                          $in = $today." ".date("H:i",strtotime($transactions[0]));
                          echo "I:".date("H:i",strtotime($transactions[0]))."<br/>";
                      
                          if(count($transactions)>0){
                            if(count($transactions) == 1){
                              echo "O:MC <br/>";                          
                              if($attach_pay){
                                echo "A:".round($attach_pay,0)."<br/>";
                                $total_pay+=round($attach_pay,0);
                              }                       
                              if($leave_pay){
                                echo "L:".round($leave_pay,0)."<br/>";
                                $total_pay+=round($leave_pay,0);
                              }  
                            }else{
                              $out = $today." ".date("H:i",strtotime($transactions[count($transactions)-1]));
                              echo "O:".date("H:i",strtotime($transactions[count($transactions)-1]));
                               $month=date("n",strtotime($today));  
                               $year=date("Y",strtotime($today));  
                               $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                               $daily_hours = (strtotime($tcat_timeout) - strtotime($tcat_timein))/(60*60);
                               //check if employee is not early out
                               if($tcat_timeout<$out){
                                  $seconds_worked = strtotime($tcat_timeout) - strtotime($in);                                
                               }else{
                                  $seconds_worked = strtotime($out) - strtotime($in);
                               }

                               $overtime_seconds_worked = strtotime($out) - strtotime($tcat_timeout);
                               $hours_worked = date('H:i:s',$seconds_worked);
                               $hour_rate_salary = $dt_attendance->hour_rate_salary;
                               $fix_salary = $dt_attendance->fixed_salary;
                               $daily_salary = $fix_salary/($days);
                               $hourly_salary = 0;
                               if($daily_hours){
                                  $hourly_salary = $daily_salary/($daily_hours); 
                               }
                               $minute_salary = $hourly_salary/60;       
                               $per_second_salary = $minute_salary/60;                               
                               $daily_pay = $per_second_salary*$seconds_worked;
                               $pay= round($daily_pay);
                               $total_pay += $pay;
                               echo "<br/>P:".$pay;
                               if($hour_rate_salary!="" && $tcat_timeout<$out){
                                //lets calculate the overtime
                                $overtime = ($overtime_seconds_worked/3600)*$hour_rate_salary;
                                echo "<br/>OT:".round($overtime,0);
                                $overtime_pay+=$overtime;
                                $total_pay += ($pay+$overtime);
                               }

                               //now check for bonus
                               if($emp_bonus){
                                  $check_bonus=$emp_bonus->where('date',$i->format('Y-m-d'))->where('user_id',$dt_attendance->user_id);
                                  if(count($check_bonus)>0){
                                    $bonus = $check_bonus->first()->amount;
                                    $total_pay += $bonus;
                                    echo "<br/> B:".$bonus;
                                  }

                               }
                               //now check for deduction
                               if($emp_deduction){
                                  $check_deduction=$emp_deduction->where('date',$i->format('Y-m-d'))->where('user_id',$dt_attendance->user_id);
                                  if(count($check_deduction)>0){
                                    $deduction = $check_deduction->first()->amount;
                                    $total_pay = $total_pay-$deduction;
                                    echo "<br/> D:".$deduction;
                                  }

                               }                       
                              if($attach_pay){
                                echo "A:".round($attach_pay,0)."<br/>";
                                $total_pay+=round($attach_pay,0);
                              }                 
                              if($leave_pay){
                                echo "L:".round($leave_pay,0)."<br/>";
                                $total_pay+=round($leave_pay,0);
                              }  
                            }
                          }
                          echo "</td>";
                          
                        }                        
                      }

                    }
                  @endphp
                  <td class="text-center" >{{ $dt_attendance->fixed_salary }}</td>
                  <td class="text-center" >{{ $total_pay }}</td>
                  <td class="text-center" >{{ $overtime_pay }}</td>
                </tr>
                @endforeach
            </tbody>
         </table>
        </div>
        </div>
      </div>

     </div>
    </div>
   </div>
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