@extends('layout.layout')
@section('title','Roster Assign')
@section('content')
<style type="text/css">
select option { 
	width: 80px;
}
#widget-body{
	max-width: 100em;
	height: 60em;
	/*padding: 1em;*/
	margin: auto;
	border: 0.062em solid #999;*/
	background-color: #fff;
	overflow: auto;
	direction: left;
	text-align: left;
}
.roster_tcat{
  color: #fff !important;
}
.roster-tcat-panel{
  min-height: 62px;
}
</style>
@include('Roasters.roster_modal')


<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">
            <div class="grid-body ">
                @foreach($timecategory as $t) 
                <div class="col-lg-1 col-md-1">
                  <div class="panel roster-tcat-panel" style="<?php echo "background-color:$t->color"; ?>">
                    <div class="panel-heading">
                      <div class="row">
                        <div class="col-xs-12 text-center">
                          <div class="roster_tcat">{{$t->title}}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
          </div> 
      </div>
</div>       
<div class="row-fluid">
  <div class="pull-right">
      <button class="btn-cogs" style="margin-top:2px" id="roster_report" ><i class="icon-ok"></i>Print</button>
  </div>  
<div class="col-md-12" id="widget-body"> 
  <div class="col-md-4"> 
        <table class="table table-bordered no-more-tables onlyPrint" id="dutyschedule" style="background-color:#e6d0d0;">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>  
            </tr>
          </thead>
           <tbody>
              <?php $i=1?>
              @foreach($users as $u)      
                  <tr style="height:80px !important">
                     <td><?php echo $u->id?> </td>    
                     <td>{{$u->name}}</td>    
                  </tr>   
                 <?php  $i++ ?>
              @endforeach
        
          </tbody>
          
        </table>
        <br>
  </div>
  <div class="col-md-8" style="margin-left:0px;">     
  <div class="table-responsive "> 
  <table  class="table table-bordered no-more-tables" style="background-color:#e6d0d0;">
        <thead>
          <tr style="width: 80px">
            <?php 
            $a_date=$roster->year."-".$roster->month."-01";
            $last_day = date("t", strtotime($a_date));
            for($i=1;$i<=$last_day;$i++){
              echo "<th class='clientID'>".$i."</th>";
            }
            ?>         
          </tr>                                
        </thead>
        <form>
          @csrf
          <tbody>
    
    <?php     
          $label_width='';           
          foreach($users as $u){
          $timecategory_id ="";
          $tcatt_id="";
          $user_id="";
          $leaves ="";
          $day="";
           $selected= "";
              
            echo "<tr>";            
            $user_previous_tcat = [];
            for($i=1;$i<=$last_day;$i++){ 
              $tcats ="";
              $colspan="";
              $selectfirst="";
              $bg_color="";
              $tcat_name=""; 
              $day=$i;
              $num_padded=sprintf("%02d",$day);
              
              $current_roster_date = $roster->year."-".str_pad(str_pad($roster->month, 2, '0', STR_PAD_LEFT), 2)."-".$num_padded;
              $day = date("D",strtotime($current_roster_date));    
              $timecategory_from_date=$roster_details->where('user_id',$u->id)->where('tcat_from_date','=',$current_roster_date);
               $leaves_icon_from_date=$leaves_icon->where('user_id',$u->id)->where('from_date','=',$current_roster_date);
              $emp_leav = $roster_leaves->where('user_id',$u->id)
                                          ->where('leave_date','=',$current_roster_date);
              $emp_attach = $roster_attachs->where('user_id',$u->id)
                                          ->where('attach_date','=',$current_roster_date);
               $this_date_roster_detail  = $roster_details->where('user_id',$u->id)->where('tcat_from_date','<=',$current_roster_date)->where('tcat_to_date','>=',$current_roster_date);

               $tcat_label = "";
                $tcat_name = "";
                $bg_color = "";
                $colspan = "";
                $show_td = false;
              if($this_date_roster_detail->count()>0){
                $tcat_name = $dept_tcats->where('id',$this_date_roster_detail->first()->tcat_id)->first()->title;
                $color_name = $dept_tcats->where('id',$this_date_roster_detail->first()->tcat_id)
                                   ->first()->color;
                $bg_color='background-color:'.$color_name;
                  $show_td = true;
              }
             if($show_td){
              $close_icon="";
              $time_in = "";
              $this_day_transactions = [];

              $this_day_attendance = $attendance->where('checkdate',$current_roster_date)
                                      ->where('user_id',$u->id);

              $roster_tcat_details = $roster_user_tcats->where('tcat_from_date',$current_roster_date)->where('user_id',$u->id);
              $attendance_status = "";
              if($this_day_attendance->count()>0){
                  $this_day_transactions = [];
                  foreach($this_day_attendance as $att){
                      $this_day_transactions[$att->checktime] = $att->checktime;
                  }
                  sort($this_day_transactions);
              }
              if($roster_tcat_details->count()>0){
                $check_day_off=$roster_user_tcats->where('tcat_from_date',$current_roster_date)->where('user_id',$u->id);

                if($check_day_off->first()->type==3){
                   $attendance_status = "Absent";
                }
                elseif($check_day_off->first()->type==2){
                 $attendance_status = "leave";

                }
                  else{
                    $attendance_status = "d/o3"; 
                }
                
                  //means the users have this date time category
                  $tcat_detail = $roster_tcat_details->first();
                  if($tcat_detail->time_cat_type == 2){
                    $user_previous_tcat[$u->id] = [
                            'tcat_id'=>$tcat_detail->tcat_id,
                            'tcat_from_date'=>$tcat_detail->tcat_from_date,
                            'tcat_to_date'=>$tcat_detail->tcat_to_date,
                        ];
                    if(count($this_day_transactions)>0){  //means the user is present
                        $attendance_status = date("H:m:i",strtotime($this_day_transactions[0]));
                    }else{
                        //the user is absent
                         if($check_day_off->first()->type==1){
                              $attendance_status = "Absent";
                         }
                         if($check_day_off->first()->type==3){
                               $attendance_status = "d/o";  
                         }                             
                    }
                  }else{

                      if(count($this_day_transactions)>1){
                          $time_in = $this_day_transactions[0];
                          $time_out = $this_day_transactions[count($this_day_transactions)-1];
                          $attendance_status = $time_in."-".$time_out;
                      }else if(count($this_day_transactions)==1){
                          $time_in = $this_day_transactions[0];
                          $attendance_status = $time_in."-MTO";
                      }else{
                          //the user is absent 
                          if($check_day_off->first()->type==1){
                              $attendance_status = "Absent";
                         }
                         elseif ($check_day_off->first()->type==2) {
                              $attendance_status = "leave";   
                         }
                         else{
                               $attendance_status = "d/o"; 
                          } 
                                                
                     }
                  }

              }else{
                  
                  if(array_key_exists($u->id,$user_previous_tcat)){
                    if($user_previous_tcat[$u->id]['tcat_to_date'] == $current_roster_date){
                        //here show timeout
                      if(count($this_day_transactions)>0){
                        $time_out = date("H:m:i",strtotime($this_day_transactions[count($this_day_transactions)-1]));
                        $attendance_status = "OUT-".$time_out;
                        unset($user_previous_tcat[$u->id]);
                      }else{
                           //the user is absent
                          $attendance_status = "Absent";                       
                      }
                    }
                  }
               }
               if($timecategory_from_date->count()>0){

                   if($timecategory_from_date->first()->tcat_from_date==$current_roster_date){

                       $close_icon="<i class='fa fa-trash text-danger' aria-hidden='true'></i>";
                   }
               }
                 echo "<td  class='celldelete' style='".$bg_color.";height:80px;text-align:center;'>".$close_icon."
                            <label id='label_date'  style='display:none'>".$roster->year."-".$roster->month."-".$i."</label>
                            <label style='display:none' id='label_id' >".$u->id."</label>
                            <span style='color:#fff;font-size:10px'>".$attendance_status."</span>
                        </td>";
                  }else{
                        
                if($emp_leav->count()>0){

                    if($emp_leav->where('user_id',$u->user_id)->where('leave_date',$current_roster_date)){
                       $close_icon="";  
                      if($leaves_icon_from_date->count()>0){

                      if($leaves_icon_from_date->first()->from_date==$current_roster_date){
                       $close_icon="<i class='fa fa-trash text-danger' aria-hidden='true' style='color:white;'></i>";
                        }
                     }
                         echo "<td  class='cell_leave_delete'    
                              style='background-color:#466319;height:80px;text-align:center;'>".$close_icon."
                              <label id='label_date' style='display:none'>".$roster->year."-".$roster->month."-".$i."</label>
                              <label style='display:none' id='label_id' >".$u->id."</label>
                              <span style='color:#fff;font-size:10px'>Leave</span>
                          </td>";
                         
                        }
                    }else if($emp_attach->count()>0){

                         echo "<td    
                              style='background-color:#466319;height:80px;text-align:center;'>
                              </label>
                              <label style='display:none' id='label_id' >".$u->id."</label>
                              <span style='color:#fff;font-size:10px'>ATTACH</span>
                          </td>";
                         
                        
                    }
                      else{
                      echo "<td  class='cellvalue' style='height:80px;text-align:center;'><label id='label_date'  style='display:none'>".$roster->year."-".$roster->month."-".$i."</label>
                                <label style='display:none' id='label_id' >".$u->id."</label>
                            </td>";
                     }
                   }
                 }
            echo "</tr>";

      }        
        ?>
          
        </tbody>
      </form>
    </table>
  </div>
  
