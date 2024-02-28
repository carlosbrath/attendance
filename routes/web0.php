<?php

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
Route::get('form-wizard', function () {
    return view('form-wizard');
});
Route::get('/', function () {
    return view('Login.login');
});
Route::get('date', function () {
    return view('form-component');
});

Route::get('table', function () {
    return view('table');
});



Auth::routes();
Route::get('login', 'LoginController@index');
Route::post('loginaction', 'LoginController@loginaction');
Route::group(['middleware' => 'checkuser'], function () {
Route::get('logout', 'LoginController@logout'); 
// Route::resource('users','UsersController');
Route::resource('users','UsersController');
Route::get('dashboard','DashboardController@dashboard');
Route::get('graphstats', 'DashboardController@graphStats');

Route::resource('clients','ClientsController');
Route::resource('departments','DepartmentsController');
Route::resource('roles','RolesController');
Route::get('assign_right/{id}','RolesController@assign_right');

Route::resource('designations','DesignationsController');
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
// Route::get('roster_filter/{id}','RoastersController@filterRoaster')->name('roaster.roster_filter');
Route::get('roasterassign/{id}','RoastersController@roaster_assign')->name('roaster.assigntable');
Route::match(['get','post'],'report','ReportsController@reports')->name('report');
Route::get('report_excel','ReportsController@excel');
Route::get('leavereports','LeaveReportsController@getLeaves')->name('leavereports');
Route::post('view_leave_report','LeaveReportsController@viewLeave')->name('view_leave_report');
//Route::get('approve_leave/{id}','LeaveRequestsController@leave_approved');
Route::get('leave_request/{id}','LeaveRequestsController@leave_approved');
Route::get('leave_reject/{id}','LeaveRequestsController@leave_reject');
Route::get('attach_approvel/{id}','AttachEmployeesController@attach_approvel');
Route::get('attach_rejected/{id}','AttachEmployeesController@attach_rejected');
Route::post('rosters/assign_time','RoastersController@assigntime')->name("rosters.assigntime");
Route::post('rosters/roster_detail','RoastersController@roster_detail')->name("rosters.rosterdetail");
Route::post('rosters/assign_time','RoastersController@assigntime')->name("rosters.assigntime");
Route::post('rosters/roster_delete','RoastersController@roster_delete')->name("rosters.rosterdelete");
Route::post('rosters/save_roster','RoastersController@save_roster')->name("rosters.save_roster");
Route::get('monthly_report/{id}','ReportsController@monthly_report')->name("monthly_report");
Route::match(['get','post'],'individual_report','IndividualReportController@reports')->name('individual_report');
Route::match(['get','post'],'monthly_detailed_att_report','Monthly_Detailed_Report@reports')->name('monthly_det_rep');
Route::match(['get','post'],'forgot-password','UsersController@forgotPassword');
Route::resource('regions','RegionsController');
Route::resource('zones','ZonesController');
Route::resource('branchs','BranchsController');
Route::resource('devices','DevicesController');
Route::get('inactives','UsersController@inactiveEmployee')->name('inactives');
Route::get('inactive_edit/{id}','UsersController@inactive_edit')->name('users.inactive_edit');
Route::get('active_employee/{id}','UsersController@active_employee')->name('active_employee');
Route::put('inactive_update/{id}','UsersController@inactive_update')->name('inactive_update');
});

Route::match(['get','post'],'forgot-password','UsersController@forgotPassword');


//Regions Route






