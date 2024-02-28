       @extends('layout.layout')
       @section('title','Add Bonus/Deduction')
       @section('content')
  
       <style type="text/css">
       .form-actions {
       background-color: #f6f7f8;
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
                     
       			<div class="grid-body no-border">

       				<form class="form-no-horizontal-spacing" method="post"  action ="{{route('day_off.store')}}" id="form-condensed" novalidate="novalidate">
       					{{csrf_field()}}
       					<div class="row column-seperation">
       						<div class="col-md-8">
                                          
                                                        <div class="row form-row">
                                                               <div class="col-md-8">
                                                               <label class="form-label"  style="font-size:15px"> 
                                                                 <strong>Department</strong><span style="color:red"> *</span>
                                                               </label>
                                                               <select name="department_id" id="bonus_department_iddd" style="width:100%">
                                                               <option value="">Please Select Department...</option>
                                                                   @foreach($department as $d)

                                                                     <option name="" <?php echo ($department_id==$d->id)?'selected':''  ?>  value="{{$d->id}}">{{$d->name}}</option>
                                                                   @endforeach
                                                               </select> 
                                                                @if($errors->any())
                                                                  <p style="color:red">{{$errors->first('department_id','Department field is required')}}
                                                                  </p>
                                                                 @endif                                       
                                                               <br>
                                                               <div class="clearfix"></div>
                                                               </div>
                                                        <div class="col-md-4">
                                                        <div class="loader" id="preloader" style="display:none;">       
                                                        </div>
                                                        </div>
                                                        </div> 
                                                        <!-- <div class="row form-row">
                                                               <div class="col-md-8">
                                                               <label class="form-label"  style="font-size:15px"> 
                                                                 <strong>Department Wise Employee</strong><span style="color:red"> *</span>
                                                               </label>
                                                               <select name="user_id" id="source1" style="width:100%">
                                                               <option value="">Please Select employee...</option>
                                                                  
                                                               </select> 
                                                                @if($errors->any())
                                                                  <p style="color:red">{{$errors->first('user_id','Employee field is required')}}
                                                                  </p>
                                                                 @endif                                        
                                                               <br>
                                                               <div class="clearfix"></div>
                                                               </div>
                                                      
                                                        </div> -->
                                   		

                                   
                                                 <div class="row form-row">
                                                        <div class="col-md-8">
                                                        <label class="form-label"  style="font-size:15px ">Date<span style="color:red"> *</span></label>
                                                        <div style="" class="input-append success date col-md-12 col-lg-11 no-padding">
                                                        <input type="text" autocomplete="off" value="{{$date}}" name="date" placeholder="" class="form-control">
                                                        <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                                        @if($errors->any())
                                                        <p style="color:red">{{$errors->first('date')}}</p>
                                                        @endif
                                                        </div>
                                                        </div>
                                                 </div>      
       					    </div>
       					</div>

       				</div>

       			</div>
       			<div class="form-actions">

       				<div class="pull-left">
       					<button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Filter Employees</button>
       					<!-- <button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Save & AddNew</button> -->
       					<button class="btn btn-white btn-cons" type="reset">Cancel</button>
       				</div>
       			</div>
       		</form>
       	</div>
 @if(count($users)>0)    
<div class="row-fluid">
    <div class="span12">
        <div class="grid simple ">
        <form  method="post" action="{{url('emps_dedicated_day_off_creation')}}">
           {{ csrf_field() }}
            <div class="grid-body ">
            
             
              <h5><b>select employees to assign dedicated day off</b> </h5>
                <table class="table dataTable" id="dataexample" aria-describedby="example3_info">
                    <thead>
                        <tr role="row">
                            <th>
                              <div class="dd-handle">
                                <input type="checkbox" id="selectAll"  name="">
                              </div>
                            </th>
                            <th>S.No</th>
                            <th>User</th>     
                        </tr>
                    </thead>
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                            
                       @foreach($users as $u)
                            <tr>
                              <td><div class="dd-handle"><input type="checkbox" value="{{$u->id}}" name="emp_dep[]">
                              <input value="{{$date}}" name="ded_off_day_date" type="hidden">
                              <input value="{{$department_id}}" name="department_idd" type="hidden"></div></td>                
                              <td>{{$u->id}}</td>  
                              <td>{{$u->name}}</td>          
                            </tr>
                       @endforeach    
                    </tbody>
                </table>
                <button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button> 
               
            </div>
          </form>
        </div>
      
    </div>
</div>

@endif

</div>
</div> 

       </div>
   </div>
</div>


@include('include.important')
@endsection