</div>
    <div id="printable_attendance" class="onlyPrint">
    @include('Reports.roster_print_header')
    <table class="table table-bordered" >
      <thead>
        <tr>
          <th>S#</th>
          <th>ID</th>
          <th>Name</th>  
          @php
            $a_date=$roster->year."-".$roster->month."-01";
            $last_day = date("t", strtotime($a_date));
            for($i=1;$i<=$last_day;$i++){
              echo "<th class='clientID'>".$i."</th>";
            }
          @endphp 
        </tr>
      </thead>
       <tbody>
          @foreach($users as $u)      
              <tr>
                 <td>{{ $loop->iteration }}</td> 
                 <td>{{ $u->id }}</td> 
                 <td>{{ $u->name }}</td>
                  @php
                    $a_date=$roster->year."-".$roster->month."-01";
                    $last_day = date("t", strtotime($a_date));
                    for($i=1;$i<=$last_day;$i++){
                      $thisdate = $roster->year.'-'.$roster->month.'-'.str_pad($i,2,'0',STR_PAD_LEFT);
                      $attendance_today = $attendance->where('user_id',$u->id)->where('checkdate',$thisdate)->sortBy('checktime');
                      echo '<td style="font-size:9px">';
                      if(count($attendance_today)>0){
                        dd($attendance_today);
                        echo "IN-".date("H:i",strtotime($attendance_today->first()->checktime));
                      }
                      if(count($attendance_today)>1){
                        echo "<br/>OUT-".date("H:i",strtotime($attendance_today->last()->checktime));
                      }
                      echo '</td>';
                    }
                  @endphp                     
              </tr>   
          @endforeach
    
      </tbody>
      
    </table>
    </div>
