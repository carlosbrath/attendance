
@extends('layout.layout')
@section('title','Present Employee')
@section('content')
<div class="row">
  <div class="span12">
    <div class="grid simple ">

      <div class="grid-body ">
        <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
          <thead>
            <tr role="row"><th>ID</th>
              <th>Name</th>
              <th>Department</th>
              <th>Designation</th>
              <th>Bps</th>
              <th>In</th>
              <th>Out</th>
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
    ?>    
        @foreach($attandance_reports->groupBy('id') as $rep)
        <?php 
         $checkdata[]=$rep;

         $Emp_Status='Present';
         if($rep->first()->attendance_time!=''){    
          if($rep->first()->attendance_time <= $rep->first()->time_in  ){
           $total_present+=1; 
           $Emp_Status='Present';     
           }
         }
         ?>
      
       @if($Emp_Status=='Present')
  
        <tr style="background-color:#0aa699;">
         <td style="background-color:#0aa699;color:white">{{$rep->first()->id}}</td>  
         <td style="color:white;">{{$rep->first()->name}}</td>
         <td style="color:white;">{{$rep->first()->deparment_name}}</td>
         <td style="color:white;">{{$rep->first()->title}}</td>
         <td style="color:white;">{{$rep->first()->bps}}</td> 
         @if($rep->first()->attendance_time=='')
         <td style="color:white;">{{$timein}}</td>
         @else
         <?php $timein = $rep->first()->attendance_time; ?>
         <td style="color:white;">{{$timein}}</td>
         @endif

          @if($rep->count()>1)
          <?php
              $timeout=  $rep->last()->attendance_time;
           ?>
          <td style="color: white">{{$timeout}}</td>
          @elseif($rep->count()==1)
           <td style="color: white">--</td>
          @else
           <td style="color: white">--</td>
          @endif 
           <td style="color: white">{{$Emp_Status}}</td>
           @if($rep->count()==1)  
           <td style="width:112px;color:white;background-color:red">Missing Check Out</td>
           @else
           <td style="color: white">--</td>       
           @endif          
       </tr>
       @endif
       @endforeach 
          </tbody>
        </table>
         </div>
        </div>
      </div>
  </div>
  @endsection