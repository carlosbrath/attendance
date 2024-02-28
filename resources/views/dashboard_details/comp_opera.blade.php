@extends('layout.layout')
@section('title','Computer Operator Employee')
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
            
              @foreach($comp_oper as $c)
              <tr>
                <td>{{$c->id}}</td>
                <td>{{$c->name}}</td>
                <td>{{$c->title}}</td>
                <td>{{$c->department_name}}</td>
                <td>{{$c->dob}}</td>
              </tr>
              @endforeach
          </tbody>
        </table>
         </div>
        </div>
      </div>
  </div>
  @endsection