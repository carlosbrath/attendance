<?php

namespace App\Exports;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReportExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	

    	$date= date('Y-m-d');
      $attandance_reports = DB::select( DB::raw("select level2.* , CASE WHEN level2.attendance_time IS NULL THEN 'Absent' ELSE IF(level2.attendance_time>level2.time_in,'Late','Present') END as Emp_Status from (SELECT

     temp.*
                                    FROM
                                    (SELECT u.id,u.name,clients.name as deparment_name,designations.title ,ADDTIME(CONCAT('".$date."',' ',tcat.time_in),'00:15:00') as time_in,att.datetime as attendance_time from users u 
    LEFT JOIN roaster_staffs rs on u.id = rs.user_id and rs.date = '".$date."'
    LEFT JOIN time_categories tcat on tcat.id = rs.tcat_id 
    LEFT JOIN attendance att on u.id = att.user_id and date(att.datetime) = '".$date."'
    LEFT JOIN designations on designations.id=u.designation_id
    LEFT JOIN clients on clients.id=designations.department_id                            
    order by u.id,datetime) as temp group by id) as level2"));
      return $attandance_reports;
        
    }
}
