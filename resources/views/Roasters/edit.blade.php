       @extends('layout.layout')
       @section('title','Update Roster')
        @section('content')
           @if(session('message'))
               <div class="alert alert-success" style="text-align: center;">{{session('message')}}
                </div>
             @endif
    <style type="text/css">
    .form-actions {
    background-color:#f6f7f8;
    border: 0px;
    margin-bottom: 20px;
    margin-top: 0px;
    padding: 19px 20px 9px;
    }
    .grid {
    clear: both;
    margin-top: 0px;
    margin-bottom: 0px;
    padding: 0px;
    }
    </style>      
        <div class="row">
            <div class="col-md-12">
                <div class="grid simple form-grid">
                    <div class="grid-title no-border">
                        <div class="tools">
                            <a href="javascript:;" class="collapse"></a>
                            <a href="#grid-config" data-toggle="modal" class="config"></a>
                            <a href="javascript:;" class="reload"></a>
                            <a href="javascript:;" class="remove"></a>
                        </div>
                    </div>
                    <div class="grid-body no-border">
                        <form class="form-no-horizontal-spacing" action ="{{route('roasters.update',$roaster->id)}}" method="post"  id="form-condensed" novalidate="novalidate">
                              {{method_field('PUT')}}
                              @csrf

                            <div class="row column-seperation">
                                <div class="col-md-8">

                                 <!--    <div class="form-group">
                                        <label class="form-label"><strong>Deparmtents</strong></label>
                                       
                                          
                                        <select name="deparment_id" id="department_id"  style="width: 66%" class="select2 form-control">
                                        <option value="">
                                         Please Select...
                                        </option>
                                        @foreach($department as $d)
                                          <option name=""  value="{{$d->id}}" <?php echo ($d->id==$roaster->department_id) ?  'selected':'' ?>>{{$d->name}}</option>
                                        @endforeach
                                    </select>
                                     @if($errors->any())
                                       <p style="color:red">{{$errors->first('deparment_id','Department field is required')}}
                                       </p>
                                      @endif
                                    </div> -->

                                    <div class="row form-row">
                                        <div class="col-md-8">
                                            <label class="form-label"><strong>Title</strong></label>
                                            <input name="title" autocomplete="off" value="{{$roaster->title}}" id="form3FirstName" type="text" class="form-control" placeholder="Enter title">
                                        @if($errors->any())
                                          <p style="color:red">{{$errors->first('title')}}</p>
                                        @endif
                                        </div>

                                    </div>
                                    <div class="row form-row">
                                        <div class="col-md-8">

                                          <label class="form-label"><strong>Month</strong></label>
                                            <input  name="month" autocomplete="off"  type="text" id="datepicker" class="form-control" value="{{$roaster->month}}" placeholder="Enter Month">                                           
                                            <br>
                                            <div class="clearfix"></div>
                                        @if($errors->any())
                                          <p style="color:red">{{$errors->first('month')}}</p>
                                        @endif
                                        </div>
                          
                                        <div class="col-md-8">
                                            <label class="form-label"><strong>Year</strong></label>
                                            <input name="year" autocomplete="off" id="form3FirstName"  value="{{$roaster->year}}"  type="text" class="form-control" placeholder="Enter Year">
                                        @if($errors->any())
                                          <p style="color:red">{{$errors->first('year')}}</p>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  <div class="form-actions">
                    <div class="pull-left">
                        <button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Update</button>
                    </div>
                  </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('include.important')
@endsection