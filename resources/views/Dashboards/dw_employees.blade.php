@extends('layout.layout')
@section('title','State Dashboard')
@section('content')

@include('include.drilldown_filters')

  <div class="row" style="margin-bottom:20px"s>
    @foreach($departments as $department)
      <div class="col-md-12 p-2"><h2 class="text-center">{{ $department->name }}</h2></div>
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-5">
            <div class="row">
              <div class="col-md-6 p-2 card_main">
                <div class="card b1 card_shadow">
                  <a href="{{ route('dw-employees',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}" target="_blank">
                    <p class="text_heading">Total Employees</p>
                    <p class="text_count">{{ number_format($department->bb_total_employees,0) }}</p>
                  </a>
                </div>
              </div>
              <div class="col-md-6 p-2 card_main">
                <div class="card b2 card_shadow">
                  <a href="{{ route('dw-registered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}" title="Drill Down" target="_blank">
                    <p class="text_heading">Registered</p>
                    <p class="text_count">{{ number_format($department->reg_employees['total'],0) }}</p>
                  </a>
                  <div style="display: flex ; flex-direction:row">
                    @if(isset($department->reg_employees['childs']))
                      @foreach($department->reg_employees['childs'] as $key=>$value)
                      <span class="text-white sub"><span class="mytooltip" data-toggle="tooltip"  data-original-title="{{$key}}">{{$value}}</span></span>
                      @endforeach
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-7">
            <div class="row">
              <div class="col-md-4 p-2 card_main">
                <div class="card b2 card_shadow">
                  <a href="{{ route('dw-unregistered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}" title="Drill Down" target="_blank">
                    <p class="text_heading">Field Staff</p>
                    <p class="text_count">{{ number_format($department->unreg_employees['total'],0) }}</p>
                  </a>
                  <div style="display: flex ; flex-direction:row">
                    @if(isset($department->unreg_employees['childs']))
                      @foreach($department->unreg_employees['childs'] as $key=>$value)
                      <span class="text-white sub"><span class="mytooltip" data-toggle="tooltip"  data-original-title="{{$key}}">{{$value}}</span></span>
                      @endforeach
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-4 p-2 card_main">
                  <div class="card b11 card_shadow">
                    <p class="text_heading">Vacant Posts</p>
                      <p class="text_count">{{ number_format($department->bb_vacant_posts,0) }}</p>
                  </div>
              </div>
              <div class="col-md-4 p-2 card_main">
                  <div class="card b11 card_shadow">
                    <p class="text_heading">Unaccounted</p>
                      <p class="text_count">{{ number_format($department->bb_total_employees - ($department->unreg_employees['total']+$department->reg_employees['total']+$department->bb_vacant_posts),0)  }}</p>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3 p-2 card_main">
            <div class="card b3 c_present card_shadow">
              <a href="{{ route('dw-present',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}" target="_blank" title="Drill Down">
                <p class="text_heading">Present</p>
                <p class="text_count">{{ number_format($department->present,0) }}</p>
              </a>
            </div>
          </div>
          <div class="col-md-3 p-2 card_main">
            <div class="card b4 c_absent card_shadow">
              <a href="{{ route('dw-absent',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}"  target="_blank" title="Drill Down">
                <p class="text_heading">Absent</p>
                <p class="text_count">{{ number_format($department->absent,0) }}</p>
              </a>
            </div>
          </div>
          <div class="col-md-3 p-2 card_main">
            <div class="card b5 c_late card_shadow">
              <a href="{{ route('dw-late',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}"  target="_blank" title="Drill Down">
                <p class="text_heading">Late</p>
                <p class="text_count">{{ number_format($department->late,0) }}</p>
              </a>
            </div>
          </div>
          <div class="col-md-3 p-2 card_main">
            <div class="card b7 c_leave card_shadow">
              <a href="{{ route('dw-leave',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}"  target="_blank" title="Drill Down">
                <p class="text_heading">Leave</p>
                <p class="text_count">{{ number_format($department->leave,0) }}</p>
              </a>
            </div>
          </div>

          <div class="col-md-3 p-2 card_main">
            <div class="card b8 card_shadow">
              <p class="text_heading">On Schedule</p>

              <p class="text_count">-</p>

            </div>
          </div>
          <div class="col-md-3 p-2 card_main">

              <div class="card b9 card_shadow">
                <a href="{{ route('dw-offdays',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}"  target="_blank" title="Drill Down">
                  <p class="text_heading">Off Days</p>
                  <p class="text_count">{{ number_format($department->offdays,0) }}</p>
                </a>
              </div>

          </div>
          <div class="col-md-3 p-2 card_main">
            <div class="card b10 card_shadow">
              <p class="text_heading">Shift Not Started</p>

                <p class="text_count">-</p>

            </div>
          </div>
          <div class="col-md-3 p-2 card_main">
            <div class="card b6 card_shadow">
              <a href="{{ route('dw-attach',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}" target="_blank" title="Drill Down">
                <p class="text_heading">On-Duty</p>
              <p class="text_count">{{ number_format($department->attach,0) }}</p>
              </a>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>


  <div class="row">
    <div class="col-md-12 col-vlg-12 m-b-10 ">
      <div class="tiles card_shadow white p-t-10 p-b-5 p-l-20 p-r-20 table-responsive">
        <table class="table m-t-20 m-l-0 m-b-30 dept_table">
          <thead style="">
            <tr>
              <th class="width_5"> S.No </th>
              <th class="width_25" style="text-align:left!important"> Sub-department </th>
              <th class="width_5"> Total </th>
              <th class="width_5"> Registered </th>
              <th class="width_5"> FieldStaff </th>
              <th class="width_5"> Vacant </th>
              <th class="width_5"> Unaccounted </th>
              <th class="width_5"> Present </th>
              <th class="width_5"> Absent </th>
              <th class="width_5"> Late </th>
              <th class="width_5"> Leave </th>
              <th class="width_5"> Attach </th>
              <th class="width_5"> Off Days </th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1?>
            @foreach($departments as $department)
              <tr>
                <td> <?php echo  $i?></td>
                <td style="text-align:left!important">
                  <a href="{{ route('dw-employees',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) }}"  >
                    {{ $department->name }}
                  </a>
                  </td>
                <td  class="bold">
                  <a href="{{ route('dw-employees',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) }}">
                    {{ number_format($department->bb_total_employees,0) }}
                  </a>
                </td>
                <td  class="bold">
                  <a href="{{ route('dw-registered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs'],'list'=>'yes']) }}" title="Drill Down" target="_blank">
                    {{ number_format($department->reg_employees['total'],0) }}
                  </a>
                </td>
                <td  class="bold">
                  <a href="{{ route('dw-unregistered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs'],'list'=>'yes']) }}" title="Drill Down" target="_blank">
                    {{ number_format($department->unreg_employees['total'],0) }}
                  </a>
                </td>
                <td  class="bold">{{ number_format($department->bb_vacant_posts,0) }}</td>
                <td  class="bold">{{ number_format($department->bb_total_employees - ($department->unreg_employees['total']+$department->reg_employees['total']+$department->bb_vacant_posts),0) }}</td>
                <td  class="bold">
                  <a class="t-present" href="{{ route('dw-present',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) }}">
                    {{ number_format($department->present,0) }}
                  </a>
                </td>
                <td  class="bold">
                  <a class="t-absent" href="{{ route('dw-absent',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) }}">
                    {{ number_format($department->absent,0) }}
                  </a>
                </td>
                <td  class="bold">
                  <a class="t-late" href="{{ route('dw-late',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) }}">
                    {{ number_format($department->late,0) }}
                  </a>
                </td>
                <td  class="bold">
                  <a class="t-leave" href="{{ route('dw-leave',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) }}">
                    {{ number_format($department->leave,0) }}
                  </a>
                </td>
                <td  class="bold">
                  <a  href="{{ route('dw-attach',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) }}">
                    {{ number_format($department->attach,0) }}
                  </a>
                </td>
                <td  class="bold">
                  <a href="{{ route('dw-offdays',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) }}">
                    {{ number_format($department->offdays,0) }}
                  </a>
                </td>
              </tr>
              <?php  $i++ ?>
            @endforeach
          </tbody>
        </table>

      </div>
    </div>
  </div>


@endsection
