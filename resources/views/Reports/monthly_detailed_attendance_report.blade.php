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



        .table-striped > tbody > tr:nth-child(odd) > td, 
.table-striped > tbody > tr:nth-child(odd) > th {
  white-space: nowrap;
}
.portal-drivers {
  overflow: auto;
}


</style>
<div class="row" id="form_content">
  <div class="col-md-12">
    <div class="grid simple form-grid">
      <div class="grid-body no-border">
        <form class="form-no-horizontal-spacing" action="{{route('monthly_details_attendance')}}" 
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
    @if($report_type == "detailed")
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
                <th width="1%">IN/OUT</th>
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
                <th width="3%">Holiday</th>
              </tr>
            </thead>
            <tbody>
                
                @foreach($daytime_attendance as $dt_attendance)
                <tr>
                  <td rowspan="2"  class="text-center">{{ $dt_attendance->user_id }}</td>
                  <td rowspan="2"  class="text-center">
                            {{ $dt_attendance->name }} <br/>
                            <b>{{ $dt_attendance->designation }}</b> <br/>
                            @php 
                              if($dt_attendance->is_night_shift==1) 
                                echo "(Night Shift)";
                              else 
                                echo "(Day Shift)";
                            @endphp
                  </td>
                  <td>IN</td>
                  @php
                  
                    $time_cat_history=$time_cat_history_test->where('user_id',$dt_attendance->user_id);
                   
                    $begin = new DateTime($date_from);
                    $end   = new DateTime($date_to);
                    $is_night_shift = $dt_attendance->is_night_shift;

                    $present = 0;
                    $absent = 0;
                    $leave = 0;
                    $attach = 0;
                    $holiday = 0;
                    $offday = 0;
                    $late = 0;
                    $user_trxs_arr = json_decode($dt_attendance->att_transactions);

                    for($i = $begin; $i <= $end; $i->modify('+1 day')){
                      $today = $i->format("Y-m-d");
                   
                      $yesterday = date('Y-m-d',strtotime($today . "+1 days"));
                      $tomorrow = date('Y-m-d',strtotime($today . "+1 days"));
                      $tcat_day_check = "tc_".strtolower(date('l', strtotime($today)))."_in";
                      $tcat_day_check_out = "tc_".strtolower(date('l', strtotime($today)))."_out";

                      if($tcat_day_check == 'tc_saturday_in'){
                          $tcat_day_check = "tc_saturday_in";
                          $tcat_day_check_out = "tc_saturday_out";
                      }
                      if($tcat_day_check == 'tc_thursday_in'){
                          $tcat_day_check = "tc_thursday_in";
                          $tcat_day_check_out = "tc_thursday_out";
                      }
                      $tcat_yesterday_check = "tc_".strtolower(date('l', strtotime($yesterday)))."_in";
                      $tcat_yesterday_check_out = "tc_".strtolower(date('l', strtotime($yesterday)))."_out";
                      if($tcat_yesterday_check == 'tc_saturday_in'){
                          $tcat_yesterday_check = "tc_saturday_in";
                          $tcat_yesterday_check_out = "tc_saturday_out";
                      }
                      if($tcat_yesterday_check == 'tc_thursday_in'){
                          $tcat_yesterday_check = "tc_thursday_in";
                          $tcat_yesterday_check_out = "tc_thursday_out";
                      }
                      $time_category_history_info =  monthly_attendance_status_report($time_cat_history->where('date','<=',$i->format("Y-m-d")),$dt_attendance);
                      $is_night_shift = $time_category_history_info->is_night_shift;

                      $tcat_timein = $today." ".$time_category_history_info->$tcat_yesterday_check;
                      $tcat_timeout = $yesterday." ".$time_category_history_info->$tcat_yesterday_check_out;

                      $total_trxs = 0;
                      if(!is_null($user_trxs_arr) && array_key_exists($today,$user_trxs_arr)){
                        $total_trxs = count($user_trxs_arr->$today);
                        $transactions = $user_trxs_arr->$today;
                      }
                      $tcat_lower_limit_in = date("Y-m-d H:i:s",strtotime('-120 minutes', strtotime($tcat_timein)));
                      $tcat_upper_limit_in = date("Y-m-d H:i:s",strtotime('+120 minutes', strtotime($tcat_timein)));


                      if($total_trxs == 0){
                        //check if the user is on off day or not
                      
                        if($dt_attendance->$tcat_day_check == ""){
                          $offday+=1;
                            if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)=="--"){
                              echo "<td class='text-center' >--</td>"; 
                            }else{
                              echo "<td class='text-center' >OFF</td>";  
                            }
                        }else{
                          //check in leaves
                          $today_leaves = $leaves->where('user_id',$dt_attendance->user_id)->where('leave_date',$today);
                          $today_attachs = $attachs->where('user_id',$dt_attendance->user_id)->where('attach_date',$today);
                          $holidays_filtered = $holidays->where('holiday_date',$today)->where('department_id',$dt_attendance->user_department_id);
                          
                          if(count($today_leaves)>0){
                            $leave+=1;
                            echo "<td class='text-center' >L</td>";    
                          }else if(count($today_attachs)>0){
                            $attach+=1;
                            if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)!="--"){
                              echo "<td class='text-center' >T</td>";                                    
                            }else{
                              echo "<td class='text-center' >--</td>";
                            }  
                          }else if(count($holidays_filtered)>0){
                            
                            $holiday+=1;
                            echo "<td class='text-center' >H</td>";    
                          }else{
                          if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)=="A"){
                            $absent+=1;
                           }
                           if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)=="--"){
                              echo "<td class='text-center' >--</td>";  
                           }else{
                            echo "<td  class='danger text-center' >".checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)."</td>";                                
                           }

                          }
                          
                        }
                      }else{
                        
                        
                        $tcat_timein = $today." ".$time_category_history_info->$tcat_day_check;
                        $gracetime_split = explode(':',$time_category_history_info->grace_time);
                        $gracetime_min   = ($gracetime_split[0]*60)+($gracetime_split[1]);
                        
                        $tcat_gracetime = date('Y-m-d H:i:s', strtotime('+'.$gracetime_min.' minutes', strtotime($tcat_timein)));
                        $today_trxs = [];
                        if($is_night_shift==1){
                          foreach($transactions as $trx){
                            if($trx > $tcat_lower_limit_in && $trx < $tcat_upper_limit_in){
                              $today_trxs[] = $trx;
                            }
                          }                
                          $transactions = $today_trxs;
                        }
                        if(count($transactions)>0){
                          if($transactions[0] > $tcat_gracetime){
                              $exploded_trx = explode("-",$transactions[0]);
                              $date = $exploded_trx[0]."-".$exploded_trx[1]."-".$exploded_trx[2];
                              $star = '';
                              if($exploded_trx[3] == 1){ $star = '<span class="text-danger">*</span>'; }
                              if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)=="--"){
                                echo "<td class='text-center' >--</td>"; 
                              }else{
                                echo "<td class='warning text-center'>".date("H:i:s",strtotime($date)).$star."</td>";
                                $late+=1;
                                $present+=1;                                
                              }
                          }else{
                              $exploded_trx = explode("-",$transactions[0]);
                              $date = $exploded_trx[0]."-".$exploded_trx[1]."-".$exploded_trx[2];
                              
                              $star = '';
                              if($exploded_trx[3] == 1){ $star = '<span class="text-danger">*</span>'; }
                              if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)=="--"){
                                echo "<td class='text-center' >--</td>"; 
                              }else{
                                echo "<td class='text-center'>".date("H:i:s",strtotime($date)).$star."</td>";  
                                $present+=1;
                              }

                          }
                        }else{
                          echo "<td class='text-center'>--</td>";
                        }
                        
                      }                        
                      

                    }
                  @endphp
                  <td class="text-center" rowspan="2">{{ $present }}</td>
                  <td class="text-center" rowspan="2">{{ $offday }}</td>
                  <td class="text-center" rowspan="2">{{ $late }}</td>
                  <td class="text-center" rowspan="2">{{ $absent }}</td>
                  <td class="text-center" rowspan="2">{{ $leave }}</td>
                  <td class="text-center" rowspan="2">{{ $attach }}</td>
                  <td class="text-center" rowspan="2">{{ $holiday }}</td>
                </tr>
                <tr>
                  <td>OUT</td>
                  @php
                    $begin = new DateTime($date_from);
                    $end   = new DateTime($date_to);
                    $user_trxs_arr = json_decode($dt_attendance->att_transactions);
                    for($i = $begin; $i <= $end; $i->modify('+1 day')){
                      $today = $i->format("Y-m-d");
                      $yesterday = date('Y-m-d',strtotime($today . "+1 days"));
                      $tomorrow = date('Y-m-d',strtotime($today . "+1 days"));

                      $time_category_history_info =  monthly_attendance_status_report($time_cat_history->where('date','<=',$i->format("Y-m-d")),$dt_attendance);
                      $is_night_shift = $time_category_history_info->is_night_shift;

                      $total_trxs = 0;
                      if(!is_null($user_trxs_arr) && array_key_exists($today,$user_trxs_arr)){
                        $total_trxs = count($user_trxs_arr->$today);
                        $transactions = $user_trxs_arr->$today;

                      }

                      if($total_trxs == 0){
                          if($dt_attendance->$tcat_day_check == ""){
                            echo "<td class='text-center' >OFF</td>";  
                          }else{
                            //check in leaves
                            $today_leaves = $leaves->where('user_id',$dt_attendance->user_id)->where('leave_date',$yesterday);
                            $today_attachs = $attachs->where('user_id',$dt_attendance->user_id)->where('attach_date',$yesterday);
                            if(count($today_leaves)>0){
                              echo "<td class='text-center' >L</td>";    
                            }else if(count($today_attachs)>0){
                              if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)!="--"){
                                echo "<td class='text-center' >T</td>";                                    
                              }else{
                                echo "<td class='text-center' >--</td>";
                              } 
                            }else{
                              if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)=="A"){
                              }
                              echo "<td  class='danger text-center' >".checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)."</td>";  
                            }
                            
                          }
                      }
                      
                      $yesterday_total_trxs = 0;
                      if($is_night_shift == 1){
                        if(!is_null($user_trxs_arr) && array_key_exists($tomorrow,$user_trxs_arr)){
                          $total_trxs = count($user_trxs_arr->$tomorrow);
                          $transactions = $user_trxs_arr->$tomorrow;
                        }
                      }

                      if($is_night_shift == 1){
                          
                          $tcat_timein = $today." ".$time_category_history_info->$tcat_yesterday_check;
                          $tcat_timeout = $yesterday." ".$time_category_history_info->$tcat_yesterday_check_out;

                          $tcat_lower_limit = date("Y-m-d H:i:s",strtotime('-120 minutes', strtotime($tcat_timeout)));
                          $tcat_upper_limit = date("Y-m-d H:i:s",strtotime('+120 minutes', strtotime($tcat_timeout))); 
                          if($total_trxs>0){
                            $calculated_out_trx = false;

                          $today_trxs = [];
                          foreach($transactions as $trx){
                            if($trx > $tcat_lower_limit && $trx < $tcat_upper_limit){
                              $today_trxs[] = $trx;
                            }
                          }                
                          $transactions = $today_trxs;

                            foreach($transactions as $trx){

                              $trx_parts = explode("-",$transactions[0]);
                              $trx = $trx_parts[0]."-".$trx_parts[1]."-".$trx_parts[2];


                              if($trx > $tcat_lower_limit && $trx <$tcat_upper_limit){

                                $calculated_out_trx = $trx;
                                $calculated_out_trx_is_manual = $trx_parts[3];
                              }
                            }
                            if($calculated_out_trx){
                                                  
                                $star = '';
                                if($calculated_out_trx_is_manual == 1){ 
                                  $star = '<span class="text-danger">*</span>'; 
                                }
                                if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)=="--"){
                                  echo "<td class='warning text-center'></td>";
                                }else{
                                  echo "<td class='text-center'>".date("H:i:s",strtotime($calculated_out_trx)).$star."</td>";
                                }

                            }else{
                              if(count($transactions)>1){
                                if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)=="--"){
                                  echo "<td class='text-center' >--</td>"; 
                                }else{
                                  echo "<td class='text-center warning'>MC</td>";
                                }
                              }
                            }
                          }
                      }else{

                          if($total_trxs>0){
                            if($total_trxs == 1){
                                if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)=="--"){
                                  echo "<td class='text-center' >--</td>"; 
                                }else{
                                  echo "<td class='text-center warning'>MC</td>";
                                }                              
                            }else{

                              $exploded_trx = explode("-",$transactions[count($transactions)-1]);
                              $date = $exploded_trx[0]."-".$exploded_trx[1]."-".$exploded_trx[2];

                              $star = '';
                              if($exploded_trx[3] == 1){ $star = '<span class="text-danger">*</span>'; }
                              if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)=="--"){
                                echo "<td class='warning text-center'></td>";
                              }else{
                                echo "<td class='text-center'>".date("H:i:s",strtotime($date)).$star."</td>";
                              }
                            }
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
            <th width="3%">Holiday</th>
          </tr>
        </thead>
        <tbody>
            @foreach($daytime_attendance as $dt_attendance)
            <tr>
              <td   class="text-center">{{ $dt_attendance->user_id }}</td>
              <td  class="text-center">
                        {{ $dt_attendance->name }} <br/>
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
                  if($tcat_day_check == 'tc_saturday_in'){
                      $tcat_day_check = "tc_saturday_in";
                  }
                  if($tcat_day_check == 'tc_thursday_in'){
                      $tcat_day_check = "tc_thursday_in";
                  }


                  $tcat_yesterday_check = "tc_".strtolower(date('l', strtotime($yesterday)))."_in";
                  if($tcat_yesterday_check == 'tc_saturday_in'){
                      $tcat_yesterday_check = "tc_saturday_in";
                  }
                  if($tcat_yesterday_check == 'tc_thursday_in'){
                      $tcat_yesterday_check = "tc_thursday_in";
                  }

                  $total_trxs = 0;
                  if(!is_null($user_trxs_arr) && array_key_exists($today,$user_trxs_arr)){
                    $total_trxs = count($user_trxs_arr->$today);
                    $transactions = $user_trxs_arr->$today;
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
                      }else{
                        //check in leaves
                        $today_leaves = $leaves->where('user_id',$dt_attendance->user_id)->where('leave_date',$yesterday);
                        $today_attachs = $attachs->where('user_id',$dt_attendance->user_id)->where('attach_date',$yesterday);
                        if(count($today_leaves)>0){
                          $leave+=1;
                        }else if(count($today_attachs)>0){
                          $attach+=1;
                        }else{
                          if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)=="A"){
                            $absent+=1;
                          }
                        }
                        
                      }
                    }else{
                      
                      $tcat_timein = $yesterday." ".$dt_attendance->$tcat_yesterday_check;

                      $tcat_timein = $today." ".$dt_attendance->$tcat_day_check;
                      $gracetime_split = explode(':',$dt_attendance->grace_time);
                      $gracetime_min   = ($gracetime_split[0]*60)+($gracetime_split[1]);


                      $tcat_gracetime = date('Y-m-d H:i:s', strtotime('+'.$gracetime_min.' minutes', strtotime($tcat_timein)));
                      if($yesterday_transactions[0] > $tcat_gracetime){
                          $late+=1;
                          $present+=1;
                      }else{
                          $present+=1;
                      }
                      
                    }
                  }else{
                    if($total_trxs == 0){
                      //check if the user is on off day or not
                      if($dt_attendance->$tcat_day_check == ""){
                        $offday+=1; 
                      }else{
                        //check in leaves
                        $today_leaves = $leaves->where('user_id',$dt_attendance->user_id)->where('leave_date',$today);
                        $today_attachs = $attachs->where('user_id',$dt_attendance->user_id)->where('attach_date',$today);
                        $holidays_filtered = $holidays->where('holiday_date',$today)->where('department_id',$dt_attendance->user_department_id);
                        
                        if(count($today_leaves)>0){
                          $leave+=1; 
                        }else if(count($today_attachs)>0){
                          $attach+=1;    
                        }else if(count($holidays_filtered)>0){
                          $holiday+=1;
                        }else{
                          if(checkAbsentOfMonthlyReport($today,$dt_attendance->reg_date)=="A"){ 
                            $absent+=1;
                          }
                        }
                        
                      }
                    }else{
                    
                      $tcat_timein = $today." ".$dt_attendance->$tcat_day_check;
                      $gracetime_split = explode(':',$dt_attendance->grace_time);
                      $gracetime_min   = ($gracetime_split[0]*60)+($gracetime_split[1]);

                      $tcat_gracetime = date('Y-m-d H:i:s', strtotime('+'.$gracetime_min.' minutes', strtotime($tcat_timein)));
                      if($transactions[0] > $tcat_gracetime){
                          $late+=1;
                          $present+=1;
                      }else{
                          $present+=1;
                      }
                      
                    }                        
                  }

                }
              @endphp
              <td class="text-center">{{ $present }}</td>
              <td class="text-center">{{ $offday }}</td>
              <td class="text-center">{{ $late }}</td>
              <td class="text-center">{{ $absent }}</td>
              <td class="text-center">{{ $leave }}</td>
              <td class="text-center">{{ $attach }}</td>
              <td class="text-center">{{ $holiday }}</td>
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