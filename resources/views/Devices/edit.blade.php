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

                <div class="grid-body no-border">
                   <form class="form-no-horizontal-spacing" method="post"
                   action="{{route('devices.update',$device->id)}}" id="form-condensed" novalidate="novalidate">
                           {{method_field('PUT')}}
                           {{csrf_field()}}
                        <div class="row column-seperation">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-label"><strong>Department</strong></label>
                                    <div class=" right">
                                        <i class=""></i>
                                        <select name="department_id"  id="department_id" style="width: 66%" class="select2 form-control">

                                        </select>
                                        @if($errors->any())
                                        <p style="color:red">{{$errors->first('department_id')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label"><strong>Other Departments</strong></label>
                                    <div class=" right">
                                        <i class=""></i>
                                        <select name="other_deps[]"  id="other_deps" style="width: 66%" class="select2 form-control" multiple>

                                        </select>
                                        @if($errors->any())
                                        <p style="color:red">{{$errors->first('other_deps')}}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Title</strong></label>
                                        <input id="form3FirstName"  name="title" value="{{$device->title}}" type="text" class="form-control" placeholder="First Name">
                                          @if($errors->any())
                                            <p style="color:red">{{$errors->first('title')}}</p>
                                            @endif
                                    </div>

                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Serial Number</strong></label>
                                        <input name="serial_number" type="text" value="{{$device->serial_number}}" id="form3FirstName" type="text" class="form-control" placeholder="">
                                         @if($errors->any())
                                            <p style="color:red">{{$errors->first('serial_number')}}</p>
                                         @endif
                                    </div>

                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Remarks</strong></label>
                                        <input id="form3FirstName"  name="remarks" value="{{$device->remarks}}" type="text" class="form-control" placeholder="Remarks">
                                          @if($errors->any())
                                            <p style="color:red">{{$errors->first('remarks')}}</p>
                                            @endif
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>

                </div>
                <div class="form-actions">
                        <div class="pull-left">
                            <button class="btn btn-success btn-cons"  type="submit"><i class="icon-ok"></i>Update</button>
                        </div>
                    </div>
            </form>
        </div>
       </div>
   </div>
</div>

<script src=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js "></script>
<link href=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css " rel="stylesheet">

<link href="{{url('select2totree/select2totree.css')}}" rel="stylesheet" type="text/css" />
<script src="{{url('select2totree/select2totree.js')}}" type="text/javascript"></script>
<script>
    var all_depts_data = @php echo json_encode($cdeps);@endphp;

    $("#department_id").select2ToTree({treeData: {dataArr: all_depts_data}, maximumSelectionLength: 3});
   // $("#department_id").val("@php echo (!empty($device->department_id))?$device->department_id:'';@endphp").trigger('change');

    $("#other_deps").select2ToTree({treeData: {dataArr: all_depts_data}, maximumSelectionLength: 0});
    //$("#other_deps").val("@php echo (!empty($device->other_deps))?$device->other_deps:'';@endphp").trigger('change');
   var otherDepsValue = "@php echo (!empty($device->other_deps)) ? implode(',', $device->other_deps) : ''; @endphp";

    $("#other_deps").val(otherDepsValue.split(',')).trigger('change');

    $("#other_deps").select2ToTree({
        treeData: { dataArr: allDeptsData },
        maximumSelectionLength: 0
    });


</script>
@endsection
