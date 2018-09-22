<?php
class Dates {
   public function date_with_time($unix_timestamp="") {
       return date("jS F Y \a\\t H:I  A", $unix_timestamp);
   }

   public function date_only($unix_timestamp="") {
       return date("jS F Y", $unix_timestamp);
   }  

   public function date_abbr($unix_timestamp="") {
       return date("M d, Y", $unix_timestamp);
   }   

    public function gen_mysql_date_format($day, $month, $year) {
       return "{$year}-{$month}-{$day} 00:00:00";
   }

   public function validate_date($day=0, $month=0, $year=0) {
   	    $y = (int)$year;
   	    $m = (int)$month;
   	    $d = (int)$day;

   	    return checkdate($m, $d, $y);
   }  

   public function generate_date_select($current_day=0, $current_month=0, $current_year=0) {
   	   $months = array(1 => "January", 
   	   	               2 => "February",
   	   	               3 => "March",
   	   	               4 => "April",
   	   	               5 => "May",
   	   	               6 => "June",
   	   	               7 => "July",
   	   	               8 => "August",
   	   	               9 => "September",
   	   	               10 => "October",
   	   	               11 => "November",
   	   	               12 => "December");

   	   //Days
       $output = "<div class='date-container'> <select name='day'>";
   	   for ($i = 1; $i < 32; $i++) {
   	   	   if ((int)$current_day == $i) {
              $output .= "<option selected='selected' value='". $i ."'>". $i ."</option>";
           } else {
           	  $output .= "<option value='". $i ."'>". $i ."</option>";
           }
   	   }
   	   $output .= "</select>";

       //Month
   	   $output .= "<select name='month'>";
       foreach ($months as $m => $v) {
       	   if ((int)$current_month == $m) {
       	      $output .= "<option selected='selected' value='". $m ."'>". $v ."</option>";
       	   } else {
       	   	  $output .= "<option value='". $m ."'>". $v ."</option>";
       	   }
       }
       $output .= "</select>";

       //Year
       $output .= "<select name='year'>";
       $y = (int)date("Y", time());
       for ($i = $y; $i < ($y + 5); $i++) {
       	   if ((int)$current_year == $i) {
       	      $output .= "<option selected='selected' value='". $i ."'>". $i ."</option>";
       	   } else {
       	   	   $output .= "<option value='". $i ."'>". $i ."</option>";
       	   }
       }
       $output .= "</select> </div>";
       return $output;
   }
   
}

$Dates = new Dates();
?>