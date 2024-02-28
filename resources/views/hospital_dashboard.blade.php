<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!--  This file has been downloaded from bootdey.com @bootdey on twitter -->
    <!--  All snippets are MIT license http://bootdey.com/license -->
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.1/dist/js/bootstrap.bundle.min.js"></script> -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <!-- https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.1/assets/owl.carousel.css -->
  <link rel="stylesheet" href="{{asset('css_files/index.css')}}">
  <link rel="stylesheet" href="https://themes.audemedia.com/html/goodgrowth/css/owl.theme.default.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/material-design-icons/3.0.1/iconfont/material-icons.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.1/owl.carousel.min.js"></script>
 <style>
  
  .staff1 .owl-nav .owl-prev i, .owl-nav .owl-next i {
    color: #fff;
    font-size: 20px;
    position: relative;
    left: 0px;   
  }
 
  .main{
    display: flex;
    padding: 6px;
    justify-content: space-between;
    align-items: center;
    height: 100%;
    width: 100%;
  }
  .in{
    font-size: 12px;
    margin-right: 8px;
  }
  .out{
    font-size: 12px;
  }
  .row {
    overflow: hidden !important;
  }
 </style>
</head>
<body>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<section class="row py-5"  style="margin-left: 8px!important;">

<?php 
     $absent=array_slice($data['absent'], 1); 
     $present=array_slice($data['present'], 1);

?>


@if(count($absent)>0)
<h2 class="h4 block-title text-center mt-2">Absent Employees</h2>
        <div class="container-fluid pt-3" style="height:279px!important">
            <div class="owl-carousel owl-theme staff1" id="staff">
              
          @foreach($absent as $abs)
        
                <div class="item"> 
                <div class="col-lg-3 col-sm-6 mb-30 pb-2 pl-0 pr-0" style="width: 94%!important;height:500px;padding-left:0px;padding-right:0px">
                    <div class="team-card-style-3 mx-auto card-custom-padding" >
                        <div class="team-thumb"><img class="img-circle" src="http://137.59.195.228:8080/emp_images/{{ $abs['pic'] }}" alt="Author Picture">
                        </div>
                        <h4 class="team-name">{{$abs['name']}}</h4>
                        <span class="designation">{{$abs['designation']}}</span>
                        <p>Dep:{{$abs['department_name']}}</p>
                        <div class="team-social-bar-wrap">
                            <div class="team-social-bar">
                                  <div class="main">
                                      <div class="in"><b>Status:</b>Absent</div>
                                  </div>
                            </div>
                        </div>
                    </div>
                </div>
          </div>
          @endforeach 
       </div>
    </div>
   @endif 

   @if(count($present)>0)
    <h2 class="h4 block-title text-center mt-2">Present Employees</h2>
    <div class="container-fluid pt-3" style="height:279px!important">
    <div class="owl-carousel owl-theme staff1" id="staff">
      <?php $date=date('Y-m-d H:i:s');?> 
       @foreach($present as $p)
        <div class="item"> 
          <div class="col-lg-3 col-sm-6 mb-30 pb-2 pl-0 pr-0" style="width: 94%!important;height:500px;padding-left:0px;padding-right:0px">
              <div class="team-card-style-3 mx-auto card-custom-padding">
                  <div class="team-thumb"><img class="img-circle" src="https://bootdey.com/img/Content/avatar/avatar6.png" alt="Author Picture">
                  </div>
                  <h4 class="team-name">{{$p['name']}}</h4>
                  <span class="designation">{{$p['designation']}}</span> 
                  <p>Dep:{{$p['department_name']}}</p>
                  <div class="team-social-bar-wrap">
                      <div class="team-social-bar">
                          <div class="main">
                           <?php 
                              $datetime1 = new DateTime($p['datetime']);
                              $datetime2 = new DateTime($date);
                              $interval = $datetime1->diff($datetime2);
                              $timeDiff= $interval->format('%h')." hrs ".$interval->format('%i')." Mins";
                            ?>
                            <div class="in"><b>In Office Since: {{ $timeDiff}}</b></div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    @if(count($data['attach'])>0)   
    <h2 class="h4 block-title text-center mt-2">Attach Employees</h2>
    <div class="container-fluid pt-3" style="height:230px!important">
    <div class="owl-carousel owl-theme staff1" id="staff">
      <?php $date=date('Y-m-d H:i:s');?> 
       @foreach($data['attach'] as $attach)
       <div class="item"> 
                <div class="col-lg-3 col-sm-6 mb-30 pb-2 pl-0 pr-0" style="width: 94%!important;height:500px;padding-left:0px;padding-right:0px">
                    <div class="team-card-style-3 mx-auto card-custom-padding" >
                        <div class="team-thumb"><img class="img-circle" src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="Author Picture">
                        </div>
                        <h4 class="team-name">{{$attach['name']}}</h4>
                        <span class="designation">{{$attach['designation']}}</span>
                        <p>Dep:{{$attach['department_name']}}</p>
                        <div class="team-social-bar-wrap">
                            <div class="team-social-bar">
                                  <div class="main">
                                      <div class="in"><b>Status:</b>Atach</div>
                                  </div>
                            </div>
                        </div>
                    </div>
                </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    
    @if(count($data['leave'])>0)
    <h2 class="h4 block-title text-center mt-2">Leave Employees</h2>
    <div class="container-fluid pt-3" style="height:230px!important">
    <div class="owl-carousel owl-theme staff1" id="staff">
      <?php $date=date('Y-m-d H:i:s');?> 
       @foreach($data['leave'] as $l)
       <div class="item"> 
                <div class="col-lg-3 col-sm-6 mb-30 pb-2 pl-0 pr-0" style="width: 94%!important;height:500px;padding-left:0px;padding-right:0px">
                    <div class="team-card-style-3 mx-auto card-custom-padding" >
                        <div class="team-thumb"><img class="img-circle" src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="Author Picture">
                        </div>
                        <h4 class="team-name">{{$l['name']}}</h4>
                        <span class="designation">{{$l['designation']}}</span>
                        <p>Dep:{{$l['department_name']}}</p>
                        <div class="team-social-bar-wrap">
                            <div class="team-social-bar">
                                  <div class="main">
                                      <div class="in"><b>Status:</b>{{$l['status']}}</div>
                                  </div>
                            </div>
                        </div>
                    </div>
                </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif



        <!-- Author-->
   
        <!-- Author-->
     
        <!-- Author-->
