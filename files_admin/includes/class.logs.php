<?php
class Logs {
   private $logs_table = "logs";
   private $users_table = "users";

   public function count_all() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->logs_table;
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

   public function fetch_all() {
   	   global $Database, $Dates;
       $sql  = "SELECT * FROM ".$this->logs_table." L LEFT JOIN ";
       $sql .= $this->users_table." U ON L.user_id_fk = U.user_id ORDER BY L.date_added DESC";
       $result = $Database->query($sql);

       if($Database->num_rows($result) < 1) return false;
       $output = array();
       while ($row = $Database->fetch_data($result)) {
       	   $row->date_added = $Dates->date_with_time($row->date_added);
       	   $output[] = $row;
       }
       return $output;
   }

   public function fetch_recent($limit=5) {
       global $Database, $Dates;

       $limit = (int)$Database->clean_data($limit);

       $sql  = "SELECT * FROM ".$this->logs_table." L LEFT JOIN ";
       $sql .= $this->users_table." U ON L.user_id_fk = U.user_id ORDER BY L.date_added DESC LIMIT {$limit}";
       $result = $Database->query($sql);

       if($Database->num_rows($result) < 1) return false;
       $output = array();
       while ($row = $Database->fetch_data($result)) {
           $row->date_added = $Dates->date_with_time($row->date_added);
           $output[] = $row;
       }
       return $output;
   }

   public function log($message="") {
   	   global $Database, $Session;
       
       $user_id = $Session->user()['id'];
       $message = $Database->clean_data($message);
       $date    = time();

       $sql = "INSERT INTO ".$this->logs_table." (user_id_fk, message, date_added) VALUES ('{$user_id}', '{$message}', '{$date}')";
       $Database->query($sql);
   }

   public function delete($log_id=0) {
       global $Database, $Session;

       $log_id = (int)$Database->clean_data($log_id);
       $sql = "DELETE FROM ".$this->logs_table." WHERE log_id = '{$log_id}' LIMIT 1";
       $Database->query($sql);
       return ($Database->affected_rows() == 1) ? "Log deleted successfully!" : "Oops!, error deleting log";
   }
}

$Logs = new Logs();
?>