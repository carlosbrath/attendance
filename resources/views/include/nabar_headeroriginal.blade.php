<div class="header navbar navbar-inverse ">
		<div class="navbar-inner">

			<div class="header-seperation" style="padding-top:20px;">
				<div class="user-profile">
					        <?php               
                              $dpermtent_id=Session::get('dep_id');
                                $client_image_logo= App\Client::where('id',$dpermtent_id)->first();
       					      ?>
       					      @if(Session::get('role_id')==1)
								<h4   style="font-size: 23px;position: relative;left: 10px; bottom: 10px;color: white;">Super Admin</h4>
                             @else
			                <?php           
			         $client_name=App\Client::where('parent_id',Session::get('dep_id'))->first();
                     
 			         if($client_name==null){
			               
			                $image_path= asset('photos/'.$client_image_logo->logo);

			                     echo  '<img src="'.$image_path.'"  alt="" style="margin-left: 97px; width: 60px; height: 60px">';

			         }
			         else{

                        
			              $client_check_sub_child=App\Client::where('parent_id',$client_name->id)->first();
			              if($client_check_sub_child==null){
			                 
			                    
                               $parent_logo_to_child= App\Client::where('id',$client_image_logo->parent_id)
                               ->first();
                              if($parent_logo_to_child==null){
                                    $image_path= asset('photos/'.$client_image_logo->logo);
			                     echo  '<img src="'.$image_path.'"  alt="" style="margin-left: 97px; width: 60px; height: 60px">';
                              }else{

			                   $image_path= asset('photos/'.$parent_logo_to_child->logo);
			                     echo  '<img src="'.$image_path.'"  alt="" style="margin-left: 97px; width: 60px; height: 60px">'; 
			                 }
			                
			              }
			              else{
			              	
			              	     $image_path= asset('photos/'.$client_image_logo->logo);
			                     echo  '<img src="'.$image_path.'"  alt="" style="margin-left: 97px; width: 60px; height: 60px">';
			                    }
			                 }   
			               ?>


                           
                             
                             
							 @endif
								<!-- {{url('public/photos/')}} -->
							</div>
				<a href="index.html" style="margin-top:10px;">	

                     <span style="margin-left:85px; float:left !important; margin-top:6px !important; font-size:12px;color:white; text-align: center;">

                     	<?php/* echo Session::get('name'); */?></span> 
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
								<span style="margin-right: 477px;
    font-size: 22px;
    color: #0a0a0a;
    font-family: serif;
    font-weight: bold;"><?php echo Session::get('name'); ?></span>
								<a data-toggle="dropdown" class="dropdown-toggle  pull-right " href="#" id="user-options">

									<i class="material-icons">power_settings_new</i>
								</a>

								<ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">

								     <li>
									   <a href="{{route('logout')}}"><i class="material-icons">power_settings_new</i>
									   	  &nbsp;&nbsp;Log Out

									   </a>
									</li>
									
									 <li>
									   <a href="{{route('employee.profile')}}"><i class="material-icons">power_settings_new</i>
									   	  &nbsp;&nbsp;Setting
									   </a>
									</li>


								</ul>
							</li>
            			</ul>
				 </div>
			</div>
		</div>
	</div>