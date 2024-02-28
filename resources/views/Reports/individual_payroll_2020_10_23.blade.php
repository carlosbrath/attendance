
@extends('layout.layout')
@section('title','Individual Payrol Report')
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
        .missing_out{
        	font-size: 9px;
        }
        </style>
<div class="row">
	<div class="col-md-12">
		<div class="grid simple form-grid">
			<div class="grid-body no-border">
				<form class="form-no-horizontal-spacing" method="post" action="{{route('individual_payroll')}}" id="form-condensed" novalidate="novalidate">
					@csrf
					<div class="row column-seperation">
						<div class="col-md-12">
							<h4></h4>
                <div class="row form-row">
						<div class="col-md-4" style="width:379px;">
						   <div class="grid simple">
							<div class="grid-title no-border" style="padding-bottom: 0px">
							</div>	
						<div class="row">
							<div class="col-md-12">
							<br>
							<label class="form-label"  style="font-size:15px">Department</label>
									<select name="deparment_id" id="indivi_department_id"  class="select2 form-control">
										<option value="">
										 Please Select...
										</option>
										@foreach($department as $d)
									      <option name=""  value="{{$d->id}}">{{$d->name}}</option>
									    @endforeach
									  </select>
										 @if($errors->any())
									   <p style="color:red">{{$errors->first('deparment_id','Department field is required')}}
									   </p>
									  @endif
							</div>
						</div>
					</div>
					</div>
					  <div class="col-md-4">
				        <div class="loader" id="preloader" style="display:none;">	  
				  </div>
			     </div>	
				</div>

				 <div class="row form-row">
						<div class="col-md-4" style="width:379px;">
						   <div class="grid simple">
							<div class="grid-title no-border">
							</div>
						
						<div class="row">
							<div class="col-md-12">
							<br>
							<label class="form-label"  style="font-size:15px">Employee</label>
								<select name="user_id" id="source1" style="width:100%">
									<option value="">Please Select Employee</option>
									
								</select>
								<p id="emp_msg" style="color: red"></p>
							</div>
						</div>
					</div>
					</div>
				</div>
							
							<div class="row form-row">
								<div class="col-md-12">
									<label class="form-label"  style="font-size:15px ">From Date</label>
									<div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
										<input type="text" autocomplete="off" name="from_date" placeholder="From Date" class="form-control">
										<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
									@if($errors->any())
						            <p style="color:red">{{$errors->first('from_date')}}</p>
						            @endif  
									</div>
								</div>
							</div>
						  	<div class="row form-row">
								<div class="col-md-12">
									<label class="form-label"  style="font-size:15px ">To Date</label>
									<div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
										<input type="text" autocomplete="off" name="to_date" placeholder="To Date" class="form-control">
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
@if(isset($attendance))
<div class="row" id="indivi_rep_profile">
	<div class="col-md-4 col-vlg-3 col-sm-6" style="float:left;">
