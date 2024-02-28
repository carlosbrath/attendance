
       @extends('layout.layout')
       @section('title','Add Role')
       @section('content')
       @if(session('message'))
       <div class="alert alert-success" style="text-align: center;">{{session('message')}}
       </div>
       @endif
       <div class="row">
        <div class="col-md-12">
            <div class="grid simple form-grid">
                <div class="grid-title"><b>Add New Jurisdiction</b></div>
                <div class="grid-body">
                    <form class="form-no-horizontal-spacing" method="post"  action ="{{route('jurs.store')}}" id="form-condensed" novalidate="novalidate">
                          {{csrf_field()}}
                        <div class="row column-seperation">
                            <div class="col-md-8">

                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Title</strong></label>
                                        <input name="title" id="form3FirstName" value="{{old('title')}}" type="text" class="form-control" placeholder="Enter Title">
                                        @if($errors->any())
                                         <p style="color:red">{{$errors->first('title')}}</p>
                                       @endif
                                    </div>

                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8 mb-2">
                                      <label class="form-label"><strong>Departments</strong></label>
                                      <select name="department_id[]" id="all_departments" class="select2 form-control" multiple>

                                      </select>
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8 mb-2">
                                        <label class="form-label"><strong>Regions</strong></label>
                                        {!! Form::select('regions', ['all'=>'All Regions']+$data['regions']->toArray(), old('regions'), ['class' => 'form-control','id'=>'region_id']) !!}
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8 mb-2">
                                        <label class="form-label"><strong>Zones</strong></label>
                                        {!! Form::select('zones', ['all'=>'All Zones'], old('regions'), ['class' => 'form-control','id'=>'r_zone_id']) !!}
                                    </div>
                                </div>
                                <div class="row form-row">
                                    <div class="col-md-8 mb-2">
                                        <label class="form-label"><strong>Branches</strong></label>
                                        {!! Form::select('branches', ['all'=>'All Branches'], old('regions'), ['class' => 'form-control','id'=>'z_branch_id']) !!}
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="row column-seperation">
                            <div class="pull-left">
                                <button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
                                <button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Register & AddNew</button>
                                <button class="btn btn-white btn-cons" type="reset">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
       </div>
   </div>
</div>

<script src="{{url('css/new_css/css/js/jquery-3.3.1.js')}}" type="text/javascript"></script>
<script>
  $(document).ready(function(){

    $('#level_id,.db_region_id,.db_zone_id,.db_branch_id').change(function(){
        getDepartments();
    });




  });

</script>
<script src=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js "></script>
<link href=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css " rel="stylesheet">

<link href="{{url('select2totree/select2totree.css')}}" rel="stylesheet" type="text/css" />
<script src="{{url('select2totree/select2totree.js')}}" type="text/javascript"></script>
<script>

    var all_depts_data = @php echo json_encode($data['all_departments']);@endphp;

    $("#all_departments").select2ToTree({treeData: {dataArr: all_depts_data}, maximumSelectionLength: 3});
    $("#all_departments").val("@php echo (isset($data['department_id']))?$data['department_id']:'all';@endphp").trigger('change');

  //});
</script>
@endsection
