<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('esef_attendance','AttendanceController@esef_attendance')->name('esef_attendance');

Route::post('login', 'UsersController@api_authenticate');
Route::group(['middleware' => ['jwt.verify']], function() {
    
    Route::post('mark_checkin','AttendanceController@mark_checkin')->name('mark_checkin_app');
    Route::post('get_employees_app','AttendanceController@get_employees')->name('get_employees_app');
    Route::post('user_dashboard', 'UsersController@user_dashboard');
	Route::post('individual_report_api','IndividualReportController@individual_report_api')->name('individual_report_api');
});









Route::get('leaverequest','Api\LeaveRequestController@index');


