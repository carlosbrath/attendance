@extends('layout.layout')
@section('title','Director Employee')
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
              <th>Title</th>
              <th>Department Name</th>
              <th>dob</th>
              
          </tr>
          </thead>
          <tbody role="alert" aria-live="polite" aria-relevant="all">
            
              @foreach($director as $d)
              <tr>
                <td>{{$d->id}}</td>
                <td>{{$d->name}}</td>
                <td>{{$d->title}}</td>
                <td>{{$d->department_name}}</td>
                <td>{{$d->dob}}</td>
              </tr>
              @endforeach
          </tbody>
        </table>
         </div>
        </div>
      </div>
  </div>
  @endsection