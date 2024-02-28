@extends('layout.layout')
@section('title','Present Employee')
@section('content')
<div class="row">
  <div class="span12">
    <div class="grid simple ">

      <div class="grid-body ">
        <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
          <thead>
            <tr role="row"><th>ID</th>
              <th>Name</th>
              <th>Department</th>
              <th>Designation</th>
              <th>Bps</th>
              <th>Remarks</th>
          </tr>
          </thead>

          <tbody role="alert" aria-live="polite" aria-relevant="all">  
        @foreach($attandance_reports->groupBy('id') as $rep)
            <tr style="background-color:#577089;">
                <td style="background-color:#577089;color:white">{{$rep->first()->id}}</td>  
                <td style="color:white;">{{$rep->first()->name}}</td>
                <td style="color:white;">{{$rep->first()->deparment_name}}</td>
                <td style="color:white;">{{$rep->first()->title}}</td>
                <td style="color:white;">{{$rep->first()->bps}}</td> 
                <td style="color: white">{{$rep->first()->remarks}}</td>   
           </tr>
       @endforeach 
          </tbody>
        </table>
         </div>
        </div>
      </div>
  </div>
  @endsection