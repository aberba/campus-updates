<?php
class Posts {
    private $posts_table             = "posts";
    private $tag_table               = "tags";
    private $posts_images_table      = "posts_images";
    private $posts_tag_link_table    = "posts_tag_link";
    private $posts_attachments_table = "posts_attachments";
    private $users_table             = "users";
    
    //Private declaration of post images
    private $image_one   = null;
    private $image_two   = null;
    private $image_three = null;
    private $image_four  = null;
    private $image_five  = null;
    private $image_six   = null;

    private $allowed_tags = "<a><p><hr><br><em><strong><ul><li><table><tr><th><td><tbody><h1><h2><h3><h4><h5><h6><span><pre><code><figcaption>";
    

    public function count_all() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->posts_table;
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function count_published() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->posts_table." WHERE publish = '1'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function count_unpublished() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->posts_table." WHERE publish = '0'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    // Fetches post record and images only (also used for paginating records using offest param)
    public function fetch_all($offset=0) {
    	global $Database, $Settings;

        $rec_per_page = $Settings->records_per_pagination();
        $offset      = (int)$Database->clean_data($offset);

    	$sql  = "SELECT P.*, PI.image_one, U.* FROM ".$this->posts_table." P, ".$this->posts_images_table." PI, ".$this->users_table." U ";
    	$sql .= "WHERE P.post_id = PI.post_id_fk AND P.added_by = U.user_id ORDER BY P.date_added DESC LIMIT {$rec_per_page} OFFSET {$offset} ";
        $result = $Database->query($sql);
        if($Database->num_rows($result) < 1) return false;
        
        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
    }

    //Fetches one post record, images and author info by post ID
    public function find_by_id($post_id=0) {
    	global $Database, $Dates;
        
        $post_id = (int)$Database->clean_data($post_id);

    	$sql  = "SELECT * FROM ".$this->posts_table." P LEFT JOIN ";
        $sql .= $this->posts_images_table." PI ON P.post_id = PI.post_id_fk LEFT JOIN ";
        $sql .= $this->users_table." U ON P.added_by = U.user_id LEFT JOIN ";
        $sql .= $this->posts_attachments_table." A ON P.post_id = A.post_id_fk ";
        $sql .= "WHERE P.publish = '1' AND P.post_id = '{$post_id}' LIMIT 1";

        $result = $Database->query($sql);
        if($Database->num_rows($result) != 1) return false;
        $row = $Database->fetch_data($result);
        
        //Assing images to private variables
        $this->image_one   = $row->image_one;
        $this->image_two   = $row->image_two;
        $this->image_three = $row->image_three;
        $this->image_four  = $row->image_four;
        $this->image_five  = $row->image_five;
        $this->image_six   = $row->image_six;

        $row->content_formatted    = $this->insert_images($row->content);
        $row->date_added = $Dates->date_only($row->date_added);
        return $row;
    }

      //used to insert images dynamically in post images page
    public function insert_images($content="") {
        //assign the private images
        
        $post_images_path = "/uploads/posts/";
        $img1 = "<figure><img class='post-image' src='".$post_images_path . $this->image_one."'   alt='".$this->image_one."' /></figure>";
        $img2 = "<figure><img class='post-image' src='".$post_images_path . $this->image_two."'   alt='".$this->image_two."' /></figure>";
        $img3 = "<figure><img class='post-image' src='".$post_images_path . $this->image_three."' alt='".$this->image_three."' /></figure>";      
        $img4 = "<figure><img class='post-image' src='".$post_images_path . $this->image_four."'  alt='".$this->image_four."' /></figure>";        
        $img5 = "<figure><img class='post-image' src='".$post_images_path . $this->image_five."'  alt='".$this->image_five."' /></figure>";
        $img6 = "<figure><img class='post-image' src='".$post_images_path . $this->image_six."'   alt='".$this->image_six."' /></figure>";

        $content = str_replace("@IMAGE1@", $img1, $content);
        $content = str_replace("@IMAGE2@", $img2, $content);
        $content = str_replace("@IMAGE3@", $img3, $content);
        $content = str_replace("@IMAGE4@", $img4, $content);
        $content = str_replace("@IMAGE5@", $img5, $content);
        $content = str_replace("@IMAGE6@", $img6, $content);
        return $content; //return after images are inserted
    }

