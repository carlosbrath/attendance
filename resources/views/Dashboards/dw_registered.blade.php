@extends('layout.layout')
@section('title','State Dashboard')
@section('content')

@include('include.drilldown_filters_nodate')


<div class="row" style="margin-bottom:20px">
  @foreach($departments as $department)
    @if($department['registered']['total']>0)
      <div class="col-md-4 p-2 card_main">
          <div class="card text-center">
            <a href="{{   route('dw-registered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}" title="Drill Down" target="_blank">
              <p class="text_heading">{{ $department->name }}</p>
              <p class="text_count">{{ $department['registered']['total'] }}</p>
            </a>
          </div>
      </div>     
    @endif  
  @endforeach
  @foreach($external_departments as $department)
  @endforeach
</div>

  <div class="row">
    <div class="col-md-12 col-vlg-12 m-b-10 ">
      <div class="tiles white p-t-20 p-b-20 p-l-20 p-r-20">
        <table class="table no-more-tables m-t-20 m-b-30" id="dataexample">
          <thead style="">
            <tr>
              <th class="width_80">Department</th>
              <th style="width_20">Regular</th>
              <th style="width_20">Contract</th>
              <th style="width_20">Contingency</th>
              <th style="width_20">Temporary</th>
              <th style="width_20">Attach</th>
              <th style="width_20">NotSet</th>
              <th style="width_20">Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($departments as $department)
              @if($department['registered']['total']>0)
                <tr>
                  <td class="v-align-middle bold text-success">
                    <a href="{{ route('dw-registered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}">
                      {{ $department->name }}
                    </a>
                  </td>
                  <td class="v-align-middle"><span class="muted">
                    <a href="{{ route('dw-registered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs'],'list'=>'yes','emp_type'=>'Regular']) }}">
                    {{ $department['registered']['childs']['Regular'] }}</a></span> 
                    
                  </td>
                  <td class="v-align-middle"><span class="muted">
                    <a href="{{ route('dw-registered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs'],'list'=>'yes','emp_type'=>'Contract']) }}">
                    {{ $department['registered']['childs']['Contract'] }}</a></span> 
                    
                  </td>
                  <td class="v-align-middle"><span class="muted">
                    <a href="{{ route('dw-registered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs'],'list'=>'yes','emp_type'=>'Contingency']) }}">
                    {{ $department['registered']['childs']['Contingency'] }}</a></span> 
                    
                  </td>
                  <td class="v-align-middle"><span class="muted">
                    <a href="{{ route('dw-registered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs'],'list'=>'yes','emp_type'=>'Temporary']) }}">
                    {{ $department['registered']['childs']['Temporary'] }}</a></span> 
                    
                  </td>
                  <td class="v-align-middle"><span class="muted">
                    <a href="{{ route('dw-registered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs'],'list'=>'yes','emp_type'=>'Attach']) }}">
                    {{ $department['registered']['childs']['Attach'] }}</a></span> 
                    
                  </td>
                  <td class="v-align-middle"><span class="muted">
                    <a href="{{ route('dw-registered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs'],'list'=>'yes','emp_type'=>'NotSet']) }}">
                    {{ $department['registered']['childs']['NotSet'] }}</a></span> 
                    
                  </td>
                  <td class="v-align-middle"><span class="muted">
                    <a href="{{ route('dw-registered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs'],'list'=>'yes']) }}">
                    {{ $department['registered']['total'] }}</a></span> 
                    
                  </td>
                </tr>
              @endif
            @endforeach
            @foreach($external_departments as $department)
            <tr>
              <td class="v-align-middle bold text-success">
                <a href="{{ $department['login_link'] }}" target="_blank">
                  {{ $department['name'] }}
                </a>
                </td>
              <td class="v-align-middle"><span class="muted">
              <a href="{{ $department['login_link'] }}"  target="_blank">
                {{ $department['late'] }}
              </a>
              </span> 
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        
      </div>
    </div>    
  </div>


@endsection