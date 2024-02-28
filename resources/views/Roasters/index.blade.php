@extends('layout.layout')
@section('title','Roster List')
@section('content')
<div class="row-fluid">
	<div class="span12">
		<div class="grid simple ">
			<div class="grid-body ">
				<h5><b>List of all duty rosters created for this department</b></h5>
				<a href="{{ route('timecategories.index') }}" 
					class="badge badge-warning float-md-right"
					style="float:right" 
				><i class="fa fa-clock-o"> </i> All Duty Rosters</a>
				<table class="table --dataTable" id="--dataexample" aria-describedby="example3_info">
					<thead>
						<tr role="row">
							<th>S#</th>
							<th>Title</th>
							<th>Department</th>
							<th width="20%">Action</th>
						</tr>
					</thead>

					<tbody role="alert" aria-live="polite" aria-relevant="all">
						<?php $i = 1 ;?>
						@foreach($roasters as $u)
						@foreach($u->roaster as $r)
						<tr>   
							<td> <?php echo $i?></td>                
							<td>{{ date('F', mktime(0, 0, 0, $r->month, 10)) }},{{ $r->year}}</td>         
							<td>{{$u->name}}</td> 
							<td>            
								<form action="{{route('roasters.destroy',$r->id) }}" method="post">       {{method_field('delete')}}
								@csrf   		
									<a href="{{ route('roaster.assigntable', $r->id) }}" class="btn btn-primary" style="color: white"><i class="fa fa-check"></i></a>
									<a href="{{ route('roasters.edit',$r->id) }}"class="btn btn-success" style="color: white" ><i class="fa fa-edit"></i> </a>                	
									<button type="submit" onclick="return confirm('Are you sure want to delete this record ?')" class="btn btn-danger">
										<i class="fa fa-trash"></i> 
									</button> 
									<a href="{{ route('roaster.copy_to_next_month',\Crypt::encrypt($r->id)) }}"class="btn btn-success" style="color: white" ><i class="fa fa-forward"></i> </a> 
								</form>         
							</td>

						</tr>

						<?php $i++?>  
						@endforeach
						@endforeach

					</tbody>
				</table>
				@include('sweetalert::alert')
				<a href="{{route('roasters.create')}}" class="btn btn-primary btn-cons" name="action" value="save" type="submit"><i class="fa fa-plus"></i> Add New</a>
			</div>

		</div>
	</div>
</div>
</div>
</div>	

@endsection