<?php
use App\TimeCategory;
if (!function_exists("checkCnicValidityExpiry")) {
    function checkCnicValidityExpiry($issueDate=null, $expiryDate=null)
    {
        $currentDate=date('Y-m-d'); 
        if($issueDate <  $currentDate &&   $expiryDate >   $currentDate ){
              $days = calculatedDays($currentDate,$expiryDate);
             if($days<31){
                echo '<span class="badge badge-danger">'.$days.' days </span>';
             }else{
                echo '<span class="badge badge-success">Valid</span>';
             } 
        }
        elseif(is_null($issueDate) && is_null($expiryDate)){
            return '--';
        }
        else{
            echo '<span class="badge badge-danger"><i class="fa fa-i"></i> Expired</span>';
        }     
    }
}
if (!function_exists("checkEmployeeAttachmentExpireValidity")) {
    function checkEmployeeAttachmentExpireValidity($issueDate=null, $expiryDate=null)
    {
        
        $currentDate=date('Y-m-d'); 
        if($issueDate <  $currentDate &&   $expiryDate >   $currentDate ){
             return 'red';
        }
        elseif(is_null($issueDate) && is_null($expiryDate)){
            return '';
        }
        else{
            return '';
        }     
    }
}
    if (!function_exists("calculatedDays")) {
    function calculatedDays($currentDate,$expiryDate){
        $your_date = strtotime($currentDate);
        $datediff = strtotime($expiryDate) - $your_date;
        $days= round($datediff / (60 * 60 * 24));
        return $days;
    }
    } 
    if (!function_exists("checkAbsent")) {
        function checkAbsent($reportDate='', $empRegDate=null,$empMinTransaction=null)
        {
            if(!isset($empRegDate)){
                return 'absent';
            }elseif($reportDate <  $empRegDate){
                if($reportDate>$empMinTransaction){
                    return 'absent';
                }else{
                    return '--';
                }
                
            }else{

                return 'absent';
            }      
        }
    }
    if (!function_exists("employeeRegistraionComparision")) {
        function employeeRegistraionComparision($filterDate, $empRegDate)
        {
            if($filterDate <  $empRegDate){
                return true;
            }else{
                return false;
            } 
            
        }
    }
    if (!function_exists("timeCategoryTotalHourWork")) {
        function timeCategoryTotalHourWork($time)
        {
            if($time==0){
                $expected_hours = '00:00:00';
            }else{
                $expected_hours = date('h:i:s', $time);
            }  
            return  $expected_hours;
        }
  }
if (!function_exists("checkAbsentOfMonthlyReport")) {
    function checkAbsentOfMonthlyReport($reportDate='', $empRegDate=null)
    {
        if(!isset($empRegDate)){
            return 'A';
        }elseif($reportDate <  $empRegDate){
            return '--';
        }else{
            return 'A';
        }
       
    }
}
if (!function_exists("checkTimeCategoryHistoryByDate")) {
    function checkTimeCategoryHistoryByDate($data,$date,$transactionData)
    {
      
         return  checkTimeCategoryHistory($data->where('date','<=',$date),$transactionData);
        
    }
}
if (!function_exists("checkTimeCategoryHistory")) {
    function checkTimeCategoryHistory($data,$transactionData)
    {
       
        if($data->count()==1){
            $time_cat_his_info=  $data->first();
            return  $time_cat_his_info; 
           }elseif($data->count()>1){
            $time_cat_his_info= $data->last();
            return  $time_cat_his_info;   
         }else{
           return  $transactionData;
         }
        
         
    }
}
function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}
if (!function_exists("checkTimeCategoryHistoryByDateForMonthy")) {
    function checkTimeCategoryHistoryByDateForMonthy($data,$date,$transaction_data)
    {
        return $transaction_data; 
         return  checkTimeCategoryHistoryforMonthly($data->where('date','<=',$date),$transaction_data);
        
    }
}
if (!function_exists("checkTimeCategoryHistoryforMonthly")) {
    function checkTimeCategoryHistoryforMonthly($data,$transaction_data)
    {
        return $transaction_data;
        if($data->count()==1){
            $time_cat_his_info=  $data->first();
            
            return  $time_cat_his_info; 
           }elseif($data->count()>1){

            $time_cat_his_info= $data->last();
            return  $time_cat_his_info; 
            
         }else{
            return $transaction_data;
         }
        
         
    }
}

if (!function_exists("checkOffDayByTimeCategoryHistory")) {
    function checkOffDayByTimeCategoryHistory($data,$date,$preOffDay)
    {    
       return  checkOffDayByTimeCategory($data->where('date','<=',$date),$preOffDay);     
    }
}
if (!function_exists("checkOffDayByTimeCategory")) {
    function checkOffDayByTimeCategory($data,$preOffDay)
    {   
          if($data->count()==1){
            $time_cat_for_offday= $data->first();
           }
           elseif($data->count()>1){
             $time_cat_for_offday=$data->last();
           }else{
            $time_cat_for_offday=$preOffDay;
           }

          return  $time_cat_for_offday;
           
    }
}
if (!function_exists("timeCategoryHistForDashboard")) {
       function timeCategoryHistForDashboard($data,$transaction){
    
        if($data->count()==1){
          $r=$data->first()->tcat_id;
          $records= TimeCategory::withTrashed()->find($r);
         
        }
        elseif($data->count()>1){  
          $r=$data->latest('join_date')->first()->tcat_id;
          $records= TimeCategory::withTrashed()->find($r);
        }else{
          $records=$transaction;
        }
        return $records;

    }
 }

 if (!function_exists("checkHolidaycandarExistByDepOrEmp")) {
    function checkHolidaycandarExistByDepOrEmp($holidayCalendar,$attendanceData){
       if($holidayCalendar->count()>0){
           
            if($holidayCalendar->where('user_id','!=','')->where('user_id',$attendanceData['user_id'])->count()>0){
              
                 return 'Holiday :: '.$holidayCalendar->first()->title;
                
                 
             }elseif($holidayCalendar->where('user_id','==','')->where('department_name',$attendanceData['department_name'])->count()>0){
               
                return 'Holiday ::'.$holidayCalendar->first()->title;
            }
            else{
               return $attendanceData['status'];
            }
       }else{
         return $attendanceData['status'];
       }
        

    //    return $arr = array_filter($holidayCalendar, 'filter_callback');
       

    }
}

if (!function_exists("filter_callback")) {
    function filter_callback($element){
      
        if (isset($element) && $element->user_id == null) {
            return TRUE;
          }
          return FALSE;
    }
}

if (!function_exists("monthly_attendance_status_report")) {
    function monthly_attendance_status_report($time_category_history,$dt_attendance){
        if($time_category_history->count()==1){
            $time_cat_his_info=  $time_category_history->first();
            return  $time_cat_his_info; 
           }elseif($time_category_history->count()>1){
            $time_category_history= $time_category_history->last();
            return  $time_category_history; 
         }else{
            return $dt_attendance;
         }
        
    }
}
if (!function_exists("get_child_department")) {
    function get_child_department($id){
        return \App\Client::withCount('employees')->where('parent_id',$id)->get();
    }
}


 
?>