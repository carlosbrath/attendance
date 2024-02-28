<?php
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/c2', function() {
/*
  $department_ids = [2751,278,278,284,284,368,368,369,369,370,370,371,371,2691,2691,2698,2698,2692,2692,2707,2707,2696,2696,2697,2697,2699,2699,2711,2711,2700,2700,2714,2714,2701,2701,2712,2712,2705,2705,2732,2732,2733,2733,2748,2748,287,287,568,568,1960,1960,1961,1961,2049,2049,1962,1962,2374,2374,1963,1963,2499,2499,1964,1964,2500,2500,1965,1965,2513,2513,1966,1966,2509,2509,1967,1967,2512,2512,1968,1968,2510,2510,1969,1969,2511,2511,1970,1970,2381,2381,2749,2749,2750,2750,2755,2755,2762,2762,2756,2756,2771,2771,2757,2757,2776,2776,2763,2763,2765,2765,2764,2764,2766,2766,2767,2767,2768,2768,2769,2769,2770,2770,2772,2772,2773,2773,2774,2774,2775,2775];
  foreach($department_ids as $key){
    $sql = "INSERT INTO `time_categories` (`title`, `department_id`, `description`, `time_in`, `time_out`, `color`, `time_cat_type`, `leave_type`, `hours`, `grace_time`, `type`, `is_night_shift`, `created_at`, `updated_at`, `deleted_at`, `tc_monday_in`, `tc_monday_out`, `tc_tuesday_in`, `tc_tuesday_out`, `tc_wednesday_in`, `tc_wednesday_out`, `tc_thursday_in`, `tc_thursday_out`, `tc_friday_in`, `tc_friday_out`, `tc_saturday_in`, `tc_saturday_out`, `tc_sunday_in`, `tc_sunday_out`) VALUES ('Morning Shift', ".$key.", NULL, '8:30', '16:00', '#000000', 1, NULL, '', '20:00', 1, 0, NULL, NULL, NULL, '8:30', '16:00', '8:30', '16:00', '8:30', '16:00', '8:30', '16:00', '8:30', '16:00', '8:30', '16:00', '8:30', '16:00')";

    Illuminate\Support\Facades\DB::insert($sql);
    $lid = DB::select('SELECT LAST_INSERT_ID() as lid')[0]->lid;

    $users_sql = "select id from users where department_id =".$key;
    $users = DB::select($users_sql);
    foreach($users as $user){
      $user_id = $user->id;
      $department_id = $key;
      $tcat_hist_sql = "Insert into time_category_histories set user_id=".$user_id.", tcat_id=".$lid.",join_date='2023-07-01 00:00:00',is_current = 1";
      Illuminate\Support\Facades\DB::insert($tcat_hist_sql);

      DB::table('users')
          ->where('id', $user_id)
          ->update(['tcat_id' =>$lid]);
    }

  } */

    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('route:clear');
    dd(Artisan::output());
});



Route::get('check-email', function () {
    $toEmail = 'syedfakhar25@gmail.com';
    $subject = 'Test Email';
    $message = 'This is a test email .';

    // Set SMTP server and port dynamically
    $smtpServer = 'smtp.gmail.com';
    $smtpPort = 587;
    $smtpUsername = 'itboardajk@gmail.com';
    $smtpPassword = 'P@kistan24*';

    ini_set("SMTP", $smtpServer);
    ini_set("smtp_port", $smtpPort);
    ini_set("SMTP","ssl://smtp.gmail.com");

    $headers = 'From: itboardajk@gmail.com' . "\r\n" .
        'Reply-To: itboardajk@gmail.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    if (mail($toEmail, $subject, $message, $headers)) {
        return "Email sent successfully!";
    } else {
        return "Failed to send email.";
    }
});


Route::get('form-wizard', function () {
	return view('form-wizard');
});
Route::get('/', function () {
	return view('Login.login');
});
Route::get('date', function () {
	return view('form-component');
});
Route::get('test1',function(){
  return view('Dashboards.dashboard-view');
});
Route::get('test2',function(){
	return view('Dashboards.dashboard-view2');
  });
Route::get('test3',function(){
	return view('Dashboards.dashboard-view3');
  });
Route::get('table', function () {
	return view('table');
});

Route::get('dashboardView1', 'DashboardController@dashboardView');

Route::get('dashboardView', 'hospitalDashboard@dashboardView')->name('dashboardView');

