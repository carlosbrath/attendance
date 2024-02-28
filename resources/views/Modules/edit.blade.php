       @extends('layout.layout')
       @section('title','Edit Module')
        @section('content')
           @if(session('message'))
               <div class="alert alert-success" style="text-align: center;">{{session('message')}}
                </div>
             @endif
        <div class="row">
        	<div class="col-md-12">
        		<div class="grid simple form-grid">
        			<div class="grid-body no-border">
        				<form class="form-no-horizontal-spacing" method="post" action = "{{route('modules.update',$module->id)}}" id="form-condensed" novalidate="novalidate">
        					{{method_field('put')}}
                             @csrf
        					<div class="row">
        						<div class="col-md-8">
                                   <div class="row form-row">
                                    <div class="form-group">
                                        <p></p>
                                      <label class="form-label"><strong>Parent id</strong></label>
                                      <div class=" right">
                                        <i class=""></i>
                                        <select name="parent_id"  style="width: 66%" class="select2 form-control">
                                             @foreach($module_name as $m)
                                            <option name=""  value="{{$m->id}}">{{$m->name}}</option>
                                            @endforeach                       
                                         
                                         </select>                        
                                 </div>
                             </div>

                         </div>
                                </div>
        							
        							<div class="row form-row">
        								<div class="col-md-8">
        									<label class="form-label"><strong>Name</strong></label>
        									<input value="{{$module->name}}"  name="name"  id="form3FirstName" type="text" class="form-control" placeholder="Enter type">
        						@if($errors->any())
            <p style="color:red">{{$errors->first('name')}}</p>
            @endif
        								</div>

        							</div>
                                    <div class="row form-row">
                                        <div class="col-md-8">
                                            <label class="form-label"><strong>Route</strong></label>
                                            <input value="{{$module->route}}"  name="route" id="form3FirstName" type="text" class="form-control" placeholder="Enter type">
                                       @if($errors->any())
            <p style="color:red">{{$errors->first('route')}}</p>
            @endif
                                        </div>

                                    </div>
        							<div class="row form-row">
                                        <div class="col-md-8">
                                            <label class="form-label"><strong>Module Class</strong></label>
                                            <input value="{{$module->icon_class}}"   name="module_class" id="form3FirstName" type="text" class="form-control" placeholder="Enter type">
                                       
                                        </div>

                                    </div>

        						</div>
        					</div>

        				</div>

        			</div>
        			<div class="form-actions">

        				<div class="pull-left">
        					<button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Update</button>
        					<button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Update & AddNew</button>
        					<button class="btn btn-white btn-cons" type="reset">Cancel</button>
        				</div>
        			</div>
        		</form>
        	</div>
        </div>
    </div>
</div>
@endsection