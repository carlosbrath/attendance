@extends('layout.layout')
@section('title','State Dashboard')
@section('content')


  <div class="row">

    <div class="col-md-12 col-vlg-12 m-b-10 ">
      <div class="tiles white p-t-20 p-b-20 p-l-20 p-r-20">
        <table class="table no-more-tables m-t-20 m-b-30" id="dataexample">
          <thead style="">
            <tr>
              <th class="width_30">Name / F.Name</th>
              <th class="width_20">Designation</th>
              <th class="width_10">BPS</th>
              <th class="width_30">Remarks</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data['employees'] as $employee)
            <tr>
              <td class="v-align-middle bold text-success">
              <a href="{{ route('employee_daily_report',$employee->id) }}">
                {{ $employee->name }} / {{ $employee->father_name }}
              </a>
              </td>
              <td class="v-align-middle bold text-success">{{ $employee->designation ?? '--' }}</td>
              <td class="v-align-middle bold text-success">{{ $employee->bps }}</td>
              <td class="v-align-middle bold text-success">{{ $employee->remarks }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>

      </div>
    </div>
  </div>


@endsection
