	<div class="page-sidebar noPrint " id="main-menu">

			<div class="page-sidebar-wrapper scrollbar-dynamic" id="main-menu-wrapper">
					<ul>

					@if(\Session::get('role_id')==1 ||  \Session::get('role_id') ==8)
						<li> <a href="{{url('home')}}" style="color:white;" title="Home"><i class="fa fa-home "></i><span class="title"> Home</span></a></li>
						<!-- <li> <a href="{{url('super-admin')}}" style="color:white;"><i class="fa fa-tachometer "></i> Dashboard</a></li> -->
					@elseif(\Session::get('role_id')==2)
						<li> <a href="{{url('dashboard-manager')}}" style="color:white;" title="Dashboard"><i class="fa fa-tachometer "></i><span class="title"> Dashboard</span></a></li>
					@else
						<li> <a href="{{url('dashboard')}}" style="color:white;" title="Dashboard"><i class="fa fa-tachometer "></i><span class="title"> Dashboard</span></a></li>
					@endif
					 @foreach(Session::get('assigned_modules') as $modules)

						<li class="">

							<a href="javascript:;" style="color:white;" title="{{$modules->name}}"> <i class="{{$modules->icon_class}}"></i><span class="title"> {{$modules->name}}</span> <span class="arrow"></span> </a>
							<?php
								$module =  App\Role::with('module_name')->where('id',Session::get('role_id'))->get();
							?>
							<ul class="sub-menu" style="display: none;">
								@foreach ($module as $m)
									@foreach ($m['module_name']->where('parent_id',$modules->id) as $mm)
										@if($mm->id !=71)
											<li><a  class="" href="{{url($mm->route)}}">{{$mm ->name}} </a></li>
										@endif
									@endforeach

								@endforeach

								<!-- @foreach(App\Module::where('parent_id', $modules->id)->get() as $module_name)
								<li> <a href="{{url($module_name->route)}}">{{$module_name->name}} </a> </li>
								@endforeach -->
							</ul>
						</li>

					@endforeach
				</ul>
				<br>
				<br>

				<div class="clearfix"></div>

			</div>
		</div>
