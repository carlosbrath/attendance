<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Attendance;
use App\WebsterCheckInOut;

class attendanceData extends Command
{
    
    protected $signature = 'add:attendance';


    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    
    public function handle()
    {

        $get_att_max_id= Attendance::max('att_checkinout_id');                       
        $max_id= $get_att_max_id;
        if($max_id==''){
          $max_id_att=0;
        }
    else{
          $max_id_att=$max_id;
        }
        $new_webster_att=WebsterCheckInOut::where('id','>',$max_id_att)->get();
        foreach ($new_webster_att as $attendance) {
            $attendance_table = new Attendance();
            $attendance_table->user_id=$attendance->userid;
            $attendance_table->att_checktime=date("Y-m-d H:i:s");
            $attendance_table->datetime=$attendance->checktime;
            $attendance_table->dev_id=$attendance->serialno;
            $attendance_table->att_checkinout_id=$attendance->id;
            $attendance_table->save();
        }

                
    
    }
}
