<?php
class UsersUploads extends Uploads {
   private $users_uploads_table      = "users_uploads";
   private $uploads_categories_table = "users_uploads_categories";
   private $users_table              = "users";

   public function count_all() {
      global $Database;
      
      $sql = "SELECT COUNT(*) AS num FROM ".$this->users_uploads_table;
      $result = $Database->query($sql);
      return $Database->fetch_data($result)->num;
   }

   public function fetch_all() {
       global $Database, $Dates;

       $sql  = "SELECT * FROM ".$this->users_uploads_table." UPL LEFT JOIN ";
       $sql .= $this->users_table." U ON UPL.user_id_fk = U.user_id LEFT JOIN ";
       $sql .= $this->uploads_categories_table." C ON UPL.Category_id_fk = C.category_id ORDER BY UPL.date_added DESC";
       $result = $Database->query($sql);
       if($Database->num_rows($result) < 1) return false;

       $output = array();
       while ($row = $Database->fetch_data($result)) {
       	  $row->date_added = $Dates->date_with_time($row->date_added);
       	  $output[] = $row;
       }
       return $output;
   }    

   public function remove($upload_id=0) {
   	   global $Database, $Logs;

   	   $upload_id = (int)$Database->clean_data($upload_id);

   	   //select upload file an delete
   	   $sql = "SELECT file_name, subject FROM ".$this->users_uploads_table." WHERE upload_id = '{$upload_id}' LIMIT 1";
   	   $result = $Database->query($sql);
   	   $row = $Database->fetch_data($result);
       $file_name = $row->file_name;
       $subject   = $row->subject;

       $file = $this->get_dir("users_uploads").DS.$file_name;
       if(is_file($file)) unlink($file);

       //now delete record from DB
       $sql = "DELETE FROM ".$this->users_uploads_table." WHERE upload_id = '{$upload_id}' LIMIT 1";
       $Database->query($sql);

       //Record Log
       $Logs->log("Deleted a user upload with subject => ". $subject);

       return ($Database->affected_rows() == 1) ? "Upload removed successfully!" : "Ooops!, error removing upload";
   }
}

$UsersUploads = new UsersUploads();
?>
