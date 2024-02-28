@extends('layout.layout')
       @section('title','Add Device')
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
       #s2id_department_id{display:none}
       </style>

       <div class="row">
       	<div class="col-md-12">
       		<div class="grid simple form-grid">
				<div class="grid-title no-border">
                    <h4>Register New Device</h4>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"></a>
                    </div>
                </div>
       			<div class="grid-body no-border">
       				<form class="form-no-horizontal-spacing" method="post" action="{{route('devices.store')}}" id="form-condensed" novalidate="novalidate">
       					@csrf
       					<div class="row column-seperation">
       						<div class="col-md-8">
       							<div class="row form-row">
       								<div class="col-md-8">
       									<label class="form-label"><strong>Title</strong></label>
       									<input id="form3FirstName"  name="title" value="{{old('title')}}" type="text" class="form-control" placeholder="Device Title">
					       			  @if($errors->any())
							            <p style="color:red">{{$errors->first('title')}}</p>
							            @endif
       								</div>
       							</div>
                                <div class="row form-row">
                                    <div class="col-md-8">

                                        <label class="form-label"><strong>Serial Number</strong></label>
                                        <input name="serial_number" type="text" value="{{old('serial_number')}}" id="form3FirstName" type="text" class="form-control" placeholder="">
                                         @if($errors->any())
                                            <p style="color:red">{{$errors->first('serial_number')}}</p>
                                         @endif
                                    </div>

                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8">

                                        <label class="form-label"><strong>Department</strong></label>
                                        <div><select class="select2 form-control" id="department_id"  required name="department_id">

                                        </select></div>
                                         @if($errors->any())
                                            <p style="color:red">{{$errors->first('department_id')}}</p>
                                         @endif
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Other Departments</strong><em>(if any)</em></label>
                                        <div>
                                            <select class="select2 form-control" id="other_deps" name="other_deps[]" multiple >
                                                <!-- Add options here if needed -->
                                            </select>
                                        </div>
                                        @if($errors->any())
                                            <p style="color:red">{{$errors->first('other_deps')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="row form-row">
       								<div class="col-md-8">
       									<label class="form-label"><strong>Remarks</strong></label>
       									<input id="form3FirstName"  name="remarks" value="{{old('remarks')}}" type="text" class="form-control" placeholder="Remarks">
					       			  @if($errors->any())
							            <p style="color:red">{{$errors->first('remarks')}}</p>
							            @endif
       								</div>
       							</div>
                                <br/>

       						</div>
       					</div>

       				</div>

       			</div>
       			<div class="form-actions">
						<div class="pull-left">
							<button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Register & Add New</button>
							<button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
							<button class="btn btn-white btn-cons" type="reset">Cancel</button>
						</div>
					</div>
       		</form>
       	</div>
       </div>
   </div>
</div>

<script src="{{url('css/new_css/css/js/jquery-3.3.1.js')}}" type="text/javascript"></script>
<script src=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js "></script>
<link href=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css " rel="stylesheet">

<link href="{{url('select2totree/select2totree.css')}}" rel="stylesheet" type="text/css" />
<script src="{{url('select2totree/select2totree.js')}}" type="text/javascript"></script>
<script>
//(function ($) {

    var all_depts_data = @php echo json_encode($cdeps);@endphp;

    $("#department_id").select2ToTree({treeData: {dataArr: all_depts_data}, maximumSelectionLength: 3});
    //$("#department_id").val("@php echo (isset($data['department_id']))?$data['department_id']:'all';@endphp").trigger('change');

    $("#other_deps").select2ToTree({treeData: {dataArr: all_depts_data}, maximumSelectionLength: 3});
    //$("#department_id").val("@php echo (isset($data['other_deps']))?$data['other_deps']:'all';@endphp").trigger('change');

//});
</script>

@endsection
