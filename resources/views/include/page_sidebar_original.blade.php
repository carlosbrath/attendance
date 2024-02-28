	<div class="page-sidebar " id="main-menu">

			<div class="page-sidebar-wrapper scrollbar-dynamic" id="main-menu-wrapper">
				<div class="user-info-wrapper sm">
					<div class="profile-wrapper sm">
						
						<!-- <div class="availability-bubble online"></div> -->
					</div>
					<div class="user-info sm">
						<div class="username" style="width: 100%;"></div>
						<div class="status" style="text-align: center;line-height: 34px;">TIme Nex</div>
					</div>
				</div>
				<ul>
					<li> <a href="{{url('dashboard')}}" style="color:white;width: 100%;"><i class="fa fa-tachometer "></i> Dashboard</a></li>
					 @foreach(Session::get('assigned_modules') as $modules)
					 <li class="">
						<a href="javascript:;" style="color:white;"> <i class="{{$modules->icon_class}}"></i><span class="title"> {{$modules->name}}</span> <span class="arrow"></span> </a>
						<?php   
                              $module =  App\Role::with('module_name')->where('id',Session::get('role_id'))->get();
                          ?>
						<ul class="sub-menu" style="display: none;">
                             @foreach ($module as $m)
							       @foreach ($m['module_name']->where('parent_id',$modules->id) as $mm)        
							          <li><a  class="" href="{{url($mm->route)}}">{{$mm ->name}} </a></li>
					         @endforeach
					         @endforeach

							<!-- @foreach(App\Module::where('parent_id', $modules->id)->get() as $module_name)
							<li> <a href="{{url($module_name->route)}}">{{$module_name->name}} </a> </li>
							@endforeach -->
						</ul>
					 </li>
					@endforeach
				</ul>

				<div class="clearfix"></div>

			</div>
		</div>
	