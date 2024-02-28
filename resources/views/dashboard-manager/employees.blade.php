@extends('layout.layout')
@section('title','Dashboard')
@section('content')

    <style type="text/css">
        .form-actions {background-color:#f6f7f8;border: 0px;margin-bottom: 20px;margin-top: 0px;padding: 19px 20px 9px;}
        .rcorners2 {border-radius: 25px;padding: 20px 10px;}
        .box-wrap{display:flex;gap:20px}
        .box-wrap+.box-wrap{margin-top:20px}
        .box-wrap .box{flex:1}
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="grid simple" style="position: static;">
                <div class="grid-title no-border">
                    <h4>Report <span class="semi-bold">Filters</span></h4>
                    <div class="tools"><a href="javascript:;" class="collapse"></a></div>
                </div>
                <div class="grid-body no-border" style="display: block;padding-bottom: 0;">

                    <form class="form-no-horizontal-spacing" action=""
                          method="post" id="form-condensed" novalidate="novalidate">
                        @csrf
                        <div class="row form-row">
                            <div class="col-md-6">
                                <label class="form-label"  style="font-size:15px">Department</label>
                                <select name="department[]" id="source" style="width:100%" tabindex="-1" class="search select2-offscreen" multiple placeholder="All Departments/Select Department">
                                    @foreach($departments as $dept)
                                        <option
                                            @if(!is_null($choosen_departments))
                                            @if(in_array($dept->id,$choosen_departments))
                                            selected
                                            @endif
                                            @endif
                                            value="{{$dept->id}}">{{$dept->name}}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->any())
                                    <p style="color:red">
                                        {{$errors->first('search','Department field is required')}}
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"  style="font-size:15px ">Date</label>
                                <div  class="input-append success date col-md-10 col-lg-4 no-padding" style="width:calc(100% - 36px);">
                                    <input type="text"
                                           @if($report_date) value="{{ date("Y/m/d",strtotime($report_date)) }}" @endif
                                           autocomplete="off" name="report_date" placeholder="Report Date" class="form-control">
                                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                    @if($errors->any())
                                        <p style="color:red">
                                            {{$errors->first('date','Date field is required')}}
                                        </p>
                                    @endif
                                </div>

                            </div>
                        </div>
                        <div class="row form-row">
                            <div class="col-md-6">
                                <label class="form-label"  style="font-size:15px ">Status</label>
                                <div class="radio">
                                    <input id="all" @if($status=="all") checked @endif type="radio"  name="status" value="all">
                                    <label for="all">All</label>
                                    <input id="present" @if($status=="present") checked @endif type="radio" name="status" value="present" >
                                    <label for="present">Present</label>
                                    <input id="absent" @if($status=="absent") checked @endif type="radio" name="status" value="absent">
                                    <label for="absent">Absent</label>
                                    <input id="late" @if($status=="late") checked @endif type="radio" name="status" value="late">
                                    <label for="late">Late</label>
                                    <input id="earlyout" @if($status=="earlyout") checked @endif type="radio" name="status" value="earlyout">
                                    <label for="earlyout">Early Timeout</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"  style="font-size:15px ">Sort By</label>
                                <div class="radio">
                                    <input id="id" checked="" @if($orderby=="id") checked @endif type="radio"  name="orderby" value="id">
                                    <label for="id">ID</label>
                                    <input id="name" @if($orderby=="name") checked @endif type="radio" name="orderby" value="name" >
                                    <label for="name">EmpName</label>
                                    <input id="bps" @if($orderby=="bps") checked @endif type="radio" name="orderby" value="bps" >
                                    <label for="bps">PayScale</label>

                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                    <input id="asc" checked @if($ordertype=="asc") checked @endif type="radio" name="ordertype" value="asc" >
                                    <label for="asc">ASC</label>
                                    <input id="desc" @if($ordertype=="desc") checked @endif type="radio" name="ordertype" value="desc" >
                                    <label for="desc">DESC</label>
                                </div>

                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="pull-left">
                                <button class="btn btn-success btn-cons" type="submit" name="generate_report"><i class="icon-ok"></i>Generate Report</button>
                            </div>
                            @if(count($users)>0)
                                <div class="pull-right">
                                    <a class="btn btn-success noPrint" id="print_report"><i class="fa fa-print"> </i> Print Full Report</a>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
