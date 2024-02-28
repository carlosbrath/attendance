<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="apple-touch-icon" sizes="180x180" href="{{asset('images/icons/apple-touch-icon.png')}}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{asset('images/icons/favicon-32x32.png')}}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/icons/favicon-16x16.png')}}">
	<link rel="manifest" href="{{asset('images/icons/site.webmanifest')}}">
	<link rel="mask-icon" href="{{asset('images/icons/safari-pinned-tab.svg')}}" color="#5bbad5">
	<link rel="shortcut icon" href="{{asset('images/icons/favicon.ico')}}">
	<meta name="apple-mobile-web-app-title" content="AJK Boimetric Attendance">
	<meta name="application-name" content="AJK Boimetric Attendance">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-config" content="{{asset('images/icons/browserconfig.xml')}}">
	<meta name="theme-color" content="#ffffff">

<!--===============================================================================================-->	
	<link rel="icon" id="icon" alt="User Icon" type="image/png" href="{{url('design/login')}}/timenex.png"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('vendor/bootstrap/css/bootstrap.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('fonts/iconic/css/material-design-iconic-font.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('vendor/animate/animate.css')}}">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{url('vendor/css-hamburgers/hamburgers.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('vendor/animsition/css/animsition.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('vendor/select2/select2.min.css')}}">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{url('vendor/daterangepicker/daterangepicker.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('css/util.css')}}">
	<link rel="stylesheet" type="text/css" href="{{url('css/main.css')}}">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter" >

		<div class="container-login100" style="background: url('{{url('design')}}/17973908.jpg');background-repeat: no-repeat;background-size: cover;">
			<div class="wrap-login100" style="opacity: 0.8;">
				<form class="login100-form validate-form"  method="post"  action="{{url('loginaction')}}">
					<p style="color:red;text-align:center;">{{session()->get('msg')}}</p>
     {{csrf_field()}}
					<span class="login100-form-title p-b-20">
						Attendance Management System
					</span>
					<span class="login100-form-title p-b-20">
						<img style="width: 40%;" src="{{url('ajk.png')}}" id="icon" alt="User Icon" />
					</span>

					<div class="wrap-input100 validate-input" data-validate = "Valid email is: a@b.c">
						<input class="input100" type="text" id="login" name="email">
						<span class="focus-input100" data-placeholder="User Name"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Enter password">
						<span class="btn-show-pass">
							<i class="zmdi zmdi-eye"></i>
						</span>
						<input class="input100" type="password" name="password" id="password" >
						<span class="focus-input100" data-placeholder="Password"></span>
					</div>

					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn">
								Login
							</button>
						</div>
					</div>
					<style type="text/css">
						.btn-store {
						  color: #777777;
						  min-width: 254px;
						  padding: 12px 20px !important;
						  border-color: #dddddd !important;
						}

						.btn-store:focus, 
						.btn-store:hover {
						  color: #ffffff !important;
						  background-color: #168eea;
						  border-color: #168eea !important;
						}

						.btn-store .btn-label, 
						.btn-store .btn-caption {
						  display: block;
						  text-align: left;
						  line-height: 1;
						}

						.btn-store .btn-caption {
						  font-size: 24px;
						}
					</style>
					<div class="text-center p-t-20">
			        	<!-- <p>
				            <a href="https://play.google.com/store/apps/details?id=com.alkausar.timenexapp" class="btn btn-store">
				                <span class="fa fa-android fa-3x pull-left"></span> 
				                <span class="btn-label">Download on the</span>
				                <span class="btn-caption">Google Play</span>
				            </a>
				        </p> -->
						<span class="txt1">
							All Rights Reserved IT Board AJ&K <?php echo date('Y')?> 
						</span>


					</div>
				</form>
				
			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="{{url('vendor/jquery/jquery-3.2.1.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{url('vendor/animsition/js/animsition.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{url('vendor/bootstrap/js/popper.js')}}"></script>
	<script src="{{url('vendor/bootstrap/js/bootstrap.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{url('vendor/select2/select2.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{url('vendor/daterangepicker/moment.min.js')}}"></script>
	<script src="{{url('vendor/daterangepicker/daterangepicker.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{url('vendor/countdowntime/countdowntime.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{url('js/main.js')}}"></script>

</body>
</html>