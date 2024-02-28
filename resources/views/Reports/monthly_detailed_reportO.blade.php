@extends('layout.layout')
@section('title',' Monthly Status Report')
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
        .table-striped > tbody > tr:nth-child(odd) > td, 
.table-striped > tbody > tr:nth-child(odd) > th {
  white-space: nowrap;
}
.portal-drivers {
  overflow: auto;
}
</style>
<div class="row">
  <div class="col-md-12">
    <div class="grid simple form-grid">

      <div class="grid-body no-border">
        <form class="form-no-horizontal-spacing" action="{{route('monthly_det_rep')}}" 
             method="post" id="form-condensed" novalidate="novalidate">
          @csrf
          <div class="row column-seperation">
            <div class="col-md-12">
              <h4></h4>
                <div class="row form-row">
                <div class="col-md-12">
                  <label class="form-label"  style="font-size:15px ">Month</label>
                  <div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
                    <input type="text" autocomplete="off" name="dbmonth" placeholder="From Date" class="form-control">
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                   
                  </div>
                
                </div>
                 <div class="col-md-12">
                  <label class="form-label"  style="font-size:15px ">Date</label>
                  <div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
                    <input type="text" autocomplete="off" value="dd/mm/yyyy" name="from_date" placeholder="From Date" class="form-control">
                     <input type="text" autocomplete="off" value="dd/mm/yyyy" name="to_date" placeholder="From Date" class="form-control">
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                    
                  </div>
                
                </div>
              </div>
              <div  class="row form-row">
                <div class="control-group">
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
        </div>
        <div class="control-group">
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
        </div>
              </div>

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
              </div>
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

<div class="row">

 @if(session('message'))
 <div class="alert alert-success" style="text-align: center;">{{session('message')}}
 </div>
 @endif
 @if(session('error'))
 <div class="alert alert-danger" style="text-align: center;">{{session('error')}}
 </div>
 @endif

@if($users!='')

<h4><b>Monthly Status Report August 2019</b></h4>
  <div class="span12">
    <div class="grid simple ">

      <div class="grid-body table-responsive">
        <table id="example" class="table dataTable display nowrap table-responsive portal-drivers" aria-describedby="example3_info" style="width:100%">
        <!--<table class="table dataTable" id="example" aria-describedby="example3_info">-->
          <thead>
            <tr role="row">
              <th>S# No</th>
              <th>ID</th>
              <th>Name</th>
              <th>Designation</th>
                <th style="text-align:center;">Date</th>
                 <?php 


                                           

                                           $first_day=$first_day.'-01';
                                           $last_day = date("t", strtotime($first_day));


                                            for($i=1;$i<=$last_day;$i++){                           

                                              echo "<th>".$i."</th>";
                                         
                                             }
                                             ?> 
          </tr>
          </thead>

          <tbody role="alert" aria-live="polite" aria-relevant="all">
             
         @foreach($users as $u)     
                                           <tr style="height:43px !important">
                                             <td>{{$loop->iteration}} </td> 
                                             <td><?php echo $u->id?> </td>    
                                             <td>{{$u->name}}</td>
                                             <td>{{$u->title}}</td>
                                            <td style="text-align:center;padding:0px">in <br><hr style="margin:0px;border-color:#d6d5d5"> out</td>
                                      <?php 

                                        $first_day_date=$first_day.'-01';
                                        $last_day = date("t", strtotime($first_day_date));

                                        for($i=1;$i<=$last_day;$i++){ 

                                        $day=$i;
                                        $num_padded=sprintf("%02d",$day);  
                                        $date = $first_day."-".$num_padded;
                                        $emp_stat='A';
                                        $emp_status='<span>'.$emp_stat.'<br><hr style="margin:0px;border-color:#e2d3d3">'.$emp_stat.'</span>';
                                       
                                  if($emp_status_time_in->where('user_id',$u->id)->where('type',1)->count()>0){                                  
                                      foreach ($emp_status_time_in->where('user_id',$u->id) as $att) {
                                          
                                          if(date("Y-m-d",strtotime($att->att_checktime))==$date){

                                                $time_in = strtotime("+15 minutes", strtotime($att->time_in));
                                                $time_out =  strtotime($att->time_out);
                                                $time_in =  date("h:i", $time_in); 
                                     
                                                $time_out =  date("h:i", $time_out);
                                                
                                   
                                            if ((date("h:i:s",strtotime($att->att_checktime))) > $time_in) {
                               
                                                    $emp_status ='L';                                                                 
                                                                                      
                                               }       

                                            else{      
                                                    
                                                        
                                                     $emp_status='<span>'.$time_in.'<br><hr style="margin:0px;border-color:#e2d3d3">'.$time_out.'</span>';    

                                                               

                                              }                
                                                 

                                                   }
                                                 
                                                }

                                            }

                                    if($emp_status_leave_status->where('id',$u->id)->where('type',2)->count()>0){
                                            
                                           foreach ($emp_status_leave_status->where('id',$u->id) as $att_leave) {

                                            if(date("Y-m-d",strtotime($att_leave->date))==$date){
                                            

                                             $leave_status=$att_leave->title;
                                                  
                                            $emp_status='<span>'.$leave_status.'<br><hr style="margin:0px;border-color:#d6d5d5">'.$leave_status.'</span>';

                                                                                    
                                              
                                            }


                                          }

                                          
                                        } 

                                               echo  '<td style="text-align:center;padding:0px">'.$emp_status.'</td>';

                                          }
                                  
                                     ?> 
                                                 
                                           </tr>                                          
                                        @endforeach            
          </tbody>
        </table>
      </div>
        </div>
      </div>
  
                            <div class="span2" style="margin-left:0px;">
                                <div class="table-responsive">
                                 <table id="example" class="table dataTable display nowrap" aria-describedby="example3_info" style="width:100%;border-right-style:none;">
                                    <thead>
                                    <tr>
                                        <th>Absent</th>
                                        <th>Present</th>
                                        <th>Late</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        
                                          @foreach($users as $u)
                                          <tr style="height:43px !important">

                                           
                                             <td>0</td> 
                                             <td>0</td> 
                                             <td>0</td>    
                                              
                                          
                                            

                                           </tr>
                                          @endforeach                                     
                                    </tbody>
                                </table>
                              </div>

                            </div>
                            @endif
  </div>
</div>

</div>
@include('include.important')

@endsection