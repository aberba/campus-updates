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
}

$Dates = new Dates();
?>