</section>
<style type="text/css">
body{
    margin-top:20px;
    background:#eee;
    overflow-x: hidden;
    background-image:url("/assets/img/bg.jpg");
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;
   
   
}
.team-card-style-1, .team-card-style-3, .team-card-style-5 {
  position: relative;
  max-width: 360px;
  text-align: center;
  background:#fff;
 box-shadow: 0 22px 36px -12px rgba(64, 64, 64, .13);
}
.team-contact-link {
  display: block;
  margin-top: 4px;
  transition: all 0.25s;
  font-size: 12px;
  font-weight: 700;
  text-decoration: none;
}
.team-contact-link > i {
  display: inline-block;
  font-size: 1.1em;
  vertical-align: middle;
}
.team-card-style-1 .team-position, .team-card-style-3 .team-position, .team-card-style-4 .team-position {
  display: block;
  margin-bottom: 8px;
  color: #8c8c8c;
  font-size: 12px;
  font-weight: 700;
  opacity: 0.6;
}
.team-card-style-3 .team-name, .team-card-style-4 .team-name, .team-card-style-5 .team-name {
  margin-bottom: 16px;
  font-size: 14px;
  font-weight: 600;
  margin:0px;
}
.card-custom-padding{
  padding:7px !important;
}
.team-card-style-3 .designation{
  margin-bottom: 4px;
  font-size: 14px;
  font-weight: 600;
}
.team-thumb > img {
  display: block;
  width: 100%;
  
}
.team-card-style-1 {
  padding-bottom: 36px;
}
.team-card-style-1 > * {
  position: relative;
  z-index: 5;
}
.team-card-style-1::before {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 0;
  transition: all 0.3s 0.12s;
  content: '';
  opacity: 0;
}
.team-card-style-1 .team-card-inner {
  margin-bottom: 16px;
  padding-top: 48px;
  padding-right: 16px;
  padding-bottom: 20px;
  padding-left: 16px;
  background-color: #fff;
  box-shadow: 0 22px 36px -12px rgba(64, 64, 64, .13);
}
.team-card-style-1 .team-thumb {
  width: 108px;
  height: 108px;
  margin: auto;
  margin-bottom: 16px;
  border-radius: 50%;
  overflow: hidden;
}
.team-card-style-1 .team-social-bar {
  margin-top: 16px;
  margin-bottom: 8px;
  transform: scale(0.8);
}
.team-card-style-1 .team-contact-link {
  transition-delay: 0.12s;
  color: #8c8c8c;
  opacity: 0.6;
}
.team-card-style-1 .team-contact-link:hover {
  color: #8c8c8c;
  opacity: 1;
}
.team-card-style-1 .team-card-inner, .team-card-style-1 .team-thumb, .team-card-style-1 .team-social-bar {
  transition: all 0.3s 0.12s;
}
.team-card-style-1 .team-position, .team-card-style-1 .team-name {
  transition: color 0.3s 0.12s;
}
.team-card-style-1 .team-name {
  margin-bottom: 0;
  font-size: 20px;
  font-weight: bold;
}
.no-touchevents .team-card-style-1:hover::before {
  height: 100%;
  box-shadow: 0 22px 36px -12px rgba(64, 64, 64, .13);
  opacity: 1;
}
.no-touchevents .team-card-style-1:hover .team-card-inner {
  background-color: transparent;
  box-shadow: none;
}
.no-touchevents .team-card-style-1:hover .team-thumb {
  transform: scale(1.1);
}
.no-touchevents .team-card-style-1:hover .team-social-bar {
  transform: scale(1);
}
.no-touchevents .team-card-style-1:hover .team-contact-link, .no-touchevents .team-card-style-1:hover .team-position, .no-touchevents .team-card-style-1:hover .team-name {
  color: #fff;
}
.no-touchevents .team-card-style-1:hover .team-contact-link {
  opacity: 1;
}
.team-card-style-2 {
  position: relative;
}
.team-card-style-2 > img {
  display: block;
  width: 100%;
}
.team-card-style-2::before, .team-card-style-2::after {
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transition: opacity 0.35s 0.12s;
  content: '';
  z-index: 1;
}
.team-card-style-2::before {
  background-color: rgba(0, 0, 0, .25);
}
.team-card-style-2::after {
  opacity: 0;
}
.team-card-style-2 .team-card-inner {
  position: absolute;
  top: 50%;
  width: 100%;
  padding: 20px;
  transform: translateY(-45%);
  transition: all 0.35s 0.12s;
  text-align: center;
  opacity: 0;
  z-index: 5;
}
.team-card-style-2 .team-name, .team-card-style-2 .team-position, .team-card-style-2 .team-contact-link {
  color: #fff;
}
.team-card-style-2 .team-name {
  margin-bottom: 5px;
  font-size: 20px;
  font-weight: bold;
}
.team-card-style-2 .team-position {
  display: block;
  margin-bottom: 16px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}
