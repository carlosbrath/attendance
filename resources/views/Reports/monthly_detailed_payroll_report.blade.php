
@extends('layout.layout')
@section('title',' Monthly PayRoll Report')
@section('content')

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
          font-size:11px;
        }

        #monthlyreport th{
          font-size: 10px !important;
        }
        #monthlyreport td{
          font-size: 10px !important;
          padding-left:0px !important;
          padding-right:0px !important; 
         
          padding-bottom:0px !important; 
          width: 2px !important;
        }
         #monthlyreport span{
          font-size: 10px !important;
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
        <form class="form-no-horizontal-spacing" action="{{route('monthly_payroll')}}" 
             method="post" id="form-condensed" novalidate="novalidate">
          @csrf
          <div class="row column-seperation">
            <div class="col-md-12">
              <h4></h4>
                <div class="row form-row">
                  <div class="col-md-5" >
                          <label>Month <span style="color:red"> *</span></label>
                          <input type="text" style="width:367px" id="datepicker" class="form-control" autocomplete="off" name="month" autocomplete="off">
                    @if($errors->any())
                     <p style="color:red">{{$errors->first('month','Month field is required')}}
                     </p>
                    @endif
                  </div>    
                <div class="col-md-8">
                    <h4></h4>
                    <div class="form-group">
                      <label class="form-label"><strong>Departments</strong></label>
                      <div class=" right">
                      <i class=""></i>
                      <select name="deparment_id" id="department_id" style="width:367px"  class="select2 form-control">
                      <option value="">
                      Please Select...
                      </option>
                       <option name=""  value="all">All</option>
                      @foreach($department as $key=>$value)
                      <option name=""  value="{{$key}}">{{$value}}</option>
                      @endforeach
                      
                      </select>
                      @if($errors->any())
                      <p style="color:red">{{$errors->first('deparment_id','Department field is required')}}
                      </p>
                      @endif

                      </div>
                    </div>
                </div>
                @if($sub_department->count()>0)
                  <div class="col-md-8">
                    <h4></h4>
                    <div class="form-group">
                      <label class="form-label"><strong>Sub Departments</strong></label>
                      <div class=" right">
                      <i class=""></i>
                      <select name="subdeparment_id" id="sub_department" style="width:367px"  class="sub_department">
                      <option value="">
                      Please Select...
                      </option>
                      @foreach($sub_department as $key=>$value)
                      <option name=""  value="{{$key}}">{{$value}}</option>
                      @endforeach
                      </select>
                      @if($errors->any())
                      <p style="color:red">{{$errors->first('subdeparment_id','Sub Department field is required')}}
                      </p>
                      @endif

                      </div>
                    </div>
                </div>
                @endif
                 <div class="col-md-12">
                  <label class="form-label"  style="font-size:15px ">From Date</label>
                  <div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
                    <input type="text" autocomplete="off" value="dd/mm/yyyy" name="from_date" placeholder="From Date" class="form-control">
                     
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                    
                  </div>
                
                </div>

                <div class="col-md-12">
                  <label class="form-label"  style="font-size:15px ">To Date</label>
                  <div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
                    <input type="text" autocomplete="off" value="dd/mm/yyyy" name="to_date" placeholder="From Date" class="form-control">
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                  </div>
                </div>
              </div>
              <div  class="row form-row">
         <!--        <div class="control-group">
          <label class="control-label">Select Region</label>
          <div class="controls">
            <select class="span6 chzn-select" name="user_name"  tabindex="1">
                                       <option value="">Please Select...</option>
                                        
                                          <option name=""  value="">sad</option>
                                          <option name=""  value="">sad</option>
                                          <option name=""  value="">sad</option>
                                          <option name=""  value="">sad</option>
                                          
             </select>
            
          </div>
        </div> -->
       <!--  <div class="control-group">
          <label class="control-label">Select Zone</label>
          <div class="controls">
            <select class="span6 chzn-select" name="user_name"  tabindex="1">
                                       <option value="">Please Select...</option>
                                        
                                          <option name=""  value="">sad</option>
                                          <option name=""  value="">sad</option>
                                          <option name=""  value="">sad</option>
                                          <option name=""  value="">sad</option>
                                          
             </select>
            
          </div>
        </div> -->
              </div>
