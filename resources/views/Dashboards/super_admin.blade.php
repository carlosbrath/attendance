@extends('layout.layout')
@section('title','Dashboard')
 @section('content')

 
    <div class="row">
        <div class="col-md-4 col-vlg-3 col-sm-6">
            <div class="tiles green m-b-10">
                <div class="tiles-body">
                    <div class="controller">
                        <a href="javascript:;" class="reload"></a>
                        <a href="javascript:;" class="remove"></a>
                    </div>
                    <div class="tiles-title text-black">Employees</div>
                        <br>
                        <div class="wrapper transparent">
                            <span class="item-title"></span> <span class="item-count animate-number semi-bold" data-value="{{$user}}" data-animation-duration="700"></span>
                        </div>
                    <div class="progress transparent progress-small no-radius m-t-20" style="width:90%">
                        <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="64.8%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-vlg-3 col-sm-6">
            <div class="tiles blue m-b-10">
                <div class="tiles-body">
                    <div class="controller">
                        <a href="javascript:;" class="reload"></a>
                        <a href="javascript:;" class="remove"></a>
                    </div>
                    <div class="tiles-title text-black">Designations</div>
                     <br>               
                        <div class="wrapper transparent">
                            <span class="item-title"></span> <span class="item-count animate-number semi-bold" data-value="{{$designation}}" data- animation-duration="700">0</span>
                        </div>
                
                    <div class="progress transparent progress-small no-radius m-t-20" style="width:90%">
                        <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="54%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-vlg-3 col-sm-6">
            <div class="tiles purple m-b-10">
                <div class="tiles-body">
                    <div class="controller">
                        <a href="javascript:;" class="reload"></a>
                        <a href="javascript:;" class="remove"></a>
                    </div>
                    <div class="tiles-title text-black">Departments</div>
                     <br>
                        <div class="wrapper transparent">
                            <span class="item-title"></span> <span class="item-count animate-number semi-bold" 
              data-value="{{$department}}" data-animation-duration="700">0</span>
                        </div>
                    
                    <div class="progress transparent progress-small no-radius m-t-20" style="width:90%">
                        <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="90%"></div>
                    </div>
            </div>
        </div>
       
        </div>
    </div>
  
@endsection