<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\leaveRequest;
use Session;

class LeaveRequestController extends Controller
{
    public function index(){
          
    	  $leaverequest= leaveRequest::with('user')->orderBy('id', 'desc')->get();
    	  return  json_encode($leaverequest);
    }
}
