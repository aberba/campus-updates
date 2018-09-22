<?php
class Events {
   private $events_table = "events";
   private $events_images_table = "events_images";
   private $users_table  = "users";

   private $image_one = null;
   private $image_two = null;
   private $image_three = null;


   private $allowed_tags = "<a><p><hr><br><em><strong><ul><li><table><tr><th><td><tbody><h1><h2><h3><h4><h5><h6><span><pre><code>";

   public function count_all() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->events_table;
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
   }

    public function count_published() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->events_table." WHERE publish = '1'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function count_unpublished() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->events_table." WHERE publish = '0'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function count_confirmed() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->events_table." WHERE confirmed = '1'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function count_unconfirmed() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->events_table." WHERE confirmed = '0'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }



   public function fetch_all() {
       global $Database, $Dates;

       $sql  = "SELECT * FROM ".$this->events_table." E LEFT JOIN ";
       $sql .= $this->events_images_table." EI ON E.event_id = EI.event_id_fk ";
       $sql .= "LEFT JOIN ".$this->users_table." U ON E.added_by = U.user_id ";
       $sql .= "ORDER BY E.date_added DESC";
       $result = $Database->query($sql);

       if($Database->num_rows($result) < 1) return false;

       $output = array();
       while ($row = $Database->fetch_data($result)) {
       	   $row->date_added = $Dates->date_with_time($row->date_added);
       	   $output[] = $row;
       }

       return $output;
   }  

    public function find_by_id($event_id=0) {
       global $Database, $Dates;

       $event_id = (int)$Database->clean_data($event_id);

       $sql  = "SELECT * FROM ".$this->events_table." E LEFT JOIN ";
       $sql .= $this->events_images_table." EI ON E.event_id = EI.event_id_fk ";
       $sql .= "LEFT JOIN ".$this->users_table." U ON E.added_by = U.user_id ";
       $sql .= "WHERE E.event_id = '{$event_id}' LIMIT 1";

       $result = $Database->query($sql);
       if($Database->num_rows($result) != 1) return false;  

       $row = $Database->fetch_data($result);
       $this->image_one = $row->image_one;
       $this->image_two = $row->image_two;
       $this->image_three = $row->image_three;
       $row->content_formatted = $this->insert_images($row->content);
       
       return $row;
   }      

   public function save($post=null) {
       global $Database, $Session;
       
       $event_id = (int)$Database->clean_data($post['event_id']);
       $title    = $Database->clean_data($post['title']);
       $owner_name = $Database->clean_data($post['owner_name']);
       $contact  = $Database->clean_data($post['contact']);
       $priority = (int)$Database->clean_data($post['priority']);
       $content  = $Database->clean_data($post['content'], $this->allowed_tags);
       $user_id  = $Session->user()['id'];

       if ($priority > 99) return "Priority must be between 0 to 99";

       $sql  = "UPDATE ".$this->events_table." SET title = '{$title}', owner_name = '{$owner_name}', contact = '{$contact}', priority = '{$priority}', content = '{$content}', edited_by = '{$user_id}' ";
       $sql .= "WHERE event_id = '{$event_id}' LIMIT 1";
       return ($Database->query($sql) === true) ? "Event saved successfully": "Ooops!, error saving event";
   }

   public function change_status($event_id=0) {
       global $Database;

       $event_id = (int)$Database->clean_data($event_id);
       $sql = "SELECT publish FROM ".$this->events_table." WHERE event_id = '{$event_id}' LIMIT 1";
       $result = $Database->query($sql);
       $status = $Database->fetch_data($result)->publish;
      
       $new_status = ($status == 1) ? 0 : 1;
       $message    = ($status == 1) ? "Event now hidden from public view" : "Event now shown in public view";

       //Update change
       $sql = "UPDATE ".$this->events_table." SET publish = '{$new_status}' WHERE event_id = '{$event_id}' LIMIT 1";
       return ($Database->query($sql) === true) ? $message : "Oops!, error changing publishing status";
   }

   public function change_confirmation($event_id=0) {
       global $Database;

       $event_id = (int)$Database->clean_data($event_id);
       $sql = "SELECT confirmed FROM ".$this->events_table." WHERE event_id = '{$event_id}' LIMIT 1";
       $result = $Database->query($sql);
       $status = $Database->fetch_data($result)->confirmed;
      
       $new_status = ($status == 1) ? 0 : 1;
       $message    = ($status == 1) ? "Event is now confirmed for public view" : "Event is now unconfirmed";

       //Update change
       $sql = "UPDATE ".$this->events_table." SET confirmed = '{$new_status}' WHERE event_id = '{$event_id}' LIMIT 1";
       return ($Database->query($sql) === true) ? $message : "Oops!, error changing confirmation status";
   }

   public function add($post=null) {
       global $Database, $Session;

       $title   = $Database->clean_data($post['title']);
       $content = $Database->clean_data($post['content']);
       $user_id = $Session->user()['id'];
       $date    = time();
       $expire  = time()+3600*60*60*24;

       $sql  = "INSERT INTO ".$this->events_table." (title, content, owned_by, added_by, edited_by, date_added, date_of_expire) ";
       $sql .= "VALUES ('{$title}', '{$content}', '{$user_id}', '{$user_id}', '{$user_id}', '{$date}', '{$expire}')";
       if(!$Database->query($sql)) return "Oops!, error inserting event information into DB";

       //Insert event record into event images table
       $last_id = $Database->insert_id();
       $sql = "INSERT INTO ".$this->events_images_table." (event_id_fk) VALUES ('{$last_id}')";
       return ($Database->query($sql) === true) ? "Event added successfully" : "Oops!, error adding event";
   }

    public function delete($event_id=0) {
      global $Database, $Uploads;

      $event_id = (int)$Database->clean_data($event_id);
      $event    = $this->find_by_id($event_id);
      $dir      = $Uploads->get_dir("event");

      if(is_file($dir.DS.$event->image_one)) unlink($dir.DS.$event->image_one);
      if(is_file($dir.DS.$event->image_two)) unlink($dir.DS.$event->image_two);
      if(is_file($dir.DS.$event->image_three)) unlink($dir.DS.$event->image_three);
     
      $sql = "DELETE FROM ".$this->events_table." WHERE event_id = '{$event_id}' LIMIT 1";
      $Database->query($sql);
      return ($Database->affected_rows() == 1) ? "Event has been deleted successfully" : "Oops!, error deleting event";
   }

    
    //used to insert images dynamically in post images page
    public function insert_images($content="") {
        //assign the private images
        
        $event_images_path = "/uploads/events/";
        $img1 = "<figure><img class='event-image' src='".$event_images_path . $this->image_one."'   alt='".$this->image_one."' /></figure>";
        $img2 = "<figure><img class='event-image' src='".$event_images_path . $this->image_two."'   alt='".$this->image_two."' /></figure>";
        $img3 = "<figure><img class='event-image' src='".$event_images_path . $this->image_three."' alt='".$this->image_three."' /></figure>";      

        $content = str_replace("@IMAGE1@", $img1, $content);
        $content = str_replace("@IMAGE2@", $img2, $content);
        $content = str_replace("@IMAGE3@", $img3, $content);
        return $content; //return after images are inserted
    }
}

$Events = new Events();
?>