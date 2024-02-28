<div class="header navbar navbar-inverse ">
	<div class="navbar-inner">
		<div class="header-seperation" >
			<div class="user-profile" style="text-align:center">
					@php
					if(\Session::get('role_id')==1 || \Session::get('role_id')==2){
						$image_path = asset('photos/1597950367.jpg');
					}else{
						$dpermtent_id=Session::get('dep_id');
						$client_image_logo = App\Client::where('id',$dpermtent_id)->first();

						if($client_image_logo!=null && $client_image_logo->parent_id != null && $client_image_logo->logo == null){
							$client_image_logo = App\Client::where('id',$client_image_logo->parent_id)->first();
						}
						$image_path = "";
						if($client_image_logo != null){
							$image_path = asset('photos/'.$client_image_logo->logo);
						}
					}
					@endphp
					<img src="{{ $image_path }}" class="logo" alt="" data-src="{{ $image_path }}" data-src-retina="{{ $image_path }}" height="20">
			</div>

		</div>

		<div class="header-quick-nav mobile-pullup">
			<div class="pull-left">
				<ul class="nav quick-section">
					<li class="quicklinks">
					<a href="#" class="" id="layout-condensed-toggle">
					<i class="material-icons">menu</i>
					</a>
					</li>
				</ul>
				<ul class="nav quick-section mobile-hidden">
					<li><b><h5 style="text-transform: uppercase;"><?php //echo  Session::get('name'); ?>
					{{ $page_title ?? 'No Page Title Choosen' }}
					</h5></b></li>
				</ul>
			</div>

			<div class="pull-right">
				<ul class="nav quick-section">
					<li class="quicklinks">
						<a data-toggle="dropdown" class="dropdown-toggle  pull-right " href="#" id="user-options" style="display: flex;gap: 10px;align-items: center;">
							<i class="fa fa-user" style="font-size:16px"></i> <span class="title">Welcome {{Session::get('name')}}</span> <i class="fa fa-angle-down"></i>
						</a>
						<ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">

							@if(\Session::get('role_id') == 1)
							<li>
								<a  href="{{route('sync-attendance')}}"> Sync Attendance</a>
							</li>
							@endif
							<li>
								<a href="{{route('employee.profile')}}"> Change Password</a>
							</li>

							<li></li>
							<li class="divider"></li>
							<li>
								<a href="{{route('logout')}}"><i class="material-icons">power_settings_new</i>&nbsp;&nbsp;Log Out</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>

