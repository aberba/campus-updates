<?php
class Posts {
    private $posts_table           = "posts";
    private $posts_images_table    = "posts_images";
    private $posts_attachments_table = "posts_attachments";
    private $posts_tag_link_table  = "posts_tag_link";
    private $tag_table             = "tags";
    private $users_table           = "users";
    
    //Private declaration of post images
    private $image_one   = null;
    private $image_two   = null;
    private $image_three = null;
    private $image_four  = null;
    private $image_five  = null;
    private $image_six   = null;
    

    public function count_all() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM posts WHERE publish = '1'";
        $result = $Database->query($sql);
        return (int)$Database->fetch_data($result)->num;
    }

    public function search($keyword="") {
        global $Database;

        $keyword = $Database->clean_data($keyword);

        $sql  = "SELECT * FROM ".$this->posts_table." P LEFT JOIN ";
        $sql .= $this->posts_images_table." PI ON P.post_id = PI.post_id_fk ";
        $sql .= "WHERE P.title LIKE '%{$keyword}%' AND P.publish = '1' ORDER BY P.date_added DESC LIMIT 10";
        $result = $Database->query($sql);

        if($Database->num_rows($result) < 1) return false;
        
        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
    }

    public function find_by_tag_name($tag_name="", $limit=6, $offset=0) {
        global $Database, $Tags;

        $post_ids = $Tags->fetch_tagged("posts", $tag_name);
        if (!$post_ids) return false;

        $limit  = (int)$limit;
        $offset = (int)$offset;

        $sql  = "SELECT * FROM ".$this->posts_table." P LEFT JOIN ";
        $sql .= $this->posts_images_table." PI ON P.post_id = PI.post_id_fk ";
        $sql .= "WHERE P.post_id = '0'";
        
        foreach ($post_ids as $post_id) {
            $sql .= " OR P.post_id = '{$post_id}'";
        }
        $sql .= " AND P.publish = '1' ORDER BY P.date_added DESC LIMIT {$limit} OFFSET {$offset}";

        $result = $Database->query($sql);

        if($Database->num_rows($result) < 1) return false;
        
        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
    }

    // Fetches post record and images only (also used for paginating records using offest param)
    public function fetch_all($offset=4, $limit=4) {
    	global $Database, $Settings;

        $limit  = (int)$Database->clean_data($limit);
        $offset = (int)$Database->clean_data($offset);

    	$sql  = "SELECT * FROM ".$this->posts_table." P LEFT JOIN ";
        $sql .= $this->posts_images_table." PI ON P.post_id = PI.post_id_fk ";
    	$sql .= "WHERE P.publish = '1' ORDER BY P.date_added DESC LIMIT {$limit} OFFSET {$offset}";
        $result = $Database->query($sql);

        if($Database->num_rows($result) < 1) return false;
        
        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
    }

    public function fetch_popular($limit=4) {
        global $Database, $Settings;

        $limit  = (int)$Database->clean_data($limit);

        $sql  = "SELECT * FROM ".$this->posts_table." P LEFT JOIN ";
        $sql .= $this->posts_images_table." PI ON P.post_id = PI.post_id_fk ";
        $sql .= "WHERE P.publish = '1' ORDER BY P.num_readers DESC, P.date_added DESC LIMIT {$limit}";
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
    	global $Database;
        $post_id = (int)$Database->clean_data($post_id);

    	$sql  = "SELECT * FROM ".$this->posts_table." P LEFT JOIN ";
        $sql .= $this->posts_images_table." PI ON P.post_id = PI.post_id_fk LEFT JOIN ";
        $sql .= $this->users_table." U ON P.added_by = U.user_id LEFT JOIN ";
        $sql .= $this->posts_attachments_table." A ON P.post_id = A.post_id_fk ";
        $sql .= "WHERE P.post_id = '{$post_id}' LIMIT 1";
        
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
        return $row;
    }


     //used to insert images dynamically in post images page
    public function insert_images($content="") {
        //assign the private images
        
        $post_images_path = "/uploads/posts/";
        $img1 = "<img class='post-image' src='".$post_images_path . $this->image_one."'   alt='".$this->image_one."' />";
        $img2 = "<img class='post-image' src='".$post_images_path . $this->image_two."'   alt='".$this->image_two."' />";
        $img3 = "<img class='post-image' src='".$post_images_path . $this->image_three."' alt='".$this->image_three."' />";      
        $img4 = "<img class='post-image' src='".$post_images_path . $this->image_four."'  alt='".$this->image_four."' />";        
        $img5 = "<img class='post-image' src='".$post_images_path . $this->image_five."'  alt='".$this->image_five."' />";
        $img6 = "<img class='post-image' src='".$post_images_path . $this->image_six."'   alt='".$this->image_six."' />";

        $content = str_replace("@IMAGE1@", $img1, $content);
        $content = str_replace("@IMAGE2@", $img2, $content);
        $content = str_replace("@IMAGE3@", $img3, $content);
        $content = str_replace("@IMAGE4@", $img4, $content);
        $content = str_replace("@IMAGE5@", $img5, $content);
        $content = str_replace("@IMAGE6@", $img6, $content);
        return $content; //return after images are inserted
    }

    public function add_reader($post_id=0) {
        global $Database;
        $post_id = (int)$Database->clean_data($post_id);
        $sql = "UPDATE ".$this->posts_table." SET num_readers = num_readers + 1 WHERE post_id = '{$post_id}' LIMIT 1";
        $Database->query($sql);
    }
 
    // fetch raleted post using post id
    public function fetch_related($post_id=0) {
        global  $Database, $Tags;
         
        $post_id = (int)$Database->clean_data($post_id);
        $tag_ids = $Tags->fetch_tags_ids("post", $post_id);
        if(!$tag_ids) return false;

        //fetch posts tagged under these tags from tag_lik
        $sql = "SELECT post_id_fk FROM ".$this->posts_tag_link_table." WHERE tag_id_fk = '0'";
        foreach ($tag_ids as $tid) {
            $sql .= " OR tag_id_fk = '{$tid}'";
        }

        $result_ids = $Database->query($sql);
        if($Database->num_rows($result_ids) < 1) return false;

        $related_ids = array();
        while ($row = $Database->fetch_data($result_ids)) {
            $id = (int)$row->post_id_fk;
            if ((int)$id != $post_id) {
                $related_ids[] = $id;
            }
        }

        // now fetch those posts ignoring current post 
        $sql  = "SELECT * FROM ".$this->posts_table." P LEFT JOIN ";
        $sql .= $this->posts_images_table ." PI ON P.post_id = PI.post_id_fk ";
        $sql .= "WHERE P.post_id = '0'";

        foreach ($related_ids as $pid ) {
           $sql .= " OR P.post_id = '{$pid}'";
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
    public function block_comments($post=0) {
        global $Database;

        $post_id = (int)$Database->clean_data($post_id);

        $sql = "SELECT block_comments as blocked FROM ".$this->posts_table." WHERE post_id = '{$post_id}' LIMIT 1";
        $result = $Database->query($sql);
        if($Database->num_rows($result) != 1) return false;
        return ($Database->fetch_data($result)->blocked == 1) ? true : false;
    }
}
$Posts = new Posts();
?>