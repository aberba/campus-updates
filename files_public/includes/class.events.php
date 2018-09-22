<?php
class Events {
   private $events_table = "events";
   private $events_images_table = "events_images";
   private $events_tag_link_table = "events_tag_link";
   private $users_table  = "users";

   //Private declaration of post images
   private $image_one   = null;
   private $image_two   = null;
   private $image_three = null;

   public function count_all() {
        global $Database;

        $sql  = "SELECT COUNT(*) as num FROM ".$this->events_table;
        $sql .= " WHERE publish = '1' AND confirmed = '1'";
        $result = $Database->query($sql);
        return (int)$Database->fetch_data($result)->num;
   }

   public function search($keyword="") {
        global $Database, $Dates;

        $keyword = $Database->clean_data($keyword);
        $sql  = "SELECT * FROM ".$this->events_table." E LEFT JOIN ";
        $sql .= $this->events_images_table." EI ON E.event_id = EI.event_id_fk ";
        $sql .= "LEFT JOIN ".$this->users_table." U ON E.added_by = U.user_id ";
        $sql .= "WHERE E.title LIKE '%{$keyword}%' AND E.publish = '1' AND E.confirmed = '1' ORDER BY E.priority DESC, E.date_added DESC ";
        $sql .= "LIMIT 12";
        $result = $Database->query($sql);

        if($Database->num_rows($result) < 1) return false;

        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $row->date_added = $Dates->date_with_time($row->date_added);
            $output[] = $row;
        }

        return $output;
   }

   public function find_by_tag_name($tag_name="", $limit=6, $offset=0) {
        global $Database, $Tags;

        $event_ids = $Tags->fetch_tagged("events", $tag_name);
        if (!$event_ids) return false;

        $limit  = (int)$limit;
        $offset = (int)$offset;

        $sql  = "SELECT * FROM ".$this->events_table." E LEFT JOIN ";
        $sql .= $this->events_images_table." EI ON E.event_id = EI.event_id_fk ";
        $sql .= "LEFT JOIN ".$this->users_table." U ON E.added_by = U.user_id ";
        $sql .= "WHERE E.event_id = '0'";
        
        foreach ($event_ids as $event_id) {
            $sql .= " OR E.event_id = '{$event_id}'";
        }
        $sql .= " AND E.publish = '1' AND E.confirmed = '1' ORDER BY E.date_added DESC LIMIT {$limit} OFFSET {$offset}";

        $result = $Database->query($sql);

        if($Database->num_rows($result) < 1) return false;
        
        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
   }

   public function fetch_all($offset=0, $records_per_pagination=9) {
       global $Database, $Dates, $Settings;

       $limit  = (int)$Database->clean_data($records_per_pagination);
       $offset = (int)$Database->clean_data($offset);

       $sql  = "SELECT * FROM ".$this->events_table." E LEFT JOIN ";
       $sql .= $this->events_images_table." EI ON E.event_id = EI.event_id_fk ";
       $sql .= "WHERE E.publish = '1' AND E.confirmed = '1' ORDER BY E.priority DESC, E.date_added DESC ";
       $sql .= "LIMIT {$limit} OFFSET {$offset}";
       $result = $Database->query($sql);

       if($Database->num_rows($result) < 1) return false;

       $output = array();
       while ($row = $Database->fetch_data($result)) {
       	   $output[] = $row;
       }
       return $output;
   }  

    // fetch raleted event using event id
   public function fetch_related($event_id=0) {
        global  $Database, $Tags;
         
        $event_id = (int)$Database->clean_data($event_id);
        $tag_ids = $Tags->fetch_tags_ids("event", $event_id);
        if(!$tag_ids) return false;

        //fetch events tagged under these tags from tag_lik
        $sql = "SELECT event_id_fk FROM ".$this->events_tag_link_table." WHERE tag_id_fk = '0'";
        foreach ($tag_ids as $tid) {
            $sql .= " OR tag_id_fk = '{$tid}'";
        }

        $result_ids = $Database->query($sql);
        if($Database->num_rows($result_ids) < 1) return false;

        $related_ids = array();
        while ($row = $Database->fetch_data($result_ids)) {
            $id = (int)$row->event_id_fk;
            if ((int)$id != $event_id) {
                $related_ids[] = $id;
            }
        }

        // now fetch those events ignoring current event 
        $sql  = "SELECT * FROM ".$this->events_table." E LEFT JOIN ";
        $sql .= $this->events_images_table." EI ON E.event_id = EI.event_id_fk ";
        $sql .= "LEFT JOIN ".$this->users_table." U ON E.added_by = U.user_id ";
        $sql .= "WHERE E.event_id = '0'";

        foreach ($related_ids as $pid ) {
           $sql .= " OR E.event_id = '{$pid}'";
        }
        $sql .= " AND E.publish = '1' AND E.confirmed = '1' ORDER BY E.date_added DESC LIMIT 4";

        $result = $Database->query($sql);
        if($Database->num_rows($result) < 1) return false;

        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
   }

   public function fetch_popular($limit=3) {
       global $Database, $Dates, $Settings;

       $limit  = (int)$Database->clean_data($limit);

       $sql  = "SELECT * FROM ".$this->events_table." E LEFT JOIN ";
       $sql .= $this->events_images_table." EI ON E.event_id = EI.event_id_fk ";
       $sql .= "WHERE E.publish = '1' AND E.confirmed = '1' ORDER BY E.priority DESC, E.date_added DESC ";
       $sql .= "LIMIT {$limit}";
       $result = $Database->query($sql);

       if($Database->num_rows($result) < 1) return false;

       $output = array();
       while ($row = $Database->fetch_data($result)) {
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
       $sql .= "WHERE E.publish = '1' AND E.confirmed = '1' AND E.event_id = '$event_id' LIMIT 1";

       $result = $Database->query($sql);
       if($Database->num_rows($result) != 1) return false;  
       $row = $Database->fetch_data($result);

       $this->image_one   = $row->image_one;
       $this->image_two   = $row->image_two;
       $this->image_three = $row->image_three;

       return $row;
   }  

   public function add_reader($event_id=0) {
        global $Database;
        $event_id = (int)$Database->clean_data($event_id);
        $sql = "UPDATE ".$this->events_table." SET num_readers = num_readers + 1 WHERE event_id = '{$event_id}' LIMIT 1";
        $Database->query($sql);
    }

    //used to insert images dynamically in event images page
    public function insert_images($content="") {
        //assign the private images
        
        $event_images_path = "/uploads/events/";
        $img1 = "<img class='event-image' src='".$event_images_path . $this->image_one."'   alt='".$this->image_one."' />";
        $img2 = "<img class='event-image' src='".$event_images_path . $this->image_two."'   alt='".$this->image_two."' />";
        $img3 = "<img class='event-image' src='".$event_images_path . $this->image_three."' alt='".$this->image_three."' />";      

        $content = str_replace("@IMAGE1@", $img1, $content);
        $content = str_replace("@IMAGE2@", $img2, $content);
        $content = str_replace("@IMAGE3@", $img3, $content);
        return $content; //return after images are inserted
    }    
}

$Events = new Events();
?>