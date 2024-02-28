<?php
namespace App\Http\Controllers;
use App\EmployeeDedicatedOffDay;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Designation;
use App\Client;
use App\Module;
use App\ModuleRight;
use App\leaveRequest;
use App\Leave;
use App\TimeCategoryHistory;
use App\AttachEmployee;
use App\TimeCategory;
use Hash;
use Mail;
use DB;
use DateTime;
use DatePeriod;
use DateInterval;
use Session;
// set_time_limit(300);
class DMController extends Controller
{
    public function dashboard(){

        $date = date("Y-m-d");
        $this_week_sat = date("Y-m-d", strtotime('saturday this week'));
        $current_date = $this_week_sat;
        $week_first_day = date('Y-m-d', (strtotime('-5 day', strtotime($current_date))));
        $date_range_txt = "";

        foreach (getDatesFromRange($week_first_day, $current_date) as $range) {
            $date_range_txt .= "'" . $range . "',";
        }
        $date_range_txt = rtrim($date_range_txt, ",");
        $pieces = explode("','", $date_range_txt);
        $weekday1 = ltrim($pieces[0], "'");
        $weekday2 = $pieces[1];
        $weekday3 = $pieces[2];
        $weekday4 = $pieces[3];
        $weekday5 = $pieces[4];
        $weekday6 = rtrim($pieces[5], "'");
        $day = date("l");

        $top_statistics_sql = "select u.id,tc.id as tcat_id,
                  tc.time_in,
                  tc.time_out,
                  tc.grace_time,
                  tc_monday_in,
                  tc_monday_out,
                  tc_tuesday_in,
                  tc_tuesday_out,
                  tc_wednesday_in,
                  tc_wednesday_out,
                  tc_thursday_in,
                  tc_thursday_out,
                  tc_friday_in,
                  tc_friday_out,
                  tc_satureday_in,
                  tc_satureday_out,
                  tc_sunday_in,
                  tc_sunday_out,
                  tc.is_night_shift,
                  a.datetime,date(a.datetime) as att_date,d.title as designation
                  from attendance a
                  JOIN users u on a.user_id=u.id
                  JOIN clients c on c.id = u.department_id
                  JOIN time_categories tc  on tc.id=u.tcat_id
                  JOIN designations d on d.id = u.designation_id

                  WHERE  u.status=1 and u.flag=1  and date(a.datetime)  IN(" . $date_range_txt . ")
                  and u.id not in (SELECT user_id FROM `attach_employees_details` where attach_date  IN('" . $date . "')
                  )
                  and u.id not in (SELECT user_id FROM `leave_requests_details` where leave_date  IN('" . $date . "')
                  )
                  GROUP by date(a.datetime),u.id ";

        $total_departmental_employees = User::where('status', 1)->where('flag', 1)->count();

        $total_roster_employees = User::where('status', 1)->where('flag', 2)->count();

        $top_statistics_data = DB::select($top_statistics_sql);


        $leave_satistics = "select
              users.id,
              leave_requests_details.leave_date,
              leave_requests.from_date,
              leave_requests.to_date
              from users
              inner join leave_requests on users.id=leave_requests.user_id
              inner join leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
              inner join clients on clients.id=users.department_id
              where leave_requests_details.leave_date IN(" . $date_range_txt . ")
              and leave_requests.leave_type=2
              and users.status=1 AND users.flag=1
              GROUP by users.id,leave_requests_details.leave_date";

        $leave_satistics_data = DB::select($leave_satistics);
        $short_leave = "select
              count(*) as short_leave
              from users
              inner join leave_requests on users.id=leave_requests.user_id
              inner join leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
              inner join clients on clients.id=users.department_id
              where leave_requests_details.leave_date='" . date('Y-m-d') . "'
              and users.status=1 AND users.flag=1
              and leave_requests.leave_type=1";
        $short_leave_data = DB::select($short_leave);
        if (empty($short_leave_data)) {
            $short_leave_count = 0;
        } else {
            $short_leave_count = $short_leave_data[0]->short_leave;
        }
        $attach_satistics = "select
                users.id,
                users.name,
                attach_date,
                users.file_number,
                users.gender,
                users.bps,
                clients.name as department_name,
                designations.title as designation
                from users
                INNER join clients on clients.id=users.department_id
                INNER join designations on designations.id=users.designation_id
                INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                where attach_employees_details.status=1
                and users.status=1 AND users.flag=1
                and attach_employees_details.attach_date IN(" . $date_range_txt . ")
                group by attach_employees_details.attach_date,users.id";

        $attach_satistics_data = DB::select($attach_satistics);


        $attendance_data['present'][] = [];
        $attendance_data['late'][] = [];
        $attendance_data['absent'][] = [];
        $attendance_data['leave'][] = [];
        $attendance_data['attach'][] = [];

        foreach ($leave_satistics_data as $l) {
            if (array_key_exists($l->leave_date, $attendance_data['leave'])) {
                $attendance_data['leave'][$l->leave_date] += 1;
            } else {

                $attendance_data['leave'][$l->leave_date] = 1;
            }
        }
        foreach ($attach_satistics_data as $att) {
            if (array_key_exists($att->attach_date, $attendance_data['attach'])) {
                $attendance_data['attach'][$att->attach_date] += 1;
            } else {
                $attendance_data['attach'][$att->attach_date] = 1;
            }
        }
        foreach ($top_statistics_data as $data) {

            //  $time_cat_hist=  TimeCategoryHistory::withTrashed()->where('user_id',$data->id)->where('join_date','<=',$data->att_date);
            //  $record=timeCategoryHistForDashboard($time_cat_hist,$data);
            $unixTimestamp = strtotime($data->att_date);
            $day = date("l", $unixTimestamp);
            if ($day == "Monday") {
                $time_in = $data->tc_monday_in;
            } elseif ($day == "Tuesday") {
                $time_in = $data->tc_tuesday_in;
            } elseif ($day == "Wednesday") {
                $time_in = $data->tc_wednesday_in;
            } elseif ($day == "Thursday") {
                $time_in = $data->tc_thursday_in;
            } elseif ($day == "Friday") {
                $time_in = $data->tc_friday_in;
            } elseif ($day == "Saturday") {
                $time_in = $data->tc_satureday_in;
            } elseif ($day == "Sunday") {
                $time_in = $data->tc_sunday_in;
            }
            $min = date('i', strtotime($data->grace_time));
            $time_in = strtotime("+" . $min . " minutes", strtotime($time_in));
            // adding grace time to time in
            $time_in = date('H:i:s', $time_in);
            $att_time_in = strtotime($data->datetime);
            $attendance_time_in = date('H:i:s', $att_time_in);
            $present_previous_val = 0;

            if (array_key_exists($data->att_date, $attendance_data['present'])) {
                $present_previous_val = $attendance_data['present'][$data->att_date];
            }
            $attendance_data['present'][$data->att_date] = $present_previous_val + 1;
            if ($attendance_time_in > $time_in) {
                if (array_key_exists($data->att_date, $attendance_data['late'])) {
                    $attendance_data['late'][$data->att_date] += 1;
                } else {
                    $attendance_data['late'][$data->att_date] = 1;
                }
            }
        }

        if (isset($attendance_data['attach'][$weekday1])) {
            $attach_weekday1 = $attendance_data['attach'][$weekday1];
        } else {
            $attach_weekday1 = 0;
        }

        if (isset($attendance_data['attach'][$weekday2])) {
            $attach_weekday2 = $attendance_data['attach'][$weekday2];
        } else {
            $attach_weekday2 = 0;
        }

        if (isset($attendance_data['attach'][$weekday3])) {
            $attach_weekday3 = $attendance_data['attach'][$weekday3];
        } else {
            $attach_weekday3 = 0;
        }
        if (isset($attendance_data['attach'][$weekday4])) {
            $attach_weekday4 = $attendance_data['attach'][$weekday4];
        } else {
            $attach_weekday4 = 0;
        }
        if (isset($attendance_data['attach'][$weekday5])) {
            $attach_weekday5 = $attendance_data['attach'][$weekday5];
        } else {
            $attach_weekday5 = 0;
        }
        if (isset($attendance_data['attach'][$weekday6])) {
            $attach_weekday6 = $attendance_data['attach'][$weekday6];
        } else {
            $attach_weekday6 = 0;
        }
        if (isset($attendance_data['leave'][$weekday1])) {
            $leave_weekday1 = $attendance_data['leave'][$weekday1];
        } else {
            $leave_weekday1 = 0;
        }
        if (isset($attendance_data['leave'][$weekday2])) {
            $leave_weekday2 = $attendance_data['leave'][$weekday2];
        } else {
            $leave_weekday2 = 0;
        }
        if (isset($attendance_data['leave'][$weekday3])) {
            $leave_weekday3 = $attendance_data['leave'][$weekday3];
        } else {
            $leave_weekday3 = 0;
        }
        if (isset($attendance_data['leave'][$weekday4])) {
            $leave_weekday4 = $attendance_data['leave'][$weekday4];
        } else {
            $leave_weekday4 = 0;
        }
        if (isset($attendance_data['leave'][$weekday5])) {
            $leave_weekday5 = $attendance_data['leave'][$weekday5];
        } else {
            $leave_weekday5 = 0;
        }
        if (isset($attendance_data['leave'][$weekday6])) {
            $leave_weekday6 = $attendance_data['leave'][$weekday6];
        } else {
            $leave_weekday6 = 0;
        }

        if (isset($attendance_data['present'][$weekday1])) {
            $pre_weekday1 = $attendance_data['present'][$weekday1];
        } else {
            $pre_weekday1 = 0;
        }
        if (isset($attendance_data['present'][$weekday2])) {
            $pre_weekday2 = $attendance_data['present'][$weekday2];
        } else {
            $pre_weekday2 = 0;
        }
        if (isset($attendance_data['present'][$weekday3])) {
            $pre_weekday3 = $attendance_data['present'][$weekday3];
        } else {
            $pre_weekday3 = 0;
        }
        if (isset($attendance_data['present'][$weekday4])) {
            $pre_weekday4 = $attendance_data['present'][$weekday4];
        } else {
            $pre_weekday4 = 0;
        }
        if (isset($attendance_data['present'][$weekday5])) {
            $pre_weekday5 = $attendance_data['present'][$weekday5];
        } else {
            $pre_weekday5 = 0;
        }
        if (isset($attendance_data['present'][$weekday6])) {
            $pre_weekday6 = $attendance_data['present'][$weekday6];
        } else {
            $pre_weekday6 = 0;
        }

        if (isset($attendance_data['late'][$weekday1])) {
            $late_weekday1 = $attendance_data['late'][$weekday1];
        } else {
            $late_weekday1 = 0;
        }
        if (isset($attendance_data['late'][$weekday2])) {
            $late_weekday2 = $attendance_data['late'][$weekday2];
        } else {
            $late_weekday2 = 0;
        }
        if (isset($attendance_data['late'][$weekday3])) {
            $late_weekday3 = $attendance_data['late'][$weekday3];
        } else {
            $late_weekday3 = 0;
        }
        if (isset($attendance_data['late'][$weekday4])) {
            $late_weekday4 = $attendance_data['late'][$weekday4];
        } else {
            $late_weekday4 = 0;
        }
        if (isset($attendance_data['late'][$weekday5])) {
            $late_weekday5 = $attendance_data['late'][$weekday5];
        } else {
            $late_weekday5 = 0;
        }
        if (isset($attendance_data['late'][$weekday6])) {
            $late_weekday6 = $attendance_data['late'][$weekday6];
        } else {
            $late_weekday6 = 0;
        }


        foreach (getDatesFromRange($week_first_day, $current_date) as $range) {

            if (array_key_exists($range, $attendance_data['present'])) {
                $td_leave = 0;
                $td_attach = 0;
                if (array_key_exists($range, $attendance_data['leave'])) {
                    $td_leave = $attendance_data['leave'][$range];
                }
                if (array_key_exists($range, $attendance_data['attach'])) {
                    $td_attach = $attendance_data['attach'][$range];
                }
                $amb_this_date = 0;
                $attendance_data['absent'][$range] = $total_departmental_employees - ($attendance_data['present'][$range] + $td_leave + $td_attach);
            } else {
                $td_leave = 0;
                $td_attach = 0;
                if (array_key_exists($range, $attendance_data['leave'])) {
                    $td_leave = $attendance_data['leave'][$range];
                }
                if (array_key_exists($range, $attendance_data['attach'])) {
                    $td_attach = $attendance_data['attach'][$range];
                }
                $attendance_data['absent'][$range] = $total_departmental_employees - ($td_leave + $td_attach);
                if ($range < date("Y-m-d")) {
                    $attendance_data['absent'][$range] = $total_departmental_employees;
                }
            }
        }

        $absent_weekday1 = $attendance_data['absent'][$weekday1];
        $absent_weekday2 = $attendance_data['absent'][$weekday2];
        $absent_weekday3 = $attendance_data['absent'][$weekday3];
        $absent_weekday4 = $attendance_data['absent'][$weekday4];
        $absent_weekday5 = $attendance_data['absent'][$weekday5];
        $absent_weekday6 = $attendance_data['absent'][$weekday6];

        $department_devices_sql = "select serial_number from devices where serial_number !=''";
        $department_devices = "123,";
        foreach (DB::select($department_devices_sql) as $key => $val) {
            $department_devices .= "'" . $val->serial_number . "',";
        }
        $department_devices = rtrim($department_devices, ",");
        $devices_sql = "SELECT
                            *,IF(DATE_ADD(lastactivity, INTERVAL 10 MINUTE) > NOW(), 1, 0 ) AS online_status
from webster.webster_terminal where serialno in (" . $department_devices . ")";
        $online_offline_devices = DB::connection('mysql2')->select($devices_sql);

        //sub departments statistics
        $sub_department_statistics = [];
        $tclients = Client::get();
        foreach ($tclients as $client) {
            $sub_department_statistics[$client->name] = $this->sub_dashboard($client->id);
        }

        $page_title = "Main Dashboard";

        return view(
            'dashboard-manager.dashboard',
            compact('page_title',
                'attendance_data',
                'sub_department_statistics',
                'total_departmental_employees',
                'online_offline_devices',
                'absent_weekday1',
                'absent_weekday2',
                'absent_weekday3',
                'absent_weekday4',
                'absent_weekday5',
                'absent_weekday6',
                'attach_weekday1',
                'attach_weekday2',
                'attach_weekday3',
                'attach_weekday4',
                'attach_weekday5',
                'attach_weekday6',
                'pre_weekday1',
                'pre_weekday2',
                'pre_weekday3',
                'pre_weekday4',
                'pre_weekday5',
                'pre_weekday6',
                'late_weekday1',
                'late_weekday2',
                'late_weekday3',
                'late_weekday4',
                'late_weekday5',
                'late_weekday6',
                'leave_weekday1',
                'leave_weekday2',
                'leave_weekday3',
                'leave_weekday4',
                'leave_weekday5',
                'leave_weekday6',
                'short_leave_count',
                'total_roster_employees')
        );
        //dd($dep_id);

    }
    public function employees(Request $request){
        $page_title = 'Employees List';
        $departments = Client::where('parent_id',0)->get();
        $report_date=date('Y-m-d');
        $day= date("l");
        $orderby='id';
        $ordertype='asc';
        $choosen_departments = [];
        $status = 'all';
        $users = array();

        $department_id = 1;

        if($request->has('generate_report')) {
            $choosen_departments = $request->department;
            $status = $request->status;
        }

        if($request->has('report_date')){
            $report_date = date("Y-m-d",strtotime($request->report_date));
        }

        //$users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$department_id." or c.id = ".$department_id;
        $users_department_clause = "SELECT id FROM clients c WHERE c.parent_id = ".$department_id;

        if($request->has('department')){
            $child_departments = Client::wherein('parent_id',$request->department)->pluck('id');
            $user_departments  = array_merge($child_departments->toArray(),$request->department);
            $users_department_clause = implode(",",$user_departments);
        }

            $yesterday = date('Y-m-d',strtotime($report_date . "-1 days"));
            $tomorrow = date('Y-m-d',strtotime($report_date . "+1 days"));

            $hcal_sql ="SELECT hcal.id,hcal.title,hcal.user_id,
                   hcal.date as holiday_date,
                   hcal.department_id,
                   c.name as department_name,
                   hcal.date_from,
                   hcal.date_to
            FROM holiday_calendars hcal JOIN clients c on hcal.department_id = c.id
               WHERE  hcal.department_id in (".$users_department_clause.")
            and
              hcal.`date`
            = '".$report_date."' and   hcal.deleted_at IS NULL;";

            $holidays = collect(DB::select( DB::raw($hcal_sql)));


            $users_sql = "SELECT u.id as user_id,u.*,t.*,c.name as department_name,c.parent_id as d_parent_id,d.title as designation FROM users u
                      JOIN time_categories t on t.id = u.tcat_id
                      JOIN clients c on c.id = u.department_id
                      JOIN designations d on d.id = u.designation_id
                      WHERE u.department_id IN (
                          ".$users_department_clause."
                      )
                    AND u.flag = 1   and u.status = 1";
            $users = collect(DB::select($users_sql));


            $user_ids = implode(",", $users->pluck('user_id')->toArray());
            if($users->count()==0){ $user_ids = 0; }
            $leave_details_sql = "SELECT lrd.user_id FROM
                                  `leave_requests_details` lrd
                            JOIN leave_requests lr on lr.id = lrd.parent_table_id
                            where lrd.user_id in (".$user_ids.")
                            and lr.leave_type = 2
                            and date(lrd.leave_date) = '".$report_date."'";
            $leave_users = collect(DB::select($leave_details_sql));

            $sleave_details_sql = "SELECT lrd.user_id FROM
                                  `leave_requests_details` lrd
                            JOIN leave_requests lr on lr.id = lrd.parent_table_id
                            where lrd.user_id in (".$user_ids.")
                            and lr.leave_type = 1
                            and date(lrd.leave_date) = '".$report_date."'";
            $sleave_users = collect(DB::select($sleave_details_sql));
            $attach_details_sql = "SELECT distinct user_id FROM
                                  `attach_employees_details`
                            where user_id in (".$user_ids.")
                            and date(attach_date) = '".$report_date."'";
            $attach_users = collect(DB::select($attach_details_sql));


            $users_attendance_sql = "SELECT
                                a.`user_id`,
                                a.`datetime` AS attendance_time,
                                a.mark_attendance,
                                DATE(a.`datetime`) AS attendance_date
                              FROM attendance a WHERE a.`user_id` IN (
                                ".$user_ids."
                            )
                            AND DATE(a.`datetime`)
                          BETWEEN DATE(DATE_SUB('".$report_date."', INTERVAL 1 DAY)) AND '".$report_date."'";


            $att_data_raw = collect(DB::select($users_attendance_sql));



            $att_today = collect($att_data_raw->where('attendance_date',$report_date));

            $att_yesterday = collect($att_data_raw->where('attendance_date',$yesterday));
            //$att_tomorrow = collect($att_data_raw->where('attendance_date',$tomorrow));


            foreach ($users as $key => $user) {

                $attendance = array();
                // dd($emp_dedicated_offDay->off_day_date);
                $status =checkAbsent($report_date,$user->reg_date);
                $time_cat_history = "select
                                       date(tcat_history.join_date) as date, tcat.*
                                  from
                                       time_category_histories tcat_history
                                  INNER JOIN time_categories tcat
                                  on tcat.id=tcat_history.tcat_id
                                  where tcat_history.user_id='".$user->user_id."'";
                $time_cat_history = collect(DB::select($time_cat_history));
                $tcat_data = TimeCategory::find($user->tcat_id);
                $emp_dedicated_offDay=EmployeeDedicatedOffDay::where('user_id',$user->user_id)->where('off_day_date',$request->report_date)->first();
                if($emp_dedicated_offDay){
                    $dedicated_offday="offday";
                }else{
                    $dedicated_offday="";
                }


                $tcat_day_check = "tc_".strtolower(date('l', strtotime($report_date)))."_in";
                if($tcat_day_check == 'tc_saturday_in'){
                    $tcat_day_check = "tc_satureday_in";
                }
                if($tcat_day_check == 'tc_thursday_in'){
                    $tcat_day_check = "tc_thursday_in";
                }
                if($user->tcat_id == ""){
                    $status =checkAbsent($report_date,$user->reg_date);
                    if(checkAbsent($report_date,$user->reg_date)=='--'){
                        $emp_not_reg_prev_date+=1;
                    }
                }else if($tcat_data!=""){

                    $time_cat_for_offday= checkOffDayByTimeCategoryHistory($time_cat_history,$report_date,$tcat_data);

                    if($time_cat_for_offday->$tcat_day_check == ""){
                        $status = "offday";
                    }elseif($dedicated_offday=="offday"){
                        $status = "offday";
                    }else{
                        $status = checkAbsent($report_date,$user->reg_date);
                        if(checkAbsent($report_date,$user->reg_date)=='--'){
                            $emp_not_reg_prev_date+=1;
                        }
                    }
                }

                $attendance['user_id'] = $user->user_id;
                $attendance['name']    = $user->name;
                $attendance['d_parent_id']    = $user->d_parent_id;
                $attendance['department_id']    = $user->department_id;
                $attendance['department_name']    = $user->department_name;
                $attendance['designation']    = $user->designation;
                $attendance['file_number']    = $user->file_number;
                $attendance['gender']    = $user->gender;
                $attendance['bps']    = $user->bps;
                $attendance['time_in'] = "--";
                $attendance['time_out'] = "--";
                /**
                here we are checking whether the employee is absent/present/late/leave/attach
                 */


                if($user->is_night_shift == 1){
                    // 3549
                    //the user have 24 hours time category so lets check his attendance in previous date
                    $att_records = $att_today->where('user_id',$user->user_id);
                    $att_yesterday_records = $att_yesterday->where('user_id',$user->user_id);



                    if($att_records->count()>0){

                        $emp_att_filter_date=$att_today->where('user_id',$user->user_id)->first()->attendance_date;
                        $time_cat_his_info= checkTimeCategoryHistoryByDate($time_cat_history,$emp_att_filter_date,$user);
                        $status = "present";
                        $tcat_day_check = "tc_".strtolower(date('l', strtotime($report_date)))."_in";
                        $tcat_day_check_out = "tc_".strtolower(date('l', strtotime($report_date)))."_out";
                        if($tcat_day_check == 'tc_saturday_in'){
                            $tcat_day_check = "tc_satureday_in";
                        }
                        if($tcat_day_check_out == 'tc_saturday_out'){
                            $tcat_day_check_out = "tc_satureday_out";
                        }

                        $this_day_time_in = $yesterday." ".$time_cat_his_info->$tcat_day_check;
                        $this_day_time_out = $report_date." ".$time_cat_his_info->$tcat_day_check_out;



                        $tcat_grace_time = "+".$time_cat_his_info->grace_time." minutes";


                        $this_day_time_in_with_grace_time =  date("Y-m-d H:i:s", strtotime($this->date2min($time_cat_his_info->grace_time), strtotime($this_day_time_in)));

                        $this_day_trxs = $att_records->pluck('attendance_time')->toArray();


                        $yesterday_trxs = $att_yesterday_records->pluck('attendance_time')->toArray();

                        if($yesterday_trxs){

                            $yesterday_in = $yesterday_trxs[count($yesterday_trxs)-1];

                        }else{
                            $yesterday_in = "Missing";
                        }


                        if(count($this_day_trxs)>0){

                            $this_day_out = $this_day_trxs[0];

                        }else{

                            $this_day_out = "Missing";
                        }
                        if($yesterday_in > $this_day_time_in_with_grace_time){

                            $status = "late";
                        }
                        if($this_day_out<$this_day_time_out){

                            $status = "earlyout";
                        }

                        // $attendance['time_in'] = $yesterday_in.' - '.$this_day_time_in_with_grace_time;
                        // $attendance['time_out'] = $this_day_out .' - '. $this_day_time_out;
                        $attendance['time_in'] = $yesterday_in;
                        $attendance['time_out'] = $this_day_out;
                        if($sleave_users->where('user_id',$user->user_id)->count()>0){
                            $status = "s.leave";
                        }
                    }else{
                        //check whether the employee is on leave or attach
                        if($attach_users->where('user_id',$user->user_id)->count()>0){
                            $status = "attach";
                        }
                        if($leave_users->where('user_id',$user->user_id)->count()>0){
                            $status = "leave";
                        }
                        if($sleave_users->where('user_id',$user->user_id)->count()>0){
                            $status = "s.leave";
                        }
                    }

                }else{
                    if($leave_users->where('user_id',$user->user_id)->count()>0){
                        $status = "leave";
                    }
                    if($sleave_users->where('user_id',$user->user_id)->count()>0){
                        $status = "s.leave";
                    }

                    $att_records = $att_today->where('user_id',$user->user_id);
                    if($att_records->count()>0){

                        $emp_att_filter_date=$att_today->where('user_id',$user->user_id)->first()->attendance_date;
                        $time_cat_his_info= checkTimeCategoryHistoryByDate($time_cat_history,$emp_att_filter_date,$user);
                        $status = "present";

                        $tcat_day_check = "tc_".strtolower(date('l', strtotime($report_date)))."_in";

                        $tcat_day_check_out = "tc_".strtolower(date('l', strtotime($report_date)))."_out";
                        if($tcat_day_check == 'tc_saturday_in'){
                            $tcat_day_check = "tc_satureday_in";
                        }
                        if($tcat_day_check_out == 'tc_saturday_out'){
                            $tcat_day_check_out = "tc_satureday_out";
                        }

                        $this_day_time_in = $report_date." ".$time_cat_his_info->$tcat_day_check;
                        $this_day_time_out = $report_date." ".$time_cat_his_info->$tcat_day_check_out;
                        $tcat_grace_time = "+".$time_cat_his_info->grace_time." minutes";
                        $this_day_time_in_with_grace_time =  date("Y-m-d H:i:s", strtotime($this->date2min($time_cat_his_info->grace_time), strtotime($this_day_time_in)));
                        $this_day_trxs = $att_records->pluck('attendance_time')->toArray();
                        $this_day_in = date("H:i:s A",strtotime($this_day_trxs[0]));

                        if(count($this_day_trxs)>1){
                            $this_day_out = date("H:i:s A",strtotime($this_day_trxs[count($this_day_trxs)-1]));
                            $this_day_time_out_trx =  $this_day_trxs[count($this_day_trxs)-1];
                        }else{
                            $this_day_out = "Missing";
                        }
                        if($this_day_trxs[0]>$this_day_time_in_with_grace_time){
                            $status = "late";
                        }
                        if($this_day_out !="Missing" && $this_day_time_out_trx<$this_day_time_out){
                            $status = "earlyout";
                        }
                        if($sleave_users->where('user_id',$user->user_id)->count()>0){
                            $status = "s.leave";
                        }
                        $attendance['time_in'] = $this_day_in;
                        $attendance['time_out'] = $this_day_out;
                    }else{
                        //check whether the employee is on leave or attach
                        if($attach_users->where('user_id',$user->user_id)->count()>0){
                            $status = "attach";
                        }
                        if($leave_users->where('user_id',$user->user_id)->count()>0){
                            $status = "leave";
                        }
                        if($sleave_users->where('user_id',$user->user_id)->count()>0){
                            $status = "s.leave";
                        }
                    }
                }
                $attendance['status'] = $status;

                //dd($att_today->where('user_id',$user->id));
                $user_attendance[] = $attendance;

            }; //end users

            $user_attendance = collect($user_attendance);
            if($request->status == "present"){
                $user_attendance = $user_attendance->whereIn('status',['present','late']);
            }
            if($request->status == "absent"){
                $user_attendance = $user_attendance->where('status','absent');
            }
            if($request->status == "offday"){
                $user_attendance = $user_attendance->where('status','offday');
            }
            if($request->status == "late"){
                $user_attendance = $user_attendance->where('status','late');
            }
            if($request->status == "earlyout"){
                $user_attendance = $user_attendance->where('status','earlyout');
            }
            if($request->ordertype == 'desc'){
                $user_attendance = $user_attendance->sortByDesc($request->orderby);
            }else{
                $user_attendance = $user_attendance->sortBy($request->orderby);
            }



        return view(
            'dashboard-manager.employees',
            compact('page_title','departments','users','report_date','orderby','ordertype','choosen_departments','status')
        );
    }
    public function sub_dashboard($dept_id){

        $dep_id=$dept_id;
        $current_date =  date("Y-m-d");

        $top_statistics_sql = "select u.id,tc.id as tcat_id,
                                 tc.time_in,
                                 tc.time_out,
                                 tc.grace_time,
                                 tc_monday_in,
                                 tc_monday_out,
                                 tc_tuesday_in,
                                 tc_tuesday_out,
                                 tc_wednesday_in,
                                 tc_wednesday_out,
                                 tc_thursday_in,
                                 tc_thursday_out,
                                 tc_friday_in,
                                 tc_friday_out,
                                 tc_satureday_in,
                                 tc_satureday_out,
                                 tc_sunday_in,
                                 tc_sunday_out,
                                 tc.is_night_shift,
                                 a.datetime,date(a.datetime) as att_date,d.title as designation
                                 from attendance a
                                        JOIN users u on a.user_id=u.id
                                        JOIN clients c on c.id = u.department_id
                                        JOIN time_categories tc  on tc.id=u.tcat_id
                                        JOIN designations d on d.id = u.designation_id
                                WHERE u.status=1 and u.flag=1 and c.parent_id='".$dep_id."' and date(a.datetime) = '".$current_date."'
                                and u.id not in (SELECT user_id FROM `attach_employees_details` where attach_date  ='".$current_date."'
                                )
                                and u.id not in (SELECT user_id FROM `leave_requests_details` where leave_date = '".$current_date."'
                                )
                                GROUP by date(a.datetime),u.id";




        $total_departmental_employees = User::whereHas('departments',function($q)use($dep_id){
            return $q->where('parent_id',$dep_id);
        })->where('status',1)->where('flag',1)->count();
        $total_roster_employees = User::whereHas('departments',function($q)use($dep_id){
            return $q->where('parent_id',$dep_id);
        })->where('status',1)->where('flag',2)->count();
        $top_statistics_data = DB::select($top_statistics_sql);
        $leave_satistics= "select
                                 users.id,
                                     leave_requests_details.leave_date,
                                        leave_requests.from_date,
                                           leave_requests.to_date
                          from users
                               inner join leave_requests on users.id=leave_requests.user_id
                               inner join leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
                               inner join clients on clients.id=users.department_id
                          where clients.parent_id='".$dep_id."'
                          and leave_requests_details.leave_date  = '".$current_date."'
                          and leave_requests.leave_type=2
                          and users.status=1 AND users.flag=1
                          GROUP by users.id,leave_requests_details.leave_date";

        $leave_satistics_data = DB::select($leave_satistics);
        $short_leave="select
                           count(*) as short_leave
                    from users
                          inner join leave_requests on users.id=leave_requests.user_id
                          inner join leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
                          inner join clients on clients.id=users.department_id
                    where clients.parent_id='".$dep_id."'
                    and leave_requests_details.leave_date= '".$current_date."'
                    and users.status=1 AND users.flag=1
                    and leave_requests.leave_type=1";
        $short_leave_data = DB::select($short_leave);
        if(empty($short_leave_data)){
            $short_leave_count=0;
        }else{
            $short_leave_count=$short_leave_data[0]->short_leave;
        }

        $attach_satistics="select
                                users.id,
                                 users.name,
                                  attach_date,
                                   users.file_number,
                                    users.gender,
                                     users.bps,
                                      clients.name as department_name,
                                       designations.title as designation
                              from users
                                    INNER join clients on clients.id=users.department_id
                                    INNER join designations on designations.id=users.designation_id
                                    INNER JOIN attach_employees_details on attach_employees_details.user_id=users.id
                              where attach_employees_details.status=1
                              and users.status=1 AND users.flag=1
                              and attach_employees_details.attach_date  = '".$current_date."'
                              AND clients.parent_id='".Session::get('dep_id')."'
                              group by attach_employees_details.attach_date,users.id";

        $attach_satistics_data = DB::select($attach_satistics);





        $total_present = 0;
        $total_late = 0;
        $total_leave = 0;
        $total_attach = 0;
        $attendance_data['present'][] = [];

        $attendance_data['late'][] = [];
        $attendance_data['absent'][] = [];
        $attendance_data['leave'][] = [];
        $attendance_data['attach'][] = [];
        $designation_wise_present[][] = [];
        foreach($leave_satistics_data as $l){
            if(array_key_exists($l->leave_date,$attendance_data['leave'])) {
                $attendance_data['leave'][$l->leave_date] +=1;
            }else{

                $attendance_data['leave'][$l->leave_date] = 1;
            }
        }


        foreach($attach_satistics_data as $att){

            if(array_key_exists($att->attach_date,$attendance_data['attach'])) {
                $attendance_data['attach'][$att->attach_date] +=1;
            }else{

                $attendance_data['attach'][$att->attach_date] = 1;
            }
        }


        foreach($top_statistics_data as $data){


            // $time_history->where('user_id')
            $unixTimestamp = strtotime($data->att_date);

            $day=date("l", $unixTimestamp);

            if($day=="Monday"){
                $time_in=$data->tc_monday_in;
            }elseif($day=="Tuesday"){
                $time_in=$data->tc_tuesday_in;
            }elseif($day=="Wednesday"){
                $time_in=$data->tc_wednesday_in;
            }
            elseif($day=="Thursday"){
                $time_in=$data->tc_thursday_in;
            }elseif($day=="Friday"){
                $time_in=$data->tc_friday_in;
            }elseif($day=="Saturday"){
                $time_in=$data->tc_satureday_in;
            }elseif($day=="Sunday"){
                $time_in=$data->tc_sunday_in;
            }


            $min = date('i',strtotime($data->grace_time));
            $time_in = strtotime("+".$min." minutes", strtotime($time_in));

            // adding grace time to time in

            $time_in =  date('H:i:s', $time_in);

            $att_time_in=strtotime($data->datetime);
            $attendance_time_in =  date('H:i:s',$att_time_in);



            $present_previous_val = 0;
            if(array_key_exists($data->att_date,$attendance_data['present'])) {
                $present_previous_val = $attendance_data['present'][$data->att_date];
            }

            $attendance_data['present'][$data->att_date] = $present_previous_val + 1;
            if($attendance_time_in>$time_in){


                if(array_key_exists($data->att_date,$attendance_data['late'])) {
                    $attendance_data['late'][$data->att_date] +=1;
                }else{

                    $attendance_data['late'][$data->att_date] = 1;
                }
            }

        }


        if(isset($attendance_data['attach'][$current_date])){
            $attach_today = $attendance_data['attach'][$current_date];
        }else{
            $attach_today = 0;
        }

        if(isset($attendance_data['leave'][$current_date])){
            $leave_today = $attendance_data['leave'][$current_date];
        }else{
            $leave_today = 0;
        }
        if(isset($attendance_data['present'][$current_date])){
            $present_today = $attendance_data['present'][$current_date];
        }else{
            $present_today = 0;
        }
        if(isset($attendance_data['late'][$current_date])){
            $late_today = $attendance_data['late'][$current_date];
        }else{
            $late_today = 0;
        }

        $absent_today = $total_departmental_employees - ($present_today+$leave_today+$late_today+$attach_today);

        return ['total'=>$total_departmental_employees,'present'=>$present_today,
            'leave'=>$leave_today,'attach'=>$attach_today,'late'=>$late_today];
    }

}



function getDatesFromRange($start, $end, $format = 'Y-m-d') {
    $array = array();
    $interval = new DateInterval('P1D');

    $realEnd = new DateTime($end);
    $realEnd->add($interval);

    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    foreach($period as $date) {
        $array[] = $date->format($format);
    }

    return $array;
}
?>