Route::get('pull', 'PullController@runRefreshCommand')->name('pull');
/*Route::get('',function(){

});*/
// Route::get('users/basic_info', function () {
//     return view('Users.check');
// })
Route::view('check_view','Roasters.roaster_assign');
Route::view('holiday_calendar','TimeCategoires.holiday_calendar');
Auth::routes();
Route::get('login', 'LoginController@index');
Route::post('loginaction', 'LoginController@loginaction');
Route::group(['middleware' => 'checkuser'], function () {
// // Route::post('emp/files','UsersController@addEmpFiles')->name('emp.files');
// Route::get('emp_file/download/{id}','UsersController@getDownload')->name('emp_file.download');
// Route::get('emp_file/delete/{id}','UsersController@getDelete')->name('emp_file.delete');
Route::get('logout', 'LoginController@logout');
// Route::resource('users','UsersController');
Route::resource('users','UsersController');
Route::match(['get','post'],'users','UsersController@index')->name('users');
Route::get('employee/profile','UsersController@profile')->name('employee.profile');
Route::post('step1_store','UsersController@step1_store')->name('employee.step1_store');
Route::post('suggest_unused_ids','UsersController@suggest_unused_ids')->name('suggest_unused_ids');
Route::post('load_zone','UsersController@load_zone')->name('load_zone');
Route::post('load_branch','UsersController@load_branch')->name('load_branch');
Route::put('employee/profile_update/{id}','UsersController@profile_update')->name('employee.profile_update');
Route::get('dashboard','DashboardController@dashboard')->name('dashboard');
Route::match(['get','post'],'super-admin','DashboardController@superAdmin')->name('super-admin');

//manage user on superadmin
Route::get('manage_users_index','UsersController@ManageUsersIndex')->name('manage_users_index');
Route::get('manage_users_create','UsersController@ManageUsersCreate')->name('manage_users_create');
Route::post('manage_users_store','UsersController@ManageUsersStore')->name('manage_users_store');
Route::get('manage_users_edit/{id}','UsersController@ManageUsersEdit')->name('manage_users_edit');
Route::put('manage_users_update/{id}','UsersController@ManageUsersUpdate')->name('manage_users_update');


    Route::match(['get','post'],'home','DashboardController@home')->name('home');
Route::match(['get','post'],'get-sub-deps-stats','DashboardController@get_sub_deps_stats')->name('get-sub-deps-stats');
Route::match(['get','post'],'get-sub-dmgs-stats','DashboardController@get_sub_dmgs_stats')->name('get-sub-dmgs-stats');
Route::match(['get','post'],'get-external-deps-stats','DashboardController@get_external_deps_stats')->name('get-external-deps-stats');


Route::match(['get','post'],'dw-employees','DashboardController@department_wise_employees')->name('dw-employees');
Route::match(['get','post'],'dw-present','DashboardController@department_wise_present')->name('dw-present');
Route::match(['get','post'],'dw-offdays','DashboardController@department_wise_offdays')->name('dw-offdays');
Route::match(['get','post'],'dw-late','DashboardController@department_wise_late')->name('dw-late');
Route::match(['get','post'],'dw-absent','DashboardController@department_wise_absent')->name('dw-absent');
Route::match(['get','post'],'dw-leave','DashboardController@department_wise_leave')->name('dw-leave');
Route::match(['get','post'],'dw-attach','DashboardController@department_wise_attach')->name('dw-attach');
Route::match(['get','post'],'dw_sna_employees','DashboardController@department_wise_sna')->name('dw-sna-employees');
Route::match(['get','post'],'dw-registered','DashboardController@dw_registered')->name('dw-registered');
Route::match(['get','post'],'dw-unregistered','DashboardController@dw_unregistered')->name('dw-unregistered');
Route::match(['get','post'],'dw-os-employees','DashboardController@department_wise_os_employees')->name('dw-os-employees');

Route::match(['get','post'],'dd-present-employees','DashboardController@dd_present_employees')->name('dd-present-employees');
Route::match(['get','post'],'dd-registered-employees','DashboardController@dd_registered_employees')->name('dd-registered-employees');
Route::match(['get','post'],'dd-unregistered-employees','DashboardController@dd_unregistered_employees')->name('dd-unregistered-employees');
Route::match(['get','post'],'dd-absent-employees','DashboardController@dd_absent_employees')->name('dd-absent-employees');
Route::match(['get','post'],'dd-all-employees','DashboardController@dd_all_employees')->name('dd-all-employees');
Route::match(['get','post'],'dd-os-employees','DashboardController@dd_os_schedule')->name('dd-os-employees');
Route::match(['get','post'],'dd-offdays-employees','DashboardController@dd_offdays_employees')->name('dd-offdays-employees');
Route::match(['get','post'],'dd-leave-employees','DashboardController@dd_leave_employees')->name('dd-leave-employees');
Route::match(['get','post'],'dd-late-employees','DashboardController@dd_late_employees')->name('dd-late-employees');
Route::match(['get','post'],'dd-sna-employees','DashboardController@dd_sna_employees')->name('dd-sna-employees');
Route::match(['get','post'],'dd-attach-employees','DashboardController@dd_attach_employees')->name('dd-attach-employees');

Route::get('graphstats', 'DashboardController@graphStats');
Route::resource('clients','ClientsController');
Route::get('clients-new', 'ClientsController@clientsNew');
Route::get('clients-rebuild', 'ClientsController@rebuild')->name('rebuild');

Route::get('super_admin_reset_acc','ClientsController@clients_account_res_password')
->name('super_admin_reset_acc');
Route::put('client_creadential_update/{id}','ClientsController@update_client_cread')->name('client_creadential_update');

Route::get('client_reset_edit/{id}','ClientsController@client_show')->name('client.reset');
Route::resource('departments','DepartmentsController');
Route::resource('sub_departments','Sub_DepartmentController');

Route::post('leave/get_leaves','UsersController@getEmpLeave')->name('leave.get_leaves');
Route::resource('roles','RolesController');
Route::resource('jurs','JurisdictionsController');
Route::get('assign_right/{id}','RolesController@assign_right');
Route::resource('designations','DesignationsController');
Route::resource('holidays','HolidayController');
Route::resource('modules','ModulesController');
Route::resource('modulerights','ModuleRightsController');
Route::resource('timecategories','TimeCategoiresController');
Route::resource('roasters','RoastersController');
Route::resource('roastersstaff','RoasterStaffsController');
Route::resource('leaves','LeavesController');
Route::resource('leaverequests','LeaveRequestsController');
Route::resource('attachemployees','AttachEmployeesController');
Route::post('storeassignright','AssignRightsController@add_assign_right');

Route::post('users/checkid','UsersController@check_id')->name('users.checkid');
Route::post('emp/check_file_no','UsersController@check_personal_file_no')->name('emp.check_file_no');
Route::post('users/cat_name','UsersController@getCatName')->name('users.cat_name');
Route::post('users/fetch_dep','UsersController@fetch_department')->name('users.fetch_dep');
Route::get('emp/filter_emp','UsersController@filterEmployee')->name('emp.filter_emp');
Route::get('empreg/step1/{id?}','UsersController@step1')->name('empreg.step1');
Route::post('sub_deparments','UsersController@subDeparments')->name('sub_deparments');
Route::post('empreg/step1_store','@step1_store')->name('empreg.step1_store');
Route::post('dept/bulk_tc_update','UsersController@bulk_tc_update')->name('dept.bulk_tc_update');
Route::post('users/user_tc_update','UsersController@user_tc_update')->name('user_tc_update');


Route::get('employee/basic_info/{id?}','UsersController@basic_info_create')->name('employee.basic_info');
Route::post('employee/basic_info_store','UsersController@basic_info_store')->name('employee.basic_info_store');
Route::get('employee/dep_info_create/{id?}','UsersController@dep_info_create')->name('employee.dep_info_create');
Route::put('employee/dep_info_store/{id}','UsersController@dep_info_store')->name('employee.dep_info_store');
Route::post('employee/leave_config/{id}','UsersController@employee_leave_configuration')->name('employee.leave_config');
Route::post('employee/leave_config_detail/{id}','UsersController@employee_leave_configuration_detail')->name('employee.leave_config_detail');
Route::post('employee/leave_config_delete/{id}','UsersController@employee_leave_configuration_delete')->name('employee.leave_config_delete');
//Route::post('employee/dep_info','UsersController@dep_Info')->name('employee.dep_info');
Route::get('employee/dep_form','UsersController@dep_form')->name('employee.dep_form');
Route::get('employee/roster_create/{id?}','UsersController@roster_create')->name('employee.roster_create');
Route::put('employee/roster_store/{id}','UsersController@roster_store')->name('employee.roster_store');
Route::get('employee/contact_create/{id?}','UsersController@contact_create')->name('employee.contact_create');
Route::put('employee/contact_store/{id}','UsersController@contact_store')->name('employee.contact_store');


//mark attendance routes disabled
// Route::get('marks_attendance','UsersController@MarkAttendance_index')->name('marks_attendance');
// route::get('marks_attendance/mark_attendance/{id}','userscontroller@mark_attendances')->name('mark.attendance');
// route::post('marks_attendance/markattendance','userscontroller@stor_markattedance')->name('markattendance.store');
Route::post('employee/search', 'userscontroller@search')->name('employee.search');
// Route::get('roster_filter/{id}','RoastersController@filterRoaster')->name('roaster.roster_filter');
Route::get('roasterassign/{id}','RoastersController@roaster_assign')->name('roaster.assigntable');
Route::get('copy_to_next_month/{id}','RoastersController@copy_to_next_month')->name('roaster.copy_to_next_month');
Route::match(['get','post'],'report','ReportsController@reports')->name('report');
Route::get('report_excel','ReportsController@excel');
Route::get('datewise_attendance_compliance','ReportsController@date_wise_attendance_compliance_report')->name('date_wise_attendance_compliance_report');
Route::match(['get','post'],'datewise_attendance_compliance','ReportsController@date_wise_attendance_compliance_report')->name('datewise_attendance_compliance');
Route::match(['get','post'],'employee_leave_reports','LeaveReportsController@leave_reports')->name('employee_leave_reports');
Route::post('view_leave_report','LeaveReportsController@viewLeave')->name('view_leave_report');
//Route::get('approve_leave/{id}','LeaveRequestsController@leave_approved');
Route::get('leave_request/{id}','LeaveRequestsController@leave_approved');
Route::post('leave_request/load_employees','LeaveRequestsController@getEmployee')->name('leave_request.load_employees');

Route::post('leave/leave_type','LeaveRequestsController@fetch_leave_type')->name('leave.leave_type');
Route::post('employee/fetch_total_leave','LeaveRequestsController@fetch_total_leave')->name('employee.fetch_total_leave');
Route::post('leave/leave_name','LeavesController@getleaveName')->name('leave.leave_name');
Route::post('leave/get_leaves','UsersController@getEmpLeave')->name('leave.get_leaves');

Route::get('leave/leave_revert/{id}','LeaveRequestsController@leave_revert')->name('leave.leave_revert');
route::post('levae_report_employee','leavereportscontroller@leave_report_data')->name('leave.report_employee');
Route::get('leave_reject/{id}','LeaveRequestsController@leave_reject');
Route::get('attach_approvel/{id}','AttachEmployeesController@attach_approvel');
Route::get('attach_rejected/{id}','AttachEmployeesController@attach_rejected');
Route::post('rosters/assign_time','RoastersController@assigntime')->name("rosters.assigntime");
Route::post('rosters/roster_detail','RoastersController@roster_detail')->name("rosters.rosterdetail");
Route::post('rosters/assign_time','RoastersController@assigntime')->name("rosters.assigntime");
Route::post('rosters/roster_delete','RoastersController@roster_delete')->name("rosters.rosterdelete");
Route::post('rosters/leave_delete','RoastersController@roster_leave_delete')->name("rosters.leavedelete");
Route::post('rosters/save_roster','RoastersController@save_roster')->name("rosters.save_roster");
Route::get('monthly_report/{id}','ReportsController@monthly_report')->name("monthly_report");
Route::match(['get','post'],'individual_report','IndividualReportController@reports')->name('individual_report');
Route::match(['get','post'],'individual_payroll','IndividualPayrollController@reports')->name('individual_payroll');
Route::post('individual/fetch_users','IndividualReportController@fetch_user_for_dep')
->name('individual.fetch_users');
Route::post('bonus/fetch_users','EmployeeBonusController@fetch_user_for_dep')
->name('bonus.fetch_users');
Route::post('individual_payroll/fetch_users','IndividualPayrollController@fetch_user_for_dep')
->name('individual_payroll.fetch_users');
Route::get('allemployees','UsersController@allEmployees')->name('allemployees');
Route::get('reg_employees','UsersController@RegisteredEmployees')->name('reg_employees');

Route::get('allemployees/edit/{id}','UsersController@showEmployeesTag')->name('allemployees_editshow');
Route::put('allemployees/edit/{id}','UsersController@editEmployeesTag')->name('allemployees_edit');


Route::get('employee_report','UsersController@employeeReport')->name('employee_report');
Route::get('employee_report2','UsersController@employeeYearlyReport')->name('employee_report2');
    Route::get('employee_daily_report/{user_id}','UsersController@monthlyReport')->name('employee_daily_report');

Route::match(['get','post'],'monthly_detailed_att_report','Monthly_Detailed_Report@reports')->name('monthly_det_rep');
//new report bc the existing one is not optimized
Route::match(['get','post'],'monthly_details_attendance','Monthly_Detailed_Report@monthly_details_attendance')->name('monthly_details_attendance');
Route::match(['get','post'],'monthly_payroll_report','MonthlyPayrollContoller@monthly_payroll')->name('monthly_payroll_report');


Route::match(['get','post'],'monthly_payroll','MonthlyPayrollContoller@reports')->name('monthly_payroll');
Route::match(['get','post'],'forgot-password','UsersController@forgotPassword');
Route::resource('regions','RegionsController');
Route::resource('zones','ZonesController');
Route::resource('branchs','BranchsController');
Route::resource('devices','DevicesController');
Route::resource('employees_bonuses','EmployeeBonusController');
Route::resource('day_off','EmployeeDedicatedDayOffController');
Route::resource('deductions','DeductionsController');
Route::resource('allowances','AllowancesController');
Route::match(['get','post'],'payroll','PayrollController@report')->name('payroll');
Route::post('emps_dedicated_day_off_creation','EmployeeDedicatedDayOffController@employeesDedicatedDayOff')
->name('emps_dedicated_day_off_creation');

Route::get('inactives','UsersController@inactiveEmployee')->name('inactives');
Route::get('users/inactive_edit/{id}','UsersController@inactive_edit')->name('users.inactive_edit');
Route::post('fetch/employiesByDep','UsersController@empByDepartment')->name('fetch.emp-by-dep');
Route::get('active_employee/{id}','UsersController@active_employee')->name('active_employee');
Route::get('employee/absent_employee','DashboardDetailController@absent_employee')->name('employee.absent_employee');
Route::get('employee/present_employee','DashboardDetailController@present_employee')->name('employee.present_employee');
Route::get('employee/late_employee','DashboardDetailController@late_employee')->name('employee.late_employee');
Route::get('employee/employee_leave','DashboardDetailController@employee_leave')->name('employee.employee_leave');
Route::get('employee/employee_short_leave','DashboardDetailController@short_leave')->name('employee.employee_short_leave');
Route::get('employee/employee_attach','DashboardDetailController@employee_attach')->name('employee.employee_attach');
Route::get('employee/employee_coperator','DashboardDetailController@employee_computer_operator')->name('employee.employee_coperator');
Route::get('employee/employee_director','DashboardDetailController@employee_director')->name('employee.employee_director');
Route::get('employee/deputy_director','DashboardDetailController@employee_dep_director')->name('employee.deputy_director');
Route::get('employee/field_staff','DashboardDetailController@field_staff')->name('employee.field_staff');

Route::put('inactive_update/{id}','UsersController@inactive_update')->name('inactive_update');
});
Route::match(['get','post'],'forgot-password','UsersController@forgotPassword');


//Regions Route



Route::get('client_summary','DashboardController@client_summary')->name('client_summary');
Route::get('client_summary_aims','DashboardController@client_summary_aims')->name('client_summary_aims');
Route::get('client_summary_sbm','DashboardController@client_summary_sbm')->name('client_summary_sbm');
Route::get('client_summary_ajkitb','DashboardController@client_summary_ajkitb')->name('client_summary_ajkitb');
Route::match(['get','post'],'email_attendance_status_report','EmailAttendanceController@emailAttendanceStatusReport')->name('email_attendance_status_report');





Route::get('dashboard-manager','DMController@dashboard')->name('dashboard-manager');
Route::get('employees-list','DMController@employees')->name('employees-list');



Route::get('sync-attendance','AttendanceController@sync_attendance')->name('sync-attendance');
Route::get('generate-clients-tree','AttendanceController@generate_clients_tree')->name('gct');
