
<link href="{{url('assets/plugins/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet" type="text/css" />
<link href=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css " rel="stylesheet">
<link href="{{url('select2totree/select2totree.css')}}" rel="stylesheet" type="text/css" />
<style>
  .ad_filters{display:none}
  .dataTables_filter input,.dataTables_filter select{background-color:transparent!important;background-image:linear-gradient(to bottom,transparent 0,transparent 100%)!important}
  .dataTables_filter input,.dataTables_filter select{border:1px solid #0003!important;border-radius:8px!important;height:35px!important}
  .dataTables_filter .main .ai_query-container{position:relative;display:inline-block}
  .dataTables_filter .main .ai_query{border:2px double transparent!important;background-image:linear-gradient(white,#fff),linear-gradient(to right,red,orange)!important;background-origin:border-box;background-clip:padding-box,border-box}
  .btn-all,.btn-dis,.btn-div{background-color:#e9e9e9}
</style>

<form action="" class="col-md-12 tiles card_shadow white dataTables_filter m-b-5" method="get">
  {{ csrf_field() }}
  <div class="p-t-10 p-b-10 p-l-5 p-r-5">
    <div class="row clearfix">
      <div class="col-md-5 m-y-2">
        <select name="department_id" id="all_departments" class="form-control" placeholder="Select Department">
        </select>
      </div>
      <div class="col-md-4 m-y-2 jquicss">
        <input type="text" name="allregions_input" id="allregions_input" value="" class="form-control" placeholder="Enter Region, District or Tehsil">
        <input type="hidden" name="selected_dmgs" id="allregions" value="{{$data['selected_dmgs']}}">
      </div>
      <div class="col-md-3 m-y-2">
        <input type="date" name="date" class="form-control pull-left mydateformat" value="{{ $data['c_date'] }}" data-date="" data-date-format="DD-MM-YYYY" style="width:calc(100% - 47px)!important" />
        <button type="submit" class="btn btn-primary btn-success pull-right" title="Search" name="submit" value="Filter" style="border-radius:8px"><i class="fa fa-search"></i></button>
      </div>
    </div>
  </div>
</form>


<script src=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js "></script>
<script src="{{url('select2totree/select2totree.js')}}" type="text/javascript"></script>
<script src="{{url('public/assets/plugins/jquery-ui/jquery-ui.min.js')}}" type="text/javascript"></script>


<script>
  
  var dmg_autocomplete = @php echo json_encode($data['dmg_autocomplete']) @endphp;
  var dmg_autocomplete_keys = @php echo json_encode($data['dmg_autocomplete_keys']) @endphp;
  
  var region_ac = $( "#allregions_input" ).autocomplete({
                        source: dmg_autocomplete,
                        change: function( event, ui ) {
                            if(ui.item){
                                $('#allregions').val(dmg_autocomplete_keys[ui.item.label]);
                            }
                            else
                            {
                                $('#allregions').val('');
                                $("#allregions_input").val('');
                            }
                        },
                    });
  @php
    if(!empty($data['reg_autocomplete'])){
      echo 'region_ac.val("'.$data['reg_autocomplete'].'")';
    }
  @endphp
  
  var all_depts_data = @php echo json_encode($data['all_departments']);@endphp;
    //console.log(all_depts_data);

    $("#all_departments").select2ToTree({
      treeData: {
        dataArr: all_depts_data
      },
      maximumSelectionLength: 3
    });
    $("#all_departments").val("@php echo (isset($data['department_id']))?$data['department_id']:'all';@endphp").trigger('change');
    

</script>