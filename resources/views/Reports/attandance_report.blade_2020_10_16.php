
@extends('layout.layout')
@section('title','Attendance Status Report')
@section('content')
<style type="text/css">
.form-actions {
  background-color:#f6f7f8;
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
				<form class="form-no-horizontal-spacing" action="{{route('report')}}" 
       method="post" id="form-condensed" novalidate="novalidate">
       @csrf
       <div class="row column-seperation">
        <div class="col-md-12">
         <h4></h4>
        
         <div class="row form-row">					
          <div class="col-md-4" style="width:379px;">
           <div class="grid simple">
            <div class="grid-title no-border">
            </div>
            
            <div class="row">
              <div class="col-md-12">
                <br>
                <label class="form-label"  style="font-size:15px">Department</label>
                <select name="search" id="source" style="width:100%" tabindex="-1" class="search select2-offscreen">
                  <option value="">Please Select ....</option>
                  <option name=""  value="all">All</option>
                  @foreach($department as $d)
                  <option name=""  value="{{$d->id}}">{{$d->name}}</option>
                  @endforeach
                
                </select>
                @if($errors->any())
                <p style="color:red">
                  {{$errors->first('search','Department field is required')}}
                </p>
                @endif
              </div>
            </div>
            @if($sub_department->count()>0)
            <div class="row">
              <div class="col-md-12">
                <br>
                <label class="form-label"  style="font-size:15px">Sub Department</label>
                <select name="subdepartment_id" id="subdepartment_id" style="width:100%" >
                  <option value="">Please Select ....</option>
                  @foreach($sub_department as $d)
                  <option name=""  value="{{$d->id}}">{{$d->name}}</option>
                  @endforeach    
                </select>
                @if($errors->any())
                <p style="color:red">
                  {{$errors->first('subdepartment_id','Sub Department field is required')}}
                </p>
                @endif
              </div>
            </div>
          @endif

          </div>
        </div>
      </div>
   
      <div class="row form-row">
        <div class="col-md-12">
         <label class="form-label"  style="font-size:15px ">Date</label>
         <div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
          <input type="text" autocomplete="off" name="date" placeholder="From Date" class="form-control">
          <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
          @if($errors->any())
          <p style="color:red">
            {{$errors->first('date','Date field is required')}}
          </p>
          @endif 
        </div>
        
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
       <div class="radio">
        <input id="male" type="radio" name="status" value="present" checked="checked">
        <label for="male">Present</label>
        <input id="female" type="radio" name="status" value="absent">
        <label for="female">Absent</label>
        <input id="all" type="radio" name="status" value="all">
        <label for="all">All</label>
      </div>
    </div>
  </div>
  <br>
</div>
</div>
<div class="form-actions">
  <div class="pull-left">
   <button class="btn btn-success btn-cons" type="submit"><i class="icon-ok"></i>Filter Report</button>								
 </div>
</div>
</form>
</div>
</div>
</div>
</div>
<br>
<div class="row">
  <div class="row-fluid">
    <h3 class="page-title">
      Attendance Status Reports   
      <?php if($rep_date==""){ echo date('d,D, F, Y');
    }else{
     $date = new DateTime($rep_date);
     echo $date->format('d,D,F,Y');
   }?> 
 </h3>
</div>
</div>
<div class="row">
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
		<div class="grid simple ">

			<div class="grid-body ">
        <div class="table-responsive">
          <table id="dataexample" class="table dataTable display nowrap" aria-describedby="example3_info" >
            <!--<table class="table dataTable" id="example" aria-describedby="example3_info">-->
             <thead>
              <tr role="row">
              
               <th>ID</th>
               <th>Name</th>
               <th>Department</th>
               <th>Designation</th>
               <th>File #</th>
               <th>Gender</th>
               <th>Bps</th>
               <th>In</th>
               <th>Out</th>
             <!--   <th>Total Leave</th>
               <th>Avail Leave</th>
               <th>Time From</th>
               <th>Time To</th> -->
               <th>Status</th>
               <th>Remarks</th>
             </tr>
           </thead>

           <tbody role="alert" aria-live="polite" aria-relevant="all">
             <?php
             
             
             $t=date('d-m-Y');
             $day=date("D",strtotime($t));      
             $type='';
             $total_present=0;
             $total_late=0;
             $total_absent=0;
             $total_leave=0;
             $total_attach=0;
             $start_date_of_year  = date('Y-m-d', strtotime('first day of january this year'));
             $end_date_of_year    = date('Y-m-d', strtotime('last day of december this year'));

             ?>
              
              
              @if(!(empty($fetch_emp)))
              @foreach($fetch_emp as $emp)
                              
              @if($attandance_reports->where('id',$emp->id)->count()>0)
              @if($flag=="present" ||  $flag=="all")
              @foreach($attandance_reports->where('id',$emp->id)->groupBy('id') as $rep)
              <?php
              $checkdata[]=$rep;
               $attach_employee =$attach_emp_detail->where('id',$emp->id);
              $attach_emp_id="";
              if($attach_employee->count()>0 ){
                $attach_emp_id=$attach_employee->first()->id;  
              }
           
         if($rep->first()->is_night_shift==1){
            
            $expected_time_in = date('H:i',strtotime($rep->first()->time_in));
            $date=   date('Y-m-d', strtotime($rep->first()->time_in));
            $prev_date = date('Y-m-d', strtotime($date .' -1 day'));  
            $data = DB::table('attendance')
            ->where('user_id',$rep->first()->id)
            ->whereDate('datetime',$prev_date)
            ->first();
          

            if($data==null){
        
                  $attendance_date= '';
            }else{
                 
                 if(date('a',strtotime($rep->last()->attendance_time))=="pm"){
 
                          $attendance_date =  date('d-m-Y h:i:s a',strtotime($rep->last()->attendance_time));

                          }else{
                                 $attendance_date= $data->datetime;

                               } 

                
            }
            $attendance_time_in = date('H:i',strtotime($attendance_date));

          
            if($attendance_time_in <=  $expected_time_in && $attendance_time_in!="00:00" &&  $expected_time_in!="" )
            {
                  
                //dd($attendance_time_in);
                $total_present+=1; 
                $Emp_Status='Present'; 

            }  

             if($attendance_time_in >  $expected_time_in && $attendance_time_in!="" &&  $expected_time_in!="" ){

                 $total_late+=1;
                 $Emp_Status='Late';  
               
               }                          
          }else{
          // dd($rep);

           if($rep->first()->attendance_time > $rep->first()->time_in && $rep->first()->attendance_time!='' && $rep->first()->time_in!=''){
            
            $total_late+=1;
            $Emp_Status='Late';  
          }
           
          if($rep->first()->attendance_time <= $rep->first()->time_in && $rep->first()->attendance_time!='' && $rep->first()->time_in!=''  ){

           $total_present+=1; 
           $Emp_Status='Present';     
           }
        }
         //$prev_date = date('Y-m-d', strtotime($date .' -1 day'));
         ?>
         @if($Emp_Status=='Late')
         <tr style="background-color:#f35958; text-color: white;">
          <!--  <td style="background-color:#f35958; text-color: white"> {{ $loop->iteration }}</td> -->
           <td style="background-color:#f35958; color:#ffffff">{{$rep->first()->id}}</td>  
           <td style="color:white">{{$rep->first()->name}}</td>
           <td style="color:white;">{{$rep->first()->deparment_name}}</td>
           <td style="color:white">{{$rep->first()->title}}</td>
           <td style="color:white">{{$rep->first()->file_number}}</td>
            @if($rep->first()->gender=="male")
            <td style="color:white">M</td>
            @else
            <td style="color:white">F</td>
            @endif 
           <td style="color:white">{{$rep->first()->bps}}</td> 
           @if($rep->first()->attendance_time=='')

            @if($rep->first()->is_night_shift==1){
              
                 
               <?php 
                if($data==null){
                   $attendance_date='';
                }
                else{
                    $attendance_date =  $data->datetime;


                }
                $timein = $attendance_date;
                $timein= date('Y-m-d h:i:s a',strtotime($timein)); ?> 
              
                <td style="color:white;">{{$timein}}</td>   
              @else
                      <td style="color:white">{{$timein}}</td>
              @endif
           
           @else
           
                <?php 
                     
                 
                   if($rep->first()->is_night_shift==1){
                     if($data==null){
                         $attendance_date='';
                      }
                      else{

                          if(date('a',strtotime($rep->last()->attendance_time))=="pm"){

                         
                          $attendance_date =  date('d-m-Y h:i:s a',strtotime($rep->last()->attendance_time));

                          }else{

                              $attendance_date =  $data->datetime;
                          }                  

                      }
                     $timein = $attendance_date;
                     $timein= date('Y-m-d h:i:s a',strtotime($timein)); 
                    
                   }
                   else{ 

                           $timein = $rep[0]->attendance_time;
                           $timein= date('h:i:s a' ,strtotime($timein)); 
                  }          
            ?>
           <td style="color:white">{{$timein}}</td>
           @endif
            @if($rep->first()->is_night_shift==1)
           
          <?php
               if(date('a',strtotime($rep->last()->attendance_time))=="pm"){
            
            
              $fetch_timeout=   date('Y-m-d', strtotime($rep->last()->attendance_time));
              $next_date = date('Y-m-d', strtotime($fetch_timeout .' +1 day')); 
              $next_date_timeout = DB::table('attendance')
              ->where('user_id',$rep->first()->id)
              ->whereDate('datetime',$next_date)
              ->first();

              if($next_date_timeout==null){

                  $attendance_timeout=null;
              }else{
               
                $attendance_timeout =  $next_date_timeout->datetime;
                $timeout= date('Y-m-d h:i:s a' ,strtotime($attendance_timeout));
              }           
            }
            else{
                   
                   $attendance_timeout=  $rep->first()->attendance_time;
                   if($attendance_timeout==null){

                       $expected_time_in = date('H:i',strtotime($rep->first()->time_in));
                  $date=   date('Y-m-d', strtotime($rep->first()->time_in));
                  $prev_date = date('Y-m-d', strtotime($date .' -1 day'));  
                  $data = DB::table('attendance')
                  ->where('user_id',$rep->first()->id)
                  ->whereDate('datetime',$prev_date)
                  ->orderBy('datetime', 'DESC')
                  ->first();
                   if($data!=null){
                          $check_transaction = DB::table('attendance')
                          ->whereDate('datetime',$prev_date)
                          ->groupBy('datetime')
                          ->get();
                          if($check_transaction->count()>1)
                         {

                              $attendance_timeout=  $data->datetime;

                            $timeout= date('Y-m-d h:i:s a' ,strtotime($attendance_timeout));
                         }
                         else{
                                
                                $timeout='--'; 
                            

                         }                        
                     }
                   }else{
                            $timeout= date('Y-m-d h:i:s a' ,strtotime($attendance_timeout));
                   }
                 }

           ?>
           @if($attendance_timeout==null)
             <td style="color: white">--</td>

           @else

           <td style="color: white">{{$timeout}}</td>
           @endif
           @else 
           @if($rep->count()>1)
           <?php
           $timeout=  $rep->last()->attendance_time;
           $timeout= date('h:i:s a' ,strtotime($timeout));  
           ?>
           <td style="color:white;">{{$timeout}}</td>
           @elseif($rep->count()==1)
           <td style="color:white">--</td>
           @else
           <td style="color: white">--</td>
           @endif
           @endif
           @if($rep->first()->mark_attendance==1)
               @if(empty($attach_emp_id))
               <td style="color: white">{{$Emp_Status }} *</td>
               @else
               <td style="color: white">Late * + Attach</td>
               @endif
           @else
              @if(empty($attach_emp_id))
               <td style="color: white">{{$Emp_Status}}</td>
               @else
               <td style="color: white">Late + Attach</td>
               @endif
           @endif
           @if($rep->first()->is_night_shift==1)
             @if($data!=null)

                <?php $check_transaction = DB::table('attendance')
                          ->whereDate('datetime',$prev_date)
                          ->groupBy('datetime')
                          ->get();
                         
                          if($check_transaction->count()>1){
                                echo '<td>--</td>';
                          }else{
                                if($rep->first()->attendance_time==""){
                                   echo  '<td style="width:112px;color:white;background-color:red">Missing Check Out</td>';
                                }
                                else{
                                        echo '<td>--</td>';   
                                 }             
                              }
                           ?>
             @else            
             <td>--</td>
             @endif                     
           @else
           @if($rep->count()==1)  
           <td style="width:112px;color:white;background-color:red">Missing Check Out</td>
           @else
           <td style="color:white">--</td>       
           @endif
           @endif
         </tr>
         @elseif($Emp_Status=='Present')
         <tr style="background-color: #0aa699;">
         <!--  <td style="background-color:#0aa699; color: white"> {{ $loop->iteration }}</td> -->
           <td style="background-color:#0aa699;color:#ffffff">{{$rep->first()->id}}</td>  
           <td style="color:white;">{{$rep->first()->name}}</td>
           <td style="color:white;">{{$rep->first()->deparment_name}}</td>
           <td style="color:white;">{{$rep->first()->title}}</td>
           <td style="color:white;">{{$rep->first()->file_number}}</td>
          @if($rep->first()->gender=="male")
          <td style="color:white">M</td>
          @else
          <td style="color:white">F</td>
          @endif  
           <td style="color:white;">{{$rep->first()->bps}}</td> 
           @if($rep->first()->attendance_time=='')
             @if($rep->first()->is_night_shift==1){
               <?php 

                    if($data==null){
                    $attendance_date='';
                    $timein='--';
                    }
                    else{                          
                          $attendance_date =  $data->datetime;
                          $timein = $attendance_date;
                          $timein= date('Y-m-d h:i:s a',strtotime($timein));
                     } 
                 ?>   
                <td style="color:white;">{{$timein}}</td>   
              @else

              <td style="color:white;">{{$timein}}</td>
              @endif
           @else
         
           <?php 

                
                   if($rep->first()->is_night_shift==1){

                      if($data==null){
                         $attendance_date='';
                    }
                    else{
                      
                       if(date('a',strtotime($rep->last()->attendance_time))=="pm"){

                             $attendance_date =  date('d-m-Y h:i:s a',strtotime($rep->last()->attendance_time));


                       }else{
                              $attendance_date =  $data->datetime;

                        }
                      

                    }
                     $timein = $attendance_date;
                    

                     $timein= date('Y-m-d h:i:s a',strtotime($timein)); 
                    
                   }
                   else{        
                    $timein = $rep->first()->attendance_time;
                    $timein= date('h:i:s a' ,strtotime($timein)); 
                  }          
            ?>

           <td style="color:white;">{{$timein}}</td>
           @endif
           @if($rep->first()->is_night_shift==1)
            
          <?php
             $fetch_timeout=   date('Y-m-d', strtotime($rep->last()->attendance_time));
              $next_date = date('Y-m-d', strtotime($fetch_timeout .' +1 day')); 
              $next_date_timeout = DB::table('attendance')
              ->where('user_id',$rep->first()->id)
              ->whereDate('datetime',$next_date)
              ->first();


           if(date('a',strtotime($rep->last()->attendance_time))=="pm"){

             
              if($next_date_timeout==null){

                  $attendance_timeout=null;
              }else{
               
                $attendance_timeout =  $next_date_timeout->datetime;
                $timeout= date('Y-m-d h:i:s a' ,strtotime($attendance_timeout));
              }           
            }else{
                   
                   $attendance_timeout=  $rep->first()->attendance_time;
                   if($attendance_timeout==null){

                       $expected_time_in = date('H:i',strtotime($rep->first()->time_in));
                  $date=   date('Y-m-d', strtotime($rep->first()->time_in));
                  $prev_date = date('Y-m-d', strtotime($date .' -1 day'));  
                  $data = DB::table('attendance')
                  ->where('user_id',$rep->first()->id)
                  ->whereDate('datetime',$prev_date)
                  ->orderBy('datetime', 'DESC')
                  ->first();
                   if($data!=null){
                    
                          $check_transaction = DB::table('attendance')
                          ->whereDate('datetime',$prev_date)
                          ->groupBy('datetime')
                          ->get();
                          if($check_transaction->count()>1)
                         {

                              $attendance_timeout=  $data->datetime;

                            $timeout= date('Y-m-d h:i:s a' ,strtotime($attendance_timeout));
                         }
                         else{
                                
                                $timeout='--'; 
                          }          
                     }
                   }else{
                   $timeout= date('Y-m-d h:i:s a' ,strtotime($attendance_timeout));

                   }
                 }
           ?>
           @if($attendance_timeout==null)
             <td style="color: white">--</td>
           @else
           <td style="color: white">{{$timeout}}</td>
           @endif
           @else
           @if($rep->count()>1)
           <?php
           $timeout=  $rep->last()->attendance_time;
           $timeout= date('h:i:s a' ,strtotime($timeout));      
           ?>
           <td style="color: white">{{$timeout}}</td>
           @elseif($rep->count()==1)
           <td style="color: white">--</td>
           @else
           <td>--</td>
           @endif
           @endif      
           <!-- <td style="color: white">--</td>
           <td style="color: white">--</td> 
            <td style="color: white">--</td>
           <td style="color: white">--</td>  -->
           @if($rep->first()->mark_attendance==1)
              @if(empty($attach_emp_id))
              <td style="color: white">{{$Emp_Status}} *</td>
              @else
              <td style="color: white">Present * + Attach</td>
              @endif
           @else
              @if(empty($attach_emp_id))
              <td style="color: white">{{$Emp_Status}}</td>
              @else

              <td style="color: white">Present + Attach</td>
              @endif
           @endif
           @if($rep->first()->is_night_shift==1)
            @if($data!=null)

                <?php $check_transaction = DB::table('attendance')
                          ->whereDate('datetime',$prev_date)
                          ->groupBy('datetime')
                          ->get();
                         
                          if($check_transaction->count()>1){
                                echo '<td>--</td>';
                          }else{
                                if($rep->first()->attendance_time==""){
                                   echo  '<td style="width:112px;color:white;background-color:red">Missing Check Out</td>';
                                }
                                else{
                                        echo '<td>--</td>';   
                                 }             
                              }
                           ?>
             @else            
             <td>--</td>
             @endif               
           @else
           @if($rep->count()==1)  
           <td style="width:112px;color:white;background-color:red">Missing Check Out</td>
           @else
           <td>--</td>       
           @endif 
           @endif         
         </tr>
    
          @endif
          @endforeach
          @endif
          @elseif($daily_leave_reports->where('id',$emp->id)->count()>0)
          @foreach($daily_leave_reports->where('id',$emp->id) as $l)
          @if($flag=="absent" || $flag=="all")  
           <?php  $total_leave+=1 ?>
          <tr style="background-color:#1a033a99;color:white">
          <!--  <td style="background-color:#1a033a99;color:white"> {{ $loop->iteration }}</td> -->
           <td style="background-color:#1a033a99;color:white">{{$l->id}}</td>  
           <td style="color:white;">{{$l->name}}</td>
           <td style="color:white;">{{$l->deparment_name}}</td>
           <td style="color:white;">{{$l->desgnation}}</td>
           <td style="color:white;">{{$l->file_number}}</td> 
            @if($l->gender=="male")
             <td style="color:white">M</td>
             @else
             <td style="color:white">F</td>
             @endif 
           <td style="color:white;">{{$l->bps}}</td> 
           <td style="color:white;">--</td>
           <td style="color:white;"></td>
          
            @if($l->leave_type==2)
            <td style="color:white;">{{$l->title}}</td>
            @else
             <td style="color:white;">Short Leave</td>
            @endif
             <td style="color:white;">--</td>
            
         </tr>
           @endif
           @endforeach
           @elseif($attach_emp_detail->where('id',$emp->id)->count()>0)
            @foreach($attach_emp_detail->where('id',$emp->id) as $att) 
              @if($flag=="absent" || $flag=="all")          
              <?php  $total_attach+=1; ?>
           <tr style="background-color:#0090d9;color:white">
           <td style="background-color:#0090d9;color:white"> {{ $loop->iteration }}</td>
           <td style="background-color:#0090d9;color:white">{{ $att->id}}</td>  
           <td style="color:white;">{{ $att->name}}</td>
           <td style="color:white;">{{ $att->department_name}}</td>
           <td style="color:white;">{{ $att->designation}}</td>
           <td style="color:white;">{{ $att->file_number}}</td> 
            @if($att->gender=="male")
             <td style="color:white">M</td>
             @else
             <td style="color:white">F</td>
             @endif
           <td style="color:white;">{{ $att->bps}}</td>  
           <td style="color:white;">--</td> 
           <td style="color:white;">--</td>
         
          
           
            <td style="color:white;">Attach</td>
           
             <td style="color:white;">--</td>
            
         </tr>
         @endif
          @endforeach
          @else
          @if($flag=="absent" || $flag=="all")
          <?php  $total_absent+=1; ?>
          
           <tr style="background-color:#29252799;color:white;">
                <!-- <td style="background-color:#29252799;color:white;"> {{ $loop->iteration }}</td> -->
                <td style="background-color:#29252799;color:white;">{{$emp->id}}</td>  
                <td style="color:white;">{{$emp->name}}</td>
                <td style="color:white;">{{$emp->department_name}}</td>
                <td style="color:white;">{{$emp->designation}}</td> 
                <td style="color:white;">{{$emp->file_number}}</td>
                @if($emp->gender=="male")
                <td style="color:white">M</td>
                @else
                <td style="color:white">F</td>
                @endif
                <td style="color:white;">{{$emp->bps}}</td> 
                <td style="color:white;">--</td>
                <td style="color:white;">--</td>

              <?php 
                 
                   if($day_off->count()>0){

                   if($weekday=="Saturday") {       ?>
                <?php $satureday_in= $day_off->where('id',$emp->id)->first()->tc_satureday_in;

                  if(empty($satureday_in)){
                  ?> 
                    <td style="color:white;">Off Day</td>
                     
                   <?php } else{ ?>

                      @if($holidays->count()>0)
                      @foreach($holidays as $h)
                      <td style="color:white;">{{$h->title}}</td>
                      @endforeach
                      @else
                       <td style="color:white;">Absent</td>
                      @endif

                  <?php }?> <!-- end empty($satureday_in)   -->
                            <!--end weekday satureday -->
                 <?php }  
                 elseif($weekday=="Sunday") {
                    $sunday_in= $day_off->where('id',$emp->id)->first()->tc_sunday_in;

                    if(empty($sunday_in)){
                       echo '<td style="color:white;">Off Day</td>';
                    }
                    else{
                       
                           if($holidays->count()>0){
                             foreach($holidays as $h){
                              echo  '<td style="color:white;">'.$h->title.'</td>';
                             }
                           }
                           else{
                             echo '<td style="color:white;">Absent</td>';
                           }

                    }

                 } //end sunday
                   elseif($weekday=="Friday") {
                    $friday_in= $day_off->where('id',$emp->id)->first()->tc_friday_in;

                    if(empty($friday_in)){
                       echo '<td style="color:white;">Off Day</td>';
                    }
                    else{
                       
                           if($holidays->count()>0){
                             foreach($holidays as $h){
                              echo  '<td style="color:white;">'.$h->title.'</td>';
                             }
                           }
                           else{
                             echo '<td style="color:white;">Absent</td>';
                           }

                    }

                 } //end friday
                  elseif($weekday=="Thursday") {

                    $thursday_in= $day_off->where('id',$emp->id)->first()->tc_thursday_in;

                    if(empty($thursday_in)){
                       echo '<td style="color:white;">Off Day</td>';
                    }
                    else{
                       
                           if($holidays->count()>0){
                             foreach($holidays as $h){
                              echo  '<td style="color:white;">'.$h->title.'</td>';
                             }
                           }
                           else{
                             echo '<td style="color:white;">Absent</td>';
                           }

                    }

                 } //end thursday_in
                  elseif($weekday=="Wednesday") {
                   
                    $wednesday_in= $day_off->where('id',$emp->id)->first()->tc_wednesday_in;

                    if(empty($wednesday_in)){
                       echo '<td style="color:white;">Off Day</td>';
                    }
                    else{
                       
                           if($holidays->count()>0){
                             foreach($holidays as $h){
                              echo  '<td style="color:white;">'.$h->title.'</td>';
                             }
                           }
                           else{
                             echo '<td style="color:white;">Absent</td>';
                           }

                    }

                 } //end Wednesday
                  elseif($weekday=="Tuesday") {
                   
                    $tuesday_in= $day_off->where('id',$emp->id)->first()->tc_tuesday_in;
                    if(empty($tuesday_in)){
                       echo '<td style="color:white;">Off Day</td>';
                    }
                    else{
                       
                           if($holidays->count()>0){
                             foreach($holidays as $h){
                              echo  '<td style="color:white;">'.$h->title.'</td>';
                             }
                           }
                           else{
                             echo '<td style="color:white;">Absent</td>';
                           }

                    }

                 } //end Tuesday
                 elseif($weekday=="Monday") {
                   
                    $tc_monday_in= $day_off->where('id',$emp->id)->first()->tc_monday_in;
                    if(empty($tc_monday_in)){
                       echo '<td style="color:white;">Off Day</td>';
                     }
                    else{
                       
                           if($holidays->count()>0){
                             foreach($holidays as $h){
                              echo  '<td style="color:white;">'.$h->title.'</td>';
                             }
                           }
                           else{
                             echo '<td style="color:white;">Absent</td>';
                           }
                       }
                 } //end Monday
                } //end of day_off is not empty
                else{
                    
                       if($holidays->count()>0){
                             foreach($holidays as $h){
                              echo  '<td style="color:white;">'.$h->title.'</td>';
                             }
                           }
                           else{
                             echo '<td style="color:white;">Absent</td>';
                           }

                }

                ?>
                

                <td style="color: white;">--</td>        
                </tr> 
          @endif
          @endif
          
       

           
          
                

          @endforeach    <!-- //$emp variable -->

          @endif
         


         
         </tbody>
       </table>
     </div>
   </div>
 </div>
 
</div>
</div>
<br>
<div class="row-fluid">
	<div class="col-md-12">
		<div class="grid simple ">
			<div class="grid-title no-border">
				<h4>Summary</h4>
			</div>
			<div class="grid-body no-border">

				<table class="table no-more-tables">
					<thead>
						<tr>
							<th style="width:9%">S.no</th>
							<th style="width:22%">Status</th>
							<th style="width:6%">Record</th>
							
						</tr>
					</thead>
         <?php 
         $total = $total_absent + $total_present + $total_late + $total_leave + $total_attach ; 
         $total_late_present= $total_present + $total_late;
         ?>
         <tr>
          <td>1</td>
          <td>Total Employees</td>
          <td>{{$emp_by_dep}}</td>
        </tr>
        <tr>
          <td>2</td>
          <td>Present</td>
          @if($total_present > 0)
          <td style="background-color:#0aa699;color:white;">{{$total_late_present}}</td>
          @else
          <td class=" ">{{$total_present}}</td>
          @endif
        </tr>
        <tr>
          <td>3</td>
          <td>Late</td>
          @if($total_late > 0)
          <td style="background-color:#f35958;;color:white; ">{{$total_late}}</td>
          @else
          <td class=" ">{{$total_late}}</td>
          @endif
        </tr>
        <tr>
          <td>4</td>
          <td>Absent</td>
          @if($total_absent > 0)
          <td style="background-color:#29252799;color:white;">{{$total_absent}}</td>
          @else
          <td class=" ">{{$total_absent}}</td>
          @endif
        </tr>
        <tr>
          <td>5</td>
          <td>Leave</td>
        <!--   -->
          <td class=" " style="background-color:#735f87;color:white"> {{$total_leave}}</td>
        </tr>
        <tr>
          <td>6</td>
          <td>Attach</td>
          <td style="background-color:#0090d9;color:white;">{{$total_attach}}</td>

        </tr>
      </table>
    </div>
  </div>
</div>
</div>
</div>
</div>
@include('include.important')

@endsection