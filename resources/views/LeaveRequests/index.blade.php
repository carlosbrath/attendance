
@extends('layout.layout')
@section('title','Leave Requests')
@section('content')
<style>
body {font-family: Arial, Helvetica, sans-serif;}

#myImg {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image */
#caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation */
.modal-content, #caption {  
  -webkit-animation-name: zoom;
  -webkit-animation-duration: 0.6s;
  animation-name: zoom;
  animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
  from {-webkit-transform:scale(0)} 
  to {-webkit-transform:scale(1)}
}

@keyframes zoom {
  from {transform:scale(0)} 
  to {transform:scale(1)}
}

/* The Close Button */
.close {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content {
    width: 100%;
  }
}
</style>

<div class="row-fluid">
	<div class="span12">
		<div class="grid simple ">

			<div class="grid-body ">
				<table class="table dataTable" id="dataexample" aria-describedby="example3_info">
					<thead>
						<tr role="row">
							<th>S.No</th>
							<th>User</th>
							<th>From Date</th>
							<th>To Date</th>
							<th>From Time</th>
							<th>To Time</th>
              <th>Status</th>
              <th>Application</th>
							<th>Action</th>
						</tr>
					</thead>

					<tbody role="alert" aria-live="polite" aria-relevant="all">
						<?php $i = 1 ;?>
            
						@foreach($leaverequest as $l)
						@if($l->user !='')
						<tr>
					  
							<td><?php echo $i?></td>                 
							<td>{{$l->user->name}}</td>
							<td>{{$l->from_date}}</td>
							<td>{{$l->to_date}}</td>
							<td>{{$l->time_from}}</td>
							<td>{{$l->time_to}}</td>
              @if($l->status==1)
              <td>approved</td>
              @else
              <td>pending...</td>
              @endif
              
              @if($l->application_letter!="")
              <td>
              <div class="user-profile-pic" style="margin-top: 15px; width:40%;">
              <img src="<?php echo asset('photos/'.$l->application_letter)?>" width="150px" height="68px">
              </div>
              </td>
              @else
              <td>
             
              </td>
              @endif 

					<!-- 		<form action="{{ url('leaverequests/'.$l->id) }}" method="post">       {{method_field('delete')}}
								@csrf    -->
								<td>            
									<a href="{{ route('leaverequests.show', $l->id) }}" class="btn btn-success" style="color: white"><i class="fa fa-eye"></i></a>
									<a href="{{ route('leaverequests.edit',$l->id) }}"class="btn btn-primary" style="color: white" ><i class="fa fa-edit"></i> </a>  
								    <a href="{{ route('leave.leave_revert',$l->id) }}"class="btn btn-danger" style="color: white" ><i class="fa fa-trash"></i> </a>                	
									<!-- <button type="submit" onclick="return confirm('Are you sure want to delete Record ?')" class="btn btn-danger">
										 
									</button> --> 
								</td>
							<!-- </form> -->

						</tr>

						  @endif
                          <?php $i++ ?>
                         @endforeach
       

					</tbody>
				</table>
				@include('sweetalert::alert')
				<a href="{{route('leaverequests.create')}}" class="btn btn-primary btn-cons"  type="submit"><i class="icon-ok"></i>Add Leave Request</a>
			</div>

		</div>
	</div>
</div>
</div>
</div>	

@endsection