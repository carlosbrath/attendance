
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
	<link rel="icon" type="" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('timenexlogin/vendor/bootstrap/css/bootstrap.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('timenexlogin/fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('timenexlogin/fonts/Linearicons-Free-v1.0.0/icon-font.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('timenexlogin/vendor/animate/animate.css')}}">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{url('timenexlogin/vendor/css-hamburgers/hamburgers.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('timenexlogin/vendor/animsition/css/animsition.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('timenexlogin/vendor/select2/select2.min.css')}}">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{url('timenexlogin/vendor/daterangepicker/daterangepicker.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{url('timenexlogin/css/util.css')}}">
	<link rel="stylesheet" type="text/css" href="{{url('timenexlogin/css/main.css')}}">
<!--===============================================================================================-->
</head>
<body style="background: #e8e8e8">
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-l-85 p-r-85 p-t-55 p-b-55">
				<form method="post"  action="{{url('forgot-password')}}" class="login100-form validate-form flex-sb flex-w">
					{{csrf_field()}}
					<span class="login100-form-title p-b-32">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <small style="margin-right:20px">Forgot Password</small>
					</span>
				
				
					@if(session('flash_message_error'))
					<div class="alert alert-danger" style="text-align: center; width: 400px">{{session('flash_message_error')}}
                     </div>
					@endif

					<span class="txt1 p-b-11">
						Email
					</span>
					<div class="wrap-input100 validate-input m-b-36" data-validate = "Username is required">
						<input class="input100" type="text" name="email" >
						<span class="focus-input100"></span>
						<small class="text-danger" style="color:red">{{ $errors->first('email') }}</small>
					</div>
					
					
					
					<div class="flex-sb-m w-full p-b-48">
					

					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Submit
						</button>
					</div>

				</form>
				<br>
				
			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="{{url('timenexlogin/vendor/jquery/jquery-3.2.1.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{url('timenexlogin/vendor/animsition/js/animsition.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{url('timenexlogin/vendor/bootstrap/js/popper.js')}}"></script>
	<script src="{{url('timenexlogin/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{url('timenexlogin/vendor/select2/select2.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{url('timenexlogin/vendor/daterangepicker/moment.min.js')}}"></script>
	<script src="{{url('timenexlogin/vendor/daterangepicker/daterangepicker.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{url('timenexlogin/vendor/countdowntime/countdowntime.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{url('timenexlogin/js/main.js')}}"></script>

</body>
</html>