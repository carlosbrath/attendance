@extends('layout.layout')
@section('title','Add Time Category')
@section('content')
 @if(session('msg'))

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->

@endif


 <div class="container" style="width: 100%;">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">Roster</a></li>
  </ul>

  <div class="tab-content">

    <div id="home" class="tab-pane fade in active">
       <form class="form-no-horizontal-spacing" method="post" 
            action ="{{route('timecategories.update',$timecategories->id)}}"  id="form-condensed" novalidate="novalidate">
            {{ method_field('PUT')}}
            {{csrf_field()}}
      <h3>Roster</h3>
      <div class="row">
           <div class="col-md-12">

             <div class="col-md-6">
                  <div class="col-md-12">
                        <label class="form-label">Title<span style="color:red"> *</span></label>
                           <?php if($timecategories->time_cat_type=="2"){
                                   $title=$timecategories->title;
                                   $department_id=$timecategories->department_id;
                                   
                           }else{
                                    $title="";
                                    $department_id="";
                                 }
                                 ?>
                            <input name="roster_title" id="form3FirstName" value="{{$title}}" type="text" class="form-control" placeholder="Enter title">
                       @if($errors->any())
                          <p style="color:red">{{$errors->first('roster_title')}}</p>
                        @endif
                   </div>
                <div class="col-md-12">
                <label class="form-label">Department<span style="color:red"> *</span></label>      
                    <select name="roster_department_id" id="roster_department_id"   class="leave_deparments select2 form-control">
                        <option value="">
                            Please Select...
                        </option>
                        @foreach($department as $d)
                        <option name=""  value="{{$d->id}}"  <?php echo ($d->id==$department_id
                            ) ?  'selected':'' ?>>{{$d->name}}
                         
                        </option>
                        @endforeach
                    </select>
                    @if($errors->any())
                    <p style="color:red">
                        {{$errors->first('roster_department_id','Department field is required')}}
                    </p>
                    @endif              
                </div> 
                <div class="col-md-12">
                    <label>Color</label>   
                        <input name="color" id="form3FirstName" type="color"  class="form-control" placeholder="Enter title">      
                 </div>
                  <input id="" name="time_cat_type" type="hidden" value="2" checked="checked">   
                 </div>
                </div> 
             </div>
             <br>
             <br>
               
                    <button class="btn btn-success btn-cons" name="action" value="" type="submit"><i class="icon-ok"></i>Update</button> 
           </div>
       </form>
</div>
    
    </div>
@include('include.important')
@endsection