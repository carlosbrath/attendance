<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AttendanceController;
use DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
       
         Commands\attendanceData::class,
   
    ];
   

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
       // $schedule->command('add:attendance')
        //          ->everyMinute();
        //Log::info('Scheduled task started.');

        $schedule->call(function () {
            AttendanceController::fetch_attendance(date('Y-m-d'),0);
            AttendanceController::fetch_attendance_external_deps_all(date('Y-m-d'));
            $to = "ishtiaqawan.30@gmail.com";
            $subject = "Hello, User!";
            $message = "This is a test email sent from PHP.";
            $headers = "From: your_email@example.com"; // Change this to your actual email address
        
            if(mail($to, $subject, $message, $headers)) {
                echo "Email sent successfully!";
            } else {
                echo "Error sending email.";
            }
        })->hourly();

        
        //->cron('0 9,10,11,16,17,18,22,23 * * *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
