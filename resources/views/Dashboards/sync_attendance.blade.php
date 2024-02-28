@extends('layout.layout')
@section('title','Dashboard')
 @section('content')

    <form action="" method="get">
        <div class="row">
        @if($msg!='')<div class="col-md-12"><div style="background: #4eaf4e;border: 1px solid #02ab0d;padding: 4px 8px;border-radius: 5px;margin-top: 20px;margin-bottom: 20px;color: #fff;">{{$msg}}</div></div>@endif
                            
            <div class="col-md-12">
                <div class="panel panel-default" style="margin-top: 20px;border: 3px solid #d1dade;">
                    <div class="panel-heading">
                        <h3 class="panel-title">Webster Sync</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <input type="date" name="datefor" class="form-control" value="{{ $datefor }}" />
                            </div>    
                            <div class="col-md-3 mb-2">
                                <div style="margin: 8px;">
                                    <label>
                                        <input type="checkbox" name="testrecords" value="1"> Testing(View no.of Transactions)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div style="margin: 8px;">
                                    <label>
                                        Last Sync: {{$last_sync->sync_on ?? '--'}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="hidden" name="sync" value="go">
                                <input type="submit" class="btn btn-primary btn-success" name="submit" value="Sync">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default" style="margin-top: 20px;border: 3px solid #d1dade;">
                <div class="panel-heading" style="float: left;width: 100%;">
                    <h3 style="float:left" class="panel-title">External Departments Sync</h3>
                    <a  style="float:right" class="btn btn-success" href="{{ route('sync-attendance',['edeps' => '1']) }}"><i class="fa fa-refresh"></i> Sync All</a>
                </div>
                <div class="panel-body">
                    <table style="width:100%!important;" class="table dataTable"  aria-describedby="example4_info">
                        <thead>
                            <tr role="row">
                                <th>S.NO</th>
                                <th>Department Name</th>
                                <th>Updated At</th>     
                                <th>Action</th>      
                            </tr>
                        </thead>
                        <tbody role="alert" aria-live="polite" aria-relevant="all">
                            @php $counter=1; @endphp
                            @foreach($edeps as $dep)
                                <tr> 
                                    <td> {{ $counter++ }}</td>                 
                                    <td>{{$dep->department_name}}</td>
                                    <td>{{$dep->last_updated}}</td>
                                    <td><a class="btn btn-success" href="{{ route('sync-attendance',['edeps' => '1', 'dep_id' =>$dep->id,'sync'=>'go' ]) }}"><i class="fa fa-refresh"></i> Sync</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
  
@endsection