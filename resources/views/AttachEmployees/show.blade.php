@extends('layout.layout')
@section('title','Attach Employee View')
@section('content')
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">

            <div class="grid-body ">
   
            <table class="table table-striped table-bordered table-advance table-hover">
          <thead>
            <tr>  
              <td><b>Employee :</b></td>
              <td>{{$user->name}}</td></tr>
              <tr>  
                <td><b>Attach From :</b></td>
                <td>{{$attach_emp->from_date}}</td></tr>
                <tr>  
                  <td><b>Attach To :</b></td>
                  <td>{{$attach_emp->to_date}}</td>
                </tr>  
                <tr>  
                  <td><b>Time From:</b></td>
                  <td>{{$attach_emp->time_from}}</td>
                </tr>  
                <tr>  
                  <td><b>Time To :</b></td>
                  <td>{{$attach_emp->time_to}}</td>
                </tr> 
                <tr>  
                  <td><b>Description/Remarks :</b></td>
                  <td>{{$attach_emp->remarks?$attach_emp->remarks:"No remarks"}}</td>
                </tr> 
                <tr>  
                  <td><b>Status:</b></td>
                  @if($attach_emp->status==0)
                  <td>Pending</td>
                  @elseif($attach_emp->status==1)
                  <td>Approved</td>
                  @else
                  <td>Rejected</td>
                  @endif
                  
                </tr>          
              </thead>
              
              
            </tbody>

          </table>

            <a href="{{url('attach_approvel/'.$attach_emp->id)}}" class="btn  btn-primary" style="margin-left:2px;float:right" type="button" ><i class="fa fa-check"></i> Approv</a>  
            <a href="{{url('attach_rejected/'.$attach_emp->id)}}" class="btn  btn-danger" style="margin-right:2px;float:right"  type="button"><i class="fa fa-trash"></i> Reject</a> 
        </div>
        
      </div>
      
      <!-- END BASIC PORTLET-->
    </div>
  </div>
  @endsection
</div>