.team-card-style-2 .team-social-bar {
  margin-top: 16px;
  margin-bottom: 8px;
}
.team-card-style-2 .team-contact-link {
  opacity: 1;
}
.team-card-style-2:hover::before {
  opacity: 0;
}
.team-card-style-2:hover::after {
  opacity: 0.7;
}
.team-card-style-2:hover .team-card-inner {
  transform: translateY(-50%);
  opacity: 1;
}
.team-card-style-3, .team-card-style-4 {
  position: relative;
  padding-top: 30px;
  padding-right: 20px;
  padding-bottom: 38px;
  padding-left: 20px;
  transition: all 0.35s;
  border: 1px solid #e7e7e7;
}
.team-card-style-3 .team-thumb, .team-card-style-4 .team-thumb {
  width: 90px;
  margin: auto;
  position:relative;
  margin-bottom: 17px;
}
.team-card-style-3 .team-position, .team-card-style-4 .team-position {
  margin-bottom: 0;
}
.team-card-style-3 .team-contact-link, .team-card-style-4 .team-contact-link {
  color: #404040;
  font-weight: 600;
}
.team-card-style-3 .team-contact-link > i, .team-card-style-4 .team-contact-link > i {
  color: #8c8c8c !important;
}
.team-card-style-3 .team-contact-link:hover, .team-card-style-4 .team-contact-link:hover {
  color: rgba(64, 64, 64, .6);
}
.team-card-style-3 .team-social-bar-wrap, .team-card-style-4 .team-social-bar-wrap {
  position: absolute;
  bottom: -18px;
  left: 0;
  width: 100%;
}
.team-card-style-3 .team-social-bar-wrap > .team-social-bar, .team-card-style-4 .team-social-bar-wrap > .team-social-bar {
  display: table;
  margin: auto;
  background-color: #fff;
  box-shadow: 0 12px 20px 1px rgba(64, 64, 64, .11);
}
.team-card-style-3:hover, .team-card-style-4:hover {
  border-color: transparent;
  box-shadow: 0 12px 20px 1px rgba(64, 64, 64, .09);
}
.team-card-style-4 {
  padding-top: 24px;
  padding-bottom: 31px;
  padding-left: 24px;
}
.team-card-style-4 .team-name {
  margin-bottom: 5px;
}
.team-card-style-4 .team-social-bar-wrap {
  position: relative;
  bottom: auto;
  left: auto;
  margin-top: 20px;
}
.team-card-style-4 .team-social-bar-wrap > .team-social-bar {
  margin: 0;
}
.team-card-style-5 {
  padding-bottom: 24px;
  transition: box-shadow 0.35s 0.12s;
}
.team-card-style-5 .team-thumb {
  position: relative;
  margin-bottom: 24px;
}
.team-card-style-5 .team-thumb::after {
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transition: opacity 0.35s 0.12s;
  background-color: #ac32e4;
  content: '';
  opacity: 0;
  z-index: 1;
}
.team-card-style-5 .team-card-inner {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  padding: 16px;
  padding-bottom: 26px;
  transform: translateY(10px);
  transition: all 0.35s 0.12s;
  text-align: center;
  opacity: 0;
  z-index: 5;
}
.team-card-style-5 .team-contact-link, .team-card-style-5 .team-contact-link:hover {
  color: #fff;
}
.team-card-style-5 .sb-style-6.sb-light-skin, .team-card-style-5 .sb-style-7.sb-light-skin {
  border-color: rgba(255, 255, 255, .35);
}
.team-card-style-5 .team-name {
  margin-bottom: 6px;
  padding: 0 16px;
}
.team-card-style-5 .team-position {
  display: block;
  padding: 0 16px;
  transition: color 0.35s 0.12s;
}
.team-card-style-5:hover {
  box-shadow: 0 12px 20px 1px rgba(64, 64, 64, .09);
}
.team-card-style-5:hover .team-thumb::after {
  opacity: 0.7;
}
.team-card-style-5:hover .team-card-inner {
  transform: translateY(0);
  opacity: 1;
}
.team-card-style-5:hover .team-position {
  color: #ac32e4;
}
.team-card-style-3 .team-social-bar-wrap>.team-social-bar, .team-card-style-4 .team-social-bar-wrap>.team-social-bar {
    display: table;
    margin: auto;
    background-color: #fff;
    -webkit-box-shadow: 0 12px 20px 1px rgba(64,64,64,0.11);
    box-shadow: 0 12px 20px 1px rgba(64,64,64,0.20);
}
.social-btn {
    display: inline-block;
    width: 36px;
    height: 36px;
    margin: 0;
    -webkit-transition: all .3s;
    transition: all .3s;
    font-size: 18px;
    line-height: 36px;
    vertical-align: middle;
    text-align: center !important;
    text-decoration: none;
}
.sb-twitter {
    color: #55acee !important;
}
.sb-github {
    color: #4183c4 !important;
}
.sb-linkedin {
    color: #0976b4 !important;
}
.sb-skype {
    color: #00aff0 !important;
}
.sb-style-2, .sb-style-3, .sb-style-4, .sb-style-5 {
  margin-right: 10px;
  margin-bottom: 10px;
  border-radius: 50%;
  background-color: #f5f5f5;
}
.sb-style-2.sb-light-skin, .sb-style-3.sb-light-skin, .sb-style-4.sb-light-skin, .sb-style-5.sb-light-skin {
  background-color: rgba(255, 255, 255, .1);
}
.sb-style-2:hover, .sb-style-3:hover, .sb-style-4:hover, .sb-style-5:hover, .sb-style-2.hover, .sb-style-3.hover, .sb-style-4.hover, .sb-style-5.hover {
  background-color: #fff;
  box-shadow: 0 12px 20px 1px rgba(64, 64, 64, .11);
}

.owl-stage{
    display: flex;
    justify-content: center;
}

</style>
<script>

var timer = 0;
timer = setInterval(function(){ 
	var currentdate = new Date();  
	if(currentdate.getMinutes() % 5 == 0 && currentdate.getSeconds() == 0) {
    window.location.replace("{{route('dashboardView')}}");
	}
}, 1000);
//   setTimeout(function(){
//     var currentdate = new Date();  
//       if(currentdate.getMinutes() % 5 == 0 && currentdate.getSeconds() == 0) {
//          window.location.replace("{{route('dashboardView')}}");
//       }
  
// }, 1000) 


</script>


<script>
  var owl = $('.staff1');
        owl.owlCarousel({
          margin:20,
          dots:false,
          autoplay: true,
        
          slideSpeed: 300,
          loop: true,
          nav: false,
         
          autoplay: true,
          autoplayHoverPause: true,
          responsive: {
            0: {
              items: 1
            },
            600: {
              items: 2
            },
            1000: {
              items:2
            },
            1200: {
              items:4
            }
          }
});
</script>
</body>
</html>