</div>
</div>
<script src="{{ asset('template_files/theme/assets/plugins/jquery/jquery-1.11.3.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
	$(".cellvalue").on('click', function(e){
	    var day=$(this).find('label').eq(0).text();
	    var user_id=$(this).find('label').eq(1).text();
	    $('#myModal1').modal('show');
	     $("#date").val(day);
	     $("#user_id").val(user_id);          
  });
     $("#submit").on('click', function(e){
    
    var tcat_id= $('#tcat_id').val();
    var from_date= $('.from_date').val();
    var to_date= $('#dp3').val();
    if(tcat_id=='' && from_date=='dd/mm/yyyy' && to_date=='dd/mm/yyyy'){
      alert('please fill the form...');
    }
    var roster_id= $('#roster_id').val();
    var user_id= $('#user_id').val();
    var url = "{{ route('rosters.rosterdetail') }}";
    jQuery.ajax({
      url:url,
      type: 'POST',
      dataType: 'json',
      data: {
       "_token": "{{ csrf_token() }}",
        "tcat_id": tcat_id,
        "tcat_from_date":from_date,
        "tcat_to_date":to_date,
        "user_id":user_id,
        "roster_id":roster_id 
      },
      success: function(result){
           if (result == 'success'){ 
               $('#myModal1').modal('hide');
               setTimeout(function(){// wait for 5 secs(2)
                location.reload(); // then reload the page.(3)
                  }, 1000); 
             }             
          else if (result == "fail"){ 
            alert('Roster Detail exist on from - to date ');
          }
      }});

    });

     $(".celldelete i").click(function(e) {
       e.stopPropagation();
       var user_id=$(this).siblings('#label_id').text();
       var date=$(this).siblings('#label_date').text();

       var url = "{{ route('rosters.rosterdelete') }}";
     var result = confirm("Are your want to delete timecategory?");
   if (result) {
 

      jQuery.ajax({
        url:url,
        type: 'POST',
        dataType: 'json',
        data: {
         "_token": "{{ csrf_token() }}",
          "user_id":user_id,
          "tcat_from_date":date 
        },
        success: function(result){

         if (result == 'success'){

             setTimeout(function(){// wait for 5 secs(2)
                location.reload(); // then reload the page.(3)
                  }, 1000); 
             }
           }
       });
       }
       });  
   $(".cell_leave_delete i").click(function(e) {
       
       var from_date=$(this).siblings('#label_date').text();  
        var user_id=$(this).siblings('#label_id').text();
         var url = "{{ route('rosters.leavedelete') }}";
          var result = confirm("Are your want to delete timecategory?");
   if (result) {
 

      jQuery.ajax({
        url:url,
        type: 'POST',
        dataType: 'json',
        data: {
         "_token": "{{ csrf_token() }}",
          "user_id":user_id,
          "from_date":from_date 
        },
        success: function(result){
        
         if (result == 'success'){
             setTimeout(function(){// wait for 5 secs(2)
                location.reload(); // then reload the page.(3)
                  }, 1000); 
             }
           }
       });
       }
   });

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
@include('include.important')
@endsection