

@extends('layout.layout')
@section('title','Absent Employee')
@section('content')
<style type="text/css">
        .form-actions {
        background-color: #22262e;
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
        <div class="span12">
                <div class="grid simple ">

                        <div class="grid-body ">
                                <table class="table dataTable" id="example3" aria-describedby="example3_info">
                                        <thead>
                                                <tr role="row"><th>ID</th>
                                                        <th>Name</th>
                                                        <th>Department</th>
                                                        <th>Designation</th>
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
         $Emp_Status='';
         if($rep->first()->attendance_time=='' || $rep->first()->time_in==''  ){
                if($rep->first()->leave_date == null){
                   $Emp_Status='Absent';
                   $total_absent+=1;
                   $timeout='--';   
                   $timein='--';
                   $timecategory='--';   
                   $remarks='--';  
                }
         }
         ?>
              @if($Emp_Status=='Absent')
              <tr style="background-color:#29252799;color:white;">
               <td style="background-color:#29252799;color:white;">{{$rep->first()->id}}</td>  
               <td style="color:white;">{{$rep->first()->name}}</td>
               <td style="color:white;">{{$rep->first()->deparment_name}}</td>
               <td style="color:white;">{{$rep->first()->title}}</td> 
               <td style="color:white;">{{$Emp_Status}}</td>
               <td style="color: white;">--</td>        
             </tr>
            @endif 
            @endforeach   
                             </tbody>
                     </table>
               </div>
                </div>
          </div>        
   </div>
</div>
@include('include.important')
@endsection