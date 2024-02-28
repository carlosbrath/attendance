@extends('layout.layout')
@section('title','Attach Employee')
@section('content')
<div class="row">
  <div class="span12">
    <div class="grid simple ">
      <div class="grid-body ">
        <table class="table dataTable" id="example3" aria-describedby="example3_info">
          <thead>
            <tr role="row">
              <th>ID</th>
              <th>Name</th>
              <th>Department Name</th>
              <th>Date</th>
          </tr>
          </thead>
          <tbody role="alert" aria-live="polite" aria-relevant="all">
              @foreach($attandance_reports as $att)
              <tr>
                <td>{{$att->id}}</td>
                <td>{{$att->name}}</td>
                <td>{{$att->department_name}}</td>
                <td>{{$att->attach_date}}</td>
              </tr>
              @endforeach
          </tbody>
        </table>
         </div>
        </div>
      </div>
  </div>
  @endsection