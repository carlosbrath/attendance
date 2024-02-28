@extends('layout.layout')
@section('title','Datewise Attendance Compliance Report')
@section('content')
@include('Reports.print_header_datewise_compliance')
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
          font-size:10px;
        }

        #monthlyreport th{
          font-size: 8px !important;
        }
        #monthlyreport td{
          font-size: 8px !important;
          padding-left:0px !important;
          padding-right:0px !important; 
         
          padding-bottom:0px !important; 
          width: 1px !important;
        }
         #monthlyreport span{
          font-size: 8px !important;
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
        <form class="form-no-horizontal-spacing" action="{{route('datewise_attendance_compliance')}}" 
             method="post" id="form-condensed" novalidate="novalidate">
          @csrf
          <div class="row column-seperation">
            <div class="col-md-12">
              <h4></h4>
                <div class="row form-row">
                     
                  <div class="col-md-8">
                      <h4></h4>
                      <div class="form-group">
                        <label class="form-label"><strong>Departments</strong></label>
                        <div class=" right">
                        <i class=""></i>
                        <select name="department_id" id="department_id" style="width:367px"  class="select2 form-control">
                         <option  value="all">All</option>
                        @foreach($department as $key=>$value)
                        <option @if($key == $choosen_department_id) selected @endif  value="{{$key}}">{{$value}}</option>
                        @endforeach
                        
                        </select>
                        @if($errors->any())
                        <p style="color:red">{{$errors->first('deparment_id','Department field is required')}}
                        </p>
                        @endif

                        </div>
                      </div>
                  </div>
                 <div class="col-md-12">
                  <label class="form-label"  style="font-size:15px ">From Date</label>
                  <div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
                    <input type="text" autocomplete="off" value="{{ date('Y/m/01') }}" name="from_date" placeholder="From Date" class="form-control">
                     
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                    
                  </div>
                
                </div>

                <div class="col-md-12">
                  <label class="form-label"  style="font-size:15px ">To Date</label>
                  <div style="width: 338px;" class="input-append success date col-md-10 col-lg-4 no-padding">
                    <input type="text" autocomplete="off" value="{{ date('Y/m/d') }}" name="to_date" placeholder="From Date" class="form-control">
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                  </div>
                </div>
              </div>
              <div class="form-actions">
                <button class="btn btn-success" name="search" type="submit">Generate Report</button>
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


<h4><b></b></h4>
<div class="pull-right">
      <button class="btn-cogs" style="margin-top:2px" id="monthly_report_print" ><i class="icon-ok"></i>Print</button>
      <button class="btn-cogs" style="margin-top:2px" onclick="tableToExcel('monthlyreport', 'Monthly Attendance')" value="Export to Excel" ><i class="icon-ok"></i>Export to Excel</button>                
</div>
  @if(isset($users))
  <div class="col-md-12">
    <div class="grid simple ">

      <div class="grid-body table-responsive">
       <!--  <div class="col-md-12"><button id="btn_print" class="btn btn-success">Print</button></div> -->
       <div class="table-responsive">

        <table id="monthlyreport"  class="monthly_repor table dataTable display nowrap table-responsive table-bordered portal-drivers" aria-describedby="example3_info" style="width:100%">
            <thead>
               <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Expected Hours</th>
                <th>Worked Hours</th>
                <th>Late Hours</th>
                <th>Early Out Hours</th>
              </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                  <tr>
                      <th>{{ $user->id }}</th>
                      <th>{{ $user->name }}</th>
                      <th>
                          {{ $attendance->where('user_id',$user->id)->sum('expected_hours')}}
                          
                      </th>
                      <th>
                          {{ $attendance->where('user_id',$user->id)->sum('hours_worked')}}
                          
                      </th>
                      <th>
                          {{ $attendance->where('user_id',$user->id)->where('status','late')->sum('late_hours')}}
                          
                      </th>
                      <th>
                          {{ $attendance->where('user_id',$user->id)->sum('early_hours')}}
                          
                      </th>
                  </tr>
                @endforeach
            </tbody>
         </table>
        </div>
        </div>
      </div>
     </div>
    </div>
   </div>
   @endif
</div>         
@include('include.important')
@endsection

<script type="text/javascript">
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