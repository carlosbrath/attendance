@extends('layout.layout')
@section('title','Assign Right')
@section('content')
<div class="row">
	<div class="col-md-12 col-sm-6 spacing-bottom">
		<div class="widget">
			<div class="widget-title dark">
				<div class="pull-left ">
					
				</div>
				Assign Module To Roles
			</div>
			<form  method="post" action="{{url('storeassignright')}}">
            @csrf
			<div class="widget-body">
				<div class="dd" id="nestable">
                     <input id="node-0-1" name="roles"
                         value="{{Request::segment(2) }}" data-id="custom-1" type="hidden">
					  @foreach($parent_modules as $m)
					<div class="dd-handle"><input type="checkbox" value="{{$m->id}}" name="modules[]"> {{ $m->name }}</div>
					<ul class="dd-list">
						  @foreach($moduleright->where('parent_id',$m->id) as $child)
						   <?php
                                   
                                   $assgin_right =$check_module->where('role_id',Request::segment(2));
                                   $checked="";
                                   foreach ($assgin_right as $module) {
                                     
                                     if($child->id == $module->module_id ){

                                      $checked= ($child->id=$module->module_id  ? 'checked' : '');


                                    }

                                  }

                                 ?>
						<li  style="list-style-type: none;" class="dd-item" data-id="6">
							<div class="dd-handle"><input type="checkbox"  name="modules[]" value="{{$child->id}}" <?php echo $checked ?>><span> {{ $child->name }}</span></div>
						</li>
						@endforeach
					</ul>
			         @endforeach

		
			</div>
		</div>
		<div class="form-actions">

			<div class="pull-right">
				<button class="btn btn-primary btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
			</div>
		</div>
	</form>
	</div>
	</div>
	</div>





@endsection