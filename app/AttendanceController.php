<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Attendance;
use App\Client;
use Session;
use Alert;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function mark_checkin(Request $request){
        
        $att = Attendance::create('user_id'=>$request->user_id,'datetime'=>date("Y-m-d",strtotime($request->checktime)),'att_chectime'=>$request->checktime,'dev_id'=>'----']);
        if($att){
            //return json_encode(['status'=>1,"msg"=>"Attendance Marked Successfully"]);
        }else{
            //return json_encode(['status'=>0,"msg"=>"Something Went Wrong, Please Try again"]);
        }
    }
}