<!-- 
              <div  class="row form-row">
                <div class="control-group">
          <label class="control-label">Select Branch</label>
          <div class="controls">
            <select class="span6 chzn-select" name="user_name"  tabindex="1">
                                       <option value="">Please Select...</option>
                                        
                                          <option name=""  value="">sad</option>
                                          <option name=""  value="">sad</option>
                                          <option name=""  value="">sad</option>
                                          <option name=""  value="">sad</option>
                                          
             </select>
            
          </div>
        </div>

         <div class="control-group">
          <label class="control-label">Select Shift</label>
          <div class="controls">
            <select class="span6 chzn-select" name="user_name"  tabindex="1">
                                       <option value="">Please Select...</option>
                                        
                                          <option name=""  value="">sad</option>
                                          <option name=""  value="">sad</option>
                                          <option name=""  value="">sad</option>
                                          <option name=""  value="">sad</option>
                                          
             </select>
            
          </div>
        </div>
              </div> -->
              <div class="form-actions">
            <button class="btn btn-success" type="submit">Generate Report</button>
            
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

@if($users!='')
<h4><b>{{$month_year_name}}</b></h4>
<div class="pull-right">
      <button class="btn-cogs" style="margin-top:2px" id="monthly_report_print" type="submit"><i class="icon-ok"></i>Print</button>                
      </div>
  <div class="col-md-12">

    <div class="grid simple ">
      <div class="grid-body table-responsive">
       <!--  <div class="col-md-12"><button id="btn_print" class="btn btn-success">Print</button></div> -->
       <div class="table-responsive">
        <table id="monthlyreport"  class="monthly_repor table dataTable display nowrap table-responsive table-bordered portal-drivers" aria-describedby="example3_info" style="width:100%">
        <!--<table class="table dataTable" id="example" aria-describedby="example3_info">-->
          <thead>
            <tr role="row">
             <!--  <th>S# No</th> -->
              <th style="width: 38px !important">ID</th>
              <th style="width: 138px !important">Name</th>
             <!--  <th>Designation</th> -->
               
                 <?php

                    $start_date_ran=$from_date;
                    $to_date_ran=$to_date;
                    $start_date = new DateTime($start_date_ran);
                    $to_date = new DateTime($to_date_ran);
                                
                    for($i = $start_date;$i<=$to_date;$i->modify('+1 day')){
                        
                        echo  "<th>".date('d', strtotime($i->format('Y-m-d')))."</td>";
                                   
                    }
                 ?>
             <th>Monthly Salary</th>
             <th>Total Pay</th>
             <th>Bonuses</th>
             <th>Deduction</th> 
             <th>Total</th>                                   
          </tr>
          </thead>

          <tbody role="alert" aria-live="polite" aria-relevant="all">
                                @foreach($users as $u)

                                           <tr style="height:43px !important">
                                            <!--  <td style="width:1px !important">{{$loop->iteration}} </td> -->
                                             <td style="width:1px !important"><?php echo $u->id?> </td>
                                             <td style="width:1px !important; padding-left:3px !important;
          padding-right:0px !important; ">{{$u->name}}</td>
                                        <!--      <td style="width:1px !important;padding-left:3px !important;
          padding-right:0px !important; ">{{$u->title}}</td> -->
                                            
                                <?php 
                                       
                                   $total_absent=0; 
                                   $total_present=0;
                                   $total_late=0;
                                   $total_pay_count=0;
                                   $total_bonus_count=0;
                                   $total_deduction_count=0;
                                        // $first_day_date=$first_day.'-01';

                                        // $last_day = date("t", strtotime($first_day_date));
                                   $start_date_ran=$from_date;
                                   $to_date_ran=$to_date;              
                                   $start_date = new DateTime($start_date_ran);
                                  // $to_date = new DateTime($to_date_ran);

                                  for($i = $start_date;$i<=$to_date_ran;$i->modify('+1 day')){
                                    $date=$i->format('Y-m-d');
                                    $checktimes = [];
                                    $uniqe_index_user_attendance = $u->id."--".$i->format('Y-m-d');
                                    $date_attendance = [];

                                    if (array_key_exists($uniqe_index_user_attendance,$emp_status_time_in))
                                    {
                                      $date_attendance = $emp_status_time_in[$uniqe_index_user_attendance];
                                    }
                                    $date_attendance_collection = collect($date_attendance);

                                    foreach($date_attendance as $key=>$val){
                                      $checktimes[] = $val->att_date;
                                    }
                                     if($day_off->count()>0){
                                          $checking_dayoff = $day_off->where('id',$u->id);
                                          $time_category_diff=strtotime($checking_dayoff->first()->time_out)-strtotime($checking_dayoff->first()->time_in);
                                          $time_category_diff=date('H:i:s',$time_category_diff);
                                          $month=date("n",strtotime($from_date));  
                                          $days = cal_days_in_month(CAL_GREGORIAN, $month, 2013);  
                                          $hour_rate = $u->fixed_salary;
                                          $hrs=date('g',strtotime($time_category_diff));
                                          $mins=date('i',strtotime($time_category_diff));
                                          $day_time= $hrs.'.'.$mins;
                                          $monthly_hours=$day_time*$days;
                                          $basic_salary=$hour_rate;
                                          $cal_daily_salary=$basic_salary/$monthly_hours;
                                          $pay=  $cal_daily_salary*$day_time;
                                          $pay=  round($pay);
                                          

                                           if(date('l', strtotime($date))=="Monday"){
                                                  if(empty($checking_dayoff->first()->tc_monday_in)){
                                                     $emp_status='<span style="color:green">'.$pay.' Rs</span>';
                                                    $total_pay_count+= $pay;
                                                  }else{
                                                         
                                                        $check_holidays=$holidays->where('holiday_date',$date);
                                                        if($check_holidays->isEmpty())
                                                        {

                                                        $emp_stat='A';
                                                        $emp_status='<span>'.$emp_stat.'</span>';
                                                        if($emp_status){
                                                        $total_absent+=1;
                                                        }

                                                        }else{

                                                        $emp_stat=$check_holidays->first()->title;
                                                        $emp_status='<span>'.$emp_stat.'<br/>'.$pay.' Rs</span>';
                                                        $total_pay_count+= $pay;
                                                        }

                                                  }
                                           } //end monday
                                            if(date('l', strtotime($date))=="Tuesday"){
                                                  if(empty($checking_dayoff->first()->tc_tuesday_in)){
                                                     $emp_status='<span style="color:green">'.$pay.' Rs</span>';
                                                    $total_pay_count+= $pay;
                                                  }
                                                  else{

                                                      $check_holidays=$holidays->where('holiday_date',$date);
                                                      if($check_holidays->isEmpty())
                                                      {

                                                      $emp_stat='A';
                                                      $emp_status='<span>'.$emp_stat.'</span>';
                                                      if($emp_status){
                                                      $total_absent+=1;
                                                      }

                                                      }else{

                                                      $emp_stat=$check_holidays->first()->title;
                                                      $emp_status='<span>'.$emp_stat.'<br/>'.$pay.' Rs</span>';
                                                      $total_pay_count+= $pay;
                                                      }

                                                   }
                                           } //end tuesday
                                           if(date('l', strtotime($date))=="Wednesday"){
                                                  if(empty($checking_dayoff->first()->tc_wednesday_in)){
                                                     $emp_status='<span style="color:green">'.$pay.' Rs</span>';
                                                    $total_pay_count+= $pay;
                                                  }
                                                  else{
                                                         
                                                        $check_holidays=$holidays->where('holiday_date',$date);
                                                        if($check_holidays->isEmpty())
                                                        {

                                                        $emp_stat='A';
                                                        $emp_status='<span>'.$emp_stat.'</span>';
                                                        if($emp_status){
                                                        $total_absent+=1;
                                                        }

                                                        }else{

                                                              $emp_stat=$check_holidays->first()->title;
                                                              $emp_status='<span>'.$emp_stat.'<br/>'.$pay.' Rs</span>';
                                                              $total_pay_count+= $pay;
                                                        }

                                                     }
                                              } //end wednesday
                                              if(date('l', strtotime($date))=="Thursday"){
                                                  if(empty($checking_dayoff->first()->tc_thursday_in)){
                                                     $emp_status='<span style="color:green">'.$pay.' Rs</span>';
                                                     $total_pay_count+= $pay;
                                                  }
                                                  else{
                                                         
                                                          $check_holidays=$holidays->where('holiday_date',$date);
                                                          if($check_holidays->isEmpty())
                                                          {

                                                          $emp_stat='A';
                                                          $emp_status='<span>'.$emp_stat.'</span>';
                                                          if($emp_status){
                                                          $total_absent+=1;
                                                          }

                                                          }else{

                                                          $emp_stat=$check_holidays->first()->title;
                                                          $emp_status='<span>'.$emp_stat.'<br/>'.$pay.' Rs</span>';
                                                          $total_pay_count+= $pay;
                                                          }
                                                      }
                                           } //end thursday
                                            if(date('l', strtotime($date))=="Friday"){
                                                  if(empty($checking_dayoff->first()->tc_friday_in)){
                                                    $emp_status='<span style="color:green">'.$pay.' Rs</span>';
                                                    $total_pay_count+= $pay;
                                                  }else{
                                                         
                                                          $check_holidays=$holidays->where('holiday_date',$date);
                                                          if($check_holidays->isEmpty())
                                                          {

                                                          $emp_stat='A';
                                                          $emp_status='<span>'.$emp_stat.'</span>';
                                                          if($emp_status){
                                                          $total_absent+=1;
                                                          }

                                                          }else{

                                                          $emp_stat=$check_holidays->first()->title;
                                                          $emp_status='<span>'.$emp_stat.'<br/>'.$pay.' Rs</span>';
                                                          $total_pay_count+= $pay;
                                                          }

                                                      }

                                           } //end friday
                                           if(date('l', strtotime($date))=="Saturday"){
                                                  if(empty($checking_dayoff->first()->tc_satureday_in)){
                                                    $emp_status='<span style="color:green">'.$pay.' Rs</span>';
                                                    $total_pay_count+= $pay;
                                                  }
                                                  else{
                                                         
                                                        $check_holidays=$holidays->where('holiday_date',$date);
                                                        if($check_holidays->isEmpty())
                                                        {

                                                        $emp_stat='A';
                                                        $emp_status='<span>'.$emp_stat.'</span>';
                                                        if($emp_status){
                                                        $total_absent+=1;
                                                        }

                                                        }else{

                                                        $emp_stat=$check_holidays->first()->title;
                                                        $emp_status='<span>'.$emp_stat.'<br/>'.$pay.' Rs</span>';
                                                        $total_pay_count+= $pay;
                                                        }

                                                    }

                                           } //end saturday
                                           if(date('l', strtotime($date))=="Sunday"){
                                                  if(empty($checking_dayoff->first()->tc_sunday_in)){
                                                     $emp_status='<span style="color:green">'.$pay.' Rs</span>';
                                                     $total_pay_count+= $pay;
                                                  }else{
                                                         
                                                        $check_holidays=$holidays->where('holiday_date',$date);
                                                        if($check_holidays->isEmpty())
                                                        {

                                                        $emp_stat='A';
                                                        $emp_status='<span>'.$emp_stat.'</span>';
                                                        if($emp_status){
                                                        $total_absent+=1;
                                                        }

                                                        }else{

                                                        $emp_stat=$check_holidays->first()->title;
                                                        $emp_status='<span>'.$emp_stat.'<br/>'.$pay.' Rs</span>';
                                                        $total_pay_count+= $pay;
                                                        }

                                                     } 

                                           } //end sunday 
                                     }else{

                                            $emp_stat='A';
                                            $emp_status='<span>'.$emp_stat.'<br>';
                                            if($emp_status){
                                               $total_absent+=1;
                                            }

                                     }

                                    
                                    // if(date('l', strtotime($i->format('Y-m-d')))=='Sunday' || date('l', strtotime($i->format('Y-m-d')))=='Saturday'){

                                    //          $emp_status='<span style="color:pink">off day<br><hr style="margin:0px;border-color:#e2d3d3">off day</span>';
                                    //   }

                                     

                        
                                    // dd($emp_status_time_in);
                                
                                  if($date_attendance_collection->where('type',1)->count()>0){
                                      
                                      
                                      foreach ($date_attendance_collection->groupBy('date_time') as $att) {

                                           $attendance=$att->where('id',$u->id)->where('date_time',$date);
                                        
                                           if($attendance->count()>0){
                                            if(date('l', strtotime($att->first()->date_time)=="Monday")) {
                                                $time_in=$attendance->first()->tc_monday_in;
                                                $grace_time=$attendance->first()->grace_time;
                                                $time_out=$attendance->first()->tc_monday_out;
                                                $min = date('i',strtotime($grace_time));
                                                $time_in = strtotime("+".$min."minutes", strtotime($time_in));

                                                $time_out =  strtotime($time_out);
                                                $time_in =  date("H:i:s", $time_in);
                                                $time_out =  date("H:i:s", $time_out);
                                             
                                                $timecat_in_out_diff = strtotime($time_out) - strtotime($attendance->first()->tc_monday_in);
                                                $timecat_in_out_diff = date('H:i:s',$timecat_in_out_diff);
                                                
                                                
                                              }

                                             elseif(date('l', strtotime($i->format('Y-m-d'))=="Tuesday")){
                                                  

                                                  $time_in=$attendance->first()->tc_tuesday_in;

                                                
                                                  $grace_time=$attendance->first()->grace_time;
                                                  $time_out=$attendance->first()->tc_tuesday_out;
                                                  $min = date('i',strtotime($grace_time));
                                                  $time_in = strtotime("+".$min."minutes", strtotime($time_in));
                                                  $time_out =  strtotime($time_out);
                                                  $time_in =  date("H:i:s", $time_in);
                                                  $time_out =  date("H:i:s", $time_out);

                                                  $timecat_in_out_diff = strtotime($time_out) - strtotime($attendance->first()->tc_tuesday_in);
                                                  $timecat_in_out_diff = date('H:i:s',$timecat_in_out_diff);

                                                
                                               }
                                              elseif(date('l', strtotime($i->format('Y-m-d'))=="Wednesday")){

                                                  $time_in=$attendance->first()->tc_wednesday_in;
                                                  $grace_time=$attendance->first()->grace_time;
                                                  $time_out=$attendance->first()->tc_wednesday_out;
                                                  $min = date('i',strtotime($grace_time));
                                                  $time_in = strtotime("+".$min."minutes", strtotime($time_in));
                                                  $time_out =  strtotime($time_out);
                                                  $time_in =  date("H:i:s", $time_in);
                                                  $time_out =  date("H:i:s", $time_out);

                                                  $timecat_in_out_diff = strtotime($time_out) - strtotime($attendance->first()->tc_wednesday_in);
                                                  $timecat_in_out_diff = date('H:i:s',$timecat_in_out_diff);


                                               }
                                                elseif(date('l', strtotime($i->format('Y-m-d'))=="Thursday")){

                                                  $time_in=$attendance->first()->tc_thursday_in;
                                                  $grace_time=$attendance->first()->grace_time;
                                                  $time_out=$attendance->first()->tc_thursday_out;
                                                  $min = date('i',strtotime($grace_time));
                                                  $time_in = strtotime("+".$min."minutes", strtotime($time_in));
                                                  $time_out =  strtotime($time_out);
                                                  $time_in =  date("H:i:s", $time_in);
                                                  $time_out =  date("H:i:s", $time_out);

                                                  $timecat_in_out_diff = strtotime($time_out) - strtotime($attendance->first()->tc_wednesday_in);
                                                  $timecat_in_out_diff = date('H:i:s',$timecat_in_out_diff);
                                               }
                                                elseif(date('l', strtotime($i->format('Y-m-d'))=="Friday")){

                                                  $time_in=$attendance->first()->tc_friday_in;
                                                  $grace_time=$attendance->first()->grace_time;
                                                  $time_out=$attendance->first()->tc_friday_out;
                                                  $min = date('i',strtotime($grace_time));
                                                  $time_in = strtotime("+".$min."minutes", strtotime($time_in));
                                                  $time_out =  strtotime($time_out);
                                                  $time_in =  date("H:i", $time_in);
                                                  $time_out =  date("H:i", $time_out);

                                                  $timecat_in_out_diff = strtotime($time_out) - strtotime($attendance->first()->tc_friday_in);
                                                  $timecat_in_out_diff = date('H:i:s',$timecat_in_out_diff);


                                               }
                                              elseif(date('l', strtotime($i->format('Y-m-d'))=="Saturday")){
                                                  $time_in=$attendance->first()->tc_satureday_in;
                                                  $grace_time=$attendance->first()->grace_time;
                                                  $time_out=$attendance->first()->tc_satureday_out;
                                                  $min = date('i',strtotime($grace_time));
                                                  $time_in = strtotime("+".$min."minutes", strtotime($time_in));
                                                  $time_out =  strtotime($time_out);
                                                  $time_in =  date("H:i", $time_in);
                                                  $time_out =  date("H:i", $time_out);

                                                  $timecat_in_out_diff = strtotime($time_out) - strtotime($attendance->first()->tc_satureday_in);
                                                  $timecat_in_out_diff = date('H:i:s',$timecat_in_out_diff);
                                               }
                                                elseif(date('l', strtotime($i->format('Y-m-d'))=="Sunday")){

                                                  $time_in=$attendance->first()->tc_sunday_in;

                                                  $grace_time=$attendance->first()->grace_time;
                                                  $time_out=$attendance->first()->tc_sunday_out;

                                                  $min = date('i',strtotime($grace_time));
                                                  $time_in = strtotime("+".$min."minutes", strtotime($time_in));

                                                  $time_out =  strtotime($time_out);
                                                  $time_in =  date("H:i:s", $time_in);
                                                  $time_out =  date("H:i:s", $time_out);

                                                  $timecat_in_out_diff = strtotime($time_out) - strtotime($attendance->first()->tc_sunday_in);
                                                  $timecat_in_out_diff = date('H:i:s',$timecat_in_out_diff);

                                               }

                                             sort($checktimes);
                                             $timein = strtotime($checktimes[0]);
                                             $timein =  date("H:i", $timein);
                                               
                                          if($emp_bonus->count()>0){

                                          $chck_bonus=$emp_bonus->where('date',$i->format('Y-m-d'))->where('user_id',$u->id);
                                                if($chck_bonus){
                                                $total_bonus=[];
                                                      foreach ($chck_bonus as $b) {
                                                      $total_bonus[]= $b->amount;
                                                      } 
                                                      $bonus=array_sum($total_bonus);
                                                      $total_bonus_count+= $bonus;

                                                }else{
                                                $bonus=0;
                                                }                                                   

                                          }
                                          $deduction=0;
                                          if($emp_deduction->count()>0){

                                          $chck_deduction=$emp_deduction->where('date',$i->format('Y-m-d'))->where('user_id',$u->id);
                                                if($chck_deduction){
                                                $total_deduction=[];
                                                      foreach ($chck_deduction as $d) {
                                                      $total_deduction[]= $d->amount;
                                                      } 
                                                      $deduction=array_sum($total_deduction);
                                                      $total_deduction_count+= $deduction;
                                                }else{
                                                   $deduction=0;
                                                }                                                   

                                          }
 

                                            if($timein  >   $time_in) {
                                                  
                                                    $total_late+=1;
                                                    $total_absent-=1;

                                                    $timein = strtotime($checktimes[0]);
                                                    $time_out=strtotime($checktimes[count($checktimes)-1]);
                                                 
                      
                                                    $timein =  date("H:i:s", $timein);
                                                    $time_out =  date("H:i:s", $time_out);
                                                    $att_tranc_diff = strtotime($time_out) - strtotime($timein);
                                                    $employee_duty = date('H:i:s',$att_tranc_diff);
                                                   

                                                  
                              
                                                    if($timein==$time_out){
                                                      $emp_status='<span ><span style="color:red">M/o</span>';
                                                    }else{

                                                     
                                                        if(isset($u->fixed_salary)){

                                                            if(strtotime($timecat_in_out_diff) < strtotime($employee_duty)){
                                                                    
                                                                                                                          
                                                                   $timecalculation= strtotime($employee_duty)-strtotime($timecat_in_out_diff);
                                                                    $over_time= date('H:i:s',$timecalculation);

                                                                    $hour_rate =$u->hour_rate_salary;
                                                                    $month=date("n",strtotime($from_date));  
                                                                    $days = cal_days_in_month(CAL_GREGORIAN, $month, 2013);

                                                                    $hrs=date('g',strtotime($timecat_in_out_diff));
                                                                    $mins=date('i',strtotime($timecat_in_out_diff));
                                                                    $day_time= $hrs.'.'.$mins;
                                                                    $monthly_hours=$day_time*$days;
                                                                    $basic_salary=$hour_rate;
                                                                    $cal_daily_salary=$basic_salary/$monthly_hours;
                                                                    $pay=$cal_daily_salary*$day_time;
                                                                    $pay= round($pay);


                                                                  $overtime_package=$u->fixed_salary/60;
                                                                  $over_time_hrs=date('H',strtotime($over_time)); 
                                                                  $over_time_mins=date('i',strtotime($over_time));                 
                                                                  $over_day_time=$over_time_hrs.'.'.$over_time_mins;
                                                                  $overtime_pay=$overtime_package*$over_day_time;
                                                                  


                                                                  if($u->overtime_check==1){
                                                                      $pay_overtime_total=$pay+$overtime_pay;
                                                                      $pay_overtime_total= round($pay_overtime_total); 
                                                                  }else{
                                                                    $pay_overtime_total=$pay+0;
                                                                    $pay_overtime_total=round($pay_overtime_total);
                                                                  }
              
              
                
                                                                    if($emp_bonus->count()>0){
                                                                       
                                                                    $chck_bonus=$emp_bonus->where('date',$i->format('Y-m-d'));

                                                                    }
                                                                    $emp_status='<span style="color:green">'.$pay_overtime_total.' Rs <p style="margin-bottom:0px">B:'.$bonus.' Rs</p><p>D:'.$deduction.' Rs</p></span>';



                                                                
                                                              }else{
                                     
                                      
                                                                   
                                                                  $hour_rate =$u->fixed_salary;
                                                                
                                                                   $month=date("n",strtotime($from_date));  
                                                                   $days = cal_days_in_month(CAL_GREGORIAN, $month, 2013);
                                                                   $hrs=date('g',strtotime($timecat_in_out_diff));
                                                                   $mins=date('i',strtotime($timecat_in_out_diff));
                                                                   $day_time= $hrs.'.'.$mins;
                                                                   
                                                                   $short_hrs=date('g',strtotime($employee_duty));
                                                                   $short_mins=date('i',strtotime($employee_duty));
                                                                   $short_day_time= $short_hrs.'.'.$short_mins;

                                                                   $monthly_hours=$day_time*$days;
                                                                   $basic_salary=$hour_rate;
                                                                   $cal_daily_salary=$basic_salary/$monthly_hours;
                                                                   $pay=  $cal_daily_salary*$short_day_time;
                                                                   $pay= round($pay);

                                                                   $total_pay_count+= $pay;
                                                                    if(!isset($bonus)){
                                                                      $bonus = 0;
                                                                    }
                                                                    $emp_status='<span><span style="color:green">'.$pay.' Rs <p style="margin-bottom:0px">B:'.$bonus.' Rs</p><p>D:'.$deduction.' Rs</p></span>';  


                                                               }

                                                             

                                                         }else{

                                                            $emp_status='<span><span style="color:green">--</span>';  
                                                          
                                                         } 
                                                    }
                                                }

                                           elseif($timein  <=   $time_in){
                                                 
                                                    $total_present+=1;
                                                    $total_absent-=1;
                                                    sort($checktimes);
                                                    $timein = strtotime($checktimes[0]);
                                                    $timein =  date("H:i:s", $timein);
                                                    $time_out=strtotime($checktimes[count($checktimes)-1]);
                                                    $time_out =  date("H:i:s", $time_out);

                                                    $att_tranc_diff = strtotime($time_out) - strtotime($timein);
                                                    $employee_duty = date('H:i:s',$att_tranc_diff);

                                                   
                                                    if($timein==$time_out){

                                                      $emp_status='<span ><span style="color:red">M/o</span>';

                                                    }else{
                                                      

                                                        if(isset($u->fixed_salary)){

                                                            if(strtotime($timecat_in_out_diff) < strtotime($employee_duty)){
                                                                   $timecalculation= strtotime($employee_duty)-strtotime($timecat_in_out_diff);
                                                                    $over_time = date('H:i:s',$timecalculation);

                                                                    $hour_rate =$u->fixed_salary;
                                                                    $month=date("n",strtotime($from_date));  
                                                                    $days = cal_days_in_month(CAL_GREGORIAN, $month, 2013);

                                                                    $hrs=date('g',strtotime($timecat_in_out_diff));
                                                                    $mins=date('i',strtotime($timecat_in_out_diff));
                                                                    $day_time= $hrs.'.'.$mins;
                                                                    $monthly_hours=$day_time*$days;
                                                                    $basic_salary=$hour_rate;
                                                                    $cal_daily_salary=$basic_salary/$monthly_hours;
                                                                    $pay=$cal_daily_salary*$day_time;
                                                                    $pay= round($pay);


                                                                  $overtime_package=$u->fixed_salary/60;
                                                                  $over_time_hrs=date('H',strtotime($over_time)); 
                                                                  $over_time_mins=date('i',strtotime($over_time));                 
                                                                  $over_day_time=$over_time_hrs.'.'.$over_time_mins;
                                                                  $overtime_pay=$overtime_package*$over_day_time;
                                                                  


                                                                  if($u->overtime_check==1){
                                                                      $pay_overtime_total=$pay+$overtime_pay;
                                                                      $pay_overtime_total= round($pay_overtime_total);
                                                                      $total_pay_count+= $pay; 
                                                                  }else{
                                                                    $pay_overtime_total=$pay+0;
                                                                    $pay_overtime_total=round($pay_overtime_total);
                                                                    $total_pay_count+= $pay;
                                                                  }
              
                                                                 
                                                                  if(!isset($bonus)){
                                                                    $bonus = 0;
                                                                  }
                                                                    $emp_status='<span style="color:green">'.$pay_overtime_total.' Rs<p style="margin-bottom:0px">B:'.$bonus.' Rs</p><p>D:'.$deduction.' Rs</p></span>';
                                                                  



                                                                
                                                              }else{

                                                                   
                                                                   $hour_rate =$u->fixed_salary;
                                                                
                                                                   $month=date("n",strtotime($from_date));  
                                                                   $days = cal_days_in_month(CAL_GREGORIAN, $month, 2022);
                                                                   $hrs=date('g',strtotime($timecat_in_out_diff));
                                                                   $mins=date('i',strtotime($timecat_in_out_diff));
                                                                   $day_time= $hrs.'.'.$mins;
                                                                   
                                                                   $short_hrs=date('g',strtotime($employee_duty));
                                                                   $short_mins=date('i',strtotime($employee_duty));
                                                                   $short_day_time= $short_hrs.'.'.$short_mins;
                                                                    
                                                                   $monthly_hours=$day_time*$days;
                                                                   $basic_salary=$hour_rate;
                                                                   $cal_hourly_salary=$basic_salary/$monthly_hours;
                                                                   $cal_minute_salary=$cal_hourly_salary/60;
                                                                   $pay=  $cal_daily_salary*$short_day_time;
                                                                   $total_minutes = ($short_hrs*60)+$short_mins;
                                                                   $pay = $total_minutes*$cal_minute_salary;
                                                                   $pay= round($pay);
                                                                   $total_pay_count+= $pay;
                                                                  
                                                                  if(!isset($bonus)){
                                                                    $bonus = 0;
                                                                  }

                                                                    $emp_status='<span><span style="color:green">'.$pay.' Rs<p style="margin-bottom:0px">B:'.$bonus.' Rs</p><p>D:'.$deduction.' Rs</p></span>';  


                                                               }

                                                             

                                                         }else{

                                                            $emp_status='<span><span style="color:green">--</span>';  
                                                          
                                                         } 
                                                     

                                                     }
                                                   }
                                            }

                                          }
                                      }

                                     //dd($emp_status_leave_status->where('id',$u->id));
                                    if($emp_status_leave_status->where('id',$u->id)->count()>0){
                 
                                           foreach ($emp_status_leave_status->where('id',$u->id) as $att_leave) {
                                                           
                                            if(date("Y-m-d",strtotime($att_leave->leave_date))==$date){
                                              
                                            $emp_status='<span style="color:blue">LV <br/>'.$pay.' Rs</span>';
                                            $total_pay_count+= $pay;

                                            }

                                          }

                                        }
                                    if($emp_status_attach_status->where('id',$u->id)->count()>0){
                 
                                           foreach ($emp_status_attach_status->where('id',$u->id) as $att_attach) {
                                                           
                                            if(date("Y-m-d",strtotime($att_attach->attach_date))==$date){
                                              
                                            $emp_status='<span style="color:blue">ATT <br/>'.$pay.' Rs</span>';
                                            $total_pay_count+= $pay;

                                            }

                                          }

                                        }


                                        // if($emp_stat=="A"){
                                        //    c+=1;
                                        //  } 

                                              
                                               echo  '<td style="text-align:center;padding:0px">'.$emp_status.'</td>';
                                          }
                                       ?> 
                                            <td style="width: 1px !important;padding-left:10px !important">{{ $u->fixed_salary}} Rs</td> 
                                            <td style="width: 1px !important;padding-left:10px !important">{{$total_pay_count}} Rs</td> 
                                             <td style="width: 1px !important;padding-left:10px !important;">{{$total_bonus_count}} Rs</td> 
                                             <td style="width: 1px !important;padding-left:10px !important;">{{$total_deduction_count}} Rs</td>
                                             <td style="width: 1px !important;padding-left:10px !important;">{{$total_pay_count + $total_bonus_count - $total_deduction_count}} Rs</td>      
                                           </tr>                                          
                                        @endforeach            
                                 </tbody>
                                </table>
                              </div>
                              </div>
                            </div>
                         @endif
                   </div>
                <div class="span2" style="margin-left:0px;">
                </div>
                </div>
               </div>
            </div>         
@include('include.important')
@endsection