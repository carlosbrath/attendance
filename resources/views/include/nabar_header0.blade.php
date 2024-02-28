<div class="header navbar navbar-inverse ">
		<div class="navbar-inner">
			<div class="header-seperation" style="padding-top:20px;">
				<a href="index.html" style="margin-top:10px;">	       
                     <span style="margin-left:7px; font-size:12px;color:white;"><?php echo Session::get('name');
                     ?></span> 
				</a>
			</div>
			<div class="header-quick-nav">
				<div class="pull-left">
					<ul class="nav quick-section">
						<li class="quicklinks">
							<a href="#" class="" id="layout-condensed-toggle">
								<i class="material-icons">menu</i>
							</a>
						</li>
					</ul>		
				</div>
				<div id="notification-list" style="display:none">
					<div style="width:300px">
						<div class="notification-messages info">
							<div class="user-profile">
								<img src="{{url('template_files/theme/assets/img/profiles/d.jpg')}}" alt="" data-src="{{url('template_files/theme/assets/img/profiles/d.jpg')}}" data-src-retina="{{url('template_files/theme/assets/img/profiles/d2x.jpg')}}" width="35" height="35">
							</div>
							<div class="message-wrapper">
								<div class="heading">
									David Nester - Commented on your wall
								</div>
								<div class="description">
									Meeting postponed to tomorrow
								</div>
								<div class="date pull-left">
									A min ago
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="notification-messages danger">
							<div class="iconholder">
								<i class="icon-warning-sign"></i>
							</div>
							<div class="message-wrapper">
								<div class="heading">
									Server load limited
								</div>
								<div class="description">
									Database server has reached its daily capicity
								</div>
								<div class="date pull-left">
									2 mins ago
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="notification-messages success">
							<div class="user-profile">
								<img src="{{url('template_files/theme/assets/img/profiles/c.jpg')}}" alt="" data-src="{{url('template_files/theme/assets/img/profiles/d.jpg')}}" data-src-retina="{{url('template_files/theme/assets/img/profiles/d.jpg')}}" width="35" height="35">
							</div>
							<div class="message-wrapper">
								<div class="heading">
									You haveve got 150 messages
								</div>
								<div class="description">
									150 newly unread messages in your inbox
								</div>
								<div class="date pull-left">
									An hour ago
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
				<div class="pull-right">
					
						<ul class="nav quick-section ">
							<li class="quicklinks">
								<a data-toggle="dropdown" class="dropdown-toggle  pull-right " href="#" id="user-options">
									<i class="material-icons">power_settings_new</i>
								</a>
								<ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">
								     <li>
									   <a href="{{route('logout')}}"><i class="material-icons">power_settings_new</i>
									   	  &nbsp;&nbsp;Log Out
									   </a>
									</li>
									@if(Session::get('role_id')!=1)
									 <li>
									   <a href="{{route('employee.profile')}}"><i class="material-icons">power_settings_new</i>
									   	  &nbsp;&nbsp;Setting
									   </a>
									</li>
									@endif
								</ul>
							</li>
            			</ul>
				 </div>
			</div>
		</div>
	</div>