    //fetch tag ids of a post (used in fetching 'related posts' and 'post tags')
    public function fetch_tag_ids($post_id=0) {
        global $Database;

        $post_id = (int)$Database->clean_data($post_id);
        $sql = "SELECT * FROM ".$this->posts_tag_link_table." WHERE post_id_fk = '{$post_id}'";
        $result = $Database->query($sql);
        if($Database->num_rows($result) < 1) return false;

        $ids = array();
        while ($row = $Database->fetch_data($result)) {
            $ids[] = $row->tag_id_fk;
        } 
        return $ids;
    } 

    //Fetch a post tags using it's post id
    public function fetch_tags($post_id=0) {
        global $Database;
        
        $ids = $this->fetch_tag_ids($post_id); // ID is already sanitized by this methods
        if(!$ids) return false; // confirm post hast tags

        //use tag IDS to fetch tag names
        $sql = "SELECT * FROM ".$this->tag_table." WHERE tag_id = '0'";
        foreach ($ids as $id) {
            $sql .= " OR tag_id = '{$id}'";
        }
        $sql .= " ORDER BY tag_name ASC";
        $result = $Database->query($sql);

        $output = array();
        while ($row = $Database->fetch_data($result)) $output[] = $row;
        return $output;
    }
 
    // fetch raleted post using post id
    public function fetch_related($post_id=0) {
        global  $Database;
         
        $post_id = (int)$Database->clean_data($post_id);
        $tag_ids = $this->fetch_tag_ids($post_id);
        if(!$tag_ids) return false;

        //fetch posts tagged under these tags from tag_lik
        $sql = "SELECT DISTINCT post_id_fk FROM ".$this->posts_tag_link_table." WHERE tag_id_fk = '0'";
        foreach ($tag_ids as $tid) {
            $sql .= " OR tag_id_fk = '{$tid}'";
        }
        $sql .= " AND post_id_fk != '{$post_id}'";

        $result = $Database->query($sql);
        if($Database->num_rows($result) < 1) return false;

        $related = array();
        while ($row = $Database->fetch_data($result)) $related[] = $row->post_id_fk;

        // now fetch those posts and return
        $sql = "SELECT P.post_id, P.title, I.image_one FROM ".$this->posts_table." P, ".$this->posts_images_table." I WHERE post_id = '0'";
        foreach ($related as $pid ) {
           $sql .= " OR post_id = '{$pid}'";
        }
        $sql .= " AND P.publish = '1' ORDER BY P.date_added DESC LIMIT 4";
        $result = $Database->query($sql);
        if($Database->num_rows($result) < 1) return false;

        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
    }

    //Check if comments are blocked on a post
    public function has_comments_blocked($post=0) {
        global $Database;

        $post_id = (int)$Database->clean_data($post_id);

        $sql = "SELECT block_comments as blocked FROM ".$this->posts_table." WHERE post_id = '{$post_id}' LIMIT 1";
        $result = $Database->query($sql);
        if($Database->num_rows($result) != 1) return false;
        return ($Database->fetch_data($result)->blocked == 1) ? true : false;
    }

    //Add new Post
    public function add($post=null) {
        global $Database, $Session, $Logs;
        
        $title    = $Database->clean_data($post['title']);
        $content  = $Database->clean_data($post['content'], $this->allowed_tags);
        $user_id  = $Session->user()['id'];
        $date     = time();

        $sql  = "INSERT INTO ".$this->posts_table." (title, content, edited_by, added_by, date_added) ";
        $sql .= "VALUES ('{$title}', '{$content}', '{$user_id}', '{$user_id}', '{$date}')";
        
        if(!$Database->query($sql)) return "Ooops!, error inserting post into DB";
        
        //insert post id into images table
        $last_id = $Database->insert_id();
        $sql = "INSERT INTO ".$this->posts_images_table." (post_id_fk) VALUES ('$last_id')";


        if(!$Database->query($sql)) return "Ooops!, error inserting post into DB";

        //insert post id into post attachments table
        $sql = "INSERT INTO ".$this->posts_attachments_table." (post_id_fk) VALUES ('$last_id')";

        //Record log
        $Logs->log("Added post => ".$title);
        return ($Database->affected_rows() == 1) ? "Post added successfully" : "Ooops!, error adding post";
    }

