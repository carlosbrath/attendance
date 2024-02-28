@extends('layout.layout')
@section('title','Leave Requests')
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
<div class="row-fluid">
  <div class="col-md-12">  
        <table class="table table-striped table-bordered table-advance table-hover">
          <thead>
            <tr>  
              <td><b>User :</b></td>
              <td>{{$user->name}}</td></tr>
              <tr>  
                <td><b>From :</b></td>
                <td>{{$leaverequest->from_date}}</td></tr>
                <tr>  
                  <td><b>To :</b></td>
                  <td>{{$leaverequest->to_date}}</td>
                </tr>  
                <tr>  
                  <td><b>Time From:</b></td>
                  <td>{{$leaverequest->time_from}}</td>
                </tr>  
                <tr>  
                  <td><b>Time To :</b></td>
                  <td>{{$leaverequest->time_to}}</td>
                </tr> 
                <tr>  
                  <td><b>Status:</b></td>
                  @if($leaverequest->status==0)
                  <td>Pending</td>
                  @elseif($leaverequest->status==1)
                  <td>Approved</td>
                  @else
                  <td>Rejected</td>
                  @endif
                </tr>  
              </thead>              
            </tbody>

          </table>

        </div>
        
        
      </div>  
      @if($leaverequest->status == 0)
      <a href="{{url('leave_request/'.$leaverequest->id)}}" class="btn  btn-primary" style="margin-left:2px" type="button" >Approved</a> 
      <a href="{{url('leave_reject/'.$leaverequest->id)}}" class="btn  btn-danger" style="margin-right:2px"  type="button">Rejected</a> 
      @endif
     
          <!-- END BASIC PORTLET-->
    </div>
  </div>
  @endsection
</div>