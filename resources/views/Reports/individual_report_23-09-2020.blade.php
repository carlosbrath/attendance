
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
				<form class="form-no-horizontal-spacing" method="post" action="{{route('individual_report')}}" id="form-condensed" novalidate="novalidate">
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
				<table class="table datatable" id="" aria-describedby="example3_info">
					<thead>
						<tr role="row">
							<th>ID</th>
						    <th>Date</th>
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
				 $total_exp_hr=0;
                 $total_hr_work=0;
                 $total_present=0;
                 $total_late=0;
                 $total_absent=0;
                 $total_leave=0;
                 $leave=0;
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
								$seonds = strtotime($timeout)-strtotime($timein);
								$date=$date_attendance->first()->att_date;
								$hours = date("H:i:s",$seonds);             
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
					@if(date('h:i:s a' ,strtotime($timein))=="12:00:00 am")
                    	<td>--</td>
					@else
					<td><?php echo date('h:i:s a' ,strtotime($timein))?></td>
                    @endif
					

					@if(sizeof($checktimes) == 1) 
					 <td>--</td> 
					@else
					@if(date('h:i:s a' ,strtotime($timein))=="12:00:00 am" )
					<td>--</td>
					@else
					 <td><?php echo date('h:i:s a',strtotime($timeout))?></td>
                    @endif
					@endif                        
				 
					<td>{{ $hours }}</td>      
					<td>{{ $expected_hours }}</td>
					<td>{{$time_category}}</td>
				    
					<?php if($status=='Absent') {

					   
                        if($day == 'Sat'){ 
					      echo  "<td style='background-color:pink'>Off Day</td>";
                        }elseif($day == 'Sun'){
                            echo  "<td style='background-color:pink'>Off Day</td>";
                        } 
                        else{
                          
                       // $chck_date=date("Y/m/d", strtotime());
                       
                       
                        $check_emp_holiday=$holidays->where('holiday_date',$i->format('Y-m-d'))
                        ->first();
                       
                         if($check_emp_holiday==null){
                        	   $total_absent+=1;
				              echo '<td style="background-color:#29252799;color:white">'.$status.'</td>';
				            }else{
                               
                               echo '<td style="background-color:#29252799;color:white">'.$check_emp_holiday->title.'</td>';

				            }

                        }

                             
						?>
					
					
					<?php } 
				
					elseif($status =='Late'){ 
                         if($selectedTime==null){
                            echo  '<td style="background-color:pink">Off Day</td>';
                         }else{
                         	     echo  '<td style="background-color:#f35958;color:white">'.$status.'</td>';
                         }                          
					   ?>
						
				<?php }
                 elseif($status =='Late *'){ 
                         if($selectedTime==null){
                            echo  '<td style="background-color:pink">Off Day</td>';
                         }else{
                         	     echo  '<td style="background-color:#f35958;color:white">'.$status.'</td>';
                         }                          
					   ?>
						
				<?php }
					
					elseif($status =='Present'){ ?>
					 
					  <?php  if($day == 'Sat'){ 
					      echo  "<td style='background-color:pink'>Off Day</td>";
                        }elseif($day == 'Sun'){
                            echo  "<td style='background-color:pink'>Off Day</td>";
                        }
                        else{
                                echo  '<td style="background-color:#0aa699;color: white">'. $status.'</td>';  
   	
                        } 
                        ?>
				
					<?php }
					elseif($status =='Present *'){ ?>
					 
					  <?php  if($day == 'Sat'){ 
					      echo  "<td style='background-color:pink'>Off Day</td>";
                        }elseif($day == 'Sun'){
                            echo  "<td style='background-color:pink'>Off Day</td>";
                        }
                        else{
                                echo  '<td style="background-color:#0aa699;color: white">'. $status.'</td>';  
   	
                        } 
                        ?>
				
					<?php }
					
					 elseif($status =='Attach'){?>

					 <td style="background-color:#04523d99;color:white">{{ $status }}</td>

					<?php }
					 
					 elseif($flag==2){?>


                     <?php  $leave+=1; ?>
                    <?php  if($day == 'Sat'){ 
					      echo  "<td style='background-color:pink'>Off Day</td>";
                        }elseif($day == 'Sun'){
                            echo  "<td style='background-color:pink'>Off Day</td>";
                        }
                        else{

                                 echo  '<td style="background-color:#1a033a99;color: white">'.$status.'</td>';    	
                        } 
                        ?>
                    

					
					 <?php }
					 ?>
						@if(sizeof($checktimes) == 1)
						 @if($selectedTime==null)
						  <td></td>
						 @else
						  <td style="color:white; background-color: red;">Missing Check Out</td>
						@endif
						@else
                     @if(strtotime($hours)>strtotime($expected_hours))
					 <td>{{$timecalculation_remarks}} o/t</td>
					 @elseif(strtotime($hours) < strtotime($expected_hours))
                     <td>{{$timecalculation_remarks}}  h/s</td>
					 @endif
					@endif
				</tr>
			 @endfor
			 <?php  
			       
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
							<th>Total Expected Hours</th>
							<th><?php echo date('H:i:s',$total_hr_work) ;?></th>
						    <th>Total Hours Works</th>
						   	<th><?php echo date('H:i:s',$total_exp_hr);?></th>
							<th><u style="color:green">P</u>- {{$total_present}}<u style="color:red">L</u>- {{$total_late}}<u style="color:#f6b9">A</u>- {{$total_absent}} <u style="color:#a5a2a499">Leave</u>- {{$total_leave}}</th>
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