     //Add new Post
    public function save($post=null) {
        global $Database, $Logs;
        
        $post_id = (int)$Database->clean_data($post['post_id']);
        $title   = $Database->clean_data($post['title']);
        $owner_name = $Database->clean_data($post['owner_name']);
        $owner_url_address  = $Database->clean_data($post['owner_url_address']);
        $content = $Database->clean_data($post['content'], $this->allowed_tags);
        $date    = time();

        $sql  = "UPDATE ".$this->posts_table." SET title = '{$title}', owner_name = '{$owner_name}', owner_url_address = '{$owner_url_address}', content = '{$content}', date_edited = '{$date}' ";
        $sql .= "WHERE post_id = '{$post_id}' LIMIT 1";
        
        //Record log
        $Logs->log("Edited and saved post => ".$title);
        return ($Database->query($sql) === true) ? "Post saved successfully!" : "Ooops!, error saving post information";
    }


    public function change_publish_status($post_id=0) {
        global $Database, $Logs;

        $post_id = (int)$Database->clean_data($post_id);
        $sql = "SELECT publish, title FROM ".$this->posts_table." WHERE post_id = '{$post_id}' LIMIT 1";
        $result = $Database->query($sql);
        $row = $Database->fetch_data($result);
        $status = $row->publish;
        $title  = $row->title; //for log message
        
        $new_status = ($status == 1) ? 0 : 1;
        $message    = ($new_status == 1) ? "Post is now shown in public": "Post is now hidden from public";
        //insert $new_status
        $sql = "UPDATE ".$this->posts_table." SET publish = '{$new_status}' WHERE post_id = '{$post_id}' LIMIT 1";
        $Database->query($sql);
        
        //Record Log
        $log_msg = ($new_status == 1) ? "Published" : "Unpublished";
        $Logs->log($log_msg. " post => ". $title);

        return ($Database->affected_rows() == 1) ? $message : "Ooops!, an error occured whilst changing post publish status";
    }

    public function change_commenting_status($post_id=0) {
        global $Database, $Logs;

        $post_id = (int)$Database->clean_data($post_id);
        $sql = "SELECT block_comments, title FROM ".$this->posts_table." WHERE post_id = '{$post_id}' LIMIT 1";
        $result = $Database->query($sql);
        $row = $Database->fetch_data($result);
        $status = $row->block_comments;
        $title  = $row->title; //for log message
       
        $new_status = ($status == 1) ? 0 : 1;
        $message    = ($new_status == 1) ? "Comments are now blocked on post": "Comments are now allowed on post";
        //insert new_status
        $sql = "UPDATE ".$this->posts_table." SET block_comments = '{$new_status}' WHERE post_id = '{$post_id}' LIMIT 1";
        $Database->query($sql);

        //Record Log
        $log_msg = ($new_status == 1) ? "Blocked" : "Unblocked";
        $Logs->log($log_msg. " comeent on post; ". $title);

        return ($Database->affected_rows() == 1) ? $message : "Ooops!, an error occured whilst changing post commenting status";
    }

    public function deletePost($post_id=0) {
        global $Database, $Logs;

        $post_id = (int)$Database->clean_data($post_id);

        //select post attached files and images and delete all before deleting post record

        $row = $this->find_by_id($post_id);
        
        //Delete post images
        $image_one = POSTS_DIR.DS.$row->image_one;
        if(is_file($image_one)) unlink($image_one);

        $image_two = POSTS_DIR.DS.$row->image_two;
        if(is_file($image_two)) unlink($image_two);

        $image_three = POSTS_DIR.DS.$row->image_three;
        if(is_file($image_three)) unlink($image_three);

        $image_four = POSTS_DIR.DS.$row->image_four;
        if(is_file($image_four)) unlink($image_four);

        $image_five = POSTS_DIR.DS.$row->image_five;
        if(is_file($image_five)) unlink($image_five);

        $image_six = POSTS_DIR.DS.$row->image_six;
        if(is_file($image_six)) unlink($image_six);


        //Delete Post Attached Files
        $file_one = USERS_UPLOADS_DIR. DS .$row->file_one;
        if(is_file($file_one)) unlink($file_one);

        $file_two = USERS_UPLOADS_DIR. DS .$row->file_two;
        if(is_file($file_two)) unlink($file_two);

        $file_three = USERS_UPLOADS_DIR. DS .$row->file_three;
        if(is_file($file_three)) unlink($file_three);


        //Now delete post record

        $sql = "DELETE FROM ".$this->posts_table." WHERE post_id = '{$post_id}' LIMIT 1";
        $Database->query($sql);

        //Record Log
        $Logs->log("Deleted post with an ID of ". $post_id);

        return ($Database->affected_rows() == 1) ? "Post deleted successfully!" : " Ooops!, an error occured whilst deleting post: ".$post_id;
    }
}
$Posts = new Posts();
?>