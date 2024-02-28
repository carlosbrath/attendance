@extends('layout.layout')
@section('title','Individual Report')
@section('content')
<style type="text/css">
  .form-actions {
    background-color: #f6f7f8;
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
</style>

<div class="row">
  <div class="col-md-12">
    <div class="grid simple form-grid">
      <div class="grid-body no-border">
        <form class="form-no-horizontal-spacing" method="get" action="{{route('employee_report2',  $user_id)}}" id="form-condensed" novalidate="novalidate">
          @csrf
          <div class="row column-seperation">
            <div class="col-md-12">
              <div class="row form-row">
                <div class="col-md-6">
                  <label class="form-label"  style="font-size:15px ">From Date</label>
                  <div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
                    <input type="text" value="{{$from_date}}"  autocomplete="off" name="from_date" placeholder="From Date" class="form-control">
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                    @if($errors->any())
                    <p style="color:red">{{$errors->first('from_date')}}</p>
                    @endif
                  </div>
                </div>
                <input name='user_id' value={{$user_id}} hidden>
                <div class="col-md-6">
                  <label class="form-label"  style="font-size:15px ">To Date</label>
                  <div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
                    <input type="text" value="{{$to_date}}" autocomplete="off" name="to_date" placeholder="To Date" class="form-control">
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                    @if($errors->any())
                    <p style="color:red">{{$errors->first('to_date')}}</p>
                    @endif
                  </div>
                </div>
              </div>
            </div>

          </div>
          <div class="form-actions">
            <div class="pull-left">
              <button class="btn btn-success btn-cons" type="submit"><i class="icon-ok"></i>Generate Report</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<br>
<?php if(isset($attendance)){?>
  <div class="row" id="indivi_rep_profile">
    <div class="col-md-4 col-vlg-3 col-sm-6" style="float:left;">
      <div class="tiles blue m-b-10">
        <div class="tiles-body">
          <div class="tiles-title text-white"><b>ID : {{$user_info_query[0]->id}}</b></div>
          <div class="tiles-title text-white"><b>Name : {{$user_info_query[0]->user_name}}</b></div>
          <div class="tiles-title text-white"><b>Department : {{$user_info_query[0]->name}}</b> </div>
          <div class="tiles-title text-white"><b>Designation : {{$user_info_query[0]->title}}</b></div>
          @isset($user_info_query[0]->cnic_issue_date,$user_info_query[0]->cnic_expiry_date)
          <div class="tiles-title text-white"><b>Cnic Status : {{checkCnicValidityExpiry($user_info_query[0]->cnic_issue_date,$user_info_query[0]->cnic_expiry_date)}}</b>   ({{$user_info_query[0]->cnic_issue_date }} - {{$user_info_query[0]->cnic_expiry_date }}) </div>
          @endisset
        </div>
      </div>
    </div>
    <div class="col-md-4 col-vlg-3 col-sm-6" style="float:right;">
      @if($user_info_query[0]->image!='')
      <div class="user-profile-pic" style="margin-top: 17px; width: 83%;">
      <img src="{{asset('photos/' . $user_info_query[0]->image)}}" width="200px" height="98px">
      </div>
      @else
      <div class="user-profile-pic" style="margin-top: 17px; width: 83%;">
        <img src="{{url('template_files/theme/assets/img/profiles/admin.jpg')}}" width="200px" height="98px">
      </div>
      @endif
    </div>
  </div>

  <div class="row">
    <div class="row-fluid">
      <div class="span12">
        <div class="pull-right">
          <button class="btn" style="margin-top:2px" id="indivi_rep_print" type="submit"><i class="icon-ok"></i>Print</button>
        </div>
        <div class="grid simple">

          <div class="grid-body ">

            <div class="table-responsive" id="individual_report_print">
              <table class="table datatable" id="" aria-describedby="example3_info">
                <thead>
                  <tr role="row">
                    <th>ID</th>
                    <th>Date</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>Hours Work</th>
                    <th>Total Hours</th>
                    <th>Time Category</th>
                    <th>Status</th>
                    <th>Remarks</th>
                  </tr>
                </thead>

                <tbody role="alert" aria-live="polite" aria-relevant="all">
                  <?php
                  $start_date = new DateTime($from_date);
                  $to_date = new DateTime($to_date);
                  $counter = 0;
                  $sum = 0;
                  $total_exp_hr = 0;
                  $total_hr_work = 0;
                  $total_present = 0;
                  $total_late = 0;
                  $total_absent = 0;
                  $total_holiday=0;
                  $total_leave = 0;
                  $leave = 0;
                  $total_day_off=0;
                  $total_work = [];
                  $expected_hours_work = [];
                  $hour_short_array=[];
                  $overtime_array=[];
                  $attach_hrs=[];
                  $monday_in = "";
                  $monday_out = "";
                  $tuesday_in = "";
                  $tuesday_out = "";
                  $wednesday_in = "";
                  $wednesday_out = "";
                  $thursday_in = "";
                  $thursday_out = "";
                  $friday_in = "";
                  $friday_out = "";
                  $satureday_in = "";
                  $satureday_out = "";
                  $sunday_in = "";
                  $sunday_out = "";
                  $tcat_grace_time = "";
                  $night_shift = "";
                  $exp_tcat_time_in = "";
                  $exp_tcat_time_out = "";
                  $dataLeave=[];
                  ?>

                  <?php for($i = $start_date;$i<=$to_date;$i->modify('+1 day')){ ?>
                    <tr>
                      <td><?php echo ++$counter; ?> </td>
                      <td>{{$i->format('Y-m-d')}} ({{ strtoupper(substr(date("D", strtotime($i->format('Y-m-d'))),0,2)) }})</td>
                      <?php
                      $day = date("D", strtotime($i->format('d-m-Y')));

                      $checktimes = [];
                      $date_attendance = $attendance->where('att_date', $i->format('Y-m-d'));


                      foreach ($date_attendance as $key => $val)
                      {
                        $checktimes[] = $val->checktime;
                      }

                      $date_attach = $attachs->where('attach_date', $i->format('Y-m-d'));

                      $date_leaves = $leaves->where('leave_date', $i->format('Y-m-d'));





                      $timein = "--";
                      $timeout = "--";
                      $hours = "--";
                      $expected_hours = "--";
                      $hours_calculation = "--";
                      $time_category = "--";
                      $status = "Absent";
                      $timecalculation_remarks = "--";
                      $pdate = date('Y-m-d', strtotime('-1 day', strtotime($i->format('Y-m-d'))));
                      $pdate_attendance = $attendance->where('att_date', $pdate);

                      if ($date_attendance->count() > 0)
                      {

                        $time_cat_history = $timcat_his->where('date', '<=', $date_attendance->first()->att_date);

                        if ($time_cat_history->count() == 1)
                        {
                          $tcat_hist = $time_cat_history->first();
                        }
                        elseif ($time_cat_history->count() > 1)
                        {
                          $tcat_hist = $time_cat_history->last();
                        }

                        $night_shift = $tcat_hist->is_night_shift;
                        $exp_tcat_time_in = $tcat_hist->tcat_time_in;
                        $exp_tcat_time_out = $tcat_hist->tcat_time_out;
                        $monday_in = $tcat_hist->monday_in;
                        $monday_out = $tcat_hist->monday_out;
                        $tuesday_in = $tcat_hist->tuesday_in;
                        $tuesday_out = $tcat_hist->tuesday_out;
                        $wednesday_in = $tcat_hist->wednesday_in;
                        $wednesday_out = $tcat_hist->wednesday_out;
                        $thursday_in = $tcat_hist->thursday_in;
                        $thursday_out = $tcat_hist->thursday_out;
                        $friday_in = $tcat_hist->friday_in;
                        $friday_out = $tcat_hist->friday_out;
                        $satureday_in = $tcat_hist->satureday_in;
                        $satureday_out = $tcat_hist->satureday_out;
                        $sunday_in = $tcat_hist->sunday_in;
                        $sunday_out = $tcat_hist->sunday_out;
                        $tcat_grace_time = $tcat_hist->tcat_grace_time;

                        if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                        {
                          $selectedTime = $monday_in;
                          $min = date('i', strtotime($tcat_grace_time));
                          $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_in = date('H:i:s', $endTime);
                          $selectedTime = $monday_out;
                          $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_out = date('H:i:s', $time_out);
                          $expected_time_in = $time_in;
                          $expected_seconds = strtotime($monday_out) - strtotime($monday_in);
                          $expected_hours= timeCategoryTotalHourWork($expected_seconds);

                        }
                        elseif (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                        {

                          $selectedTime = $tuesday_in;
                          $min = date('i', strtotime($tcat_grace_time));
                          $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));

                          $time_in = date('H:i:s', $endTime);
                          $expected_time_in = $time_in;
                          $selectedTime = $tuesday_out;
                          $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_out = date('H:i:s', $time_out);
                          $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                          $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                        }
                        elseif (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                        {

                          $selectedTime = $wednesday_in;
                          $min = date('i', strtotime($tcat_grace_time));
                          $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_in = date('H:i:s', $endTime);

                          $expected_time_in = $time_in;
                          $selectedTime = $wednesday_out;
                          $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_out = date('H:i:s', $time_out);
                          $expected_seconds = strtotime($wednesday_out) - strtotime($wednesday_in);
                          $expected_hours= timeCategoryTotalHourWork($expected_seconds);

                        }
                        elseif (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                        {

                          $selectedTime = $thursday_in;
                          $min = date('i', strtotime($tcat_grace_time));
                          $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_in = date('H:i:s', $endTime);
                          $expected_time_in = $time_in;
                          $selectedTime = $thursday_out;
                          $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_out = date('H:i:s', $time_out);
                          $expected_seconds = strtotime($thursday_out) - strtotime($thursday_in);
                          $expected_hours = date('h:i:s', $expected_seconds);
                        }
                        elseif (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                        {
                          $selectedTime = $friday_in;
                          $min = date('i', strtotime($tcat_grace_time));
                          $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_in = date('H:i:s', $endTime);
                          $expected_time_in = $time_in;
                          $selectedTime = $friday_out;
                          $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_out = date('H:i:s', $time_out);
                          $expected_seconds = strtotime($friday_out) - strtotime($friday_in);
                          $expected_hours = date('h:i:s', $expected_seconds);

                        }
                        elseif (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                        {

                          $selectedTime = $satureday_in;
                          $min = date('i', strtotime($tcat_grace_time));
                          $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_in = date('H:i:s', $endTime);
                          $expected_time_in = $time_in;
                          $selectedTime = $satureday_out;
                          $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_out = date('H:i:s', $time_out);
                          $expected_seconds = strtotime($satureday_out) - strtotime($satureday_in);
                          $expected_hours = date('h:i:s', $expected_seconds);


                        }
                        elseif (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                        {

                          $selectedTime = $sunday_in;
                          $min = date('i', strtotime($tcat_grace_time));
                          $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_in = date('H:i:s', $endTime);
                          $expected_time_in = $time_in;
                          $selectedTime = $sunday_out;
                          $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                          $time_out = date('H:i:s', $time_out);
                          $expected_seconds = strtotime($sunday_out) - strtotime($sunday_in);
                          $expected_hours = date('h:i:s', $expected_seconds);
                        }
                        sort($checktimes);

                        if($night_shift == 1){
                          $date = $i->format('Y-m-d');
                          $pdate = date('Y-m-d', strtotime('-1 day', strtotime($i->format('Y-m-d'))));
                          $ndate = date('Y-m-d', strtotime('+1 day', strtotime($i->format('Y-m-d'))));
                          $pdate_attendance = $attendance->where('att_date', $pdate);
                          $ndate_attendance = $attendance->where('att_date', $ndate);
                          $exp_tcat_time_in = $tcat_hist->tcat_time_in;
                          $exp_tcat_time_out = $tcat_hist->tcat_time_out;
                          $monday_in = $tcat_hist->monday_in;
                          $monday_out = $tcat_hist->monday_out;
                          $tuesday_in = $tcat_hist->tuesday_in;
                          $tuesday_out = $tcat_hist->tuesday_out;
                          $wednesday_in = $tcat_hist->wednesday_in;
                          $wednesday_out = $tcat_hist->wednesday_out;
                          $thursday_in = $tcat_hist->thursday_in;
                          $thursday_out = $tcat_hist->thursday_out;
                          $friday_in = $tcat_hist->friday_in;
                          $friday_out = $tcat_hist->friday_out;
                          $satureday_in = $tcat_hist->satureday_in;
                          $satureday_out = $tcat_hist->satureday_out;
                          $sunday_in = $tcat_hist->sunday_in;
                          $sunday_out = $tcat_hist->sunday_out;
                          $day_in_pointer = strtolower(date("l",strtotime($date)))."_in";
                          if($day_in_pointer == 'saturday_in'){
                            $day_in_pointer = "satureday_in";
                          }
                          $nchecktimes = [];
                          foreach ($ndate_attendance as $key => $val)
                          {
                            $nchecktimes[] = $val->checktime;
                          }
                          sort($nchecktimes);
                          $timein = "--";
                          if (count($checktimes) > 0)
                          {
                            $ctimein = $checktimes[count($checktimes) - 1];
                            $timein_limit = date($date." H:i:s", strtotime('-120 minutes', strtotime($date . " " . $time_in)));
                            if ($ctimein > $timein_limit)
                            {
                              $timein = $ctimein;
                            }
                          }

                          if (count($nchecktimes) > 0)
                          {
                            sort($nchecktimes);
                            $timeout_limit = date($ndate." H:i:s", strtotime('+120 minutes', strtotime($ndate . " " . $time_out)));
                            if ($nchecktimes[0] < $timeout_limit)
                            {
                              $timeout = $nchecktimes[0];

                            }
                          }
                          if(is_null($tcat_hist->$day_in_pointer)){
                            if(count($checktimes) == 1){
                              $timein = $checktimes[0];
                              $timeout = "--";
                            }else if(count($checktimes)>1){
                              $timein = $checktimes[0];
                              $timeout = $checktimes[count($checktimes)-1];
                            }
                          }

                          if(date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                            $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                          elseif(date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                            $time_category = '[ ' . $tuesday_in . '-' . $tuesday_out . ' ]';

                          elseif(date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                            $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';

                          elseif(date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                            $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';

                          elseif(date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                            $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';

                          elseif(date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                            $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';

                          elseif(date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                            $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';


                          $checktimes = [];
                          if (isset($timein))
                          {
                            $checktimes[] = $timein;
                            $timein = $timein;
                          }
                          if (isset($timeout))
                          {
                            $checktimes[] = $timeout;
                          }
                        }
                        else
                        {
                          $timein = $checktimes[0];
                          $timeout = $checktimes[count($checktimes) - 1];
                        }

                        if ($timein == "--" || $timeout == "--")
                        {
                          $seonds = 0;
                        }
                        else
                        {
                          $seonds = strtotime($timeout) - strtotime($timein);
                        }

                        $date = $date_attendance->first()->att_date;
                        $hours = date("H:i:s", $seonds);
                        $total_work[] = $hours;
                        $expected_hours_work[] = $expected_hours;
                        $checktime = date("H:i", strtotime($date_attendance->first()->checktime));
                        if (strtotime($hours) > strtotime($expected_hours))
                        {
                          $timecalculation = strtotime($hours) - strtotime($expected_hours);
                        }
                        else
                        {
                          $timecalculation = strtotime($expected_hours) - strtotime($hours);
                        }
                        $timecalculation_remarks = date('H:i:s', $timecalculation);
                        $expected_time = date("H:i:s", strtotime($timein));
                        $sum += $expected_seconds;
                        if ($selectedTime == null)
                        {
                          $time_in = "--";
                          $timein = "--";
                          $hours = "--";
                          $expected_hours = "--";
                          $expected_time = "--";

                        }
                        else
                        {

                          $attach = $attachs->where('attach_date', $i->format('Y-m-d'));


                          $attach_date = "";
                          if ($attach->count() > 0)
                          {
                            $attach_date = $attach->first()->attach_date;

                          }

                          if ($expected_time <= $time_in && $expected_time!="00:00:00")
                          {

                            $total_exp_hr += strtotime($expected_hours);
                            $total_hr_work += strtotime($hours);
                            $total_present += 1;
                            if ($date_attendance->first()->mark_attendance == 1)
                            {
                              $status = 'Present *';
                            }
                            else
                            {
                              $status = 'Present';
                            }

                            $flag = 1;

                            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                            {

                              $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                            {

                              $time_category = '[ ' . $tuesday_in . '-' . $tuesday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                            {

                              $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                            {

                              $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                            {

                              $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                            {

                              $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                            {

                              $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';
                            }

                          }elseif ($expected_time > $time_in ){

                            $expect_time_check = strtotime($expected_hours);
                            $total_exp_hr += strtotime($expected_hours);

                            $total_hr_work += strtotime($hours);
                            $hour_time_check = strtotime($hours);
                            $diff = $expect_time_check - $hour_time_check;

                            $hours_calculation = date('H:i:s', $diff) . ' time late';

                            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                            {

                              $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                            {

                              $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                            {

                              $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                            {

                              $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                            {

                              $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                            {

                              $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                            }
                            else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                            {

                              $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';

                            }
                            if ($date_attendance->first()->mark_attendance == 1)
                            {
                              $status = 'Late *';
                            }
                            else
                            {

                              $status = 'Late';

                            }
                            $total_late += 1;

                          }
                        }
                      }else{
                        if($pdate_attendance->count()>0){
                          $time_cat_history = $timcat_his->where('date', '<=', $pdate_attendance->first()
                            ->att_date);

                          if ($time_cat_history->count() == 1)
                          {
                            $tcat_hist = $time_cat_history->first();
                          }
                          elseif ($time_cat_history->count() > 1)
                          {
                            $tcat_hist = $time_cat_history->last();
                          }

                          $night_shift = $tcat_hist->is_night_shift;
                          $exp_tcat_time_in = $tcat_hist->tcat_time_in;
                          $exp_tcat_time_out = $tcat_hist->tcat_time_out;
                          $monday_in = $tcat_hist->monday_in;
                          $monday_out = $tcat_hist->monday_out;
                          $tuesday_in = $tcat_hist->tuesday_in;
                          $tuesday_out = $tcat_hist->tuesday_out;
                          $wednesday_in = $tcat_hist->wednesday_in;
                          $wednesday_out = $tcat_hist->wednesday_out;
                          $thursday_in = $tcat_hist->thursday_in;
                          $thursday_out = $tcat_hist->thursday_out;
                          $friday_in = $tcat_hist->friday_in;
                          $friday_out = $tcat_hist->friday_out;
                          $satureday_in = $tcat_hist->satureday_in;
                          $satureday_out = $tcat_hist->satureday_out;
                          $sunday_in = $tcat_hist->sunday_in;
                          $sunday_out = $tcat_hist->sunday_out;
                          $tcat_grace_time = $tcat_hist->tcat_grace_time;

                          if (date('l', strtotime($i->format('Y-m-d'))) == "Monday"){
                            $selectedTime = $monday_in;
                            $min = date('i', strtotime($tcat_grace_time));
                            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_in = date('H:i:s', $endTime);
                            $selectedTime = $monday_out;
                            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_out = date('H:i:s', $time_out);
                            $expected_time_in = $time_in;
                            $expected_seconds = strtotime($monday_out) - strtotime($monday_in);
                            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                          }
                          elseif (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday"){

                            $selectedTime = $tuesday_in;
                            $min = date('i', strtotime($tcat_grace_time));
                            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));

                            $time_in = date('H:i:s', $endTime);
                            $expected_time_in = $time_in;
                            $selectedTime = $tuesday_out;
                            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_out = date('H:i:s', $time_out);
                            $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                          }
                          elseif (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday"){

                            $selectedTime = $wednesday_in;
                            $min = date('i', strtotime($tcat_grace_time));
                            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_in = date('H:i:s', $endTime);

                            $expected_time_in = $time_in;
                            $selectedTime = $wednesday_out;
                            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_out = date('H:i:s', $time_out);
                            $expected_seconds = strtotime($wednesday_in) - strtotime($wednesday_in);
                            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                          }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Thursday"){

                            $selectedTime = $thursday_in;
                            $min = date('i', strtotime($tcat_grace_time));
                            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_in = date('H:i:s', $endTime);
                            $expected_time_in = $time_in;
                            $selectedTime = $thursday_out;
                            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_out = date('H:i:s', $time_out);
                            $expected_seconds = strtotime($thursday_out) - strtotime($thursday_in);

                            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                          }elseif(date('l', strtotime($i->format('Y-m-d'))) == "Friday"){
                            $selectedTime = $friday_in;
                            $min = date('i', strtotime($tcat_grace_time));
                            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_in = date('H:i:s', $endTime);
                            $expected_time_in = $time_in;
                            $selectedTime = $friday_out;
                            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_out = date('H:i:s', $time_out);
                            $expected_seconds = strtotime($friday_out) - strtotime($friday_in);
                            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                          }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Saturday"){
                            $selectedTime = $satureday_in;
                            $min = date('i', strtotime($tcat_grace_time));
                            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_in = date('H:i:s', $endTime);
                            $expected_time_in = $time_in;
                            $selectedTime = $satureday_out;
                            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_out = date('H:i:s', $time_out);
                            $expected_seconds = strtotime($satureday_out) - strtotime($satureday_in);
                            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                          }elseif (date('l', strtotime($i->format('Y-m-d'))) == "Sunday"){
                            $selectedTime = $sunday_in;
                            $min = date('i', strtotime($tcat_grace_time));
                            $endTime = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_in = date('H:i:s', $endTime);
                            $expected_time_in = $time_in;
                            $selectedTime = $sunday_out;
                            $time_out = strtotime("+" . $min . " minutes", strtotime($selectedTime));
                            $time_out = date('H:i:s', $time_out);
                            $expected_seconds = strtotime($sunday_out) - strtotime($sunday_in);
                            $expected_hours= timeCategoryTotalHourWork($expected_seconds);
                          }

                          sort($checktimes);
                          if ($night_shift == 1)
                          {
                            $pdate = date('Y-m-d', strtotime('-1 day', strtotime($i->format('Y-m-d'))));
                            $ndate = date('Y-m-d', strtotime('+1 day', strtotime($i->format('Y-m-d'))));
                            $pdate_attendance = $attendance->where('att_date', $pdate);
                            $ndate_attendance = $attendance->where('att_date', $ndate);

                            $nchecktimes = [];
                            foreach ($ndate_attendance as $key => $val)
                            {
                              $nchecktimes[] = $val->checktime;
                            }
                            sort($checktimes);
                            $timein = "--";
                            if (count($checktimes) > 0)
                            {
                              $ctimein = $checktimes[count($checktimes) - 1];
                              $timein_limit = date("Y-m-d H:i:s", strtotime('-120 minutes', strtotime($date . " " . $time_in)));
                              if ($ctimein > $timein_limit)
                              {
                                $timein = $ctimein;
                              }
                            }
                            if (count($nchecktimes) > 0)
                            {
                              sort($nchecktimes);
                              $timeout_limit = date("Y-m-d H:i:s", strtotime('+120 minutes', strtotime($ndate . " " . $time_out)));
                              if ($nchecktimes[0] < $timeout_limit)
                              {
                                $timeout = $nchecktimes[0];

                              }
                            }

                            $checktimes = [];
                            if (isset($timein))
                            {
                              $checktimes[] = $timein;
                              $timein = $timein;
                            }
                            if (isset($timeout))
                            {
                              $checktimes[] = $timeout;
                            }
                          }
                          if ($timein == "--" || $timeout == "--")
                          {
                            $seonds = 0;
                          }
                          else
                          {
                            $seonds = strtotime($timeout) - strtotime($timein);
                          }

                          $date = $pdate_attendance->first()->att_date;

                          $hours = date("H:i:s", $seonds);

                          if($status=="Absent"){
                            $total_work[] = '00:00';

                            $expected_hours_work[] = '00:00';

                          }else{
                            $total_work[] = $hours;

                            $expected_hours_work[] = $expected_hours;
                          }

                          $checktime = date("H:i", strtotime($pdate_attendance->first()
                            ->checktime));
                          if (strtotime($hours) > strtotime($expected_hours))
                          {
                            $timecalculation = strtotime($hours) - strtotime($expected_hours);
                          }
                          else
                          {
                            $timecalculation = strtotime($expected_hours) - strtotime($hours);
                          }
                          $timecalculation_remarks = date('H:i:s', $timecalculation);
                          $expected_time = date("H:i:s", strtotime($timein));
                          $sum += $expected_seconds;


                          if($selectedTime == null || $status=="Absent" )
                          {

                            $time_in = "--";
                            $timein = "--";
                            $hours = "--";
                            $expected_hours = "--";

                            $expected_time = "--";
                          }else{

                            $attach = $attachs->where('attach_date', $i->format('Y-m-d'));


                            $attach_date = "";
                            if ($attach->count() > 0)
                            {
                              $attach_date = $attach->first()->attach_date;

                            }
                            if ($expected_time <= $time_in )
                            {

                              $total_exp_hr += strtotime($expected_hours);
                              $total_hr_work += strtotime($hours);
                              $total_present += 1;
                              if ($date_attendance->first()->mark_attendance == 1)
                              {
                                $status = 'Present *';
                              }
                              else
                              {
                                $status = 'Present';
                              }

                              $flag = 1;

                              if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                              {

                                $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                              }
                              else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                              {

                                $time_category = '[ ' . $tuesday_in . '-' . $tuesday_out . ' ]';
                              }
                              else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                              {

                                $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                              }
                              else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                              {

                                $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                              }
                              else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                              {

                                $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                              }
                              else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                              {

                                $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                              }
                              else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                              {

                                $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';
                              }
                            }elseif ($expected_time > $time_in){
                              $expect_time_check = strtotime($expected_hours);
                              $total_exp_hr += strtotime($expected_hours);


                              $total_hr_work += strtotime($hours);
                              $hour_time_check = strtotime($hours);
                              $diff = $expect_time_check - $hour_time_check;

                              $hours_calculation = date('H:i:s', $diff) . ' time late';

                              if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                              {

                                $time_category = '[ ' . $monday_in . '-' . $monday_out . ' ]';
                              }
                              else if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                              {

                                $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                              }
                              else if (date('l', strtotime($i->format('Y-m-d'))) == "Wednesday")
                              {

                                $time_category = '[ ' . $wednesday_in . '-' . $wednesday_out . ' ]';
                              }
                              else if (date('l', strtotime($i->format('Y-m-d'))) == "Thursday")
                              {

                                $time_category = '[ ' . $thursday_in . '-' . $thursday_out . ' ]';
                              }
                              else if (date('l', strtotime($i->format('Y-m-d'))) == "Friday")
                              {

                                $time_category = '[ ' . $friday_in . '-' . $friday_out . ' ]';
                              }
                              else if (date('l', strtotime($i->format('Y-m-d'))) == "Saturday")
                              {

                                $time_category = '[ ' . $satureday_in . '-' . $satureday_out . ' ]';
                              }
                              else if (date('l', strtotime($i->format('Y-m-d'))) == "Sunday")
                              {

                                $time_category = '[ ' . $sunday_in . '-' . $sunday_out . ' ]';
                              }
                              if ($date_attendance->first()->mark_attendance == 1)
                              {
                                $status = 'Late *';
                              }
                              else
                              {

                                $status = 'Late';

                              }
                              $total_late += 1;
                            }
                          }
                        }
                        else
                        {}

                        if($date_attach->count()>0){
                          foreach ($date_attach as $key => $val)
                          {

                            $time_cat_history= $timcat_his->where('date', '<', $val->attach_date);

                            if ($time_cat_history->count() == 1)
                            {
                              $tcat_hist = $time_cat_history->first();
                            }
                            elseif ($time_cat_history->count() > 1)
                            {
                              $tcat_hist = $time_cat_history->last();
                            }
                            $monday_in = $tcat_hist->monday_in;
                            $monday_out = $tcat_hist->monday_out;
                            $tuesday_in = $tcat_hist->tuesday_in;
                            $tuesday_out = $tcat_hist->tuesday_out;
                            $wednesday_in = $tcat_hist->wednesday_in;
                            $wednesday_out = $tcat_hist->wednesday_out;
                            $thursday_in = $tcat_hist->thursday_in;
                            $thursday_out = $tcat_hist->thursday_out;
                            $friday_in = $tcat_hist->friday_in;
                            $friday_out = $tcat_hist->friday_out;
                            $satureday_in = $tcat_hist->satureday_in;
                            $satureday_out = $tcat_hist->satureday_out;
                            $sunday_in = $tcat_hist->sunday_in;
                            $sunday_out = $tcat_hist->sunday_out;
                            $tcat_grace_time = $tcat_hist->tcat_grace_time;


                            if (date('l', strtotime($i->format('Y-m-d'))) == "Monday")
                            {
                              $expected_seconds = strtotime($monday_out) - strtotime($monday_in);
                              $attach_hours= timeCategoryTotalHourWork($expected_seconds);

                            }
                            if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                            {
                              $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                              $attach_hours= timeCategoryTotalHourWork($expected_seconds);

                            }
                            if (date('l', strtotime($i->format('Y-m-d'))) == "Tuesday")
                            {
                              $expected_seconds = strtotime($tuesday_out) - strtotime($tuesday_in);
                              $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                            }
                            if(date('l', strtotime($i->format('Y-m-d'))) == "Wednesday"){
                              $expected_seconds = strtotime($wednesday_out) - strtotime($wednesday_in);
                              $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                            }
                            if(date('l', strtotime($i->format('Y-m-d'))) == "Thursday"){
                              $expected_seconds = strtotime($thursday_out) - strtotime($thursday_in);
                              $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                            }
                            if( date('l', strtotime($i->format('Y-m-d'))) == "Friday"){
                              $expected_seconds = strtotime($friday_out) - strtotime($friday_in);
                              $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                            }
                            if( date('l', strtotime($i->format('Y-m-d'))) == "Saturday"){
                              $expected_seconds = strtotime($satureday_in) - strtotime($satureday_in);
                              $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                            }
                            if( date('l', strtotime($i->format('Y-m-d'))) == "Sunday"){
                              $expected_seconds = strtotime($sunday_out) - strtotime($sunday_in);
                              $attach_hours= timeCategoryTotalHourWork($expected_seconds);
                            }
                            $attach_hrs[]= $attach_hours;
                            $status = 'Attach';
                          }
                        }elseif ($date_leaves->count() > 0){
                          foreach ($date_leaves as $key => $val)
                          {
                            $dataLeave[]=$val->leave_date;
                            if ($val->leave_type == 2)
                            {
                              $status = $val->title;
                            }

                            else
                            {
                              $status = 'Short Leave';
                            }

                            $total_leave +=1;
                            $leave_type = $val->type;
                            $flag = 2;

                          }
                        }
                      }

                      for($j=0;$j<=5;$j++){
                        if(count($checktimes)>$j){
                          $devic_id="";
                          foreach($date_attendance->where('checktime',$checktimes[$j]) as $d){
                            $devic_id= $d->dev_id;
                          }?>

                          <td><a  data-toggle="tooltip" data-placement="top" title="{{$devic_id}}">{{  date('H:i:s', strtotime($checktimes[$j])) }}</a></td>
                          <a data-toggle="modal" data-target="#exampleModal"></a>
                          <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel"></h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  ...
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button type="button" class="btn btn-primary">Save changes</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        <?php }else{?>
                          <td>--</td>
                        <?php }
                      } ?>

                      <td>{{ $hours }}</td>
                      <td>{{ $expected_hours}}</td>
                      <td>{{$time_category}}</td>

                      <?php if ($status == 'Absent'){
                        $expected_hours="--";
                        $hours="--";
                        $monday_off = "";
                        $tuesday_off = "";
                        $wednesday_off = "";
                        $thursday_off = "";
                        $friday_off = "";
                        $satureday_off = "";
                        $sunday_off = "";
                        if ($day_off->count() > 0)
                        {

                          $day_off_base_on_time_cat_his = $day_off->where('join_date', '<=', $i->format('Y-m-d'));
                          if ($day_off_base_on_time_cat_his->count() == 0)
                          {
                            $monday_off = "emp_not_register_in_date";
                            $tuesday_off = "emp_not_register_in_date";
                            $wednesday_off = "emp_not_register_in_date";
                            $thursday_off = "emp_not_register_in_date";
                            $friday_off = "emp_not_register_in_date";
                            $satureday_off = "emp_not_register_in_date";
                            $sunday_off = "emp_not_register_in_date";
                          }
                          elseif ($day_off_base_on_time_cat_his->count() == 1)
                          {

                            $monday_off = $day_off_base_on_time_cat_his->first()->tc_monday_in;
                            $tuesday_off = $day_off_base_on_time_cat_his->first()->tc_tuesday_in;
                            $wednesday_off = $day_off_base_on_time_cat_his->first()->tc_wednesday_in;
                            $thursday_off = $day_off_base_on_time_cat_his->first()->tc_thursday_in;
                            $friday_off = $day_off_base_on_time_cat_his->first()->tc_friday_in;

                            $satureday_off = $day_off_base_on_time_cat_his->first()->tc_saturday_in;
                            $sunday_off = $day_off_base_on_time_cat_his->first()->tc_sunday_in;

                          }
                          elseif ($day_off_base_on_time_cat_his->count() > 1)
                          {
                            $monday_off = $day_off_base_on_time_cat_his->last()->tc_monday_in;
                            $tuesday_off = $day_off_base_on_time_cat_his->last()->tc_tuesday_in;
                            $wednesday_off = $day_off_base_on_time_cat_his->last()->tc_wednesday_in;
                            $thursday_off = $day_off_base_on_time_cat_his->last()->tc_thursday_in;
                            $friday_off = $day_off_base_on_time_cat_his->last()->tc_friday_in;
                            $satureday_off = $day_off_base_on_time_cat_his->last()->tc_saturday_in;
                            $sunday_off = $day_off_base_on_time_cat_his->last()->tc_sunday_in;
                          }
                          $check_dedicated_dayoff = $dedicate_off_day->where('off_day_date', $i->format('Y-m-d'));
                          if ($check_dedicated_dayoff->count() > 0)
                          {
                            $day_off_day = strtotime($check_dedicated_dayoff->first()
                              ->off_day_date);
                            $check_day_off_day = date('l', $day_off_day);

                            if ($check_day_off_day == 'Monday')
                            {
                              $monday_off = "";
                            }
                            if ($check_day_off_day == 'Tuesday')
                            {
                              $tuesday_off = "";
                            }
                            if ($check_day_off_day == 'Wednesday')
                            {
                              $wednesday_off = "";
                            }
                            if ($check_day_off_day == 'Thursday')
                            {
                              $thursday_off = "";
                            }
                            if ($check_day_off_day == 'Friday')
                            {
                              $friday_off = "";

                            }
                            if ($check_day_off_day == 'Saturday')
                            {
                              $satureday_off = "";
                            }
                            if ($check_day_off_day == 'Sunday')
                            {
                              $sunday_off = "";
                            }

                          }
                          else
                          {
                            $day_off_day = strtotime($i->format('Y-m-d'));

                          }

                          if (date('l', $day_off_day) == "Monday")
                          {

                            if (empty($monday_off))
                            {
                              $total_day_off += 1;
                              echo "<td style='background-color:pink'>Off Day</td>";
                            }
                            else
                            {
                              $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                              ->first();
                              if ($check_emp_holiday == null)
                              {

                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                  $total_absent += 1;
                                }
                                echo '<td style="background-color:red;color:white">' . checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) . '</td>';
                              }
                              else
                              {
                                $total_holiday += 1;
                                echo '<td style="background-color:#29252799;color:white">' . $check_emp_holiday->title . '</td>';
                              }
                            }

                          }
                          if (date('l', $day_off_day) == "Tuesday")
                          {
                            if (empty($tuesday_off))
                            {
                              $total_day_off += 1;
                              echo "<td style='background-color:pink'>Off Day</td>";
                            }
                            else
                            {
                              $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                              ->first();
                              if ($check_emp_holiday == null)
                              {

                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                  $total_absent += 1;
                                }
                                echo '<td style="background-color:red;color:white">' . checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date,$emp_min_transaction->date) . '</td>';
                              }
                              else
                              {
                                $total_holiday += 1;
                                echo '<td style="background-color:#29252799;color:white">' . $check_emp_holiday->title . '</td>';
                              }
                            }

                          }
                          if (date('l', $day_off_day) == "Wednesday")
                          {

                            if (empty($wednesday_off))
                            {
                              $total_day_off += 1;
                              echo "<td style='background-color:pink'>Off Day</td>";
                            }
                            else
                            {
                              $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                              ->first();
                              if ($check_emp_holiday == null)
                              {
                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                  $total_absent += 1;
                                }
                                echo '<td style="background-color:red;color:white">' . checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date,$emp_min_transaction->date) . '</td>';
                              }
                              else
                              {
                                $total_holiday += 1;
                                echo '<td style="background-color:#29252799;color:white">' . $check_emp_holiday->title . '</td>';
                              }
                            }

                          }
                          if (date('l', $day_off_day) == "Thursday")
                          {

                            if (empty($thursday_off))
                            {
                              $total_day_off += 1;
                              echo "<td style='background-color:pink'>Off Day</td>";
                            }
                            else
                            {
                              $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                              ->first();
                              if ($check_emp_holiday == null)
                              {
                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                  $total_absent += 1;
                                }
                                echo '<td style="background-color:red;color:white">' . checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date,$emp_min_transaction->date) . '</td>';
                              }
                              else
                              {
                                $total_holiday += 1;
                                echo '<td style="background-color:#29252799;color:white">' . $check_emp_holiday->title . '</td>';
                              }
                            }

                          }

                          if (date('l', $day_off_day) == "Friday")
                          {

                            if (empty($friday_off))
                            {

                              $total_day_off += 1;
                              echo "<td style='background-color:pink'>Off Day </td>";
                            }
                            else
                            {
                              $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                              ->first();
                              if ($check_emp_holiday == null)
                              {
                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                  $total_absent += 1;
                                }
                                echo '<td style="background-color:red;color:white">' . checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date,$emp_min_transaction->date) . '</td>';
                              }
                              else
                              {
                                $total_holiday += 1;
                                echo '<td style="background-color:#29252799;color:white">' . $check_emp_holiday->title . '</td>';
                              }
                            }
                          }
                          if (date('l', $day_off_day) == "Saturday")
                          {

                            if (empty($satureday_off))
                            {
                              $total_day_off += 1;
                              echo "<td style='background-color:pink'>Off Day</td>";
                            }
                            else
                            {
                              $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                              ->first();
                              if ($check_emp_holiday == null)
                              {

                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                  $total_absent += 1;
                                }
                                echo '<td style="background-color:red;color:white">' . checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date,$emp_min_transaction->date) . '</td>';
                              }
                              else
                              {
                                $total_holiday += 1;
                                echo '<td style="background-color:#29252799;color:white">' . $check_emp_holiday->title . '</td>';
                              }
                            }

                          }
                          if (date('l', $day_off_day) == "Sunday")
                          {


                            if (empty($sunday_off))
                            {

                              $total_day_off += 1;
                              echo "<td style='background-color:pink'>Off Day</td>";
                            }
                            else
                            {
                              $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                              ->first();
                              if ($check_emp_holiday == null)
                              {
                                if (checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date) == 'absent')
                                {
                                  $total_absent += 1;
                                }
                                echo '<td style="background-color:red;color:white">' . checkAbsent($i->format('Y-m-d') , $user_info_query[0]->reg_date,$emp_min_transaction->date) . '</td>';
                              }
                              else
                              {
                                $total_holiday += 1;

                                echo '<td style="background-color:#29252799;color:white">' . $check_emp_holiday->title . '</td>';
                              }
                            }

                          }

                        }
                        else
                        {

                          $in_var = strtolower(date('l', strtotime($i->format('Y-m-d'))))."_in";
                          if($in_var == "saturday_in"){
                            $in_var = "satureday_in";
                          }
                          $check_emp_holiday = $holidays->where('holiday_date', $i->format('Y-m-d'))
                          ->first();
                          if ($check_emp_holiday == null)
                          {

                            if(is_null($$in_var)){
                              echo '<td style="background-color:#29252799;color:white">OFF Day</td>';
                              $total_day_off += 1;
                            }else{
                              echo '<td style="background-color:red;color:white">' . $status . '</td>';
                              $total_absent += 1;
                            }


                          }else
                          {
                            $total_holiday += 1;
                            echo '<td style="background-color:#29252799;color:white">' . $check_emp_holiday->title . '</td>';
                          }
                        }
                      }elseif ($status == 'Late'){

                        if (!empty($attach_date))
                        {
                          echo '<td style="background-color:yellow;color:#000">Late + attach</td>';
                        }
                        else
                        {
                          echo '<td style="background-color:yellow;color:#000">' . $status . '</td>';
                        }

                        ?>

                        <?php
                      }elseif ($status == 'Late *'){

                        if (!empty($attach_date))
                        {
                          echo '<td style="background-color:#f35958;color:white">Late * + attach</td>';
                        }
                        else
                        {
                          echo '<td style="background-color:yellow;color:#000">' . $status . '</td>';
                        }
                        ?>


                        <?php
                      }elseif ($status == 'Present'){ ?>

                        <?php
                        if (!empty($attach_date))
                        {
                          echo '<td style="background-color:#0aa699;color: white">Present + attach</td>';
                        }
                        else
                        {
                          echo '<td style="background-color:#0aa699;color: white">' . $status . '</td>';
                        }

                        ?>

                        <?php
                      }elseif ($status == 'Present *'){
                        if (!empty($attach_date))
                        {
                          echo '<td style="background-color:#0aa699;color: white">Present * + attach</td>';
                        }
                        else
                        {
                          echo '<td style="background-color:#0aa699;color: white">' . $status . '</td>';

                        }
                      }elseif ($status == 'Attach'){

                        echo '<td style="background-color:#04523d99;color:white">'.$status.'</td>';
                      }elseif ($flag == 2){

                        $leave += 1;
                        echo '<td style="background-color:#1a033a99;color: white">' . $status . '</td>';
                      }

                      if(sizeof($checktimes) == 1){
                        if($selectedTime==null){
                          echo '<td></td>';
                        }else{
                          echo '<td style="color:white; background-color: red;">Missing Check Out</td>';
                        }
                      }else{
                        if(strtotime($hours)>strtotime($expected_hours)){
                          $overtime_array[]=$timecalculation_remarks;
                          echo '<td>'.$timecalculation_remarks.' o/t </td>';
                        }elseif(strtotime($hours) < strtotime($expected_hours)){
                          $hour_short_array[]=$timecalculation_remarks;
                          echo '<td>'.$timecalculation_remarks.'  h/s</td>';
                        }
                      }?>
                    </tr>
                  <?php }
                  $sum4 = strtotime('00:00:00');

                  $totaltime4 = 0;

                  foreach ($overtime_array as $element)
                  {

                    $timeinsec = strtotime($element) - $sum4;


                    $totaltime4 = $totaltime4 + $timeinsec;

                  }
                  $h_overtime = intval($totaltime4 / 3600);
                  $totaltime4 = $totaltime4 - ($h_overtime * 3600);
                  $m_overtime = intval($totaltime4 / 60);


                  $s_overtime = $totaltime4 - ($m_overtime * 60);

                  $total_hours_overtime_sum = "$h_overtime:$m_overtime:$s_overtime";

                  $attach_sum = strtotime('00:00:00');

                  $att_totaltime = 0;

                  foreach ($attach_hrs as $element)
                  {


                    $timeinsec = strtotime($element) - $attach_sum;


                    $att_totaltime = $att_totaltime + $timeinsec;
                  }
                  $att_h = intval($att_totaltime / 3600);
                  $att_totaltime = $att_totaltime - ($att_h * 3600);
                  $att_m = intval($att_totaltime / 60);
                  $total_attach_hours = "$att_h:$att_m";

                  $sum3 = strtotime('00:00:00');
                  $totaltime3 = 0;
                  foreach ($hour_short_array as $element)
                  {
                    $timeinsec = strtotime($element) - $sum3;

                    $totaltime3 = $totaltime3 + $timeinsec;
                  }
                  $h_short = intval($totaltime3 / 3600);
                  $totaltime3 = $totaltime3 - ($h_short * 3600);
                  $m_short = intval($totaltime3 / 60);
                  $s_short = $totaltime3 - ($m_short * 60);

                  $total_hours_short_sum = "$h_short:$m_short:$s_short";

                  $sum = strtotime('00:00:00');

                  $totaltime = 0;

                  foreach ($total_work as $element)
                  {
                    $timeinsec = strtotime($element) - $sum;
                    $totaltime = $totaltime + $timeinsec;
                  }

                  $h = intval($totaltime / 3600);
                  $totaltime = $totaltime - ($h * 3600);
                  $m = intval($totaltime / 60);
                  $s = $totaltime - ($m * 60);
                  $total_hours = "$h:$m";
                  $sum1 = strtotime('00:00:00');
                  $totaltime1 = 0;
                  foreach ($expected_hours_work as $element1)
                  {
                    $timeinsec1 = strtotime($element1) - $sum1;
                    $totaltime1 = $totaltime1 + $timeinsec1;
                  }

                  $hh = intval($totaltime1 / 3600);
                  $totaltime1 = $totaltime1 - ($hh * 3600);
                  $mm = intval($totaltime1 / 60);
                  $ss = $totaltime1 - ($mm * 60);
                  $exp_hours = "$hh:$mm";
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <br><br>
  <div class="row-fluid">
    <div class="col-md-12">
      <div class="grid simple" id="indivi_summary">
        <div class="grid-title no-border">
          <h4>Summary</h4>
        </div>
        <div class="grid-body no-border">

          <table class="table">
            <thead>

              <tr>
                <th>Total Expected Hours</th>
                <th><?php echo $exp_hours; ?></th>
              </tr>
              <tr>
                <th>Total Hours Works</th>
                <th><?php echo $total_hours; ?></th>
              </tr>
              <tr>
                <th><u style="color:green">Present </u></th>
                <th>{{$total_present + $total_late }}</th>
              </tr>
              <tr>
                <th><u>Hours Short </u></th>
                <th>{{$total_hours_short_sum}}</th>
              </tr>

              <tr>
                <th><u>Hour Overtime </u></th>
                <th>{{$total_hours_overtime_sum}}</th>
              </tr>
              <tr>
                <th><u>Total Holiday </u></th>
                <th>{{$total_holiday}}</th>
              </tr>
              <tr>
                <th><u>Attach</u></th>
                <th>{{$total_attach_hours}}</th>
              </tr>


              <tr>
                <th><u> Day Off </u></th>
                <th>{{$total_day_off}}</th>
              </tr>

              <tr>
                <th><u style="color:red"> Late </u></th>
                <th>{{$total_late}}</th>
              </tr>
              <tr>
                <th><u style="color:#f6b9"> Absent </u></th>
                <th>{{$total_absent}}</th>
              </tr>
              <tr>
                <th><u style="color:#a5a2a499">Leave</u></th>
                <th>{{$leave}}</th>
              </tr>


            </thead>

          </table>
        </div>
      </div>
    </div>
  </div>
<?php }else{?>
  <div class="row">
    <div class="col-md-12">
      <div class="grid simple ">
        <div class="grid-title no-border">
          <h4>No Employee is Selected</h4>
        </div>
        <div class="grid-body no-border">
          <h5>Please Select an Employee To Generate Report</h5>
        </div>
      </div>
    </div>
  </div>
<?php }?>
@include('include.important')
@endsection
