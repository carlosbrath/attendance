@extends('layout.layout')
@section('title','State Dashboard')
@section('content')

@include('include.drilldown_filters')

<div class="row" style="margin-bottom:20px">
  @foreach($departments as $department)
    @if($department->present>0)
      <div class="col-md-4 p-2  card_main">
          <div class="card c_present text-center">
            <a href="{{   route('dw-present',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}" title="Drill Down" target="_blank">
              <p class="text_heading">{{ $department->name }}</p>
              <p class="text_count">{{ $department->present }}</p>
            </a>
          </div>
      </div>
    @endif
  @endforeach
</div>
  <div class="row">

    <div class="col-md-12 col-vlg-12 m-b-10  ">
      <div class="tiles white p-t-20 p-b-20 p-l-20 p-r-20">
        <table class="table m-0 " id="dataexample">
          <thead style="">
            <tr>
              <th class="width_80">Department</th>
              <th class="width_20">Present</th>
            </tr>
          </thead>
          <tbody>
            @foreach($departments as $department)
              @if($department->present>0)
                <tr>
                  <td class="v-align-middle bold text-success">
                    <a href="{{   route('dw-present',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}">
                    {{ $department->name }}
                    </a>
                  </td>
                  <td class="v-align-middle"><span class="muted">
                    <a href="{{ route('dw-present',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs'],'list'=>'yes']) }}">
                    {{ $department->present }}
                    </a>
                  </span> </td>
                </tr>
              @endif
            @endforeach
            
          </tbody>
        </table>

      </div>
    </div>
  </div>


  <script src=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js "></script>
<link href=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css " rel="stylesheet">

<link href="{{url('select2totree/select2totree.css')}}" rel="stylesheet" type="text/css" />
<script src="{{url('select2totree/select2totree.js')}}" type="text/javascript"></script>



@endsection
