<?php
class Forum {
    private $forum_table            = "forum";
    private $threads_table          = "forum_threads";
    private $threads_comments_table = "forum_threads_comments";
    private $users_table            = "users";
    private $tags_table             = "tags";
    private $threads_tag_link_table = "threads_tab_link";

/********************************************************************************
    COUNT METHODS
**********************************************************************************/
    public function count_all() {
    	global $Database;

    	$sql = "SELECT COUNT(*) AS num FROM ". $this->threads_table;
    	$result = $Database->query($sql);
    	return (int)$Database->fetch_data($result)->num;
    }

    public function count_threads($forum_id=0) {
    	global $Database;

    	$forum_id = (int)$Database->clean_data($forum_id);

    	$sql = "SELECT COUNT(*) AS num FROM ". $this->threads_table . " WHERE forum_id_fk = '{$forum_id}'";
    	$result = $Database->query($sql);
    	return (int)$Database->fetch_data($result)->num;
    }

    public function count_thread_comments($thread_id=0) {
    	global $Database;

    	$thread_id = (int)$Database->clean_data($thread_id);

    	$sql = "SELECT COUNT(*) AS num FROM ". $this->threads_comments_table . " WHERE thread_id_fk = '{$thread_id}'";
    	$result = $Database->query($sql);
    	return (int)$Database->fetch_data($result)->num;
    }


/********************************************************************************
    READ METHODS
**********************************************************************************/
    
	public function find_forum_by_id($forum_id=0) {
		global $Database;

        $forum_id = (int)$Database->clean_data($forum_id);

		$sql = "SELECT * FROM ".$this->forum_table." WHERE forum_id = '{$forum_id}' LIMIT 1";
		$result = $Database->query($sql);

		if ($Database->num_rows($result) != 1) return false;
		return $Database->fetch_data($result);
	}

	public function find_thread_by_id($thread_id=0) {
		global $Database;

        $thread_id = (int)$Database->clean_data($thread_id);

		$sql = "SELECT * FROM ".$this->threads_table." WHERE thread_id = '{$thread_id}' LIMIT 1";
		$result = $Database->query($sql);

		if ($Database->num_rows($result) != 1) return false;
		return $Database->fetch_data($result);
	}

	public function fetch_all_forums() {
		global $Database;

		$sql = "SELECT * FROM ".$this->forum_table." ORDER BY name ASC";
		$result = $Database->query($sql);

		if ($Database->num_rows($result) < 1) return false;
		$output = array();

		while ($row = $Database->fetch_data($result)) {
			   $output[] = $row;
		}

		return $output;
	}

	public function fetch_threads($forum_id=0, $offset=0, $per_page=10) {
		global $Database;

		$forum_id = (int)$Database->clean_data($forum_id);
		$offset   = (int)$Database->clean_data($offset);
		$per_page = (int)$Database->clean_data($per_page);

		$sql  = "SELECT * FROM ". $this->threads_table. " T " ;
		$sql .= "LEFT JOIN ". $this->forum_table . " F ON T.forum_id_fk = F.forum_id ";
		$sql .= "LEFT JOIN ". $this->users_table ." U ON U.user_id = T.added_by WHERE T.forum_id_fk = '{$forum_id}' ";
		$sql .= "ORDER BY T.date_added DESC LIMIT {$per_page} OFFSET {$offset}";
		$result = $Database->query($sql);

		if ($Database->num_rows($result) < 1) return false;
		$output = array();

		while ($row = $Database->fetch_data($result)) {
			   $sql2 = "SELECT COUNT(*) AS num FROM ". $this->threads_comments_table. " WHERE thread_id_fk = '".$row->thread_id."'";
			   $res  = $Database->query($sql2);
			   
			   $row->num_comments = $Database->fetch_data($res)->num;  
			   $output[] = $row;
		}

		return $output;
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

	    //$sql  = "SELECT * FROM ".$this->events_table." E LEFT JOIN ";
	    //$sql .= $this->events_images_table." EI ON E.event_id = EI.event_id_fk ";
	   // $sql .= "LEFT JOIN ".$this->users_table." U ON E.added_by = U.user_id ";
	   // $sql .= "WHERE E.event_id = '0'";
	    
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


/*************************************************************************
         ADD METHODS
*************************************************************************/
	public function add_forum($post=null) {
	   global $Database;

	   $title = $Database->clean_data($post['title']);
	   $desc  = $Database->clean_data($post['description']);
	} 

	public function add_thread($post=null, $files=null) {
		global $Database, $Session;
	   
		$title = $Database->clean_data($post['title']);
		$desc  = $Database->clean_data($post['description']);
		$user_id = $Session->user()['id'];
		$date  = time();

		$sql = "INSERT INTO (title, description, added_by, date_added) VALUES ";
		$sql .= "('{$title}', '{$desc}', '{$user_id}', '{$date}')";

		return ($Database->query($sql) === true) ? "Forum category added successfully!" : "Oops! an error occured whilst adding forum category";
	} 

	public function add_comment($post=null) {
	   global $Database;

	   $title = $Database->clean_data($post['title']);
	   $desc  = $Database->clean_data($post['description']);
	} 


/*************************************************************************
         UPDATE FUNCTIONS
*************************************************************************/
	public function update_forum($post=null, $files=null) {
	   global $Database;

	   $title = $Database->clean_data($post['title']);
	   $desc  = $Database->clean_data($post['description']);
	} 

	public function update_thread($post=null, $files=null) {
	   global $Database;

	   $title = $Database->clean_data($post['title']);
	   $desc  = $Database->clean_data($post['description']);
	} 

	public function update_comment($post=null, $files=null) {
	   global $Database;

	   $title = $Database->clean_data($post['title']);
	   $desc  = $Database->clean_data($post['description']);
	} 
   
/*************************************************************************
         REMOVE FUNCTIONS
*************************************************************************/
	public function remove_forum($forum_id=0) {
	   global $Database;

	   $title = $Database->clean_data($post['title']);
	   $desc  = $Database->clean_data($post['description']);
	} 

	public function remove_thread($thread_id=0) {
	   global $Database;

	   $title = $Database->clean_data($post['title']);
	   $desc  = $Database->clean_data($post['description']);
	} 

	public function remove_comment($comment_id=0) {
	   global $Database;

	   $title = $Database->clean_data($post['title']);
	   $desc  = $Database->clean_data($post['description']);
	}   
}

$Forum = new Forum();
?>