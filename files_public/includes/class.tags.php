<?php
class Tags {
    private $tags_table = "tags";
    private $posts_tag_link_table  = "posts_tag_link";
    private $events_tag_link_table = "events_tag_link";
    private $threads_tag_link_table = "threads_tag_link";

    



/*************************************************************************************
      FETCH METHODS
*************************************************************************************/
    
    public function count_tagged($type="", $tag_id=0) {
        global $Database;
  
        $tag_id = (int)$Database->clean_data($tag_id);
        $type   = $Database->clean_data($type);
        $table  = null;
        
        switch ($type) {
            case 'posts':
                $table = $this->posts_tag_link_table;
                break;
            case 'events':
                $table = $this->events_tag_link_table;
                break;
            case 'threads':
                $table = $this->threads_tag_link_table;
                break;
            
            default:
                break;
        }

        if (!$table) return 0;

        $sql    = "SELECT COUNT(*) AS num FROM ".$table." WHERE tag_id_fk = '{$tag_id}'";
        $result = $Database->query($sql);
        return (int)$Database->fetch_data($result)->num;  
    }

    public function fetch_tag_id_with_name($tag_name="") {
        global $Database;
  
        $tag_name = strtolower($Database->clean_data($tag_name));

        $sql = "SELECT tag_id FROM ".$this->tags_table." WHERE tag_name = '{$tag_name}' LIMIT 1";
        $result = $Database->query($sql);

        if ($Database->num_rows($result) != 1) return false;
        return $Database->fetch_data($result)->tag_id;  
    }

    public function fetch_tagged($type="", $tag_name="") {
        global $Database;
  
        $type   = $Database->clean_data($type);
        $tag_id = $this->fetch_tag_id_with_name($tag_name);
        if (!$tag_id) return false; // if no item was tagged under this tag, name returns false;

        $sql    = null;

        switch ($type) {
            case 'posts':
                $sql = "SELECT post_id_fk AS item_id FROM ".$this->posts_tag_link_table." WHERE tag_id_fk = '{$tag_id}'";
                break;
            case 'events':
                $sql = "SELECT event_id_fk AS item_id FROM ".$this->events_tag_link_table." WHERE tag_id_fk = '{$tag_id}'";
                break;
            case 'threads':
                $sql = "SELECT thread_id_fk AS item_id FROM ".$this->threads_tag_link_table." WHERE tag_id_fk = '{$tag_id}'";
                break;
     
            default:
                //do nothing
                break;
        }

        if (!$sql) return false;

        $result = $Database->query($sql);
        if ($Database->num_rows($result) < 1) return false;
        
        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row->item_id;
        }
        return $output;
    }

    //Fetch a post tags using it's post id
    public function sort_fetch($type="") {
        global $Database;
  
        $type = $Database->clean_data($type);
        $sql = "SELECT * FROM ".$this->tags_table." WHERE type = '{$type}' ORDER BY tag_name ASC";
        $result = $Database->query($sql);

        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return (count($output) > 0) ? $output : false;
    }

    //fetch tag ids form tag_link table (used to fetch tag names form tags table)
    public function fetch_tags_ids($type="post", $id=0) {
        global $Database;

        $id   = (int)$Database->clean_data($id);
        $type = $Database->clean_data($type);
        $sql  = null;
        switch ($type) {
        	case 'post':
        		$sql = "SELECT * FROM ".$this->posts_tag_link_table." WHERE post_id_fk = '{$id}'";
        		break;

        	case 'event':
        		$sql = "SELECT * FROM ".$this->events_tag_link_table." WHERE event_id_fk = '{$id}'";
        		break;
            case 'threads':
                $sql = "SELECT * FROM ".$this->threads_tag_link_table." WHERE thread_id_fk = '{$id}'";
                break;
        	
        	default:
        		$sql = false;
        		break;
        }
        if(!$sql) return array(); //returns an empty array if type is not valid

        $result = $Database->query($sql);
        if($Database->num_rows($result) < 1) return false;

        $ids = array();
        while ($row = $Database->fetch_data($result)) {
            $ids[] = $row->tag_id_fk;
        } 
        return $ids; /// returns an array of tag IDS for item type
    }  

    //Fetch a post tags using it's post id
    public function fetch_tags($type="", $id=0) {
        global $Database;
  
        $ids = $this->fetch_tags_ids($type, $id);

        $loop_count = count($ids);
        if($loop_count < 1) return false;

        //use tag IDS to fetch tag names
        $sql = "SELECT * FROM ".$this->tags_table." WHERE tag_id = '0'";
        for($i=0; $i < $loop_count; $i++) {
        	$tag_id = $ids[$i];
         	$sql .= " OR tag_id = '{$tag_id}'";
        }
        $sql .= " ORDER BY tag_name ASC";

        $result = $Database->query($sql);

        $output = array();
        while ($row = $Database->fetch_data($result)) {
        	$output[] = $row;
        }
        return (count($output) > 0) ? $output : false;
    }
}

$Tags = new Tags();
?>