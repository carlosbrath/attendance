@extends('layout.layout')
@section('title','Employees on Leave')
@section('content')
<div class="row">
  <div class="span12">
    <div class="grid simple ">

      <div class="grid-body ">
        <table id="dataexample" class="table dataTable display nowrap" aria-describedby="example3_info" style="width:100%">
        <!--<table class="table dataTable" id="example" aria-describedby="example3_info">-->
          <thead>
            <tr role="row"><th>ID</th>
              <th>Name</th>
              <th>Department</th>
              <th>Designation</th>
              <th>File #</th>
              <th>Bps</th>
              <th>In</th>
              <th>Out</th>
              <th>Time From</th>
              <th>Time To</th>
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
         $Emp_Status="";
         $checkdata[]=$rep;
         if($rep->first()->attendance_time=='' || $rep->first()->time_in==''  ){

          if($rep->first()->leave_date!=''){
               $total_leave+=1;
              if($rep->first()->leave_type==1){
               $Emp_Status='Short Leave';
              }
               $timeout='--';   
               $timein='--';
               $timecategory='--';   
               $remarks='--';  
           }

         }

        

         ?>
       
        
         @if($Emp_Status=='Short Leave')
          <tr style="background-color:#1a033a99;color:white">
           <td style="background-color:#1a033a99;color:white">{{$rep->first()->id}}</td>  
           <td style="color:white;">{{$rep->first()->name}}</td>
           <td style="color:white;">{{$rep->first()->deparment_name}}</td>
           <td style="color:white;">{{$rep->first()->title}}</td>
           <td style="color:white;">{{$rep->first()->file_number}}</td> 
           <td style="color:white;">{{$rep->first()->bps}}</td> 
           <td style="color:white;">{{$timein}}</td>
           <td style="color:white;">{{$timein}}</td>
           <td style="color:white;">{{$rep->first()->time_from}}</td> 
           <td style="color:white;">{{$rep->first()->time_to}}</td> 
           <td style="color:white;">Short Leave</td>
           <td style="color:white;">--</td>
          @endif  
          @endforeach 
          </tbody>
        </table>
      </div>
        </div>
      </div>
  
  </div>
  @endsection