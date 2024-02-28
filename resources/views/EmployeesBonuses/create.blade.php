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

       				<form class="form-no-horizontal-spacing" method="post"  action ="{{route('employees_bonuses.store')}}" id="form-condensed" novalidate="novalidate">
       					{{csrf_field()}}
       					<div class="row column-seperation">
       						<div class="col-md-8">
                                          <div class="row form-rows">
                                                 <div class="col-md-12">
                                                 <label class="form-label"></label>
                                                 <div class="col-md-8">
                                                 <div class="radio">
                                                 <input id="male" type="radio"  name="bonus_deduction_status" class="" value="1" checked="">
                                                        <label for="male">Bonus</label>
                                                        <input id="female" type="radio"  class="" name="bonus_deduction_status" value="2">
                                                        <label for="female">Deduction</label>
                                                 </div>
                                                 </div>
                                                 </div>
                                          </div>
                                                        <div class="row form-row">
                                                               <div class="col-md-8">
                                                               <label class="form-label"  style="font-size:15px"> 
                                                                 <strong>Department</strong><span style="color:red"> *</span>
                                                               </label>
                                                               <select name="department_id" id="bonus_department_id" style="width:100%">
                                                               <option value="">Please Select Department...</option>
                                                                   @foreach($department as $d)
                                                                     <option name=""  value="{{$d->id}}">{{$d->name}}</option>
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
                                                        <div class="row form-row">
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
                                                      
                                                        </div>
                                   		<div class="row form-row">
                                   			<div class="col-md-8">
                                   				<label class="form-label"><strong>Amount</strong><span style="color:red"> *</span></label>
                                   				<input name="amount" autocomplete="off" id="form3FirstName" value="{{old('amount')}}" type="number" class="form-control" placeholder="Enter Amount">
                                   				@if($errors->any())
                                   				<p style="color:red">{{$errors->first('amount')}}</p>
                                   				@endif
                                   			</div>

                                   		</div>

                                                 <div class="row form-row" id="remarks">
                                                        <div class="col-md-8">
                                                               <label class="form-label"><strong>Remarks</strong><span style="color:red"> *</span></label>

                                                        <textarea name="remarks" class="form-control"   autocomplete="off" ></textarea>  
                                                         @if($errors->any())
                                                               <p style="color:red">{{$errors->first('remarks')}}</p> 
                                                         @endif             
                                                              <!--  <input name="amount" autocomplete="off" id="form3FirstName" value="{{old('amount')}}" type="number" class="form-control" placeholder="Enter Amount">
                                                               @if($errors->any())
                                                               <p style="color:red">{{$errors->first('amount')}}</p>
                                                               @endif -->
                                                        </div>

                                                 </div>
                                                 <div class="row form-row">
                                                        <div class="col-md-8">
                                                        <label class="form-label"  style="font-size:15px ">Date<span style="color:red"> *</span></label>
                                                        <div style="" class="input-append success date col-md-12 col-lg-11 no-padding">
                                                        <input type="text" autocomplete="off" name="date" placeholder="" class="form-control">
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
       					<button class="btn btn-danger btn-cons" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
       					<!-- <button class="btn btn-success btn-cons" name="action" value="save_and_close" type="submit"><i class="icon-ok"></i>Save & AddNew</button> -->
       					<button class="btn btn-white btn-cons" type="reset">Cancel</button>
       				</div>
       			</div>
       		</form>
       	</div>
       </div>
   </div>
</div>
@include('include.important')
@endsection