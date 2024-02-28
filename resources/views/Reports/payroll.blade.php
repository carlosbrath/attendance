@extends('layout.layout')
@section('title','Payroll')
@section('content')
<h1>Payroll</h1>

<div class="row monthlyreport" style="margin-top: 29px;">

<div class="row">
	<div class="col-md-12">
		<div class="grid simple form-grid">
			<div class="grid-body no-border">
				<form class="form-no-horizontal-spacing" method="post" action="{{route('payroll')}}" id="form-condensed" novalidate="novalidate">
					@csrf
					<div class="row column-seperation">
						<div class="col-md-12">
							<h4></h4>
                <div class="row form-row">
						<div class="col-md-4" style="width:379px;">
						   <div class="grid simple">
							<div class="grid-title no-border" style="padding-bottom: 0px">
							</div>	
					<div class="row">
							<div class="col-md-12">
							<br>
							<label class="form-label"  style="font-size:15px">Department</label>
									<select name="deparment_id" id="bonus_department_id"  class="department_id select2 form-control">
										<option value="">
										 Please Select...
										</option>
										@foreach($department as $d)
									      <option name=""  value="{{$d->id}}">{{$d->name}}</option>
									    @endforeach
									  </select>
										 @if($errors->any())
									   <p style="color:red">{{$errors->first('deparment_id','Department field is required')}}
									   </p>
									  @endif
							</div>
						</div>
					</div>
					</div>
					  <div class="col-md-4">
				        <div class="loader" id="preloader" style="display:none;">	  
				  </div>
			     </div>	
				</div>

				 <div class="row form-row">
						<div class="col-md-4" style="width:379px;">
						   <div class="grid simple">
							<div class="grid-title no-border">
							</div>
								<div class="row">
								<div class="col-md-12">
								<br>
								<label class="form-label"  style="font-size:15px">Employee</label>
								<select name="user_id" id="source1" style="width:100%">
									<option value="">Please Select Employee</option>
									@foreach($department_users as $user)
										<option value="{{ $user->id }}">{{ $user->id }}-{{ $user->name }}</option>	
									@endforeach
								</select>
								<p id="emp_msg" style="color: red"></p>
								</div>
								</div>				
					     </div>
					</div>
				  </div>
							
                <div class="row form-row">
                    <div class="col-md-12">
                        <label class="form-label"  style="font-size:15px ">From Date</label>
                        <div style="width: 37%;" class="input-append success date col-md-10 col-lg-4 no-padding">
                            <input type="date" autocomplete="off" name="from_date" placeholder="From Date" class="form-control">
                           
                        </div>
                    </div>
                </div>
                <div class="row form-row">
                    <div class="col-md-12">
                            <label class="form-label"  style="font-size:15px ">To Date</label>
                            <div style="width: 37%;" class="input-append success date col-md-10 col-lg-4 no-padding">
                                <input type="date" autocomplete="off" name="to_date" placeholder="From Date" class="form-control">
                                
                            </div>
                        </div>
                </div>
						
				</div>		
				</div>
                <div class="form-actions">
                    <div class="pull-left">
                        <button class="btn btn-success btn-cons" type="submit"><i class="icon-ok"></i>Generate Report</button>								
                    </div>
                </div>
			  </form>
			</div>
		</div>
	</div>
</div>
<br>

<div class="pull-right">
      <!-- <button class="btn-cogs" style="margin-top:2px" id="monthly_report_print"><i class="icon-ok"></i>Print</button>
      <button class="btn-cogs" style="margin-top:2px" onclick="tableToExcel('monthlyreport', 'Monthly Attendance')" value="Export to Excel"><i class="icon-ok"></i>Export to Excel</button>                 -->
</div>
@if($empData)
  <div class="col-md-12">
        <div class="grid simple ">

      <div class="grid-body table-responsive">
       <!--  <div class="col-md-12"><button id="btn_print" class="btn btn-success">Print</button></div> -->
       
       <div class="table-responsive">

        <table id="monthlyreport" class="monthly_repor table dataTable display nowrap table-responsive table-bordered portal-drivers no-footer" aria-describedby="example3_info" style="width:100%">
                      <thead>
               <tr>
                <th class="text-center" width="4%">User ID</th>
                <th class="text-center" width="8%">Name</th>
                <th width="1%">A/c</th>
                <th width="1%">Pay</th>
                <th width="1%">Days</th>
                <th width="1%">Amount</th>
                <th width="1%">House Rent</th>
                <th width="1%">Electricity Bill</th>
                <th width="1%">Gas Bill</th>
                <th width="1%">Advance Loan/Dedct.</th>
                <th width="1%">Total Net Salary</th>
                <th width="3%">Signature</th>
                
              </tr>
            </thead>
            <tbody>
                
                <tr role="row">
                    <td  class="text-center">{{$empData->id}}</td>
                    <td  class="text-center">
                               {{$empData->name}} <br>
                                <b> {{$empData->designations->title}}</b> <br>
                                                </td>
                    <td>Xyz</td>
                    <td class="text-center">{{$empData->fixed_salary }}</td>
                    <td class="text-center">30</td>
                    <td class="text-center">30000</td>
                    <td class="text-center">1000</td>
                    <td class="text-center">1000</td>
        
                    <td class="text-center">300</td>
                    <td class="text-center">500</td>
                    <td class="text-center">{{$empData->fixed_salary }}</td>
                    
                </tr>
                <tr role="row">
                    <td  class="text-center"></td>
                   
                    <td></td>
                    <td class=" text-center">Total</td>
                    <td class=" text-center">31000</td>
                    <td class="text-center" ></td>
                    <td class="text-center">30000</td>
                    <td class="text-center" ></td>
                   
                    <td class="text-center"></td>
                    <td class="text-center">Total Deduction</td>
                    <td class="text-center">2500</td>
                    <td class="text-center">{{$empData->fixed_salary }}</td>
                    
                </tr>
             </tbody>
         </table>
        </div>
     
        </div>
      </div>
           </div>
    @endif       
    </div>
@endsection