<div class="tiles blue m-b-10">
<div class="tiles-body">
<div class="tiles-title text-white"><b>ID : {{$user_info_query[0]->id}}</b></div>
<div class="tiles-title text-white"><b>Name : {{$user_info_query[0]->user_name}}</b></div>
<div class="tiles-title text-white"><b>Department : {{$user_info_query[0]->name}}</b> </div>
<div class="tiles-title text-white"><b>Designation : {{$user_info_query[0]->title}}</b></div>
</div>
</div>
</div>
<div class="col-md-4 col-vlg-3 col-sm-6" style="float:right;">
  @if($user_info_query[0]->image!='')
  <div class="user-profile-pic" style="margin-top: 17px; width: 83%;">
  <img src="<?php echo asset('photos/'.$user_info_query[0]->image)?>" width="200px" height="98px">
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
				<table class="table datatable table-bordered" id="" aria-describedby="example3_info">
					<thead>
						<tr role="row">
							<th>ID</th>
						    <th>Date</th>
						    <th>Time Category</th>
						   <!--  <th>In</th>
						    <th>Out</th> -->
						     <th>Total Hours</th>
						    <th>Hours Work</th>
						   
						    
						   <!--  <th>Status</th> -->
						    <th>Over Time</th>
						    <th>Time Pay</th>
						    <th>Bonus</th>
						    <th>Deduction</th>
						   
					</tr>
					</thead>

					<tbody role="alert" aria-live="polite" aria-relevant="all">
							<?php 
				 $start_date = new DateTime($from_date);
				 $to_date = new DateTime($to_date);
				 $counter = 0; 
				 $sum = 0;
				 $total_exp_hr=0;
                 $total_hr_work=0;
                 $total_present=0;
                 $total_late=0;
                 $total_absent=0;
                 $total_leave=0;
                 $leave=0;
                 $bonus_total_amount=0;
                 $deduction_total_amount=0;
                 $total_pay_count=0;
                 $total_work=[];
                 $expected_hours_work=[];


                ?>
            
 			 @for($i = $start_date;$i<=$to_date;$i->modify('+1 day'))
				 <tr>
					<td><?php echo ++$counter; ?> </td>
					<td>{{$i->format('Y-m-d')}}</td>


				 <?php
					 
									$day = date("D",strtotime($i->format('d-m-Y')));

								
				 ?>
				 <?php 

					 $checktimes = [];
					 $date_attendance = $attendance->where('att_date',$i->format('Y-m-d'));

					 foreach($date_attendance as $key=>$val){
						$checktimes[] = $val->checktime;			
					}
					$date_leaves = $leaves->where('leave_date', $i->format('Y-m-d'));
					$date_attach = $attachs->where('attach_date', $i->format('Y-m-d'));
						$timein = "--";
						$timeout = "--";
						$hours = "--";
						$expected_hours = "--";
						$hours_calculation="--";
						$time_category="--";
						$time_pay="--";
						$status = "Absent";
						$timecalculation_remarks="--";          
						if($date_attendance->count()>0){          
                            if(date('l', strtotime($i->format('Y-m-d')))=="Monday"){                              
									$selectedTime = $date_attendance->first()->monday_in;
									$min = date('i',strtotime($date_attendance->first()->tcat_grace_time));
								    $endTime = strtotime("+".$min." minutes", strtotime($selectedTime));
									$time_in =  date('H:i:s', $endTime);
									$expected_time_in = $time_in; 
									$expected_seconds = strtotime($date_attendance->first()->monday_out) - strtotime($date_attendance->first()->monday_in);
									$expected_hours = date('h:i:s',$expected_seconds);

                            }
                            elseif(date('l', strtotime($i->format('Y-m-d')))=="Tuesday"){

                                 $selectedTime = $date_attendance->first()->tuesday_in;
								 $min = date('i',strtotime($date_attendance->first()->tcat_grace_time));
								 $endTime = strtotime("+".$min." minutes", strtotime($selectedTime));
								
							     $time_in =  date('H:i:s', $endTime);
							     $expected_time_in = $time_in; 
                                 $expected_seconds = strtotime($date_attendance->first()->tuesday_out) - strtotime($date_attendance->first()->tuesday_in);
			                     $expected_hours = date('h:i:s',$expected_seconds); 

                                // dd("Tuesday");    

                            }
                             elseif(date('l', strtotime($i->format('Y-m-d')))=="Wednesday"){
                                  
                                 $selectedTime = $date_attendance->first()->wednesday_in;
                                 $min = date('i',strtotime($date_attendance->first()->tcat_grace_time)); 
								 $endTime = strtotime("+".$min." minutes", strtotime($selectedTime));
							     $time_in =  date('H:i:s', $endTime); 

							     $expected_time_in = $time_in; 
                                 $expected_seconds = strtotime($date_attendance->first()->wednesday_out) - strtotime($date_attendance->first()->wednesday_in);
			                     $expected_hours = date('h:i:s',$expected_seconds);  
                                  

                            }
                             elseif(date('l', strtotime($i->format('Y-m-d')))=="Thursday"){

                            	 $selectedTime = $date_attendance->first()->thursday_in;
								 $min = date('i',strtotime($date_attendance->first()->tcat_grace_time));
								 $endTime = strtotime("+".$min." minutes", strtotime($selectedTime));
							     $time_in =  date('H:i:s', $endTime);
							     $expected_time_in = $time_in; 
                                 $expected_seconds = strtotime($date_attendance->first()->thursday_out) - strtotime($date_attendance->first()->thursday_in);
			                     $expected_hours = date('h:i:s',$expected_seconds);                                                   	
                            }
                             elseif(date('l', strtotime($i->format('Y-m-d')))=="Friday"){
                                 $selectedTime = $date_attendance->first()->friday_in;
								 $min = date('i',strtotime($date_attendance->first()->tcat_grace_time));
								 $endTime = strtotime("+".$min." minutes", strtotime($selectedTime));
							     $time_in =  date('H:i:s', $endTime);
							     $expected_time_in = $time_in; 
                                 $expected_seconds = strtotime($date_attendance->first()->friday_out) - strtotime($date_attendance->first()->friday_in);
			                     $expected_hours = date('h:i:s',$expected_seconds);

                             }
                             elseif(date('l', strtotime($i->format('Y-m-d')))=="Saturday"){
                            	 
                            	 $selectedTime = $date_attendance->first()->satureday_in;
                            	 $min = date('i',strtotime($date_attendance->first()->tcat_grace_time));
								 $endTime = strtotime("+".$min." minutes", strtotime($selectedTime));
							     $time_in =  date('H:i:s', $endTime);
							     $expected_time_in = $time_in; 
                                 $expected_seconds = strtotime($date_attendance->first()->satureday_out) - strtotime($date_attendance->first()->satureday_in);
			                     $expected_hours = date('h:i:s',$expected_seconds);
			                   
                            }
                             elseif(date('l', strtotime($i->format('Y-m-d')))=="Sunday"){

                                 $selectedTime = $date_attendance->first()->sunday_in;
								 $min = date('i',strtotime($date_attendance->first()->tcat_grace_time));
								 $endTime = strtotime("+".$min." minutes", strtotime($selectedTime));
							     $time_in =  date('H:i:s', $endTime);
							     $expected_time_in = $time_in; 
                                 $expected_seconds = strtotime($date_attendance->first()->sunday_out) - strtotime($date_attendance->first()->sunday_in);
			                     $expected_hours = date('h:i:s',$expected_seconds);                             
                             }
							    //<-adding 15 min to expextimes tcat_time_in
							    sort($checktimes);
						        $timein = $checktimes[0];
								$timeout = $checktimes[count($checktimes)-1];
								
                                $expected_hours_work[] =  $expected_hours;  
								$seonds = strtotime($timeout)-strtotime($timein);
								$date=$date_attendance->first()->att_date;
								$hours = date("H:i:s",$seonds); 
								$total_work[] = $hours;
								            
								$checktime = date("H:i",strtotime($date_attendance->first()->checktime));
								if(strtotime($hours)>strtotime($expected_hours)){
                                   $timecalculation= strtotime($hours)-strtotime($expected_hours);
								}
								else{

									$timecalculation= strtotime($expected_hours)-strtotime($hours);
								}
								$timecalculation_remarks = date('H:i:s',$timecalculation);
								$expected_time = date("H:i:s",strtotime($timein));
								$sum+=$expected_seconds;  

                                
								// $expected_time  for time_category
								 if($selectedTime==null){
                                     $time_in="--";
                                     $timein="--";
                                     $hours="--";
                                     $expected_hours="--";
                                     $expected_time="--";

								}
								else{

								if($expected_time <= $time_in ){

                                
								    $total_exp_hr+=strtotime($expected_hours);
								    $total_hr_work+=strtotime($hours);
								    $total_present+=1;            
									if($date_attendance->first()->mark_attendance==1){
                                          $status='Present *';
									}else{
										$status='Present';
									}
									

									$flag=1;
                                 
                                 if(date('l', strtotime($i->format('Y-m-d')))=="Monday"){
                                 

                                      $time_category='[ '.$date_attendance->first()->monday_in .'-' .$date_attendance->first()->monday_out.' ]' ;     
									}	
								 else if(date('l', strtotime($i->format('Y-m-d')))=="Tuesday"){
								 	
                                      $time_category='[ '.$date_attendance->first()->tuesday_in .'-' .$date_attendance->first()->tuesday_out.' ]' ;     
									}		
								 else if(date('l', strtotime($i->format('Y-m-d')))=="Wednesday"){
								 	
                                      $time_category='[ '.$date_attendance->first()->wednesday_in .'-' .$date_attendance->first()->wednesday_out.' ]' ;     
									}	
								 else if(date('l', strtotime($i->format('Y-m-d')))=="Thursday"){
                                      
                                      $time_category='[ '.$date_attendance->first()->thursday_in .'-' .$date_attendance->first()->thursday_out.' ]' ;     
									}	
								 else if(date('l', strtotime($i->format('Y-m-d')))=="Friday"){
								               
                                      $time_category='[ '.$date_attendance->first()->friday_in .'-' .$date_attendance->first()->friday_out.' ]' ;     
									}
								  else if(date('l', strtotime($i->format('Y-m-d')))=="Saturday"){
                                      
                                      $time_category='[ '.$date_attendance->first()->satureday_in .'-' .$date_attendance->first()->satureday_out.' ]' ;     
									}
									else if(date('l', strtotime($i->format('Y-m-d')))=="Sunday"){
										
                                      $time_category='[ '.$date_attendance->first()->sunday_in .'-' .$date_attendance->first()->sunday_out.' ]' ;     
								    }
								}
                                   
								elseif($expected_time > $time_in ){

							   			$expect_time_check = strtotime($expected_hours);
							   			$total_exp_hr+=strtotime($expected_hours);

							   			$total_hr_work+=strtotime($hours);        
										$hour_time_check = strtotime($hours);
										$diff = $expect_time_check - $hour_time_check;

										$hours_calculation = date('H:i:s', $diff).' time late';

								   if(date('l', strtotime($i->format('Y-m-d')))=="Monday"){
                            
                                      $time_category='[ '.$date_attendance->first()->monday_in .'-' .$date_attendance->first()->monday_out.' ]' ;     
									}	
								 else if(date('l', strtotime($i->format('Y-m-d')))=="Tuesday"){
								 	
                                      $time_category='[ '.$date_attendance->first()->thursday_in .'-' .$date_attendance->first()->thursday_out.' ]' ;     
								 }		
								 else if(date('l', strtotime($i->format('Y-m-d')))=="Wednesday"){
								 	
                                      $time_category='[ '.$date_attendance->first()->wednesday_in .'-' .$date_attendance->first()->wednesday_out.' ]' ;     
									}	
								 else if(date('l', strtotime($i->format('Y-m-d')))=="Thursday"){
                                      
                                      $time_category='[ '.$date_attendance->first()->thursday_in .'-' .$date_attendance->first()->thursday_out.' ]' ;     
									}	
								 else if(date('l', strtotime($i->format('Y-m-d')))=="Friday"){
								      
                                      $time_category='[ '.$date_attendance->first()->friday_in .'-' .$date_attendance->first()->friday_out.' ]' ;     
									}
								  else if(date('l', strtotime($i->format('Y-m-d')))=="Saturday"){
                                      
                                      $time_category='[ '.$date_attendance->first()->satureday_in .'-' .$date_attendance->first()->satureday_out.' ]' ;     
									}
									else if(date('l', strtotime($i->format('Y-m-d')))=="Sunday"){
										
                                      $time_category='[ '.$date_attendance->first()->sunday_in .'-' .$date_attendance->first()->sunday_out.' ]' ;     
								    }
                                       	if($date_attendance->first()->mark_attendance==1){
                                             $status='Late *';
                                       	}else{

                                          $status='Late';
										 
                                       	}
                                       	 $total_late=1;
										
										
								}
							  } 
							}
						  
						else{

								if($date_leaves->count()>0){
                                   

										foreach($date_leaves as $key=>$val){
										
											if($val->leave_type==2){
												$status = $val->title;	
											}

											else{
											      $status = 'Short Leave' ;		
											}
											 $total_leave=1;
											$leave_type=$val->type;
											$flag=2;

										}
									}
								 else{
								 	   if($date_attach->count()>0){
								 	   	  foreach($date_attach as $key=>$val){
											$status = 'Attach';
										  }
									  }
								 }	
						 }

					
					?>
					<!-- @if(date('h:i:s a' ,strtotime($timein))=="12:00:00 am")
                    	<td>--</td>
					@else
					<td><?php echo date('h:i:s a' ,strtotime($timein))?></td>
                    @endif -->
					

					<!-- @if(sizeof($checktimes) == 1) 
					 <td>--</td> 
					@else
					@if(date('h:i:s a' ,strtotime($timein))=="12:00:00 am" )
					<td>--</td>
					@else
					 <td><?php echo date('h:i:s a',strtotime($timeout))?></td>
                    @endif
					@endif  -->
					<td>{{$time_category}}</td> 
					<td>{{ $expected_hours }}</td>                      
				    <?php if($hours!='00:00:00') { ?>
					<td>{{ $hours }}</td>
					 <?php }else { ?>
					 <td>--</td> 
					 <?php } ?>     
					
					
				    
					<?php if($status=='Absent') {
                               
                      if($day_off->count()>0){
                      	  echo  "<td style=''>--</td>";
					  
					  }
					
                             
						?>
					
					
					<?php }  ?>
						@if(sizeof($checktimes) == 1)
						 @if($selectedTime==null)
						  <td></td>
						 @else
						  <td style="color:red; "><span class="missing_out">Missing checkout</span></td>
						@endif
						@else

                     @if(strtotime($hours)>strtotime($expected_hours))
					 <td style="color:green">{{$timecalculation_remarks}} <span style="font-size: 10px">over time</span></td>
					 @elseif(strtotime($hours)==strtotime($expected_hours))
					 @if(strtotime($hours))
					 <td>--</td>
					 @endif
					 @elseif(strtotime($hours) < strtotime($expected_hours))
                     <td style="color: red">{{$timecalculation_remarks}} <span style="font-size: 10px" >time short</span></td>

					 @endif
					@endif
                   
                    @if(sizeof($checktimes) == 0)
                     <td>--</td>
                    @elseif(sizeof($checktimes) == 1) 
                    <td style="color:red; "><span class="missing_out">Missing checkout</span></td>
                    @elseif(sizeof($checktimes) > 1) 
                        @if(strtotime($hours) < strtotime($expected_hours))
  
                         <?php 
                           
							 //$a=12000;
							// $b=$a/31;
							// echo $b/24; 

                            $hour_rate =$emp_fixed_salary->first()->fixed_salary;

                            $hour_rate=$hour_rate/31;
                            $hour_rate=$hour_rate/24;
                            $iCostPerHour = $hour_rate;
							$timespent = $hours;
							$timeparts=explode(':',$timespent);
							$pay=$timeparts[0]*$iCostPerHour+$timeparts[1]/60*$iCostPerHour;
							$pay= round($pay, 2);
							
							?>
                          @if(isset($hour_rate)) 
                           <td>{{$pay}} Rs</td>
                             <?php  $total_pay_count+= $pay ?>
                          @else
                          <td>--</td>
                        @endif
						@elseif(strtotime($hours)==strtotime($expected_hours))
							@if(strtotime($hours))
								<?php
								$hour_rate =$emp_fixed_salary->first()->fixed_salary;
                            	$hour_rate=$hour_rate/31;
                                $hour_rate=$hour_rate/24;

								$iCostPerHour = $hour_rate;
								$timespent = $expected_hours;
								$timeparts=explode(':',$timespent);
								$pay=$timeparts[0]*$iCostPerHour+$timeparts[1]/60*$iCostPerHour;
								$pay= round($pay, 2);


								$overtime =$timecalculation_remarks;
								$over_timeparts=explode(':',$overtime);
								$overtime_pay=$over_timeparts[0]*$iCostPerHour+$over_timeparts[1]/60*$iCostPerHour;
								$overtime_pay= round($overtime_pay, 2);
								?>

								@if(isset($hour_rate)) 
								<?php  $total_pay=   $pay     ?>
								<td>{{$total_pay}}   Rs</td>
								<?php  $total_pay_count+= $total_pay ?>
								@else
								<td>--</td>
								@endif
							@endif
                        @elseif(strtotime($hours) > strtotime($expected_hours))
                         <?php

                            $hour_rate =$emp_fixed_salary->first()->fixed_salary;
                            $hour_rate=$hour_rate/31;
                            $hour_rate=$hour_rate/24;


                            $iCostPerHour = $hour_rate;
							$timespent = $expected_hours;
							$timeparts=explode(':',$timespent);
							$pay=$timeparts[0]*$iCostPerHour+$timeparts[1]/60*$iCostPerHour;
                            $pay= round($pay, 2);



                            $hour_rate_overtime =$emp_fixed_salary->first()->hour_rate_salary;
                            $iCostPerHour = $hour_rate_overtime;
							$overtime =$timecalculation_remarks;
							$over_timeparts=explode(':',$overtime);
						    $overtime_pay=$over_timeparts[0]*$iCostPerHour+$over_timeparts[1]/60*$iCostPerHour;
						   
						    if($emp_fixed_salary->first()->overtime_check==1){
                                $overtime_pay= round($overtime_pay, 2); 
						    }else{
						    	$overtime_pay=0;
						    }
							
							?>
                         
                          @if(isset($hour_rate)) 
                             <?php  $total_pay=   $pay + $overtime_pay;     ?>
                           <td>{{$pay}}-{{$overtime_pay}}={{$total_pay}}   Rs</td>
                           <?php  $total_pay_count+= $total_pay ?>
                          @else
                          <td>--</td>
                          @endif
                           

                        @endif
                      
                    @endif
					
				<!--   {{$i->format('Y-m-d')}} -->
                
                 @if($emp_bonus->count()>0)
                   <?php
                     
                     ?>
                    
		             @if(sizeof($checktimes) == 0)
                     <td>--</td>
                     @elseif(sizeof($checktimes) == 1)

                    
	                     <td style="color:red; "><span class="missing_out">Missing checkout</span></td>
	                 @elseif(sizeof($checktimes) > 1)   
	                     
	                     	<?php $bonuse=$emp_bonus->where('date',$i->format('Y-m-d'));?>

	                          @if($bonuse->count()>0)
                                 <?php $total_amount = []; ?>
	                             @foreach($bonuse as $b)
	                               <?php $total_amount[]=$b->amount; ?>
	                             @endforeach
	                           

                             
	                          <td>{{array_sum($total_amount)}}</td>
	                           <?php  $bonus_total_amount+=array_sum($total_amount);   ?>
	                          @else
	                           <td>--</td>
	                          @endif
	                 @endif
                

		          @else
		            <td>--</td>

		         @endif

		          @if($emp_deduction->count()>0)
                   <?php
                     
                     ?>
                    
		             @if(sizeof($checktimes) == 0)
                     <td>--</td>
                     @elseif(sizeof($checktimes) == 1)

                    
	                     <td style="color:red; "><span class="missing_out">Missing checkout</span></td>

	                 @elseif(sizeof($checktimes) > 1)   
	                    
	                     	<?php $deduction=$emp_deduction->where('date',$i->format('Y-m-d'));?>
      

	                          @if($deduction->count()>0)
                                 <?php $total_amount = []; ?>
	                             @foreach($deduction as $d)
	                               <?php $total_amount[]=$d->amount; ?>
	                             @endforeach
	                          <td>{{array_sum($total_amount)}}</td>
	                          <?php  $deduction_total_amount+=array_sum($total_amount);   ?>
	                          @else
	                           <td>--</td>
	                          @endif

	                 @endif
                

		          @else
		            <td>--</td>
		         @endif
			<!-- emp_deduction -->
				</tr>
			 @endfor
			 <?php 

                $sum = strtotime('00:00:00'); 
				$totaltime = 0;
				foreach( $total_work as $element ) { 

				
				$timeinsec = strtotime($element) - $sum; 
				$totaltime = $totaltime + $timeinsec; 

				}


				$h = intval($totaltime / 3600);   
                $totaltime = $totaltime - ($h * 3600);
				$m = intval($totaltime / 60); 

				// Remaining value is seconds 
				$s = $totaltime - ($m * 60); 

				// Printing the result
				$total_hours= "$h:$m";





			$sum1 = strtotime('00:00:00');
            $totaltime1 = 0; 
            foreach( $expected_hours_work as $element1 ) { 

				 
				$timeinsec1 = strtotime($element1) - $sum1; 

				$totaltime1 = $totaltime1 + $timeinsec1; 

				}

				$hh = intval($totaltime1 / 3600);   
                $totaltime1 = $totaltime1 - ($hh * 3600);
				$mm = intval($totaltime1 / 60); 

				// Remaining value is seconds 
				$ss = $totaltime1 - ($mm * 60); 

				// Printing the result
				$exp_hours= "$hh:$mm"; 
                
			           
			 //date("H:i:s",$sum);
			 ?>
				
				</tbody>
			</table>
	     	</div>
		</div>
			</div>
		</div>
		</div>
	</div>
</div>
<br>
<div class="row-fluid">
	<div class="col-md-12">
		<div class="grid simple" id="indivi_summary">
			<div class="grid-title no-border">
				<h4>Summary</h4>
			</div>
			<div class="grid-body no-border">

				<table class="table no-more-tables">
					<thead>
						<tr>

							<!-- <th>Total Expected Hours</th>
							<th><?php echo date('H:i:s',$total_hr_work) ;?></th> -->
						    <th>Total Hours Works</th>
						    <th><?php echo $exp_hours ;?></th>
						   	<th>Total Expected Hours</th>
						    <th><?php echo $total_hours ;?></th>
							
						   	<th>Time Pay total</th>
						   	<th>{{$total_pay_count}}</th>
						   	<th>Bonus</th>
						   	<th><?php echo  $bonus_total_amount;?></th>
						   	<th>Dedication</th>
						   	<th><?php echo  $deduction_total_amount?></th>
						   	<th>Total</th>
						   	<th>{{$total_pay_count+$bonus_total_amount-$deduction_total_amount}}</th>
							
						</tr>
					</thead>
	
				</table>
			</div>
		</div>
	</div>
</div>
@else
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

 @endif
</div>
@include('include.important')
@endsection