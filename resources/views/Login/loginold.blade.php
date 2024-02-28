
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login</title>

	<link rel="stylesheet" type="text/css" href="{{url('design/login')}}/login.css">
<!--===============================================================================================-->
</head>


<!-- <small class="text-danger" style="color:red">{{ $errors->first('email') }}</small>
 -->
<body>
<div class="wrapper fadeInDown">

<marquee style="color:red">Technical Support : Dear Valued Customer in Case of any Problem/Issue kindly contact Mr.Basheer khan call,Whatsapp# 0302-5992606 0313-9888043 </marquee>
<br>
<div style="background-color:#56bbef;width:441px;text-align:center;-webkit-border-radius: 10px 10px 10px 10px;"><h3 style="color:black">Cloud based time Managment System</h3></div>

  <div id="formContent">
    <div class="fadeIn first">
      <img src="{{url('design/login')}}/timenex.png" id="icon" alt="User Icon" />
      <h3>Timenex-V2</h3>
    </div>

    <!-- Login Form -->
    <form method="post"  action="{{url('loginaction')}}">
    	<p style="color:red;text-align:center;">{{session()->get('msg')}}</p>
     {{csrf_field()}}
      <input type="text" id="login" class="fadeIn second" required="" name="email" placeholder="username">
      <input type="text" id="password" class="fadeIn third" required="" name="password" placeholder="password">
      <input type="submit" class="fadeIn fourth" value="Log In">
    </form>
   <p style="color:black;font-size:12px"><?php echo date('Y')?> All Rights Resverd-timenexv2</p>
  </div>
</div>
</body>
</html>