<?php
class Tags {
    private $tags_table = "tags";
    private $posts_tag_link_table  = "posts_tag_link";
    private $events_tag_link_table = "events_tag_link";
    private $threads_tag_link_table = "events_tag_link";

    public function fetch_all() {
        global $Database;
  
        $sql = "SELECT * FROM ".$this->tags_table." ORDER BY tag_name ASC";
        $result = $Database->query($sql);

        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return (count($output) > 0) ? $output : false;
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
    private function fetch_tags_ids($type="post", $id=0) {
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
            case 'thread':
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

    //Fetch a post and event tags using it's post id
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

    //Add tag to post, events , etc using type: post, event
    public function add_new($post=null) {
        global $Database;

        $tag_name = $Database->clean_data($post['tag_name']);
        $tag_type = $Database->clean_data($post['tag_type']);

        if (empty($tag_name)) return "Please enter a tag name";
      
        //check for duplication
        $sql = "SELECT * FROM ".$this->tags_table." WHERE tag_name = '{$tag_name}' AND type = '{$type}' LIMIT 1";
        $result = $Database->query($sql);
        if ($Database->num_rows($result) == 1) return "This tag has already been added";

        //insert new tag if not added
        $sql = "INSERT INTO ".$this->tags_table." (type, tag_name) VALUES ('{$tag_type}', '{$tag_name}')";
        return ($Database->query($sql) === true) ? "Tag added successfully!" : "An error occured whilst adding tag";
    }

    //Add tag to post, events , etc using type: post, event
    public function add_to($post=null) {
        global $Database;

        $type    = $Database->clean_data($post['type']);
        $tag_id  = (int)$Database->clean_data($post['tag_id']);
        $item_id = (int)$Database->clean_data($post['item_id']);
        $table   = null;
        $column  = null;

        switch ($type) {
        	case 'post':
        		$table  = $this->posts_tag_link_table;
        		$column = "post_id_fk";
        		break;
        	case 'event':
        		$table  = $this->events_tag_link_table;
        		$column = "event_id_fk";
        		break;
            case 'thread':
                $table  = $this->threads_tag_link_table;
                $column = "thread_id_fk";
                break;

        	default:
        		//do nothing
        		break;
        }

        if (!$table || !$column) return "Invalid tag arguments";
  
        //check for duplication
        $sql = "SELECT * FROM ".$table." WHERE tag_id_fk = '{$tag_id}' AND {$column} = '{$item_id}' LIMIT 1";
        $result = $Database->query($sql);
        if ($Database->num_rows($result) >= 1) return "This content has already been tagged with this tag";

        //insert new tag if not added
        $sql = "INSERT INTO ".$table." (tag_id_fk, {$column}) VALUES ('{$tag_id}', '{$item_id}')";
        return ($Database->query($sql) === true) ? "Tag added successfully!" : "An error occured whilst adding tag";
    }

    //ARemove tag from post, events , etc using type: post, event
    public function remove_from($post=null) {
        global $Database;

        $type    = $Database->clean_data($post['type']);
        $tag_id  = (int)$Database->clean_data($post['tag_id']);
        $item_id = (int)$Database->clean_data($post['item_id']);
        $table   = null;
        $column  = null;

        switch ($type) {
        	case 'post':
        		$table  = $this->posts_tag_link_table;
        		$column = "post_id_fk";
        		break;
        	case 'event':
        		$table  = $this->events_tag_link_table;
        		$column = "event_id_fk";
        		break;
            case 'thread':
                $table  = $this->threads_tag_link_table;
                $column = "thread_id_fk";
                break;

        	default:
        		//do nothing
        		break;
        }

        if (!$table || !$column) return "Invalid tag arguments";
  
        //reomove tag form tag link: post or event
        $sql = "DELETE FROM ".$table." WHERE tag_id_fk = '{$tag_id}' AND {$column} = '{$item_id}' LIMIT 1";
        return ($Database->query($sql) === true) ? "Tag removed successfully!" : "An error occured whilst removing tag";
    }
    
    public function update_tag_name($post=null) {
        global $Database;

        $tag_id   = (int)$Database->clean_data($post['tag_id']);
        $tag_name = $Database->clean_data($post['value']);

        //check for duplication
        $sql = "SELECT * FROM ". $this->tags_table ." WHERE tag_name = '{$tag_name}' LIMIT 1";
        $result = $Database->query($sql);
        if ($Database->num_rows($result) == 1) return "This tag has already been added";

        $sql = "UPDATE ". $this->tags_table ." SET tag_name = '{$tag_name}' WHERE tag_id = '{$tag_id}' LIMIT 1";
        return ($Database->query($sql) === true) ? "Tag name updated successfully!" : "Oops! error saving tag name";
    }

    public function delete_tag($tag_id=0) {
        global $Database;

        $tag_id = (int)$Database->clean_data($tag_id);

        //deny delete if tag is in use
        $sql = "SELECT * FROM ". $this->posts_tag_link_table ." WHERE tag_id_fk = '{$tag_id}' LIMIT 1";
        $result = $Database->query($sql);
        if ($Database->num_rows($result) == 1) return "Tag is in use under posts";

        //deny delete if tag is in use
        $sql = "SELECT * FROM ". $this->events_tag_link_table ." WHERE tag_id_fk = '{$tag_id}' LIMIT 1";
        $result = $Database->query($sql);
        if ($Database->num_rows($result) == 1) return "Tag is in use under events";

        $sql = "DELETE FROM ". $this->tags_table ." WHERE tag_id = '{$tag_id}' LIMIT 1";
        return ($Database->query($sql) === true) ? "Tag removed successfully!" : "Oops! error removing tag";
    }
    
}

$Tags = new Tags();
?>