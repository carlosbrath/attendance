<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Designation;
use App\Client;
use App\Region;
use App\Zone;
use App\Branch;
use App\Module;
use App\ModuleRight;
use App\leaveRequest;
use App\Leave;
use App\Device;
use App\TimeCategoryHistory;
use App\AttachEmployee;
use App\TimeCategory;
use App\DepartmentLevel;
use Hash;
use Mail;
use DB;
use DateTime;
use DatePeriod;
use DateInterval;
use Session;

use Illuminate\Support\Facades\Cache;
use App\ExternalDepartment;
use App\ExternalDepartmentsStat;

// set_time_limit(300);
class DashboardController extends Controller
{

  public function home(Request $request){

    if(Session::get('role_id')==1)
    {


      $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
      $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
      $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
      {
          return Client::generatetree(0,false,true);
      });

      $data['c_date'] = date("Y-m-d");
      $page_title='AJ&K Biometric Attendence System';
      return view('Dashboards.home',compact('page_title','data'));
    }

    elseif( Session::get('role_id') == 8)
    {
        $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
        $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
        $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
        {
            return Client::generatetree(0,false,true);
        });

        $data['c_date'] = date("Y-m-d");
        $page_title='AJ&K Biometric Attendence System';
        return view('Dashboards.pmHome',compact('page_title','data'));
    }
  }
  public function superAdmin(Request $request){

    $page_title='Executive Dashboard';
    $departments = '';
    $departments_clause = "";
    $departments_filter_clause = " where 1 ";
    $date = date("Y-m-d");

    $data['reg_employees']['total']=0;
    $data['unreg_employees']['total']=0;

    $data['total_employees']=0;
    $data['total_vacant_posts']=0;

    $data['roster_employees']=0;
    $data['absent'] = 0;
    $data['present'] = 0;
    $data['offdays'] = 0;
    $data['leave'] = 0;
    $data['attach'] = 0;
    $data['late'] = 0;


    if($request->has('date') && $request->date !=null){
        $date = $request->date;
    }

    $clients = Client::all();

    if($request->has('department_id') && $request->get('department_id')!='all' && !str_starts_with($request->get('department_id'), 'ed_')){
        $dep = Client::find($request->department_id);
        $page_title = $dep->name . ' ' . 'Dashboard';

        $filtered_departments = $this->getChildDepartments($clients,$request->get('department_id'));
        $filtered_departments_str = implode(',',$filtered_departments);
        $departments_clause = " and u.department_id in (".$filtered_departments_str.") ";
        $departments_filter_clause .= " and id in (".$filtered_departments_str.") ";

        $all_sub_depts = Client::getSubDepartments($request->get('department_id'));

        $devices = DB::table('Devices')->leftJoin('clients', 'Devices.department_id', '=', 'clients.id')
        ->whereIn('department_id',$all_sub_depts);

    }
    else
    {
      $devices = DB::table('Devices')->leftJoin('clients', 'Devices.department_id', '=', 'clients.id');
    }

    if($request->has('allregions') && $request->allregions !="all"){
      $region_input = explode('_',$request->get('allregions'));
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      $p_title = '';




      if($dmg_check == 'reg'){
         $departments_clause .= " and u.region_id =".$dmg_id;
         $departments_filter_clause.= " and region_id=".$dmg_id;
         $region = Region::find($dmg_id);
         $p_title = $region->region_name . ' '. 'Division' ;

         $devices = $devices->where('clients.region_id',$dmg_id);
         $data['all_departments'] =  Cache::remember('all_departments_'.$request->get('allregions'), now()->addMinutes(1440), function() use ($dmg_check,$dmg_id){
            return Client::generateRegionalDepartmentsTree('region_id',$dmg_id,true,false);
         });
      }elseif($dmg_check == 'zone'){
         $departments_clause .= " and u.zone_id =".$dmg_id;
         $departments_filter_clause.= " and zone_id=".$dmg_id;
         $zone = Zone::find($dmg_id);
         $p_title = $zone->zone_name .' '. 'District' ;
         $devices = $devices->where('clients.zone_id',$dmg_id);
         $data['all_departments'] =  Cache::remember('all_departments_'.$request->get('allregions'), now()->addMinutes(1440), function() use ($dmg_check,$dmg_id){
          return Client::generateRegionalDepartmentsTree('zone_id',$dmg_id,true,false);
       });
      }elseif($dmg_check == 'branch'){
         $departments_clause .= " and u.branch_id =".$dmg_id;
         $departments_filter_clause.= " and branch_id=".$dmg_id;
         $branch = Branch::find($dmg_id);
         $p_title = $branch->branch_name .' '. 'Tehsil' ;
         $devices = $devices->where('clients.branch_id',$dmg_id);

         $data['all_departments'] =  Cache::remember('all_departments_'.$request->get('allregions'), now()->addMinutes(1440), function() use ($dmg_check,$dmg_id){
          return Client::generateRegionalDepartmentsTree('branch_id',$dmg_id,true,false);
       });
      }
      $page_title = $page_title. ' - '. $p_title;
      $data['dmg_check'] = $dmg_check;
      $data['dmg_id'] = $dmg_id;
    }
    else
    {
      $data['all_departments'] =  session()->get('clientstreefordd');
    }


    //Here filter out jurisdiction
    $jurisdiction = \Session::get('jurisdiction');
    if($jurisdiction){
      dd('Coming Soon, under maintenance....');
      $departments = $jurisdiction->departments;
      $regions = $jurisdiction->regions;
      $zones = $jurisdiction->zones;
      $branches = $jurisdiction->branches;

      if($departments!=""){
        $departments_clause.= " and u.department_id in (".$departments.") ";
        $departments_filter_clause .= " and id in (".$departments.") ";
        //dd($departments);
        $devices = $devices->whereIn('department_id',$departments);

      }
      if($zones!=""){
        $departments_clause.= " and u.zone_id in (".$zones.") ";
        $departments_filter_clause .= " and zone_id in (".$zones.") ";
        //$devices = $devices->whereIn('clients.zone_id',$zones);
      }
      if($regions!=""){
        $departments_clause.= " and u.region_id in (".$regions.") ";
        $departments_filter_clause .= " and region_id in (".$regions.") ";
        //$devices = $devices->whereIn('clients.region_id',$regions);
      }
      if($branches!=""){
        $departments_clause.= " and u.branch_id in (".$branches.") ";
        $departments_filter_clause .= " and branch_id in (".$branches.") ";
        //$devices = $devices->whereIn('clients.branch_id',$branches);
      }

    }

    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";
    $absent_sql = "select
                        COUNT(IF(CAST(tc.".$tc_in_column." AS TIME) < TIME(NOW()),1,NULL)) as total,
                        COUNT(IF(CAST(tc.".$tc_in_column." AS TIME) > TIME(NOW()),1,NULL)) as shift_not_started,
                        IFNULL(SUM(case when u.gender = 'male' then 1 else 0 end),0) as male,
                        IFNULL(SUM(case when u.gender = 'female' then 1 else 0 end),0) as female,
                        IFNULL(SUM(case when u.gender = 'male' and u.is_gazetted=1 then 1 else 0 end),0) as g_male,
                        IFNULL(SUM(case when u.gender = 'female' and u.is_gazetted=1 then 1 else 0 end),0) as g_female,
                        IFNULL(SUM(case when u.gender = 'male' and u.is_gazetted=0 then 1 else 0 end),0) as ng_male,
                        IFNULL(SUM(case when u.gender = 'female' and u.is_gazetted=0 then 1 else 0 end),0) as ng_female,
                        time(NOW()),
                        tc.time_in,
                        tc.id from
                    users u
                    LEFT JOIN time_categories tc
                    ON  tc.id = (SELECT tcat_id FROM time_category_histories
                                    WHERE user_id = u.id and date(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
                    where u.id not in (select user_id from attendance where date(datetime) = date('".$date."'))
                    and u.role_id IN(6,7) and u.status=1 and u.flag = 1
                    and u.id not in (select user_id from leave_requests_details where leave_date = date('".$date."'))
                    and u.id not in (
                      SELECT user_id FROM `attach_employees_details`  WHERE  attach_date = DATE('".$date."')
                    )
                    ".$departments_clause;

    $present_sql = "select
                        IFNULL(SUM(case when u.gender = 'male' then 1 else 0 end),0) as male,
                        IFNULL(SUM(case when u.gender = 'female' then 1 else 0 end),0) as female,
                        IFNULL(SUM(case when u.gender = 'male' and u.is_gazetted=1 then 1 else 0 end),0) as g_male,
                        IFNULL(SUM(case when u.gender = 'female' and u.is_gazetted=1 then 1 else 0 end),0) as g_female,
                        IFNULL(SUM(case when u.gender = 'male' and u.is_gazetted=0 then 1 else 0 end),0) as ng_male,
                        IFNULL(SUM(case when u.gender = 'female' and u.is_gazetted=0 then 1 else 0 end),0) as ng_female,
                        time(NOW()),
                        tc.time_in,
                        tc.id from
                    users u
                    LEFT JOIN time_categories tc
                    ON  tc.id = (SELECT tcat_id FROM time_category_histories
                                    WHERE user_id = u.id and date(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
                    where CAST(IFNULL(tc.".$tc_in_column.",'09:00:00') AS time) < time(NOW())
                    and u.id in (select user_id from attendance where date(datetime) = date('".$date."'))
                    and u.role_id IN(6,7) and u.status=1  and u.flag = 1 ".$departments_clause;

    $total_employees_sql = "SELECT sum(total_employees) as total_employees FROM `clients` ".$departments_filter_clause;
    $total_vacant_posts_sql = "SELECT sum(vacant_posts) as total_vacant_posts FROM `clients` ".$departments_filter_clause;
    $offdays_sql = "SELECT
                  COUNT(u.id) AS total
                  FROM
                users u
                LEFT JOIN time_categories tc ON  tc.id  = (SELECT tcat_id FROM time_category_histories
                                    WHERE user_id = u.id and date(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
                WHERE ".$tc_in_column." IS NULL
                AND u.role_id IN(6,7)  and u.status=1  and u.flag = 1
                and u.id not in (select user_id from attendance where date(datetime) = date('".$date."')) ".$departments_clause;

    $leave_sql = "SELECT COUNT(u.id) AS total FROM `leave_requests_details` lrd
                    JOIN users u ON lrd.user_id = u.id
                  WHERE lrd.leave_date = date('".$date."')  and u.flag = 1 and u.status=1 AND u.role_id IN(6,7)
                  and u.id not in (select user_id from attendance where date(datetime) = date('".$date."'))
                  ".$departments_clause;

    $attach_sql = "SELECT COUNT(distinct u.id) AS total FROM `attach_employees_details` a
              JOIN users u ON u.id = a.user_id
              WHERE   u.flag = 1 and u.status = 1 AND u.role_id IN(6,7) and a.attach_date = DATE('".$date."') ".$departments_clause;


    $late_sql = "SELECT IFNULL(SUM(l1.is_late),0) as total FROM
                  (
                    SELECT
                      IFNULL(IF(SUBSTR(GROUP_CONCAT(DISTINCT a.datetime ORDER BY a.datetime ASC),1,19)>CONCAT(DATE(NOW()),' ',ADDTIME(tc.".$tc_in_column.",tc.grace_time)),1,0),0) AS is_late
                      FROM
                      users u
                      LEFT JOIN time_categories tc ON  tc.id = (SELECT tcat_id FROM time_category_histories
                                    WHERE user_id = u.id and date(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
                      JOIN attendance a ON a.user_id = u.id
                      WHERE u.role_id IN(6,7) and u.status=1 AND DATE(a.datetime) = DATE('".$date."')   and u.flag = 1 ".$departments_clause."
                    GROUP BY u.id
                  ) l1";



    $absent = DB::select(DB::raw($absent_sql));
    $present = DB::select(DB::raw($present_sql));
    $offdays = DB::select(DB::raw($offdays_sql));
    $leave = DB::select(DB::raw($leave_sql));
    $attach = DB::select(DB::raw($attach_sql));
    $late = DB::select(DB::raw($late_sql));
    $total_employees = DB::select(DB::raw($total_employees_sql));
    $total_vacant_posts = DB::select(DB::raw($total_vacant_posts_sql));

    $reg_employees = User::whereIn('role_id',[6,7])->where(['status'=>1,'flag'=>1,'field_staff'=>0]);
    $unreg_employees = User::whereIn('role_id',[6,7])->where(['status'=>1,'flag'=>1,'field_staff'=>1]);
    $roster_employees = User::whereIn('role_id',[6,7])->where(['status'=>1,'flag'=>2]);

    if($request->has('department_id') && $request->get('department_id')!='all'){
      $reg_employees->whereIn('department_id',$filtered_departments);
      $unreg_employees->whereIn('department_id',$filtered_departments);
      $roster_employees->whereIn('department_id',$filtered_departments);
    }

    if($request->has('allregions') && $request->allregions !="all"){
      if($dmg_check == 'reg'){
          $reg_employees->where('region_id',$dmg_id);
          $unreg_employees->where('region_id',$dmg_id);
          $roster_employees->where('region_id',$dmg_id);
      }
      if($dmg_check == 'zone'){
          $reg_employees->where('zone_id',$dmg_id);
          $unreg_employees->where('zone_id',$dmg_id);
          $roster_employees->where('zone_id',$dmg_id);
      }
      if($dmg_check == 'branch'){
          $reg_employees->where('branch_id',$dmg_id);
          $unreg_employees->where('branch_id',$dmg_id);
          $roster_employees->where('branch_id',$dmg_id);
      }
    }

    if($jurisdiction){

      if($departments!=""){
        $departments_clause.= " and u.department_id in (".$departments.") ";
        $departments_filter_clause .= " and id in (".$departments.") ";
        $reg_employees->whereIn('department_id',explode(',',$departments));
        $unreg_employees->whereIn('department_id',explode(',',$departments));
        $roster_employees->whereIn('department_id',explode(',',$departments));
        $departments = explode(',',$departments);
      }

      if($zones!=""){
        $departments_clause.= " and u.zone_id in (".$zones.") ";
        $departments_filter_clause .= " and zone_id in (".$zones.") ";
        $reg_employees->whereIn('zone_id',explode(',',$zones));
        $unreg_employees->whereIn('zone_id',explode(',',$zones));
        $roster_employees->whereIn('zone_id',explode(',',$zones));
      }
      if($regions!=""){
        $departments_clause.= " and u.region_id in (".$regions.") ";
        $departments_filter_clause .= " and region_id in (".$regions.") ";
        $reg_employees->whereIn('region_id',explode(',',$zones));
        $unreg_employees->whereIn('region_id',explode(',',$zones));
        $roster_employees->whereIn('region_id',explode(',',$zones));
      }
      if($branches!=""){
        $departments_clause.= " and u.branch_id in (".$branches.") ";
        $departments_filter_clause .= " and branch_id in (".$branches.") ";
        $reg_employees->whereIn('branch_id',explode(',',$zones));
        $unreg_employees->whereIn('branch_id',explode(',',$zones));
        $roster_employees->whereIn('branch_id',explode(',',$zones));
      }
    }

    $empty_dev_reg=$reg_employees->select('emp_type', DB::raw('count(*) as total'))->groupBy('emp_type')->get();;
    $empty_dev_unreg = $unreg_employees->select('emp_type', DB::raw('count(*) as total'))->groupBy('emp_type')->get();

    foreach($empty_dev_reg as $emptype)
    {
      $data['reg_employees']['childs'][$emptype->emp_type]=$emptype->total;
      $data['reg_employees']['total'] += $emptype->total;
    }

    foreach($empty_dev_unreg as $emptype)
    {
      $data['unreg_employees']['childs'][$emptype->emp_type]=$emptype->total;
      $data['unreg_employees']['total'] += $emptype->total;
    }


    $data['total_employees']=$total_employees[0]->total_employees;
    $data['total_vacant_posts']=$total_vacant_posts[0]->total_vacant_posts;
    $data['roster_employees']=$roster_employees->count();

    $data['absent'] = $absent[0];
    $data['present'] = $present[0];
    $data['offdays'] = $offdays[0]->total;
    $data['leave'] = $leave[0]->total;
    $data['attach'] = $attach[0]->total;
    $data['late'] = $late[0]->total;

    $data['department_id'] = $request->department_id;
    $data['c_date'] = $date;
    //$departments = Client::where('parent_id',0)->get();


    $external_departments = ExternalDepartment::where('status',1)->get();
    $external_departments_stats=[
      'total'=>0,
      'present'=>0,
      'absent'=>0,
      'late'=>0,
      'onleave'=>0,
      'ontour'=>0,
      'onschedule'=>0,
      'onattach'=>0,
      'shift_not_started'=>0,
      'early_exit'=>0, ];

    $data['devices']=['Online'=>0,'Offline'=>0,'Inactive'=>0];
    $devices = $devices->select('Devices.status', DB::raw('count(*) as total'))
           ->groupBy('Devices.status')
           ->get();
    foreach($devices as $ds)
    {
      $data['devices'][$ds->status]=$ds->total;
    }


    $data['allregions'] = session()->get('allregions');
    $data['selected_dmgs'] = $request->allregions;


    //dd($data['all_departments']);

    return view('Dashboards.admin',compact('page_title','data','departments','external_departments','external_departments_stats'));
  }

  public function get_sub_deps_stats(Request $request){

    $date = date("Y-m-d");
    if($request->has('date') && $request->date !=null){
        $date = $request->date;
    }
    $departments = [];

    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";

    $region_filter = "";
    if($request->has('selected_dmgs') && $request->selected_dmgs !="all"){
      $region_input = explode('_',$request->get('selected_dmgs'));
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      if($dmg_check == 'reg'){
          $region_filter .= " and u.region_id =".$dmg_id;
      }
      if($dmg_check == 'zone'){
          $region_filter .= " and u.zone_id =".$dmg_id;
      }
      if($dmg_check == 'branch'){
          $region_filter .= " and u.branch_id =".$dmg_id;
      }
      $data['dmg_check'] = $dmg_check;
      $data['dmg_id'] = $dmg_id;
    }

    $sql = "SELECT
            d.id as department_id,
            d.name AS department,
            COUNT(u.id) AS total_present

          FROM
            users u
            JOIN clients d ON d.id = u.department_id
            LEFT JOIN time_categories tc
            ON  tc.id = (SELECT tcat_id FROM time_category_histories
                    WHERE user_id = u.id AND DATE(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
            WHERE CAST(IFNULL(tc.".$tc_in_column.",'09:00:00') AS TIME) < TIME(NOW())
            AND u.id IN (SELECT user_id FROM attendance WHERE DATE(DATETIME) = date('".$date."'))
            ".$region_filter."
            AND u.role_id IN(6,7) AND u.status=1 and u.flag = 1
            GROUP BY d.id";

    $absent_sql = "SELECT
                    d.id as department_id,
                    d.name AS department,
                    COUNT(u.id) AS total_absent
                    from
                        users u
                        join clients as d on d.id = u.department_id
                        LEFT JOIN time_categories tc
                        ON  tc.id = (SELECT tcat_id FROM time_category_histories
                                        WHERE user_id = u.id and date(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
                        where CAST(tc.".$tc_in_column." AS time) < time(NOW())
                        and u.id not in (select user_id from attendance where date(datetime) = date('".$date."'))
                        and u.id not in (select user_id from leave_requests_details where leave_date = date('".$date."'))
                        and u.id not in (
                          SELECT user_id FROM `attach_employees_details`  WHERE  attach_date = DATE('".$date."')
                        )
                        and u.role_id IN(6,7) and u.status=1 and u.flag = 1
                        ".$region_filter."
                    GROUP BY d.id";
    $offdays_sql =  "SELECT
            d.id AS department_id,
            d.name AS department,
            COUNT(u.id) AS total
            FROM users u
            JOIN clients AS d ON d.id = u.department_id
            LEFT JOIN time_categories tc ON  tc.id  = (SELECT tcat_id FROM time_category_histories
                                WHERE user_id = u.id AND DATE(join_date) <= DATE('".$date."') ORDER BY join_date
                                    DESC LIMIT 1)
            WHERE tc.".$tc_in_column." IS NULL
            AND u.role_id IN(6,7)  AND u.status=1  AND u.flag = 1
            ".$region_filter."
            GROUP BY d.id";
    $late_sql = "SELECT d.id as department_id,d.name,SUM(l1.is_late) AS total FROM (
                SELECT
                u.id,
                u.department_id,
                IFNULL(IF(SUBSTR(GROUP_CONCAT(DISTINCT a.datetime ORDER BY a.datetime ASC),1,19)>CONCAT(DATE(NOW()),' ',ADDTIME(tc.".$tc_in_column.",tc.grace_time)),1,0),0) AS is_late
                  FROM
                  users u
                  LEFT JOIN time_categories tc ON  tc.id = (SELECT tcat_id FROM time_category_histories
                        WHERE user_id = u.id AND DATE(join_date) <= DATE('".$date."') ORDER BY join_date DESC LIMIT 1)
                  JOIN attendance a ON a.user_id = u.id
                  WHERE u.role_id IN(6,7) AND u.status=1  AND u.flag = 1 AND DATE(a.datetime) = DATE(NOW())
                  ".$region_filter."
                GROUP BY u.id
            ) AS l1 JOIN clients AS d ON d.id = l1.department_id
            GROUP BY d.id";

    $attach_sql = "SELECT
                    u.department_id as department_id,
                    COUNT(distinct u.id) AS total
                    from

                    `attach_employees_details` a
                        JOIN users u ON u.id = a.user_id
                        WHERE   a.attach_date  = DATE('".$date."')
                          AND u.role_id IN(6,7) AND u.status=1 and u.flag = 1
                        ".$region_filter."
                    GROUP BY u.department_id";

    $leave_sql= "select
                            u.department_id as department_id,
                            COUNT(u.id) AS total
                    from users as u
                          inner join leave_requests on u.id=leave_requests.user_id
                          inner join leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
                          inner join clients on clients.id=u.department_id
                    where
                    leave_requests_details.leave_date  = DATE('".$date."')
                    AND u.role_id IN(6,7) AND u.status=1 and u.flag = 1
                    ".$region_filter."
                    GROUP by u.department_id";






    $parent_departments = [];
    $all_departments = [];

    $departments_clause = "";
    if($request->has('department_id') && $request->get('department_id')!='all'){
        $choosen_dept = $request->get('department_id');
        $child_depts = $this->getChildDepartments(Client::all(),$choosen_dept);

        if(count($child_depts)==0 || (count($child_depts) == 1 && $child_depts[0]== $choosen_dept)){
          return 'no-mor-deps';exit;
            //means we will show the employees list bc its the nth department
            //return redirect()->route('dd-all-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
        }
        $filtered_departments = implode(',',$child_depts);
        $departments_clause = " and u.department_id in (".$filtered_departments.") ";

        $parent_departments = Client::where('parent_id',$request->get('department_id'))->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
        $all_departments = Client::whereIn('parent_id',$child_depts)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
    }
    else
    {
      $parent_departments = Client::where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
      $all_departments = Client::orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
    }

    $rs = collect(DB::select($sql));
    $absent_rs = collect(DB::select($absent_sql));
    $od_rs = collect(DB::select($offdays_sql));
    $attach_rs = collect(DB::select($attach_sql));
    $leave_rs = collect(DB::select($leave_sql));
    $late_rs = collect(DB::select($late_sql));
    $data = [];
    $page_title='Department Wise';

    if($request->has('department_id') && $request->department_id != 'all'){
      $dep = Client::find($request->department_id);
      if($dep){
        $dep = $dep->name;
      $page_title= $dep;
      }
    }
    $page_title = $page_title. ' Dashboard';


    foreach($parent_departments as $key=>$department){

        $child_dept_ids = $this->getChildDepartments($all_departments,$department->id);
        $total_present = $rs->whereIn('department_id',$child_dept_ids)->sum('total_present');
        $total_leave = $leave_rs->whereIn('department_id',$child_dept_ids)->sum('total');
        $total_attach = $attach_rs->whereIn('department_id',$child_dept_ids)->sum('total');
        $total_absent = $absent_rs->whereIn('department_id',$child_dept_ids)->sum('total_absent');
        $total_offdays = $od_rs->whereIn('department_id',$child_dept_ids)->sum('total');
        $total_late = $late_rs->whereIn('department_id',$child_dept_ids)->sum('total');

        $dept_reg_employees = User::where(['status'=>1,'flag'=>1,'field_staff'=>0])->whereIn('role_id',[6,7])->whereIn('department_id',$child_dept_ids);
        $dept_unreg_employees = User::where(['status'=>1,'flag'=>1,'field_staff'=>1])->whereIn('role_id',[6,7])->whereIn('department_id',$child_dept_ids);

        $bb_total_employees = Client::whereIn('id',$child_dept_ids)->sum('total_employees');
        $bb_vacant_posts = Client::whereIn('id',$child_dept_ids)->sum('vacant_posts');

        if($request->has('selected_dmgs') && $request->selected_dmgs !="all"){
          if($dmg_check == 'reg'){
            $dept_reg_employees->where('region_id',$dmg_id);
            $dept_unreg_employees->where('region_id',$dmg_id);

            $bb_total_employees = Client::whereIn('id',$child_dept_ids)->where('region_id',$dmg_id)->sum('total_employees');
            $bb_vacant_posts = Client::whereIn('id',$child_dept_ids)->where('region_id',$dmg_id)->sum('vacant_posts');
          }
          if($dmg_check == 'zone'){
            $dept_reg_employees->where('zone_id',$dmg_id);
            $dept_unreg_employees->where('zone_id',$dmg_id);

            $bb_total_employees = Client::whereIn('id',$child_dept_ids)->where('zone_id',$dmg_id)->sum('total_employees');
            $bb_vacant_posts = Client::whereIn('id',$child_dept_ids)->where('zone_id',$dmg_id)->sum('vacant_posts');
          }
          if($dmg_check == 'branch'){
            $dept_reg_employees->where('branch_id',$dmg_id);
            $dept_unreg_employees->where('branch_id',$dmg_id);

            $bb_total_employees = Client::whereIn('id',$child_dept_ids)->where('branch_id',$dmg_id)->sum('total_employees');
            $bb_vacant_posts = Client::whereIn('id',$child_dept_ids)->where('branch_id',$dmg_id)->sum('vacant_posts');
          }
        }

        $reg_employees_sts=$dept_reg_employees->count();;
        $unreg_employees_sts = $dept_unreg_employees->count();



        $department['bb_total_employees'] = $bb_total_employees;
        $department['bb_vacant_posts'] = $bb_vacant_posts;

        $department['reg_employees'] = $reg_employees_sts;
        $department['unreg_employees'] = $unreg_employees_sts;

        $department['present'] = $total_present;
        $department['leave'] = $total_leave;
        $department['attach'] = $total_attach;
        $department['absent'] = $total_absent;
        $department['offdays'] = $total_offdays;
        $department['late'] = $total_late;
        $departments[] = $department;
    }

    $data['c_date'] = $date;
    $data['selected_dmgs'] = $request->selected_dmgs;

    $counter=0;
    foreach($departments as $department)
    {$counter++;
      ?>
      <tr>
        <td> <?php echo $counter;?></td>
        <td  style="text-align:left!important">
          <a target="_blank" href="<?php echo route('dw-employees',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) ?>">
            <?php echo  $department->name ?>
          </a>
          </td>
        <td  class="bold">
          <a target="_blank" href="<?php echo  route('dw-employees',['date'=>$data['c_date'],'department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs']]) ?>">
            <?php echo  number_format($department->bb_total_employees,0) ?>
          </a>
        </td>
        <td  class="bold">
          <a target="_blank" href="<?php echo  route('dw-registered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs'],'list'=>'yes']) ?>" title="Drill Down" >
            <?php echo  number_format($department->reg_employees,0) ?>
          </a>
        </td>
        <td  class="bold">
          <a target="_blank" href="<?php echo  route('dw-unregistered',['department_id'=>$department->id,'selected_dmgs'=>$data['selected_dmgs'],'list'=>'yes']) ?>" title="Drill Down" >
            <?php echo  number_format($department->unreg_employees,0) ?>
          </a>
        </td>
        <td  class="bold"><?php echo  number_format($department->bb_vacant_posts,0) ?></td>
        <td  class="bold"><?php echo  number_format($department->bb_total_employees - ($department->reg_employees + $department->bb_vacant_posts+$department->unreg_employees),0) ?></td>
        <td  class="bold">
          <a target="_blank" class="t-present" href="<?php echo  route('dw-present',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) ?>">
            <?php echo  number_format($department->present,0) ?>
          </a>
        </td>
        <td  class="bold">
          <a target="_blank" class="t-absent" href="<?php echo  route('dw-absent',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) ?>">
            <?php echo  number_format($department->absent,0) ?>
          </a>
        </td>
        <td  class="bold">
          <a target="_blank"  class="t-late" href="<?php echo  route('dw-late',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) ?>">
            <?php echo  number_format($department->late,0) ?>
          </a>
        </td>
        <td  class="bold">
          <a target="_blank"  class="t-leave" href="<?php echo  route('dw-leave',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) ?>">
            <?php echo  number_format($department->leave,0) ?>
          </a>
        </td>
        <td  class="bold">
          <a target="_blank" href="<?php echo  route('dw-attach',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) ?>">
            <?php echo  number_format($department->attach,0) ?>
          </a>
        </td>
        <td  class="bold">
          <a target="_blank" href="<?php echo  route('dw-offdays',['date'=>$data['c_date'],'department_id'=>$department->id,'list'=>'yes','selected_dmgs'=>$data['selected_dmgs']]) ?>">
            <?php echo  number_format($department->offdays,0) ?>
          </a>
        </td>
      </tr>
      <?php
    }

    exit();

  }

  public function get_external_deps_stats(Request $request){

    $date = date("Y-m-d");
    if($request->has('date'))
    {
      $date = $request->get('date');
    }
    
    $departments = ExternalDepartment::where('status',1)->get();
    // $external_departments_stats=[
    //   'total'=>0,
    //   'present'=>0,
    //   'absent'=>0,
    //   'late'=>0,
    //   'onleave'=>0,
    //   'ontour'=>0,
    //   'onschedule'=>0,
    //   'onattach'=>0,
    //   'shift_not_started'=>0,
    //   'early_exit'=>0, ];

    $counter=0;
    foreach($departments as $department)
    {
      $stats = ExternalDepartmentsStat::where('dep_id',$department->id)->where('updated_date',$date)->first();
      $counter++;
      if($stats)
      {
        ?>
        <tr>
          <td> <?php echo $counter;?></td>
          <td  style="text-align:left!important">
            <a target="_blank" href="<?php echo  $department->login_link ?>">
              <?php echo  $department->department_name ?>
            </a>
            </td>
          <td  class="bold">
            <a target="_blank" href="<?php echo  $department->login_link ?>">
              <?php echo  $department->total_employees ? number_format($department->total_employees,0):'--' ?>
            </a>
          </td>
          <td  class="bold">
            <a target="_blank" href="<?php echo  $department->login_link ?>">
              <?php echo  $stats->total ? number_format($stats->total,0):'--' ?>
            </a>
          </td>
          <td  class="bold">
            <a target="_blank" href="<?php echo  $department->login_link ?>">
              <?php echo  ($department->total_employees && $stats->total)  ? number_format(($department->total_employees - $stats->total),0):'--' ?>
            </a>
          </td>
          <td  class="bold">
            <a target="_blank" class="t-present" href="<?php echo  $department->login_link ?>">
            <?php echo  $stats->present ? number_format($stats->present,0):'--' ?>
            </a>
          </td>
          <td  class="bold">
            <a target="_blank" class="t-absent" href="<?php echo  $department->login_link ?>">
            <?php echo  $stats->absent ? number_format($stats->absent,0):'--' ?>
            </a>
          </td>
          <td  class="bold">
            <a target="_blank"  class="t-late" href="<?php echo  $department->login_link ?>">
            <?php echo  $stats->late ? number_format($stats->late,0):'--' ?>
            </a>
          </td>
          <td  class="bold">
            <a target="_blank"  class="t-leave" href="<?php echo  $department->login_link ?>">
            <?php echo  $stats->onleave ? number_format($stats->onleave,0):'--' ?>
            </a>
          </td>
          <td  class="bold">
            <a target="_blank" href="<?php echo  $department->login_link ?>">
            <?php echo  $stats->ontour ? number_format($stats->ontour,0):'--' ?>
            </a>
          </td>
          <td  class="bold">
            <a target="_blank" href="<?php echo  $department->login_link ?>">
            <?php echo  $stats->off_days ? number_format($stats->off_days,0):'--' ?>
            </a>
          </td>
        </tr>
        <?php
      }
      else{
        ?>
        <tr>
          <td> <?php echo $counter;?></td>
          <td  style="text-align:left!important"><a target="_blank" href="<?php echo  $department->login_link ?>"><?php echo  $department->department_name ?></a></td>
          <td  class="bold"><a target="_blank" href="<?php echo  $department->login_link ?>"><?php echo  $department->total_employees ? number_format($department->total_employees,0):'--' ?></a></td>
          <td  class="bold"><a target="_blank" href="<?php echo  $department->login_link ?>">--</a></td>
          <td  class="bold"><a target="_blank" href="<?php echo  $department->login_link ?>">--</a></td>
          <td  class="bold"><a target="_blank" class="t-present" href="<?php echo  $department->login_link ?>">--</a></td>
          <td  class="bold"><a target="_blank" class="t-absent" href="<?php echo  $department->login_link ?>">--</a></td>
          <td  class="bold"><a target="_blank"  class="t-late" href="<?php echo  $department->login_link ?>">--</a></td>
          <td  class="bold"><a target="_blank"  class="t-leave" href="<?php echo  $department->login_link ?>">--</a></td>
          <td  class="bold"><a target="_blank" href="<?php echo  $department->login_link ?>">--</a></td>
          <td  class="bold"><a target="_blank" href="<?php echo  $department->login_link ?>">--</a></td>
        </tr>
        <?php
      }
      
    }

    exit();

  }

  public function get_sub_dmgs_stats(Request $request){

    $date = $request->get('date');
    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";

    $whereClause = "";
    $choosen_dept="all";

    $posts = Client::all();
    $employees = User::where(['status'=>1,'flag'=>1])->whereIn('role_id',[6,7]);


    if($request->has('department_id') && $request->get('department_id')!='all'){
        $choosen_dept = $request->get('department_id');
        $child_depts = $this->getChildDepartments($posts,$choosen_dept);

        if(count($child_depts)==0){
          $child_depts=array($choosen_dept);
        }
        $filtered_departments = implode(',',$child_depts);
        $whereClause = " and u.department_id in (".$filtered_departments.") ";

        $posts = $posts->whereIn('id',$child_depts);
        $employees = $employees->whereIn('department_id',$child_depts);


      }




    $dmg_stats = [];
    $dmg_select = "";
    $dmg_select_key = "";
    $dmgs=[];
    $dmgs_rs = [];

    if($request->has('selected_dmgs') && $request->selected_dmgs !="all"){
      $region_input = explode('_',$request->get('selected_dmgs'));
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];

      if($dmg_check == 'reg'){
        $dmg_select_key ='zone_';
        $whereClause .= " and u.region_id = ".$dmg_id;
        $dmg_select = "u.zone_id";
        $dmgs_rs = Zone::where('region_id',$dmg_id)->select('zone_id as id','zone_name as dmg_title')->get();


        $posts = $posts->where('region_id',$dmg_id);
        $employees = $employees->where('region_id',$dmg_id);

      }
      if($dmg_check == 'zone'){
        $dmg_select_key ='branch_';
        $whereClause .= " and u.zone_id = ".$dmg_id;
        $dmg_select = "u.branch_id";
        $dmgs_rs = Branch::where('zone_id',$dmg_id)->select('branch_id as id','branch_name as dmg_title')->get();

        $posts = $posts->where('zone_id',$dmg_id);
        $employees = $employees->where('zone_id',$dmg_id);
      }
      if($dmg_check == 'branch'){
          return 'no-more-divisions';
      }
    }
    else
    {
      $dmg_select_key ='reg_';
      $dmg_select = "u.region_id";
      $dmgs_rs = Region::select('region_id as id','region_name as dmg_title')->get();
    }
    foreach($dmgs_rs as $dmg){
      $dmgs[]=$dmg->id;
      $dmg_stats[$dmg->id]=['title'=>$dmg->dmg_title,
                            'bb_total_employees'=>0,
                            'bb_vacant_posts'=>0,
                            'reg_employees'=>0,
                            'unreg_employees'=>0,
                            'present'=>0,
                            'leave'=>0,
                            'attach'=>0,
                            'absent'=>0,
                            'offdays'=>0,
                            'late'=>0];
    }

    $dmg_stats[0]=['title'=>'Not Set',
                            'bb_total_employees'=>0,
                            'bb_vacant_posts'=>0,
                            'reg_employees'=>0,
                            'unreg_employees'=>0,
                            'present'=>0,
                            'leave'=>0,
                            'attach'=>0,
                            'absent'=>0,
                            'offdays'=>0,
                            'late'=>0];


    $sql = "SELECT $dmg_select as dmg_id,
            COUNT(u.id) AS total_present
          FROM
            users u
            JOIN clients d ON d.id = u.department_id
            LEFT JOIN time_categories tc
            ON  tc.id = (SELECT tcat_id FROM time_category_histories
                    WHERE user_id = u.id AND DATE(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
            WHERE CAST(IFNULL(tc.".$tc_in_column.",'09:00:00') AS TIME) < TIME(NOW())
            AND u.id IN (SELECT user_id FROM attendance WHERE DATE(DATETIME) = date('".$date."'))
            ".$whereClause."
            AND u.role_id IN(6,7) AND u.status=1 and u.flag = 1
            GROUP BY $dmg_select";

    $absent_sql = "SELECT $dmg_select as dmg_id,
                    COUNT(u.id) AS total_absent
                    from
                        users u
                        join clients as d on d.id = u.department_id
                        LEFT JOIN time_categories tc
                        ON  tc.id = (SELECT tcat_id FROM time_category_histories
                                        WHERE user_id = u.id and date(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
                        where CAST(tc.".$tc_in_column." AS time) < time(NOW())
                        and u.id not in (select user_id from attendance where date(datetime) = date('".$date."'))
                        and u.id not in (select user_id from leave_requests_details where leave_date = date('".$date."'))
                        and u.id not in (
                          SELECT user_id FROM `attach_employees_details`  WHERE  attach_date = DATE('".$date."')
                        )
                        and u.role_id IN(6,7) and u.status=1 and u.flag = 1
                        ".$whereClause."
                    GROUP BY $dmg_select";
    $offdays_sql =  "SELECT $dmg_select as dmg_id,
            COUNT(u.id) AS total
            FROM users u
            JOIN clients AS d ON d.id = u.department_id
            LEFT JOIN time_categories tc ON  tc.id  = (SELECT tcat_id FROM time_category_histories
                                WHERE user_id = u.id AND DATE(join_date) <= DATE('".$date."') ORDER BY join_date
                                    DESC LIMIT 1)
            WHERE tc.".$tc_in_column." IS NULL
            AND u.role_id IN(6,7)  AND u.status=1  AND u.flag = 1
            ".$whereClause."
            GROUP BY $dmg_select";
    $late_sql = "SELECT dmg_id,
                  SUM(l1.is_late) AS total FROM (
                SELECT
                u.id,
                $dmg_select as dmg_id,
                IFNULL(IF(SUBSTR(GROUP_CONCAT(DISTINCT a.datetime ORDER BY a.datetime ASC),1,19)>CONCAT(DATE(NOW()),' ',ADDTIME(tc.".$tc_in_column.",tc.grace_time)),1,0),0) AS is_late
                  FROM
                  users u
                  LEFT JOIN time_categories tc ON  tc.id = (SELECT tcat_id FROM time_category_histories
                        WHERE user_id = u.id AND DATE(join_date) <= DATE('".$date."') ORDER BY join_date DESC LIMIT 1)
                  JOIN attendance a ON a.user_id = u.id
                  WHERE u.role_id IN(6,7) AND u.status=1  AND u.flag = 1 AND DATE(a.datetime) = DATE(NOW())
                  ".$whereClause."
                GROUP BY u.id
            ) AS l1
            GROUP BY dmg_id";


    $attach_sql = "SELECT $dmg_select as dmg_id,
                    COUNT(distinct u.id) AS total
                    from

                    `attach_employees_details` a
                        JOIN users u ON u.id = a.user_id
                        WHERE  a.attach_date  = DATE('".$date."')
                      AND u.role_id IN(6,7) AND u.status=1 and u.flag = 1
                        ".$whereClause."
                    GROUP BY $dmg_select";

    $leave_sql= "select $dmg_select as dmg_id,
                            COUNT(u.id) AS total
                    from users as u
                          inner join leave_requests on u.id=leave_requests.user_id
                          inner join leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
                          inner join clients on clients.id=u.department_id
                    where
                    leave_requests_details.leave_date  = DATE('".$date."')
                    AND u.role_id IN(6,7) AND u.status=1 and u.flag = 1
                    ".$whereClause."
                    GROUP by $dmg_select";
    $rs = collect(DB::select($sql));
    $absent_rs = collect(DB::select($absent_sql));
    $od_rs = collect(DB::select($offdays_sql));
    $attach_rs = collect(DB::select($attach_sql));
    $leave_rs = collect(DB::select($leave_sql));
    $late_rs = collect(DB::select($late_sql));






    $counter=0;
    foreach($dmg_stats as $key=>$dmg){
        $counter++;
        //dd($rs);
        $dmg['present'] = $rs->where('dmg_id',$key)->sum('total_present');
        $dmg['leave'] = $leave_rs->where('dmg_id',$key)->sum('total');
        $dmg['attach'] = $attach_rs->where('dmg_id',$key)->sum('total');
        $dmg['absent'] = $absent_rs->where('dmg_id',$key)->sum('total_absent');
        $dmg['offdays'] = $od_rs->where('dmg_id',$key)->sum('total');
        $dmg['late'] = $late_rs->where('dmg_id',$key)->sum('total');


        $bb_total_employees_i = clone $posts;
        $reg_employees_i = clone $employees;
        $unreg_employees_i = clone $employees;

        $bb_total_employees_i = $bb_total_employees_i->where(str_replace('u.','',$dmg_select),$key);

        $reg_employees_i = $reg_employees_i->where(str_replace('u.','',$dmg_select),$key)->where('field_staff',0);
        $unreg_employees_i = $unreg_employees_i->where(str_replace('u.','',$dmg_select),$key)->where('field_staff',1);

        $dmg['bb_total_employees']= $bb_total_employees_i->sum('total_employees');
        $dmg['bb_vacant_posts']= $bb_total_employees_i->sum('vacant_posts');

        $dmg['reg_employees'] = $reg_employees_i->count();
        $dmg['unreg_employees']= $unreg_employees_i->count();
      ?>
      <tr>
        <td> <?php echo $counter;?></td>
        <td  style="text-align:left!important">
          <a target="_blank" href="<?php echo route('super-admin',['date'=>$date,'department_id'=>$choosen_dept,'allregions'=>$dmg_select_key.$key]) ?>">
            <?php echo  $dmg['title'] ?>
          </a>
        </td>
        <td class="bold">
            <a target="_blank" href="<?php echo  route('dw-employees',['date'=>$date,'department_id'=>$choosen_dept,'selected_dmgs'=>$dmg_select_key.$key]) ?>">
              <?php echo  number_format($dmg['bb_total_employees'],0) ?>
            </a>
        </td>
        <td class="bold">
            <a target="_blank" href="<?php echo  route('dw-registered',['department_id'=>$choosen_dept,'selected_dmgs'=>$dmg_select_key.$key,'list'=>'yes']) ?>" title="Drill Down" >
              <?php echo  number_format($dmg['reg_employees'],0) ?>
            </a>
        </td>
        <td class="bold">
            <a target="_blank" href="<?php echo  route('dw-unregistered',['department_id'=>$choosen_dept,'selected_dmgs'=>$dmg_select_key.$key,'list'=>'yes']) ?>" title="Drill Down" >
              <?php echo  number_format($dmg['unreg_employees'],0) ?>
            </a>
        </td>
        <td class="bold"><?php echo  number_format($dmg['bb_vacant_posts'],0) ?></td>
        <td class="bold"><?php echo  number_format($dmg['bb_total_employees'] - ($dmg['reg_employees'] + $dmg['bb_vacant_posts']+$dmg['unreg_employees']),0) ?></td>
        <td class="bold">
          <a target="_blank" class="t-present" href="<?php echo  route('dw-present',['date'=>$date,'department_id'=>$choosen_dept,'list'=>'yes','selected_dmgs'=>$dmg_select_key.$key]) ?>">
            <?php echo  number_format($dmg['present'],0) ?>
          </a>
        </td>
        <td class="bold">
          <a target="_blank" class="t-absent" href="<?php echo  route('dw-absent',['date'=>$date,'department_id'=>$choosen_dept,'list'=>'yes','selected_dmgs'=>$dmg_select_key.$key]) ?>">
            <?php echo  number_format($dmg['absent'],0) ?>
          </a>
        </td>
        <td class="bold">
          <a target="_blank"  class="t-late" href="<?php echo  route('dw-late',['date'=>$date,'department_id'=>$choosen_dept,'list'=>'yes','selected_dmgs'=>$dmg_select_key.$key]) ?>">
            <?php echo  number_format($dmg['late'],0) ?>
          </a>
        </td>
        <td class="bold">
          <a target="_blank"  class="t-leave" href="<?php echo  route('dw-leave',['date'=>$date,'department_id'=>$choosen_dept,'list'=>'yes','selected_dmgs'=>$dmg_select_key.$key]) ?>">
            <?php echo  number_format($dmg['leave'],0) ?>
          </a>
        </td>
        <td class="bold">
          <a target="_blank" href="<?php echo  route('dw-attach',['date'=>$date,'department_id'=>$choosen_dept,'list'=>'yes','selected_dmgs'=>$dmg_select_key.$key]) ?>">
            <?php echo  number_format($dmg['attach'],0) ?>
          </a>
        </td>
        <td class="bold">
          <a target="_blank" href="<?php echo  route('dw-offdays',['date'=>$date,'department_id'=>$choosen_dept,'list'=>'yes','selected_dmgs'=>$dmg_select_key.$key]) ?>">
            <?php echo  number_format($dmg['offdays'],0) ?>
          </a>
        </td>
      </tr>
      <?php
    }

    exit();

  }

  public function department_wise_employees(Request $request){

    if($request->has('list')){
      return redirect()->route('dd-all-employees')->with(['date' => $request->get('date'),'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
    }
    $page_title = 'AJ&K';
    $date = date("Y-m-d");
    if($request->has('date'))
    {
      $date = $request->get('date');
    }

    $departments = [];
    $external_departments=[];

    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";

    $region_filter = "";
    $data['reg_autocomplete']='';
    if($request->has('selected_dmgs') && $request->selected_dmgs !="all"){

      $data['selected_dmgs'] = $request->selected_dmgs;
      $region_input = explode('_',$request->get('selected_dmgs'));
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      if($dmg_check == 'reg'){
          $region_filter .= " and u.region_id =".$dmg_id;
          $region = Region::find($dmg_id);
          $page_title = $region->region_name . ' '. 'Division' ;
          $data['reg_autocomplete']=$page_title;
      }
      if($dmg_check == 'zone'){
          $region_filter .= " and u.zone_id =".$dmg_id;
          $zone = Zone::find($dmg_id);
          $page_title = $zone->zone_name .' '. 'District' ;
          $data['reg_autocomplete']=$page_title;
      }
      if($dmg_check == 'branch'){
          $region_filter .= " and u.branch_id =".$dmg_id;
          $branch = Branch::find($dmg_id);
          $page_title = $branch->branch_name .' '. 'Tehsil' ;
          $data['reg_autocomplete']=$page_title;
      }
      $data['dmg_check'] = $dmg_check;
      $data['dmg_id'] = $dmg_id;
    }
    else
    {
      $data['selected_dmgs']='all';
    }

    $sql = "SELECT
            d.id as department_id,
            d.name AS department,
            COUNT(u.id) AS total_present,
            (SELECT COUNT(1) FROM leave_requests_details d JOIN users u ON d.user_id = u.id WHERE d.id = u.department_id AND DATE(d.leave_date) = DATE('".$date."')) AS `leave`,
            (
            SELECT COUNT(1) FROM attach_employees_details d JOIN users u ON d.user_id = u.id
            WHERE d.id = u.department_id AND
            d.from_date >= DATE('".$date."') && d.to_date<= DATE('".$date."')
            ) AS `attach`
          FROM
            users u
            JOIN clients d ON d.id = u.department_id
            LEFT JOIN time_categories tc
            ON  tc.id = (SELECT tcat_id FROM time_category_histories
                    WHERE user_id = u.id AND DATE(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
            WHERE CAST(IFNULL(tc.".$tc_in_column.",'09:00:00') AS TIME) < TIME(NOW())
            AND u.id IN (SELECT user_id FROM attendance WHERE DATE(DATETIME) = date('".$date."'))
            ".$region_filter."
            AND u.role_id IN(6,7) AND u.status=1 and u.flag = 1
            GROUP BY d.id";

    $absent_sql = "SELECT
                    d.id as department_id,
                    d.name AS department,
                    COUNT(u.id) AS total_absent
                    from
                        users u
                        join clients as d on d.id = u.department_id
                        LEFT JOIN time_categories tc
                        ON  tc.id = (SELECT tcat_id FROM time_category_histories
                                        WHERE user_id = u.id and date(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
                        where CAST(tc.".$tc_in_column." AS time) < time(NOW())
                        and u.id not in (select user_id from attendance where date(datetime) = date('".$date."'))
                        and u.id not in (select user_id from leave_requests_details where leave_date = date('".$date."'))
                        and u.id not in (
                          SELECT user_id FROM `attach_employees_details`  WHERE  attach_date = DATE('".$date."')
                        )
                        and u.role_id IN(6,7) and u.status=1 and u.flag = 1
                        ".$region_filter."
                    GROUP BY d.id";
    $offdays_sql =  "SELECT
            d.id AS department_id,
            d.name AS department,
            COUNT(u.id) AS total
            FROM users u
            JOIN clients AS d ON d.id = u.department_id
            LEFT JOIN time_categories tc ON  tc.id  = (SELECT tcat_id FROM time_category_histories
                                WHERE user_id = u.id AND DATE(join_date) <= DATE('".$date."') ORDER BY join_date
                                    DESC LIMIT 1)
            WHERE tc.".$tc_in_column." IS NULL
            AND u.role_id IN(6,7)  AND u.status=1  AND u.flag = 1
            ".$region_filter."
            GROUP BY d.id";
    $late_sql = "SELECT d.id as department_id,d.name,SUM(l1.is_late) AS total FROM (
                SELECT
                u.id,
                u.department_id,
                IFNULL(IF(SUBSTR(GROUP_CONCAT(DISTINCT a.datetime ORDER BY a.datetime ASC),1,19)>CONCAT(DATE(NOW()),' ',ADDTIME(tc.".$tc_in_column.",tc.grace_time)),1,0),0) AS is_late
                  FROM
                  users u
                  LEFT JOIN time_categories tc ON  tc.id = (SELECT tcat_id FROM time_category_histories
                        WHERE user_id = u.id AND DATE(join_date) <= DATE('".$date."') ORDER BY join_date DESC LIMIT 1)
                  JOIN attendance a ON a.user_id = u.id
                  WHERE u.role_id IN(6,7) AND u.status=1  AND u.flag = 1 AND DATE(a.datetime) = DATE(NOW())
                  ".$region_filter."
                GROUP BY u.id
            ) AS l1 JOIN clients AS d ON d.id = l1.department_id
            GROUP BY d.id";

    $attach_sql = "SELECT u.department_id as department_id,
                      COUNT(distinct u.id) AS total
                    from `attach_employees_details` a
                      JOIN users u ON u.id = a.user_id
                    WHERE   a.attach_date  = DATE('".$date."') 
                        and u.status=1 AND u.flag=1 and u.role_id IN(6,7)
                        ".$region_filter."
                    GROUP BY u.department_id";

    $leave_sql= "select
                            u.department_id as department_id,
                            COUNT(u.id) AS total
                    from users as u
                          inner join leave_requests on u.id=leave_requests.user_id
                          inner join leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
                          inner join clients on clients.id=u.department_id
                    where
                    leave_requests_details.leave_date  = DATE('".$date."')
                    and u.status=1 AND u.flag=1 and u.role_id IN(6,7)
                    ".$region_filter."
                    GROUP by u.department_id";






    $parent_departments = [];
    $all_departments = [];

    $departments_clause = "";
    if($request->has('department_id') && $request->get('department_id')!='all'){
        $dep = Client::find($request->department_id);
        $page_title .= ' - '.$dep->name . ' ' . 'Dashboard';

        $data['department_id'] = $request->department_id;
        $choosen_dept = $request->get('department_id');
        $child_depts = $this->getChildDepartments(Client::all(),$choosen_dept);

        if(count($child_depts)==0 || (count($child_depts) == 1 && $child_depts[0]== $choosen_dept)){
            //means we will show the employees list bc its the nth department
            return redirect()->route('dd-all-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
        }
        $filtered_departments = implode(',',$child_depts);
        $departments_clause = " and u.department_id in (".$filtered_departments.") ";

        $parent_departments = Client::where('parent_id',$request->get('department_id'))->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
        $all_departments = Client::whereIn('parent_id',$child_depts)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
    }
    else
    {
      $parent_departments = Client::where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
      $all_departments = Client::orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
      $data['department_id'] = 'all';
      $page_title .= ' - Department Wise Dashboard';
    }

    $rs = collect(DB::select($sql));
    $absent_rs = collect(DB::select($absent_sql));
    $od_rs = collect(DB::select($offdays_sql));
    $attach_rs = collect(DB::select($attach_sql));
    $leave_rs = collect(DB::select($leave_sql));
    $late_rs = collect(DB::select($late_sql));


    foreach($parent_departments as $key=>$department){

        $child_dept_ids = $this->getChildDepartments($all_departments,$department->id);
        $total_present = $rs->whereIn('department_id',$child_dept_ids)->sum('total_present');
        $total_leave = $leave_rs->whereIn('department_id',$child_dept_ids)->sum('total');
        $total_attach = $attach_rs->whereIn('department_id',$child_dept_ids)->sum('total');
        $total_absent = $absent_rs->whereIn('department_id',$child_dept_ids)->sum('total_absent');
        $total_offdays = $od_rs->whereIn('department_id',$child_dept_ids)->sum('total');
        $total_late = $late_rs->whereIn('department_id',$child_dept_ids)->sum('total');

        $dept_reg_employees = User::where(['status'=>1,'flag'=>1,'field_staff'=>0])->whereIn('role_id',[6,7])->whereIn('department_id',$child_dept_ids);
        $dept_unreg_employees = User::where(['status'=>1,'flag'=>1,'field_staff'=>1])->whereIn('role_id',[6,7])->whereIn('department_id',$child_dept_ids);

        $bb_total_employees = Client::whereIn('id',$child_dept_ids)->sum('total_employees');
        $bb_vacant_posts = Client::whereIn('id',$child_dept_ids)->sum('vacant_posts');

        if($request->has('selected_dmgs') && $request->selected_dmgs !="all"){
          if($dmg_check == 'reg'){
            $dept_reg_employees->where('region_id',$dmg_id);
            $dept_unreg_employees->where('region_id',$dmg_id);

            $bb_total_employees = Client::whereIn('id',$child_dept_ids)->where('region_id',$dmg_id)->sum('total_employees');
            $bb_vacant_posts = Client::whereIn('id',$child_dept_ids)->where('region_id',$dmg_id)->sum('vacant_posts');
          }
          if($dmg_check == 'zone'){
            $dept_reg_employees->where('zone_id',$dmg_id);
            $dept_unreg_employees->where('zone_id',$dmg_id);

            $bb_total_employees = Client::whereIn('id',$child_dept_ids)->where('zone_id',$dmg_id)->sum('total_employees');
            $bb_vacant_posts = Client::whereIn('id',$child_dept_ids)->where('zone_id',$dmg_id)->sum('vacant_posts');
          }
          if($dmg_check == 'branch'){
            $dept_reg_employees->where('branch_id',$dmg_id);
            $dept_unreg_employees->where('branch_id',$dmg_id);

            $bb_total_employees = Client::whereIn('id',$child_dept_ids)->where('branch_id',$dmg_id)->sum('total_employees');
            $bb_vacant_posts = Client::whereIn('id',$child_dept_ids)->where('branch_id',$dmg_id)->sum('vacant_posts');
          }
        }

        $empty_dev_reg=$dept_reg_employees->select('emp_type', DB::raw('count(*) as total'))->groupBy('emp_type')->get();;
        $empty_dev_unreg = $dept_unreg_employees->select('emp_type', DB::raw('count(*) as total'))->groupBy('emp_type')->get();


        $reg_employees_sts = ['total'=>0,'childs'=>[]];
        $unreg_employees_sts = ['total'=>0,'childs'=>[]];

        foreach($empty_dev_reg as $emptype)
        {
          if(empty($emptype->emp_type) || is_null($emptype->emp_type))
            $reg_employees_sts['childs']['NotSet']=$emptype->total;
          else
            $reg_employees_sts['childs'][$emptype->emp_type]=$emptype->total;

          $reg_employees_sts['total'] += $emptype->total;
        }

        foreach($empty_dev_unreg as $emptype)
        {
          if(empty($emptype->emp_type) || is_null($emptype->emp_type))
            $unreg_employees_sts['childs']['NotSet']=$emptype->total;
          else
            $unreg_employees_sts['childs'][$emptype->emp_type]=$emptype->total;

          $unreg_employees_sts['total'] += $emptype->total;
        }


        $department['bb_total_employees'] = $bb_total_employees;
        $department['bb_vacant_posts'] = $bb_vacant_posts;

        $department['reg_employees'] = $reg_employees_sts;
        $department['unreg_employees'] = $unreg_employees_sts;

        $department['present'] = $total_present;
        $department['leave'] = $total_leave;
        $department['attach'] = $total_attach;
        $department['absent'] = $total_absent;
        $department['offdays'] = $total_offdays;
        $department['late'] = $total_late;
        $departments[] = $department;
    }

    $data['c_date'] = $date;
    $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
    $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
    $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
    {
        return Client::generatetree(0,false,true);
    });

    return view('Dashboards.dw_employees',compact('page_title','data','departments','external_departments'));
  }

  public function dw_registered(Request $request){
    if($request->has('list'))
    {
      return redirect()->route('dd-registered-employees',['department_id'=>$request->department_id,'list'=>'yes','emp_type'=>$request->emp_type,'selected_dmgs'=>$request->selected_dmgs]);
    }

    $page_title = 'AJ&K';
    $departments = [];
    $external_departments=[];

    $data['reg_autocomplete']='';
    $region_filter = "";
    $data['selected_dmgs']='all';
    if($request->has('selected_dmgs') && $request->selected_dmgs !="all" && $request->selected_dmgs!=''){
      $region_input = explode('_',$request->get('selected_dmgs'));
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      $data['selected_dmgs']=$request->get('selected_dmgs');

      if($dmg_check == 'reg'){
        $region_filter = 'region_id';

        $region = Region::find($dmg_id);
        $page_title = $region->region_name . ' '. 'Division';
        $data['reg_autocomplete']=$region->region_name . ' '. 'Division';
      }elseif($dmg_check == 'zone'){
        $region_filter = 'zone_id';

        $zone = Zone::find($dmg_id);
        $page_title = $zone->zone_name .' '. 'District' ;
        $data['reg_autocomplete']=$region->zone_name . ' '. 'District';

      }elseif($dmg_check == 'branch'){
        $region_filter = 'branch_id';

        $branch = Branch::find($dmg_id);
        $page_title = $branch->branch_name .' '. 'Tehsil';
        $data['reg_autocomplete']=$region->branch_name . ' '. 'Tehsil';
      }
    }

    $parent_departments = [];
    $all_departments = [];

    $departments_clause = "";
    if($request->has('department_id') && $request->get('department_id')!='all'){
        $dep = Client::find($request->department_id);
        $page_title .= ' - '.$dep->name;
        $data['department_id'] = $request->department_id;

        $choosen_dept = $request->get('department_id');
        $child_depts = $this->getChildDepartments(Client::all(),$choosen_dept);
        //dd($child_depts);
        if(count($child_depts)==0 || (count($child_depts) == 1 && $child_depts[0]== $choosen_dept)){
            //means we will show the employees list bc its the nth department
            return redirect()->route('dd-registered-employees',['department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
        }
        $filtered_departments = implode(',',$child_depts);
        $departments_clause = " and u.department_id in (".$filtered_departments.") ";

        $parent_departments = Client::where('parent_id',$request->get('department_id'))->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
        $all_departments = Client::whereIn('parent_id',$child_depts)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
    }
    else
    {
      $data['department_id'] = 'all';
      $page_title .= ' - Department Wise';

      $parent_departments = Client::where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
      $all_departments = Client::orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
    }

    $page_title = $page_title. ' Biometric Registered Dashboard';


    foreach($parent_departments as $key=>$department){

        $child_dept_ids = $this->getChildDepartments($all_departments,$department->id);

        $dept_employees = User::where(['status'=>1,'flag'=>1,'field_staff'=>0])->whereIn('role_id',[6,7])->whereIn('department_id',$child_dept_ids);

        if($region_filter !=""){
          $dept_employees = $dept_employees->where($region_filter,$dmg_id);
        }
        //$department['registered']
        $empty_dev_reg = $dept_employees->select('emp_type', DB::raw('count(*) as total'))->groupBy('emp_type')->get();


        $dstats = ['total'=>0,'childs'=>['Regular'=>0,'Adhoc'=>0,'Contract'=>0,'Contingency'=>0,'Temporary'=>0,'Attach'=>0,'NotSet'=>0]];
        //dd($empty_dev_reg);
        foreach($empty_dev_reg as $emptype)
        {
          if(empty($emptype->emp_type) || is_null($emptype->emp_type))
            $dstats['childs']['NotSet']=$emptype->total;
          else
            $dstats['childs'][$emptype->emp_type]=$emptype->total;

          $dstats['total'] += $emptype->total;
        }
        $department['registered'] = $dstats;
        $departments[] = $department;
    }

    $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
    $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
    $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
    {
        return Client::generatetree(0,false,true);
    });

    return view('Dashboards.dw_registered',compact('page_title','data','departments','external_departments'));
  }
  
  public function dw_unregistered(Request $request){
    if($request->has('list'))
    {
      return redirect()->route('dd-unregistered-employees')->with(['department_id'=>$request->department_id,'list'=>'yes','emp_type'=>$request->emp_type,'selected_dmgs'=>$request->selected_dmgs]);
    }

    $page_title = 'AJ&K';
    $departments = [];
    $external_departments=[];

    $data['reg_autocomplete']='';
    $region_filter = "";
    $data['selected_dmgs']='all';
    if($request->has('selected_dmgs') && $request->selected_dmgs !="all" && $request->selected_dmgs!=''){
      $region_input = explode('_',$request->get('selected_dmgs'));
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      $data['selected_dmgs']=$request->get('selected_dmgs');

      if($dmg_check == 'reg'){
        $region_filter = 'region_id';

        $region = Region::find($dmg_id);
        $page_title = $region->region_name . ' '. 'Division';
        $data['reg_autocomplete']=$region->region_name . ' '. 'Division';
      }elseif($dmg_check == 'zone'){
        $region_filter = 'zone_id';

        $zone = Zone::find($dmg_id);
        $page_title = $zone->zone_name .' '. 'District' ;
        $data['reg_autocomplete']=$region->zone_name . ' '. 'District';

      }elseif($dmg_check == 'branch'){
        $region_filter = 'branch_id';

        $branch = Branch::find($dmg_id);
        $page_title = $branch->branch_name .' '. 'Tehsil';
        $data['reg_autocomplete']=$region->branch_name . ' '. 'Tehsil';
      }
    }

    $parent_departments = [];
    $all_departments = [];

    $departments_clause = "";
    if($request->has('department_id') && $request->get('department_id')!='all'){
        $dep = Client::find($request->department_id);
        $page_title .= ' - '.$dep->name;
        $data['department_id'] = $request->department_id;

        $choosen_dept = $request->get('department_id');
        $child_depts = $this->getChildDepartments(Client::all(),$choosen_dept);
        //dd($child_depts);
        if(count($child_depts)==0 || (count($child_depts) == 1 && $child_depts[0]== $choosen_dept)){
            //means we will show the employees list bc its the nth department
            return redirect()->route('dd-unregistered-employees')->with(['department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
        }
        $filtered_departments = implode(',',$child_depts);
        $departments_clause = " and u.department_id in (".$filtered_departments.") ";

        $parent_departments = Client::where('parent_id',$request->get('department_id'))->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
        $all_departments = Client::whereIn('parent_id',$child_depts)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
    }
    else
    {
      $data['department_id'] = 'all';
      $page_title .= ' - Department Wise';

      $parent_departments = Client::where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
      $all_departments = Client::orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
    }

    $page_title = $page_title. ' Field Staff Dashboard';


    foreach($parent_departments as $key=>$department){

        $child_dept_ids = $this->getChildDepartments($all_departments,$department->id);

        $dept_employees = User::where(['status'=>1,'flag'=>1,'field_staff'=>1])->whereIn('role_id',[6,7])->whereIn('department_id',$child_dept_ids);

        if($region_filter !=""){
          $dept_employees = $dept_employees->where($region_filter,$dmg_id);
        }
        //$department['registered']
        $empty_dev_reg = $dept_employees->select('emp_type', DB::raw('count(*) as total'))->groupBy('emp_type')->get();


        $dstats = ['total'=>0,'childs'=>['Regular'=>0,'Adhoc'=>0,'Contract'=>0,'Contingency'=>0,'Temporary'=>0,'Attach'=>0,'NotSet'=>0]];
        //dd($empty_dev_reg);
        foreach($empty_dev_reg as $emptype)
        {
          if(empty($emptype->emp_type) || is_null($emptype->emp_type))
            $dstats['childs']['NotSet']=$emptype->total;
          else
            $dstats['childs'][$emptype->emp_type]=$emptype->total;

          $dstats['total'] += $emptype->total;
        }
        $department['registered'] = $dstats;
        $departments[] = $department;

    }

    $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
    $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
    $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
    {
        return Client::generatetree(0,false,true);
    });

    return view('Dashboards.dw_unregistered',compact('page_title','data','departments','external_departments'));
  }

  public function department_wise_present(Request $request){
    if($request->has('list')){
      return redirect()->route('dd-present-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
    }
    $page_title = 'AJ&K';
    $date = date("Y-m-d");
    if($request->has('date'))
    {
      $date = $request->get('date');
    }

    $departments = [];
    $data=[];
    $data['reg_autocomplete']='';
    $data['selected_dmgs']='all';
    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";
    $region_filter = "";
    if($request->has('selected_dmgs') && $request->selected_dmgs !="all"){
      $region_input = explode('_',$request->get('selected_dmgs'));
      $data['selected_dmgs']=$request->get('selected_dmgs');
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      if($dmg_check == 'reg'){
          $region_filter .= " and u.region_id =".$dmg_id;
          $region = Region::find($dmg_id);
          $page_title = $region->region_name . ' '. 'Division' ;
          $data['reg_autocomplete']=$page_title;
      }
      if($dmg_check == 'zone'){
          $region_filter .= " and u.zone_id =".$dmg_id;

          $zone = Zone::find($dmg_id);
          $page_title = $zone->zone_name .' '. 'District' ;
          $data['reg_autocomplete']=$page_title;
      }
      if($dmg_check == 'branch'){
          $region_filter .= " and u.branch_id =".$dmg_id;

          $branch = Branch::find($dmg_id);
          $page_title = $branch->branch_name .' '. 'Tehsil' ;
          $data['reg_autocomplete']=$page_title;
      }
      $data['dmg_check'] = $dmg_check;
      $data['dmg_id'] = $dmg_id;
    }

    $sql = "SELECT
            d.id as department_id,
            d.name AS department,
            d.parent_id,
            IFNULL(SUM(CASE WHEN u.gender = 'male' THEN 1 ELSE 0 END),0) AS male,
            IFNULL(SUM(CASE WHEN u.gender = 'female' THEN 1 ELSE 0 END),0) AS female,
            IFNULL(SUM(CASE WHEN u.gender = 'male' AND u.is_gazetted=1 THEN 1 ELSE 0 END),0) AS g_male,
            IFNULL(SUM(CASE WHEN u.gender = 'female' AND u.is_gazetted=1 THEN 1 ELSE 0 END),0) AS g_female,
            IFNULL(SUM(CASE WHEN u.gender = 'male' AND u.is_gazetted=0 THEN 1 ELSE 0 END),0) AS ng_male,
            IFNULL(SUM(CASE WHEN u.gender = 'female' AND u.is_gazetted=0 THEN 1 ELSE 0 END),0) AS ng_female,
            TIME(NOW()),
            tc.time_in,
            tc.id FROM
            users u
            JOIN clients d ON d.id = u.department_id
            LEFT JOIN time_categories tc
            ON  tc.id = (SELECT tcat_id FROM time_category_histories
                    WHERE user_id = u.id AND DATE(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
            WHERE CAST(IFNULL(tc.".$tc_in_column.",'09:00:00') AS TIME) < TIME(NOW())
            AND u.id IN (SELECT user_id FROM attendance WHERE DATE(DATETIME) = date('".$date."'))
            ".$region_filter."
            AND u.role_id IN(6,7) AND u.status=1 and u.flag = 1
            GROUP BY d.id";

    $parent_departments = [];
    $all_departments = [];

    $departments_clause = "";
    if($request->has('department_id') && $request->get('department_id')!='all'){

        $dep = Client::find($request->department_id);
        $page_title .= ' - '.$dep->name;

        $depts = $this->getChildDepartments(Client::all(),$request->department_id);
        $child_depts = array_diff($depts,[$request->department_id]);
        if(count($child_depts)==0){
            //means we will show the employees list bc its the nth department
            return redirect()->route('dd-present-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
        }
        $filtered_departments = implode(',',$child_depts);
        $departments_clause = " and u.department_id in (".$filtered_departments.") ";

        $parent_departments = Client::whereIn('id',$child_depts)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
        $all_departments = Client::whereIn('parent_id',$child_depts)->get();
    }
    else{
      $parent_departments = Client::where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
      $all_departments = Client::all();

      $data['department_id'] = 'all';
      $page_title .= ' - Department Wise';
    }
    $page_title = $page_title. ' Present Dashboard';

    $rs = collect(DB::select($sql));




    foreach($parent_departments as $key=>$department){
        $child_dept_ids = $this->getChildDepartments(Client::all(),$department->id);
        $total_male = $rs->whereIn('department_id',$child_dept_ids)->sum('male');
        $total_female = $rs->whereIn('department_id',$child_dept_ids)->sum('female');
        $total = 0;
        $total = $total_male+$total_female;
        $department['present'] = $total;
        $departments[] = $department;
    }

    $data['c_date'] = $date;
    $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
    $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
    $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
    {
        return Client::generatetree(0,false,true);
    });

    return view('Dashboards.dw_present',compact('page_title','data','departments'));
  }

  public function department_wise_absent(Request $request){
    if($request->has('list')){
      //means we will show the employees list bc its the nth department
      return redirect()->route('dd-absent-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
    }
    $departments = [];
    $external_departments=[];
    $data = [];
    $page_title = 'AJ&K';
    $date = date("Y-m-d");
    if($request->has('date'))
    {
      $date = $request->get('date');
    }

    $data['reg_autocomplete']='';
    $data['selected_dmgs']='all';

    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";
    $region_filter='';
    if($request->has('selected_dmgs') && $request->selected_dmgs !="all"){
      $region_input = explode('_',$request->get('selected_dmgs'));
      $data['selected_dmgs']=$request->get('selected_dmgs');
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      if($dmg_check == 'reg'){
          $region_filter = " and u.region_id =".$dmg_id;
          $region = Region::find($dmg_id);
          $page_title = $region->region_name . ' '. 'Division' ;
          $data['reg_autocomplete']=$page_title;
      }elseif($dmg_check == 'zone'){
          $region_filter = " and u.zone_id =".$dmg_id;

          $zone = Zone::find($dmg_id);
          $page_title = $zone->zone_name .' '. 'District' ;
          $data['reg_autocomplete']=$page_title;
      }elseif($dmg_check == 'branch'){
          $region_filter = " and u.branch_id =".$dmg_id;

          $branch = Branch::find($dmg_id);
          $page_title = $branch->branch_name .' '. 'Tehsil' ;
          $data['reg_autocomplete']=$page_title;
      }
      $data['dmg_check'] = $dmg_check;
      $data['dmg_id'] = $dmg_id;
    }
        $sql = "SELECT
                d.id as department_id,
                d.name AS department,
                d.parent_id,
                IFNULL(SUM(case when u.gender = 'male' then 1 else 0 end),0) as male,
                IFNULL(SUM(case when u.gender = 'female' then 1 else 0 end),0) as female,
                IFNULL(SUM(case when u.gender = 'male' and u.is_gazetted=1 then 1 else 0 end),0) as g_male,
                IFNULL(SUM(case when u.gender = 'female' and u.is_gazetted=1 then 1 else 0 end),0) as g_female,
                IFNULL(SUM(case when u.gender = 'male' and u.is_gazetted=0 then 1 else 0 end),0) as ng_male,
                IFNULL(SUM(case when u.gender = 'female' and u.is_gazetted=0 then 1 else 0 end),0) as ng_female
                from
                    users u
                    join clients as d on d.id = u.department_id
                    LEFT JOIN time_categories tc
                    ON  tc.id = (SELECT tcat_id FROM time_category_histories
                                    WHERE user_id = u.id and date(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
                    where CAST(tc.".$tc_in_column." AS time) < time(NOW())
                    and u.id not in (select user_id from attendance where date(datetime) = date('".$date."'))
                    and u.role_id IN(6,7) and u.status=1 and u.flag = 1
                    and u.id not in (select user_id from leave_requests_details where leave_date = date('".$date."'))
                    and u.id not in (
                      SELECT user_id FROM `attach_employees_details`  WHERE  attach_date = DATE('".$date."')
                    )

                    ".$region_filter."
                GROUP BY d.id";

        $parent_departments = [];
        $all_departments = [];

        $departments_clause = "";
        if($request->has('department_id') && $request->get('department_id')!='all'){
            $depts = $this->getChildDepartments(Client::all(),$request->department_id);
            $child_depts = array_diff($depts,[$request->department_id]);


              $dep = Client::find($request->department_id);
              $page_title .= ' - '.$dep->name;
              $data['department_id'] = $request->department_id;

            if(count($child_depts)==0){
                //means we will show the employees list bc its the nth department
                return redirect()->route('dd-absent-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
            }
            $filtered_departments = implode(',',$depts);
            $departments_clause = " and u.department_id in (".$filtered_departments.") ";

            $parent_departments = Client::where('parent_id',$request->get('department_id'))->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
            $all_departments = Client::whereIn('parent_id',$child_depts)->get();
        }
        else
        {
          $parent_departments = Client::where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
          $all_departments = Client::all();



          $data['department_id'] = 'all';
          $page_title .= ' - Department Wise';
        }
        $rs = collect(DB::select($sql));

        $page_title = $page_title. ' Absent Dashboard';

        $departments = [];
        foreach($parent_departments as $key=>$department){
            $child_dept_ids = $this->getChildDepartments($all_departments,$department->id);
            $total_male = $rs->whereIn('department_id',$child_dept_ids)->sum('male');
            $total_female = $rs->whereIn('department_id',$child_dept_ids)->sum('female');
            $total = 0;
            $total = $total_male+$total_female;
            $department['present'] = $total;
            $departments[] = $department;
        }

        $data['c_date'] = $date;
        $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
        $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
        $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
        {
            return Client::generatetree(0,false,true);
        });

        return view('Dashboards.dw_absent',compact('page_title','data','departments','external_departments'));
  }

  public function department_wise_late(Request $request){
    if($request->has('list')){
      //means we will show the employees list bc its the nth department
      return redirect()->route('dd-late-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
    }
    $page_title = 'AJ&K';
    $date = date("Y-m-d");
    if($request->has('date'))
    {
      $date = $request->get('date');
    }

    $departments = [];
    $external_departments=[];
    $data = [];

    $data['reg_autocomplete']='';
  	$data['selected_dmgs']='all';


    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";

    $region_filter = "";
    if($request->has('selected_dmgs') && $request->selected_dmgs !="all"){
      $region_input = explode('_',$request->get('selected_dmgs'));
      $data['selected_dmgs']=$request->get('selected_dmgs');
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      if($dmg_check == 'reg'){
          $region_filter .= " and u.region_id =".$dmg_id;
          $region = Region::find($dmg_id);
          $page_title = $region->region_name . ' '. 'Division' ;
          $data['reg_autocomplete']=$page_title;
      }
      if($dmg_check == 'zone'){
          $region_filter .= " and u.zone_id =".$dmg_id;

          $zone = Zone::find($dmg_id);
          $page_title = $zone->zone_name .' '. 'District' ;
          $data['reg_autocomplete']=$page_title;
      }
      if($dmg_check == 'branch'){
          $region_filter .= " and u.branch_id =".$dmg_id;

          $branch = Branch::find($dmg_id);
          $page_title = $branch->branch_name .' '. 'Tehsil' ;
          $data['reg_autocomplete']=$page_title;
      }
      $data['dmg_check'] = $dmg_check;
      $data['dmg_id'] = $dmg_id;
    }
    $sql = "SELECT d.id as department_id,d.name,SUM(l1.is_late) AS total_late FROM (
                SELECT
                u.id,
                u.department_id,
                IFNULL(IF(SUBSTR(GROUP_CONCAT(DISTINCT a.datetime ORDER BY a.datetime ASC),1,19)>CONCAT(DATE(NOW()),' ',ADDTIME(tc.".$tc_in_column.",tc.grace_time)),1,0),0) AS is_late
                  FROM
                  users u
                  LEFT JOIN time_categories tc ON  tc.id = (SELECT tcat_id FROM time_category_histories
                        WHERE user_id = u.id AND DATE(join_date) <= DATE('".$date."') ORDER BY join_date DESC LIMIT 1)
                  JOIN attendance a ON a.user_id = u.id
                  WHERE u.role_id IN(6,7) AND u.status=1  AND u.flag = 1 AND DATE(a.datetime) = DATE(NOW())
                  ".$region_filter."
                GROUP BY u.id
            ) AS l1 JOIN clients AS d ON d.id = l1.department_id
            GROUP BY d.id";

    $parent_departments = [];
    $all_departments = [];

    $departments_clause = "";
    if($request->has('department_id') && $request->get('department_id')!='all'){
      $data['department_id'] = $request->get('department_id');
        $dep = Client::find($request->department_id);
        $page_title .= ' - '.$dep->name;

        
        $child_depts = $this->getChildDepartments(Client::all(),$request->get('department_id'));
        if(count($child_depts)==0 ){
            //means we will show the employees list bc its the nth department
            return redirect()->route('dd-late-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
        }
        $filtered_departments = implode(',',$child_depts);
        $departments_clause = " and u.department_id in (".$filtered_departments.") ";

        $parent_departments = Client::where('parent_id',$request->get('department_id'))->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
        $all_departments = Client::whereIn('parent_id',$child_depts)->get();
    }
    else
    {
      $parent_departments = Client::where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
      $all_departments = Client::all();


      $data['department_id'] = 'all';
      $page_title .= ' - Department Wise';

    }
    $page_title = $page_title. ' Late Dashboard';
    $rs = collect(DB::select($sql));




    foreach($parent_departments as $key=>$department){
        $child_dept_ids = $this->getChildDepartments($all_departments,$department->id);
        $dept_employees = User::where(['status'=>1,'flag'=>1])->whereIn('department_id',$child_dept_ids)->count();
        $total = $rs->whereIn('department_id',$child_dept_ids)->sum('total_late');
        $department['late'] = $total;
        $departments[] = $department;
    }

    $data['c_date'] = $date;
    $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
    $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
    $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
    {
        return Client::generatetree(0,false,true);
    });

    return view('Dashboards.dw_late',compact('page_title','data','departments','external_departments'));
  }

  public function department_wise_leave(Request $request){
    if($request->has('list')){
      //means we will show the employees list bc its the nth department
      return redirect()->route('dd-leave-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
    }
    $departments = [];
    $external_departments=[];
    $data = [];

    $page_title = 'AJ&K';
    $date = date("Y-m-d");
    if($request->has('date'))
    {
      $date = $request->get('date');
    }

    $data['reg_autocomplete']='';
    $data['selected_dmgs']='all';

    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";
    $region_filter='';
    if($request->has('selected_dmgs') && $request->selected_dmgs !="all"){
      $region_input = explode('_',$request->get('selected_dmgs'));
      $data['selected_dmgs']=$request->get('selected_dmgs');
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      if($dmg_check == 'reg'){
          $region_filter = " and u.region_id =".$dmg_id;
          $region = Region::find($dmg_id);
          $page_title = $region->region_name . ' '. 'Division' ;
          $data['reg_autocomplete']=$page_title;
      }elseif($dmg_check == 'zone'){
          $region_filter = " and u.zone_id =".$dmg_id;

          $zone = Zone::find($dmg_id);
          $page_title = $zone->zone_name .' '. 'District' ;
          $data['reg_autocomplete']=$page_title;
      }elseif($dmg_check == 'branch'){
          $region_filter = " and u.branch_id =".$dmg_id;

          $branch = Branch::find($dmg_id);
          $page_title = $branch->branch_name .' '. 'Tehsil' ;
          $data['reg_autocomplete']=$page_title;
      }
      $data['dmg_check'] = $dmg_check;
      $data['dmg_id'] = $dmg_id;
    }

        $sql = "SELECT
                d.id as department_id,
                d.name AS department,
                COUNT(u.id) AS total
                    FROM `leave_requests_details` lrd
                JOIN users u ON lrd.user_id = u.id
                JOIN clients d ON d.id = u.department_id
                WHERE lrd.leave_date = DATE('".$date."')  AND u.flag = 1
                ".$region_filter."
                group by d.id";

        $parent_departments = [];
        $all_departments = [];

        $departments_clause = "";
        if($request->has('department_id') && $request->get('department_id')!='all'){
          
            $child_depts = $this->getChildDepartments(Client::all(),$request->get('department_id'));

            $dep = Client::find($request->department_id);
            $page_title .= ' - '.$dep->name;
            $data['department_id'] = $request->department_id;

            if(count($child_depts)==0){
                //means we will show the employees list bc its the nth department
                return redirect()->route('dd-leave-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
            }
            $filtered_departments = implode(',',$child_depts);
            $departments_clause = " and u.department_id in (".$filtered_departments.") ";

            $parent_departments = Client::where('parent_id',$request->get('department_id'))->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
            $all_departments = Client::whereIn('parent_id',$child_depts)->get();
        }
        else
        {
          $parent_departments = Client::where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
          $all_departments = Client::all();




          $data['department_id'] = 'all';
          $page_title .= ' - Department Wise';
        }
        $rs = collect(DB::select($sql));


        $page_title = $page_title. ' Leave Dashboard';


        $departments = [];
        foreach($parent_departments as $key=>$department){
            $child_dept_ids = $this->getChildDepartments($all_departments,$department->id);
            $total = $rs->whereIn('department_id',$child_dept_ids)->sum('total');
            $department['total'] = $total;
            $departments[] = $department;
        }

        $data['c_date'] = $date;
        $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
        $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
        $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
        {
            return Client::generatetree(0,false,true);
        });

        return view('Dashboards.dw_leave',compact('page_title','data','departments','external_departments'));
  }

  public function department_wise_os_employees(Request $request){
    if($request->has('list')){
      //means we will show the employees list bc its the nth department
      return redirect()->route('dd-os-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes']);
    }
    $departments = [];
    $external_departments=[];
    $data=[];
    $page_title = 'AJ&K';
    $date = date("Y-m-d");
    if($request->has('date'))
    {
      $date = $request->get('date');
    }
    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";
    $data['reg_autocomplete']='';
  	$data['selected_dmgs']='all';
    $region_filter='';
    if($request->has('selected_dmgs') && $request->selected_dmgs !="all" && $request->selected_dmgs!=''){
      $region_input = explode('_',$request->get('selected_dmgs'));
      $data['selected_dmgs']=$request->get('selected_dmgs');
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      if($dmg_check == 'reg'){
          $region_filter = " and u.region_id =".$dmg_id;
          $region = Region::find($dmg_id);
          $page_title = $region->region_name . ' '. 'Division' ;
          $data['reg_autocomplete']=$page_title;
      }elseif($dmg_check == 'zone'){
          $region_filter = " and u.zone_id =".$dmg_id;

          $zone = Zone::find($dmg_id);
          $page_title = $zone->zone_name .' '. 'District' ;
          $data['reg_autocomplete']=$page_title;
      }elseif($dmg_check == 'branch'){
          $region_filter = " and u.branch_id =".$dmg_id;

          $branch = Branch::find($dmg_id);
          $page_title = $branch->branch_name .' '. 'Tehsil' ;
          $data['reg_autocomplete']=$page_title;
      }
      $data['dmg_check'] = $dmg_check;
      $data['dmg_id'] = $dmg_id;
    }



    $sql = "SELECT
            d.id as department_id,
            d.name AS department,
            COUNT(u.id) AS total
            from users u
            JOIN clients d ON d.id = u.department_id
            WHERE u.flag = 2 and u.status = 1
            group by d.id";


    $parent_departments = [];
    $all_departments = [];

    $departments_clause = "";
    if($request->has('department_id') && $request->get('department_id')!='all'){
        $child_depts = $this->getChildDepartments(Client::all(),$request->get('department_id'));
        

        $dep = Client::find($request->department_id);
        $page_title .= ' - '.$dep->name;
        $data['department_id'] = $request->department_id;

        if(count($child_depts)==0){
            //means we will show the employees list bc its the nth department
            return redirect()->route('dd-os-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes']);
        }
        $filtered_departments = implode(',',$child_depts);
        $departments_clause = " and u.department_id in (".$filtered_departments.") ";

        $parent_departments = Client::where('parent_id',$request->get('department_id'))->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
        $all_departments = Client::whereIn('parent_id',$child_depts)->get();
    }else{
      $parent_departments = Client::where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
      $all_departments = Client::all();


      $data['department_id'] = 'all';
      $page_title .= ' - Department Wise';
    }
    $rs = collect(DB::select($sql));
    $page_title = $page_title. ' On Schedule Dashboard';

    $departments = [];
    foreach($parent_departments as $key=>$department){
        $child_dept_ids = $this->getChildDepartments($all_departments,$department->id);
        $total = $rs->whereIn('department_id',$child_dept_ids)->sum('total');
        $department['total'] = $total;
        $departments[] = $department;
    }

    $data['c_date'] = $date;
    $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
    $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
    $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
    {
        return Client::generatetree(0,false,true);
    });

    return view('Dashboards.dw_on_schedule_employees',compact('page_title','data','departments','external_departments'));
  }

  public function department_wise_sna(Request $request){
    if($request->has('list')){
      //means we will show the employees list bc its the nth department
      return redirect()->route('dd-sna-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes']);
    }
    $departments = [];
    $external_departments=[];
    $data=[];
    $page_title = 'AJ&K';
    $date = date("Y-m-d");
    if($request->has('date'))
    {
      $date = $request->get('date');
    }

    $data['reg_autocomplete']='';
  	$data['selected_dmgs']='all';
    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";
    $region_filter='';
    if($request->has('selected_dmgs') && $request->selected_dmgs !="all" && $request->selected_dmgs!=''){
      $region_input = explode('_',$request->get('selected_dmgs'));
      $data['selected_dmgs']=$request->get('selected_dmgs');
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      if($dmg_check == 'reg'){
          $region_filter = " and u.region_id =".$dmg_id;
          $region = Region::find($dmg_id);
          $page_title = $region->region_name . ' '. 'Division' ;
          $data['reg_autocomplete']=$page_title;
      }elseif($dmg_check == 'zone'){
          $region_filter = " and u.zone_id =".$dmg_id;

          $zone = Zone::find($dmg_id);
          $page_title = $zone->zone_name .' '. 'District' ;
          $data['reg_autocomplete']=$page_title;
      }elseif($dmg_check == 'branch'){
          $region_filter = " and u.branch_id =".$dmg_id;

          $branch = Branch::find($dmg_id);
          $page_title = $branch->branch_name .' '. 'Tehsil' ;
          $data['reg_autocomplete']=$page_title;
      }
      $data['dmg_check'] = $dmg_check;
      $data['dmg_id'] = $dmg_id;
    }

    $sql = "select
                c.id as department_id,
                c.name as department,
                COUNT(IF(CAST(".$tc_in_column." AS TIME) > TIME(NOW()),1,NULL)) as total
            from
            users u
            join clients c on c.id = u.department_id
            LEFT JOIN time_categories tc
            ON  tc.id = (SELECT tcat_id FROM time_category_histories
                    WHERE user_id = u.id and date(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
            where u.id not in (select user_id from attendance where date(datetime) = date('".$date."'))
            and u.role_id IN(6,7) and u.status=1 and u.flag = 1
            group by c.id";
    $parent_departments = [];
    $all_departments = [];

    $departments_clause = "";
    if($request->has('department_id') && $request->get('department_id')!='all'){
        $child_depts = $this->getChildDepartments(Client::all(),$request->get('department_id'));
       

        $dep = Client::find($request->department_id);
        $page_title .= ' - '.$dep->name;
        $data['department_id'] = $request->department_id;
        if(count($child_depts)==0 ){
            //means we will show the employees list bc its the nth department
            return redirect()->route('dd-sna-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes']);
        }
        $filtered_departments = implode(',',$child_depts);
        $departments_clause = " and u.department_id in (".$filtered_departments.") ";

        $parent_departments = Client::where('parent_id',$request->get('department_id'))->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
        $all_departments = Client::whereIn('parent_id',$child_depts)->get();
    }else{
      $parent_departments = Client::where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
      $all_departments = Client::all();

      $data['department_id'] = 'all';
      $page_title .= ' - Department Wise';
    }
    $rs = collect(DB::select($sql));

    $departments = [];
    foreach($parent_departments as $key=>$department){
        $child_dept_ids = $this->getChildDepartments($all_departments,$department->id);
        $total = $rs->whereIn('department_id',$child_dept_ids)->sum('total');
        $department['total'] = $total;
        $departments[] = $department;
    }

    $page_title = $page_title. ' Shift Not Started Dashboard';

    $data['c_date'] = $date;
    $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
    $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
    $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
    {
        return Client::generatetree(0,false,true);
    });

    return view('Dashboards.dw_sna_employees',compact('page_title','data','departments','external_departments'));
  }

  public function department_wise_offdays(Request $request){
    if($request->has('list')){
      //means we will show the employees list bc its the nth department
      return redirect()->route('dd-offdays-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
   }
    $departments = [];
    $external_departments=[];
    $data=[];

   $page_title = 'AJ&K';
   $date = date("Y-m-d");
   if($request->has('date'))
   {
     $date = $request->get('date');
   }

   $data['reg_autocomplete']='';
   $data['selected_dmgs']='all';
   $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";
   $region_filter='';
    if($request->has('selected_dmgs') && $request->selected_dmgs !="all" && $request->selected_dmgs!=''){
      $region_input = explode('_',$request->get('selected_dmgs'));
      $data['selected_dmgs']=$request->get('selected_dmgs');
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      if($dmg_check == 'reg'){
          $region_filter = " and u.region_id =".$dmg_id;
          $region = Region::find($dmg_id);
          $page_title = $region->region_name . ' '. 'Division' ;
          $data['reg_autocomplete']=$page_title;
      }elseif($dmg_check == 'zone'){
          $region_filter = " and u.zone_id =".$dmg_id;

          $zone = Zone::find($dmg_id);
          $page_title = $zone->zone_name .' '. 'District' ;
          $data['reg_autocomplete']=$page_title;
      }elseif($dmg_check == 'branch'){
          $region_filter = " and u.branch_id =".$dmg_id;

          $branch = Branch::find($dmg_id);
          $page_title = $branch->branch_name .' '. 'Tehsil' ;
          $data['reg_autocomplete']=$page_title;
      }
      $data['dmg_check'] = $dmg_check;
      $data['dmg_id'] = $dmg_id;
    }

    $sql = "SELECT
            d.id AS department_id,
            d.name AS department,
            COUNT(u.id) AS total
            FROM users u
            JOIN clients AS d ON d.id = u.department_id
            LEFT JOIN time_categories tc ON  tc.id  = (SELECT tcat_id FROM time_category_histories
                                WHERE user_id = u.id AND DATE(join_date) <= DATE('".$date."') ORDER BY join_date
                                    DESC LIMIT 1)
            WHERE tc.".$tc_in_column." IS NULL
            AND u.role_id IN(6,7)  AND u.status=1  AND u.flag = 1
            ".$region_filter."
            GROUP BY d.id";

    $parent_departments = [];
    $all_departments = [];

    $departments_clause = "";
    if($request->has('department_id') && $request->get('department_id')!='all'){
      
        $child_depts = $this->getChildDepartments(Client::all(),$request->get('department_id'));
        

        $dep = Client::find($request->department_id);
        $page_title .= ' - '.$dep->name;
        $data['department_id'] = $request->department_id;
        if(count($child_depts)==0){
            //means we will show the employees list bc its the nth department
            return redirect()->route('dd-offdays-employees')->with(['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
        }
        $filtered_departments = implode(',',$child_depts);
        $departments_clause = " and u.department_id in (".$filtered_departments.") ";
        
        $parent_departments = Client::where('parent_id',$request->get('department_id'))->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
        $all_departments = Client::whereIn('parent_id',$child_depts)->get();
    }else{
      $parent_departments = Client::where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
      $all_departments = Client::all();

      $data['department_id'] = 'all';
      $page_title .= ' - Department Wise';
    }
    $rs = collect(DB::select($sql));


    $departments = [];
    foreach($parent_departments as $key=>$department){
        $child_dept_ids = $this->getChildDepartments($all_departments,$department->id);
        $total = $rs->whereIn('department_id',$child_dept_ids)->sum('total');
        $department['total'] = $total;
        $departments[] = $department;
    }


    $page_title = $page_title. ' Off Day Dashboard';

    $data['c_date'] = $date;
    $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
    $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
    $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
    {
        return Client::generatetree(0,false,true);
    });
    return view('Dashboards.dw_off_days',compact('page_title','data','departments','external_departments'));
  }

  public function department_wise_attach(Request $request){
    $date = date("Y-m-d");
    if($request->has('date'))
    {
      $date = $request->get('date');
    }
    
    if($request->has('list')){
      //means we will show the employees list bc its the nth department
      return redirect()->route('dd-attach-employees',['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
    }
    $data = [];
    $page_title = 'AJ&K';
    

    $data['reg_autocomplete']='';
  	$data['selected_dmgs']='all';

    $region_filter='';
    if($request->has('selected_dmgs') && $request->selected_dmgs !="all" && $request->selected_dmgs!=''){
      $region_input = explode('_',$request->get('selected_dmgs'));
      $data['selected_dmgs']=$request->get('selected_dmgs');
      $dmg_check = $region_input[0];
      $dmg_id = $region_input[1];
      if($dmg_check == 'reg'){
          $region_filter = " and u.region_id =".$dmg_id;
          $region = Region::find($dmg_id);
          $page_title = $region->region_name . ' '. 'Division' ;
          $data['reg_autocomplete']=$page_title;
      }elseif($dmg_check == 'zone'){
          $region_filter = " and u.zone_id =".$dmg_id;

          $zone = Zone::find($dmg_id);
          $page_title = $zone->zone_name .' '. 'District' ;
          $data['reg_autocomplete']=$page_title;
      }elseif($dmg_check == 'branch'){
          $region_filter = " and u.branch_id =".$dmg_id;

          $branch = Branch::find($dmg_id);
          $page_title = $branch->branch_name .' '. 'Tehsil' ;
          $data['reg_autocomplete']=$page_title;
      }
      $data['dmg_check'] = $dmg_check;
      $data['dmg_id'] = $dmg_id;
    }


    $sql = "SELECT
                d.id as department_id,
                d.name AS department,
                COUNT(distinct u.id) AS total
            FROM `attach_employees_details` a
                JOIN users u ON a.user_id = u.id
                JOIN clients d ON u.department_id = d.id 
                WHERE a.attach_date = DATE('".$date."')   AND u.role_id IN(6,7) AND u.status=1  AND u.flag = 1
                ".$region_filter." group by d.id";

    $data['department_id'] = 'all';
    $page_title .= ' - Department Wise';

    $departments_clause = "";
    if($request->has('department_id') && $request->get('department_id')!='all'){
        $child_depts = $this->getChildDepartments(Client::all(),$request->get('department_id'));
        if(count($child_depts)==0){
          //means we will show the employees list bc its the nth department
          return redirect()->route('dd-attach-employees',['date' => $date,'department_id'=>$request->department_id,'list'=>'yes','selected_dmgs'=>$request->selected_dmgs]);
        }

        $parent_departments = Client::where('parent_id',$request->get('department_id'))->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
        $all_departments = Client::whereIn('parent_id',$child_depts)->get();
        

        $dep = Client::find($request->department_id);
        $page_title .= ' - '.$dep->name;
        $data['department_id'] = $request->department_id;

    }else{
      $parent_departments = Client::where('parent_id',0)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
      $all_departments = Client::all();

      $data['department_id'] = 'all';
      $page_title .= ' - Department Wise';
    }

    $rs = collect(DB::select($sql));
    $departments = [];
    foreach($parent_departments as $key=>$department){
        $child_dept_ids = $this->getChildDepartments($all_departments,$department->id);
        $total = $rs->whereIn('department_id',$child_dept_ids)->sum('total');
        $department['total'] = $total;
        $departments[] = $department;
    }

    $page_title = $page_title. ' On-Duty Dashboard';

    $data['c_date'] = $date;
    $data['dmg_autocomplete'] = session()->get('dmg_autocomplete');
    $data['dmg_autocomplete_keys'] = session()->get('dmg_autocomplete_keys');
    $data['all_departments'] =  Cache::remember('all_departments', now()->addMinutes(1440), function()
    {
        return Client::generatetree(0,false,true);
    });
    return view('Dashboards.dw_attach',compact('page_title','data','departments'));
  }

  public function dd_offdays_employees(Request $request){
    $date = session()->get('date');
    $department_id = session()->get('department_id');
    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";

    $departments_clause = " and u.department_id=".$department_id;

    $region_filter = "";
    if(session()->has('selected_dmgs') && session()->get('selected_dmgs') !="all"){
        $region_input = explode('_',session()->get('selected_dmgs'));
        $dmg_check = $region_input[0];
        $dmg_id = $region_input[1];
        if($dmg_check == 'reg'){
           $region_filter = " and u.region_id =".$dmg_id;
        }
        if($dmg_check == 'zone'){
           $region_filter = " and u.zone_id =".$dmg_id;
        }
        if($dmg_check == 'branch'){
           $region_filter = " and u.branch_id =".$dmg_id;
        }
    }


    if(session()->has('list')){
        $departments = Client::all();
        $child_depts = $this->getChildDepartments($departments,$department_id);
        if(count($child_depts)!=0){
            $filtered_departments = implode(',',$child_depts);
            $departments_clause = " and u.department_id in (".$filtered_departments.") ";
            $departments_clause.=$region_filter;
        }
    }
    $sql = "SELECT
            u.id,
                u.name AS `name`,
                u.father_name,
                u.bps,
            u.gender,
            d.title AS designation
                FROM users u
                LEFT JOIN designations d ON d.id = u.designation_id
                LEFT JOIN time_categories tc ON  tc.id  = (SELECT tcat_id FROM time_category_histories
                                    WHERE user_id = u.id AND DATE(join_date) <= DATE('".$date."') ORDER BY join_date
                                        DESC LIMIT 1)
                WHERE tc.".$tc_in_column." IS NULL
                AND u.role_id IN(6,7)  AND u.status=1  AND u.flag = 1 ".$departments_clause."
                GROUP BY u.id";
    $data['employees'] = DB::select($sql);
    $page_title = "Employees";
    return view('Dashboards.dd_present_employees',compact('page_title','data'));
  }

  public function dd_present_employees(Request $request){
    $date = session()->get('date');
    $department_id = session()->get('department_id');
    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";
    $departments_clause = " and u.department_id=".$department_id;
    $region_clause = "";

    if(session()->has('selected_dmgs') && session()->get('selected_dmgs') !="all"){
        $region_input = explode('_',session()->get('selected_dmgs'));
        $dmg_check = $region_input[0];
        $dmg_id = $region_input[1];
        if($dmg_check == 'reg'){
           $region_clause = " and u.region_id =".$dmg_id;
        }
        if($dmg_check == 'zone'){
           $region_clause = " and u.zone_id =".$dmg_id;
        }
        if($dmg_check == 'branch'){
           $region_clause = " and u.branch_id =".$dmg_id;
        }
    }

    if(session()->has('list')){
        $departments = Client::all();
        $child_depts = $this->getChildDepartments($departments,$department_id);
        if(count($child_depts)!=0){
            $filtered_departments = implode(',',$child_depts);
            $departments_clause = " and u.department_id in (".$filtered_departments.") ";
            $departments_clause .= $region_clause;
        }
    }

    $sql = "SELECT
            u.id,
                u.name AS `name`,
                u.father_name,
                u.bps,
            u.gender,
            d.title AS designation
                FROM
                users u
                LEFT JOIN designations d ON d.id = u.designation_id
                LEFT JOIN time_categories tc
                ON  tc.id = (SELECT tcat_id FROM time_category_histories
                        WHERE user_id = u.id AND DATE(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
                WHERE CAST(IFNULL(tc.".$tc_in_column.",'09:00:00') AS TIME) < TIME(NOW())
                AND u.id IN (SELECT user_id FROM attendance WHERE DATE(DATETIME) = date('".$date."'))
                AND u.role_id IN(6,7) AND u.status=1 AND u.flag = 1 ".$departments_clause."
                GROUP BY u.id";
    $data['employees'] = DB::select($sql);
    $page_title = "Employees";
    return view('Dashboards.dd_present_employees',compact('page_title','data'));
  }

  public function dd_absent_employees(Request $request){
    $date = session()->get('date');
    $department_id = session()->get('department_id');
    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";

    $departments_clause = " and u.department_id=".$department_id;

    $region_clause = "";

    if(session()->has('selected_dmgs') && session()->get('selected_dmgs') !="all"){
        $region_input = explode('_',session()->get('selected_dmgs'));
        $dmg_check = $region_input[0];
        $dmg_id = $region_input[1];
        if($dmg_check == 'reg'){
           $region_clause = " and u.region_id =".$dmg_id;
        }
        if($dmg_check == 'zone'){
           $region_clause = " and u.zone_id =".$dmg_id;
        }
        if($dmg_check == 'branch'){
           $region_clause = " and u.branch_id =".$dmg_id;
        }
    }

    if(session()->has('list')){
        $departments = Client::all();
        $child_depts = $this->getChildDepartments($departments,$department_id);
        if(count($child_depts)!=0){
            $filtered_departments = implode(',',$child_depts);
            $departments_clause = " and u.department_id in (".$filtered_departments.") ";
            $departments_clause.=$region_clause;
        }
    }

    $region_clause = "";

    if(session()->has('selected_dmgs') && session()->get('selected_dmgs') !="all"){
        $region_input = explode('_',session()->get('selected_dmgs'));
        $dmg_check = $region_input[0];
        $dmg_id = $region_input[1];
        if($dmg_check == 'reg'){
           $region_clause = " and u.region_id =".$dmg_id;
        }
        if($dmg_check == 'zone'){
           $region_clause = " and u.zone_id =".$dmg_id;
        }
        if($dmg_check == 'branch'){
           $region_clause = " and u.branch_id =".$dmg_id;
        }
    }

    $sql = "SELECT
            u.id,
                u.name AS `name`,
                u.father_name,
                u.bps,
            u.gender,
            d.title AS designation
                FROM
                users u
                JOIN designations d ON d.id = u.designation_id
                LEFT JOIN time_categories tc
                ON  tc.id = (SELECT tcat_id FROM time_category_histories
                        WHERE user_id = u.id AND DATE(join_date) <= date('".$date."') ORDER BY join_date DESC LIMIT 1)
                WHERE CAST(IFNULL(tc.".$tc_in_column.",'09:00:00') AS TIME) < TIME(NOW())
                AND u.id NOT IN (SELECT user_id FROM attendance WHERE DATE(DATETIME) = date('".$date."'))
                AND u.role_id IN(6,7) AND u.status=1 AND u.flag = 1 ".$departments_clause."
                and u.id not in (select user_id from leave_requests_details where leave_date = date('".$date."'))
                and u.id not in (
                  SELECT user_id FROM `attach_employees_details`  WHERE  attach_date = DATE('".$date."')
                )
                GROUP BY u.id";
    $data['employees'] = DB::select($sql);
    $page_title = "Employees";
    return view('Dashboards.dd_absent',compact('page_title','data'));
  }

  public function dd_all_employees(Request $request){
    $date = session()->get('date');
    $department_id = session()->get('department_id');
    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";
    $departments_clause = " and u.department_id=".$department_id;

    $region_clause = "";

    if(session()->has('selected_dmgs') && session()->get('selected_dmgs') !="all"){
        $region_input = explode('_',session()->get('selected_dmgs'));
        $dmg_check = $region_input[0];
        $dmg_id = $region_input[1];
        if($dmg_check == 'reg'){
           $region_clause = " and u.region_id =".$dmg_id;
        }
        if($dmg_check == 'zone'){
           $region_clause = " and u.zone_id =".$dmg_id;
        }
        if($dmg_check == 'branch'){
           $region_clause = " and u.branch_id =".$dmg_id;
        }
    }

    if(session()->has('list')){
        $departments = Client::all();
        $child_depts = $this->getChildDepartments($departments,$department_id);
        if(count($child_depts)!=0){
            $filtered_departments = implode(',',$child_depts);
            $departments_clause = " and u.department_id in (".$filtered_departments.") ";
            $departments_clause.=$region_clause;
        }
    }
    $sql = "SELECT
            u.id,
                u.name AS `name`,
                u.father_name,
                u.bps,
            u.gender,
            d.title AS designation
                FROM
                users u
                LEFT JOIN designations d ON d.id = u.designation_id
                where u.role_id IN(6,7) AND u.status=1 AND u.flag = 1 ".$departments_clause."
                GROUP BY u.id";
    $data['employees'] = DB::select($sql);
    $page_title = "Employees";
    return view('Dashboards.dd_absent',compact('page_title','data'));
  }

  public function dd_late_employees(Request $request){
    $date = session()->get('date');
    $department_id = session()->get('department_id');
    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";

    $departments_clause = " and l1.department_id=".$department_id;

    $region_clause = "";

    if(session()->has('selected_dmgs') && session()->get('selected_dmgs')!="all"){
        $region_input = explode('_',session()->get('selected_dmgs'));
        $dmg_check = $region_input[0];
        $dmg_id = $region_input[1];
        if($dmg_check == 'reg'){
           $region_clause = " and l1.region_id =".$dmg_id;
        }
        if($dmg_check == 'zone'){
           $region_clause = " and l1.zone_id =".$dmg_id;
        }
        if($dmg_check == 'branch'){
           $region_clause = " and l1.branch_id =".$dmg_id;
        }
    }

    if(session()->has('list')){
        $departments = Client::all();
        $child_depts = $this->getChildDepartments($departments,$department_id);
        if(count($child_depts)!=0){
            $filtered_departments = implode(',',$child_depts);
            $departments_clause = " and l1.department_id in (".$filtered_departments.") ";
            $departments_clause.=$region_clause;
        }
    }
    $sql = "SELECT
                l1.id,
                l1.name,
                l1.father_name,
                l1.bps,
                l1.gender,
                l1.checkin,
                l1.designation,
                l1.department_id,
                l1.region_id,
                l1.zone_id,
                l1.branch_id
            FROM (
            SELECT
            u.id,
            u.name AS `name`,
            u.father_name,
            u.bps,
            u.region_id,
            u.zone_id,
            u.branch_id,
            u.gender,
            u.department_id,
            ds.title as designation,
            SUBSTR(GROUP_CONCAT(DISTINCT a.datetime ORDER BY a.datetime ASC),1,19) AS checkin,
            IFNULL(IF(SUBSTR(GROUP_CONCAT(DISTINCT a.datetime ORDER BY a.datetime ASC),1,19)>CONCAT(DATE(NOW()),' ',ADDTIME(tc.".$tc_in_column.",tc.grace_time)),1,0),0) AS is_late
                  FROM
                  users u
                  LEFT JOIN designations ds on ds.id = u.designation_id
                  LEFT JOIN time_categories tc ON  tc.id = (SELECT tcat_id FROM time_category_histories
                    WHERE user_id = u.id AND DATE(join_date) <= DATE('".$date."') ORDER BY join_date DESC LIMIT 1)
                  JOIN attendance a ON a.user_id = u.id
                  WHERE u.role_id IN(6,7) AND u.status=1 AND DATE(a.datetime) = DATE(NOW())  AND u.flag = 1
            GROUP BY u.id) AS l1
            WHERE l1.is_late = 1 ".$departments_clause;

    $data['employees'] = DB::select($sql);
    $page_title = "Employees";
    return view('Dashboards.dd_late_employees',compact('page_title','data'));
  }

  public function dd_leave_employees(Request $request){
    $date = session()->get('date');
    $department_id = session()->get('department_id');
    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";

    $departments_clause = " and u.department_id=".$department_id;
    $region_clause = "";

    if(session()->has('selected_dmgs') && session()->get('selected_dmgs') !="all"){
        $region_input = explode('_',session()->get('selected_dmgs'));
        $dmg_check = $region_input[0];
        $dmg_id = $region_input[1];
        if($dmg_check == 'reg'){
           $region_clause = " and u.region_id =".$dmg_id;
        }
        if($dmg_check == 'zone'){
           $region_clause = " and u.zone_id =".$dmg_id;
        }
        if($dmg_check == 'branch'){
           $region_clause = " and u.branch_id =".$dmg_id;
        }
    }
    if(session()->has('list')){
        $departments = Client::all();
        $child_depts = $this->getChildDepartments($departments,$department_id);
        if(count($child_depts)!=0){
            $filtered_departments = implode(',',$child_depts);
            $departments_clause = " and u.department_id in (".$filtered_departments.") ";
            $departments_clause.=$region_clause;
        }
    }

    $sql = "SELECT
                u.id,
                u.name AS `name`,
                u.father_name,
                u.bps,
                u.gender
                    FROM `leave_requests_details` lrd
                JOIN users u ON lrd.user_id = u.id
                JOIN clients d ON d.id = u.department_id
                WHERE lrd.leave_date = DATE('".$date."')  AND u.flag = 1
                     ".$departments_clause;

    $data['employees'] = DB::select($sql);
    $page_title = "Employees";
    return view('Dashboards.dd_absent',compact('page_title','data'));
  }

  public function dd_registered_employees(Request $request){

    $region_clause = "";
    //dd($request->department_id);
     $department_id = $request->get('department_id');
     $departments_clause = " and u.department_id=".$department_id;

    if($request->has('selected_dmgs') && $request->get('selected_dmgs') !="all"){
        $region_input = explode('_',$request->get('selected_dmgs'));
        $dmg_check = $region_input[0];
        $dmg_id = $region_input[1];
        if($dmg_check == 'reg'){
           $region_clause = " and u.region_id =".$dmg_id;
        }
        if($dmg_check == 'zone'){
           $region_clause = " and u.zone_id =".$dmg_id;
        }
        if($dmg_check == 'branch'){
           $region_clause = " and u.branch_id =".$dmg_id;
        }
    }

    $departments = Client::all();
    $child_depts = $this->getChildDepartments($departments,$department_id);

    if(count($child_depts)!=0){
        $filtered_departments = implode(',',$child_depts);
        $filtered_departments .=','.$department_id;

        $departments_clause = " and u.department_id in (".$filtered_departments.")  ";
    }
    $departments_clause.=$region_clause;

    if($request->has('emp_type') && $request->get('emp_type') !="NotSet")
    {
      $departments_clause.=" and u.emp_type ='".$request->get('emp_type')."'";
    }elseif($request->get('selected_dmgs') == "NotSet")
    {
      $departments_clause.=" and (u.emp_type='' or u.emp_type is NULL)";
    }

    $sql = "SELECT
            u.id,
                u.name AS `name`,
                u.father_name,
                u.bps,
            u.gender,
            d.title AS designation
                FROM
                users u
                LEFT JOIN designations d ON d.id = u.designation_id
                where u.role_id IN(6,7) AND u.status=1 AND u.flag = 1 AND u.field_staff=0 ".$departments_clause."
                GROUP BY u.id order by u.bps DESC";
    $data['employees'] = DB::select($sql);

    $page_title='All';
    $dep = Client::find($department_id);
    if($dep){
      $page_title = $dep->name;
    }
    if($request->has('emp_type'))
      $page_title .= ' '.$request->get('emp_type');
    $page_title = $page_title. ' Registered Employees';

    return view('Dashboards.dd_absent',compact('page_title','data'));

  }

  public function dd_unregistered_employees(Request $request){

    $region_clause = "";
    $department_id = session()->get('department_id');
    $departments_clause = " and u.department_id=".$department_id;

    $departments_clause = '';

    if(session()->has('selected_dmgs') && session()->get('selected_dmgs') !="all"){
        $region_input = explode('_',session()->get('selected_dmgs'));
        $dmg_check = $region_input[0];
        $dmg_id = $region_input[1];
        if($dmg_check == 'reg'){
           $region_clause = " and u.region_id =".$dmg_id;
        }
        if($dmg_check == 'zone'){
           $region_clause = " and u.zone_id =".$dmg_id;
        }
        if($dmg_check == 'branch'){
           $region_clause = " and u.branch_id =".$dmg_id;
        }
    }

    $departments = Client::all();
    $child_depts = $this->getChildDepartments($departments,$department_id);

    if(count($child_depts)!=0){
        $filtered_departments = implode(',',$child_depts);
        $departments_clause = " and u.department_id in (".$filtered_departments.") ";
    }
    $departments_clause.=$region_clause;

    if(session()->has('emp_type') && session()->get('emp_type') !="NotSet")
    {
      $departments_clause.=" and u.emp_type ='".session()->get('emp_type')."'";
    }elseif(session()->get('selected_dmgs') =="NotSet")
    {
      $departments_clause.=" and (u.emp_type='' or u.emp_type is NULL)";
    }

    $sql = "SELECT
                u.id,
                u.name AS `name`,
                u.father_name,
                u.bps,
                u.remarks,
                u.gender,
                d.title AS designation
            FROM
                users u
            LEFT JOIN designations d ON d.id = u.designation_id
            where u.role_id IN(6,7) AND u.status=1 AND u.flag = 1 AND u.field_staff=1 ".$departments_clause."
                GROUP BY u.id order by u.bps DESC";
    $data['employees'] = DB::select($sql);

    $page_title='All';
    $dep = Client::find($department_id);
    if($dep){
      $page_title = $dep->name;
    }
    if(session()->has('emp_type'))
      $page_title .= ' '.session()->get('emp_type');
    $page_title = $page_title. ' Unaccounted Employees';

    return view('Dashboards.dd_unregistered_employees',compact('page_title','data'));

  }

  public function dd_os_schedule(Request $request){
    $date = session()->get('date');
    $department_id = session()->get('department_id');
    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";

    $departments_clause = " and u.department_id=".$department_id;
    $region_clause = "";

    if(session()->has('selected_dmgs') && session()->get('selected_dmgs') !="all"){
        $region_input = explode('_',session()->get('selected_dmgs'));
        $dmg_check = $region_input[0];
        $dmg_id = $region_input[1];
        if($dmg_check == 'reg'){
           $region_clause = " and u.region_id =".$dmg_id;
        }
        if($dmg_check == 'zone'){
           $region_clause = " and u.zone_id =".$dmg_id;
        }
        if($dmg_check == 'branch'){
           $region_clause = " and u.branch_id =".$dmg_id;
        }
    }
    if(session()->has('list')){
        $departments = Client::all();
        $child_depts = $this->getChildDepartments($departments,$department_id);
        if(count($child_depts)!=0){
            $filtered_departments = implode(',',$child_depts);
            $departments_clause = " and u.department_id in (".$filtered_departments.") ";
            $departments_clause.=$region_clause;
        }
    }

    $sql = "SELECT
            u.id,
                u.name AS `name`,
                u.father_name,
                u.bps,
            u.gender,
            d.title AS designation
                FROM
                users u
                JOIN designations d ON d.id = u.designation_id
                where u.role_id IN(6,7) AND u.status=1 AND u.flag = 2 ".$departments_clause."
                GROUP BY u.id";
    $data['employees'] = DB::select($sql);
    $page_title = "Employees";
    return view('Dashboards.dd_absent',compact('page_title','data'));
  }

  public function dd_sna_employees(Request $request){
    $date = session()->get('date');
    $department_id = session()->get('department_id');
    $tc_in_column = "tc_".strtolower(date('l',strtotime($date)))."_in";

    $departments_clause = " and u.department_id=".$department_id;
    $region_clause = "";

    if(session()->has('selected_dmgs') && session()->get('selected_dmgs') !="all"){
        $region_input = explode('_',session()->get('selected_dmgs'));
        $dmg_check = $region_input[0];
        $dmg_id = $region_input[1];
        if($dmg_check == 'reg'){
           $region_clause = " and u.region_id =".$dmg_id;
        }
        if($dmg_check == 'zone'){
           $region_clause = " and u.zone_id =".$dmg_id;
        }
        if($dmg_check == 'branch'){
           $region_clause = " and u.branch_id =".$dmg_id;
        }
    }
    if(session()->has('list')){
        $departments = Client::all();
        $child_depts = $this->getChildDepartments($departments,$department_id);
        if(count($child_depts)!=0){
            $filtered_departments = implode(',',$child_depts);
            $departments_clause = " and u.department_id in (".$filtered_departments.") ";
            $departments_clause.=$region_clause;
        }
    }

    $sql = "SELECT
                u.id,
                u.name AS `name`,
                u.father_name,
                u.bps,
                u.gender,
            d.title AS designation
                FROM
                users u
                JOIN designations d ON d.id = u.designation_id
                LEFT JOIN time_categories tc
                ON  tc.id = (SELECT tcat_id FROM time_category_histories
                        WHERE user_id = u.id AND DATE(join_date) <= DATE('".$date."') ORDER BY join_date DESC LIMIT 1)
                WHERE u.id NOT IN (SELECT user_id FROM attendance WHERE DATE(DATETIME) = DATE('".$date."'))
                AND u.role_id IN(6,7) AND u.status=1 AND u.flag = 1
                AND CAST(tc.".$tc_in_column." AS TIME) > TIME(NOW())
                ".$departments_clause;
    $data['employees'] = DB::select($sql);
    $page_title = "Employees";
    return view('Dashboards.dd_absent',compact('page_title','data'));
  }

  public function dd_attach_employees(Request $request){
    $date = date("Y-m-d");
    if($request->has('date'))
    {
      $date = $request->get('date');
    }
    $department_id = $request->get('department_id');

    $departments_clause = " and u.department_id=".$department_id;
    $region_clause = "";

    if($request->has('selected_dmgs') && $request->get('selected_dmgs') !="all"){
        $region_input = explode('_',$request->get('selected_dmgs'));
        $dmg_check = $region_input[0];
        $dmg_id = $region_input[1];
        if($dmg_check == 'reg'){
           $region_clause = " and u.region_id =".$dmg_id;
        }
        if($dmg_check == 'zone'){
           $region_clause = " and u.zone_id =".$dmg_id;
        }
        if($dmg_check == 'branch'){
           $region_clause = " and u.branch_id =".$dmg_id;
        }
    }
    if($request->has('list')){
        $departments = Client::all();
        $child_depts = $this->getChildDepartments($departments,$department_id);
        if(count($child_depts)!=0){
            $filtered_departments = implode(',',$child_depts);
            $departments_clause = " and u.department_id in (".$filtered_departments.") ";
            
        }
    }
    $departments_clause.=$region_clause;

    $sql = "SELECT
                u.id,
                u.name AS `name`,
                u.father_name,
                u.bps,
                u.gender,
            d.title AS designation
                FROM users u                
                left join attach_employees_details a ON u.id = a.user_id
                left JOIN designations d ON  u.designation_id = d.id
                Where a.attach_date = DATE('".$date."')  AND u.role_id IN(6,7) AND u.status=1  AND u.flag = 1
                ".$departments_clause;
                //dd($sql);

    $data['employees'] = DB::select($sql);
    $page_title = "On-Duty Employees";
    return view('Dashboards.dd_absent',compact('page_title','data'));
  }

  public function dashboard(){
      if(Session::get('role_id') == 8){

      }
      $dep_id=Session::get('dep_id');
      //dd($dep_id);
      $date=date("Y-m-d");
      $this_week_sat= date("Y-m-d", strtotime('saturday this week'));
      $current_date =  $this_week_sat;
      $week_first_day =  date('Y-m-d',(strtotime ( '-5 day' , strtotime ( $current_date) ) ));
      $date_range_txt = "";
      foreach(getDatesFromRange($week_first_day,$current_date) as $range){
          $date_range_txt.= "'".$range."',";
      }
      $date_range_txt = rtrim($date_range_txt,",");
      $pieces = explode("','", $date_range_txt);
      $weekday1=ltrim($pieces[0],"'");
      $weekday2=$pieces[1];
      $weekday3=$pieces[2];
      $weekday4=$pieces[3];
      $weekday5=$pieces[4];
      $weekday6=rtrim($pieces[5],"'");
      $day= date("l");

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
                                 tc_saturday_in,
                                 tc_saturday_out,
                                 tc_sunday_in,
                                 tc_sunday_out,
                                 tc.is_night_shift,
                                 a.datetime,date(a.datetime) as att_date,d.title as designation
                                 from attendance a
                                        JOIN users u on a.user_id=u.id
                                        JOIN clients c on c.id = u.department_id
                                        LEFT JOIN time_categories tc  on tc.id=u.tcat_id
                                        LEFT JOIN designations d on d.id = u.designation_id

                                WHERE  u.status=1 and u.flag=1 and u.field_staff=0 and c.parent_id='".Session::get('dep_id')."' and date(a.datetime)  IN(".$date_range_txt.")
                                and u.id not in (SELECT user_id FROM `attach_employees_details` where attach_date  IN('".$date."')
                                )
                                and u.id not in (SELECT user_id FROM `leave_requests_details` where leave_date  IN('".$date."')
                                )
                                GROUP by date(a.datetime),u.id ";

                                // 24992
      $present_att_sql = "SELECT date(`datetime`) as `date`,count(distinct user_id) as total FROM `attendance` where date(`datetime`) IN(".$date_range_txt.")
                      and user_id in (select user_id from attach_employees_details where date(attach_date) IN(".$date_range_txt.")) GROUP by date(datetime),user_id";


      $present_leave_sql = "SELECT date(`datetime`) as `date`,count(distinct user_id) as total FROM `attendance` where date(`datetime`) IN(".$date_range_txt.")
                      and user_id in (select user_id from leave_requests_details where date(leave_date) IN(".$date_range_txt.")) GROUP by date(datetime),user_id";
      $total_departmental_employees = User::whereHas('departments',function($q)use($dep_id){
        return $q->where('parent_id',$dep_id);
      })->where('status',1)->where('flag',1)->count();
      $total_departmental_employees_fieldstaff = User::whereHas('departments',function($q)use($dep_id){
        return $q->where('parent_id',$dep_id);
      })->where('status',1)->where('flag',1)->where('field_staff',1)->count();

      $total_roster_employees = User::whereHas('departments',function($q)use($dep_id){
        return $q->where('parent_id',$dep_id);
      })->where('status',1)->where('flag',2)->count();

      $top_statistics_data = DB::select($top_statistics_sql);


      // $department_ids = Client::where('parent_id',$dep_id)->pluck('id');
      // if($department_ids->isEmpty()){
      //   $department=Client::where('id',$dep_id)->first();
      //   $dep_id = $department->parent_id;
      //   $department_ids = Client::where('parent_id',$dep_id)->pluck('id');
      // }

      // $department_ids = $department_ids->toArray();
      // $department_ids = implode(",",$department_ids);
      // $q="select
      //           tcat_history.user_id,
      //           date(tcat_history.join_date) as date,
      //            time_categories.*
      //     from users
      //             INNER JOIN time_category_histories as tcat_history on users.id=tcat_history.user_id
      //             INNER JOIN time_categories on time_categories.id=tcat_history.tcat_id
      //     where users.department_id in (".$department_ids.")";
      // $time_cat_history = collect(DB::select($q));





      $ai_features_sql = "SELECT    IFNULL(SUM(IF(fa.face_mask='MASK',1,NULL)),0) AS masked,
                                    IFNULL(SUM(IF(fa.face_mask='NOMASK',1,NULL)),0) AS no_mask,
                                    IFNULL(SUM(IF(fa.face_expression='SURPRISE',1,NULL)),0) AS surprise,
                                    IFNULL(SUM(IF(fa.face_expression='ANGRY',1,NULL)),0) AS angry,
                                    IFNULL(SUM(IF(fa.face_expression='CALM',1,NULL)),0) AS calm,
                                    IFNULL(SUM(IF(fa.face_expression='CONFUSED',1,NULL)),0) AS confuse,
                                    IFNULL(SUM(IF(fa.face_expression='HAPPY',1,NULL)),0) AS happy
                                 FROM timenex_new.attendance a
                                        JOIN timenex_new.users u ON a.user_id=u.id
                                        JOIN timenex_new.clients c ON c.id = u.department_id
                                        JOIN tbl_attandance fa ON a.face_att_id = fa.att_id
                                WHERE u.status=1 AND u.flag=1  AND u.field_staff=0 AND c.parent_id='".Session::get('dep_id')."' AND DATE(a.datetime)  = DATE(NOW())
                                AND a.face_att_id IS NOT NULL";
      $ai_features   = DB::select($ai_features_sql);
      $leave_satistics= "select
                                 users.id,
                                     leave_requests_details.leave_date,
                                        leave_requests.from_date,
                                           leave_requests.to_date
                          from users
                               inner join leave_requests on users.id=leave_requests.user_id
                               inner join leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
                               inner join clients on clients.id=users.department_id
                          where clients.parent_id='".Session::get('dep_id')."'
                          and leave_requests_details.leave_date IN(".$date_range_txt.")
                          and leave_requests.leave_type=2
                          and users.status=1 AND users.flag=1  AND users.field_staff=0
                          GROUP by users.id,leave_requests_details.leave_date";

      $leave_satistics_data = DB::select($leave_satistics);
      $short_leave="select
                           count(*) as short_leave
                    from users
                          inner join leave_requests on users.id=leave_requests.user_id
                          inner join leave_requests_details on leave_requests_details.parent_table_id=leave_requests.id
                          inner join clients on clients.id=users.department_id
                    where clients.parent_id='".Session::get('dep_id')."'
                    and leave_requests_details.leave_date='".date('Y-m-d') ."'
                    and users.status=1 AND users.flag=1  AND users.field_staff=0
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
                              and users.status=1 AND users.flag=1  AND users.field_staff=0
                              and attach_employees_details.attach_date IN(".$date_range_txt.")
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

      //  $time_cat_hist=  TimeCategoryHistory::withTrashed()->where('user_id',$data->id)->where('join_date','<=',$data->att_date);
      //  $record=timeCategoryHistForDashboard($time_cat_hist,$data);
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
             $time_in=$data->tc_saturday_in;
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
    //  dd($attendance_data['late']['2022-10-12'].'-'. $attendance_data['present']['2022-10-12']);





      if(isset($attendance_data['attach'][$weekday1])){
        $attach_weekday1= $attendance_data['attach'][$weekday1];
      }else{
        $attach_weekday1= 0;
      }

      if(isset($attendance_data['attach'][$weekday2])){
        $attach_weekday2= $attendance_data['attach'][$weekday2];
      }else{
          $attach_weekday2= 0;
      }

      if(isset($attendance_data['attach'][$weekday3])){
        $attach_weekday3= $attendance_data['attach'][$weekday3];
      }else{
          $attach_weekday3= 0;
      }
      if(isset($attendance_data['attach'][$weekday4])){
        $attach_weekday4= $attendance_data['attach'][$weekday4];
      }else{
          $attach_weekday4= 0;
      }
      if(isset($attendance_data['attach'][$weekday5])){
        $attach_weekday5= $attendance_data['attach'][$weekday5];
      }else{
          $attach_weekday5= 0;
      }
      if(isset($attendance_data['attach'][$weekday6])){
        $attach_weekday6= $attendance_data['attach'][$weekday6];
      }else{
          $attach_weekday6= 0;
      }
      if(isset($attendance_data['leave'][$weekday1])){
        $leave_weekday1= $attendance_data['leave'][$weekday1];
      }else{
          $leave_weekday1= 0;
      }
      if(isset($attendance_data['leave'][$weekday2])){
        $leave_weekday2= $attendance_data['leave'][$weekday2];
      }else{
          $leave_weekday2= 0;
      }
      if(isset($attendance_data['leave'][$weekday3])){
        $leave_weekday3= $attendance_data['leave'][$weekday3];
      }else{
          $leave_weekday3= 0;
      }
      if(isset($attendance_data['leave'][$weekday4])){
        $leave_weekday4= $attendance_data['leave'][$weekday4];
      }else{
          $leave_weekday4= 0;
      }
      if(isset($attendance_data['leave'][$weekday5])){
        $leave_weekday5= $attendance_data['leave'][$weekday5];
      }else{
          $leave_weekday5= 0;
      }
     if(isset($attendance_data['leave'][$weekday6])){
        $leave_weekday6= $attendance_data['leave'][$weekday6];
      }else{
          $leave_weekday6= 0;
      }

      if(isset($attendance_data['present'][$weekday1])){
          $pre_weekday1= $attendance_data['present'][$weekday1];
      }else{
          $pre_weekday1= 0;
      }
       if(isset($attendance_data['present'][$weekday2])){
          $pre_weekday2= $attendance_data['present'][$weekday2];
      }else{
          $pre_weekday2= 0;
      }
      if(isset($attendance_data['present'][$weekday3])){
        $pre_weekday3= $attendance_data['present'][$weekday3];
      }else{
        $pre_weekday3= 0;
      }
      if(isset($attendance_data['present'][$weekday4])){
        $pre_weekday4= $attendance_data['present'][$weekday4];
      }else{
        $pre_weekday4= 0;
      }if(isset($attendance_data['present'][$weekday5])){
        $pre_weekday5= $attendance_data['present'][$weekday5];
      }else{
        $pre_weekday5=0;
      }if(isset($attendance_data['present'][$weekday6])){
        $pre_weekday6= $attendance_data['present'][$weekday6];
      }else{
        $pre_weekday6= 0;
      }

        if(isset($attendance_data['late'][$weekday1])){
          $late_weekday1= $attendance_data['late'][$weekday1];
        }else{
          $late_weekday1= 0;
        }
       if(isset($attendance_data['late'][$weekday2])){
          $late_weekday2= $attendance_data['late'][$weekday2];
        }else{
          $late_weekday2= 0;
        }
        if(isset($attendance_data['late'][$weekday3])){
          $late_weekday3= $attendance_data['late'][$weekday3];
        }else{
          $late_weekday3= 0;
        }
        if(isset($attendance_data['late'][$weekday4])){
          $late_weekday4= $attendance_data['late'][$weekday4];
        }else{
          $late_weekday4= 0;
        }if(isset($attendance_data['late'][$weekday5])){
          $late_weekday5= $attendance_data['late'][$weekday5];
        }else{
          $late_weekday5=0;
        }if(isset($attendance_data['late'][$weekday6])){
          $late_weekday6= $attendance_data['late'][$weekday6];
        }else{
          $late_weekday6= 0;
        }


      foreach(getDatesFromRange($week_first_day,$current_date) as $range){

          if(array_key_exists($range,$attendance_data['present'])){
              $td_leave = 0;
              $td_attach = 0;
              if(array_key_exists($range,$attendance_data['leave'])){
                $td_leave = $attendance_data['leave'][$range];
              }
              if(array_key_exists($range,$attendance_data['attach'])){
                $td_attach = $attendance_data['attach'][$range];
              }
              $amb_this_date = 0;
              /*
              if($present_attach->where('date',$range)->count()>0){
                $amb_this_date+=$present_attach->where('date',$range)->first()->total;
              }
              if($present_leave->where('date',$range)->count()>0){
                $amb_this_date+=$present_leave->where('date',$range)->first()->total;
              }*/

              $attendance_data['absent'][$range] = $total_departmental_employees - $total_departmental_employees_fieldstaff - ($attendance_data['present'][$range]+$td_leave + $td_attach+$short_leave_count);
          }else{
            $td_leave = 0;
            $td_attach = 0;
            if(array_key_exists($range,$attendance_data['leave'])){
              $td_leave = $attendance_data['leave'][$range];
            }
            if(array_key_exists($range,$attendance_data['attach'])){
              $td_attach = $attendance_data['attach'][$range];
            }

            $attendance_data['absent'][$range] = $total_departmental_employees-($td_leave + $td_attach + $total_departmental_employees_fieldstaff + $short_leave_count);
            if($range < date("Y-m-d")){
               $attendance_data['absent'][$range] = $total_departmental_employees - $total_departmental_employees_fieldstaff - $short_leave_count;
            }

          }
       }

        $absent_weekday1=$attendance_data['absent'][$weekday1];
        $absent_weekday2=$attendance_data['absent'][$weekday2];
        $absent_weekday3=$attendance_data['absent'][$weekday3];
        $absent_weekday4=$attendance_data['absent'][$weekday4];
        $absent_weekday5=$attendance_data['absent'][$weekday5];
        $absent_weekday6=$attendance_data['absent'][$weekday6];

        $department_devices_sql = "select serial_number from devices where serial_number !='' and department_id = ".$dep_id;
        $department_devices = [];
        foreach(DB::select($department_devices_sql) as $key=>$val){
            $department_devices[] = $val->serial_number;
        }

        $child_dept_ids = Client::where('parent_id',$dep_id)->pluck('id')->toArray();
        foreach($child_dept_ids as $cdid){
          $child_dept_ids = array_merge($child_dept_ids,Client::where('parent_id',$dep_id)->pluck('id')->toArray());
        }
        $child_dept_ids[] = $dep_id;

        $client = new \GuzzleHttp\Client();
        $devices = Device::whereIn('department_id',$child_dept_ids)->pluck('serial_number');
        $devices = $devices->toJson();

        $dev_response = $client->request(
                        'POST',
                        'http://168.119.186.114:8080/api/devices_stats',
                        [
                            'form_params' => [
                                'devices' => $devices
                            ]
                        ]
                    );

        /*

        $dev_response = $client->request('GET','http://168.119.186.114:8080/api/devices_stats'); */
        $responseJSON = json_decode($dev_response->getBody(), true);
        $online_devices = 0;
        $offline_devices = 0;
        $online_devices_list = [];
        $offline_devices_list = [];
        if($responseJSON['status'] == 1){
            $online_devices = $responseJSON['data'][0]['online'];
            $offline_devices = $responseJSON['data'][0]['offline'];
        }
        $online_offline_devices = $responseJSON;



        //sub departments statistics
        $sub_department_statistics = [];
        foreach(Client::where(['parent_id'=>$dep_id,'sub_account'=>2])->get() as $client){
          $sub_department_statistics[$client->name] = $this->sub_dashboard($client->id);
        }

        $page_title = "Main Dashboard";

        return view('Dashboards.department_admin',compact('page_title',
                                                           'attendance_data',
                                                           'sub_department_statistics',
                                                           'total_departmental_employees',
                                                           'online_offline_devices',
                                                           'online_devices',
                                                           'offline_devices',
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
                                                          'ai_features',
                                                          'total_roster_employees',
                                                          'total_departmental_employees_fieldstaff'


                                                        ));

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
                                 tc_saturday_in,
                                 tc_saturday_out,
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
             $time_in=$data->tc_saturday_in;
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

  public function getChildDepartments($departments, $parentId) {
        $childIds = array();
        $childIds[] = $parentId;
        foreach ($departments as $department) {
            if ($department['parent_id'] == $parentId) {
                //$childIds[] = $department['id'];
                $childIds = array_merge($childIds, $this->getChildDepartments($departments, $department['id']));
            }
        }

        return $childIds;
  }

  // public function sub_departments($id){
  //     $ids = Client::where('parent_id',$id)->pluck('id')->toArray();
  //     foreach($ids as $id){
  //         if(Client::where('parent_id',$id)->count()>0){
  //             array_merge($ids,Client::where('parent_id',$id)->pluck('id')->toArray());
  //         }
  //     }
  //     return $ids;
  // }
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
