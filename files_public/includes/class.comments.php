<?php
//NB: This class extends the posts class

class Comments extends Posts {
    private $posts_table    = "posts";
    private $comments_table = "posts_comments";
    private $users_table    = "users";

    //Allowed tags in commenting
    private $allowed_tags = "<a><strong><b><em><i><span><br>";

    public function count_all($post_id=0) {
        global $Database;

        $sql = "SELECT COUNT(*) AS num FROM ".$this->comments_table." WHERE post_id_fk = '{$post_id}' AND publish = '1'";
        $result = $Database->query($sql);
        return (int)$Database->fetch_data($result)->num;
    }

    public function fetch_recent($limit=3) {
        global $Database;
        
        $limit = (int)$Database->clean_data($limit);

        $sql  = "SELECT * FROM ".$this->comments_table." C LEFT JOIN ";
        $sql .= $this->users_table." U ON U.user_id = C.added_by LEFT JOIN ";
        $sql .= $this->posts_table." P ON P.post_id = C.post_id_fk ";
        $sql .= "WHERE C.publish = '1' ORDER BY C.date_added DESC";
        $result = $Database->query($sql);
        if($Database->num_rows($result) < 1) return false;
      
        $output = array();
        while($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
    }

    //Fetch comments and user infomation of a post using the post ID
    public function find_by_post_id($post_id=0) {
        global $Database;
        
        $post_id = (int)$Database->clean_data($post_id);

        $sql  = "SELECT * FROM ".$this->comments_table." C LEFT JOIN ";
        $sql .= $this->users_table." U ON U.user_id = C.added_by WHERE C.post_id_fk = '{$post_id}' ";
        $sql .= "AND C.publish = '1' ORDER BY C.date_added ASC";
        $result = $Database->query($sql);
        if($Database->num_rows($result) < 1) return false;
      
        $output = array();
        while($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
    }

    //Inserts a new Comment
    public function add_new($post=null) {
        global $Database, $Session, $Settings;
        
        if(!$Session->logged_in()) return "Please login to post a comment.";
        if(!$Session->is_activated()) return "Please activate your account to post comments.";

        $post_id    = (int)$Database->clean_data($post['post_id']);
        $user_id    = $Session->user()['id'];
        $comment      = $Database->clean_data($post['comment'], $this->allowed_tags);
        $date       = time();
        if(empty($comment)) return "Enter a comment to post.";

        //Check for repeatition
        $sql  = "SELECT comment_id FROM ".$this->comments_table." WHERE added_by = '{$user_id}' AND ";
        $sql .= "post_id_fk = '{$post_id}' AND comment = '{$comment}' LIMIT 1";
        $result = $Database->query($sql);
        if($Database->num_rows($result) == 1) return "You have already posted this comment";

        //Check for exceeding max comments users has on individual posts
        $sql  = "SELECT COUNT(*) AS num FROM ".$this->comments_table." WHERE added_by = '{$user_id}'";
        $sql .= "AND post_id_fk = '{$post_id}'";
        $result = $Database->query($sql);
        $num = (int)$Database->fetch_data($result)->num;
        if($num > $Settings->max_allowed_comments_on_post()) return "You have exceeded the maximum allowed comments on this post";

        //Check for exceeding max allowed total comments
        $sql  = "SELECT COUNT(*) AS num FROM ".$this->comments_table." WHERE ";
        $sql .= "post_id_fk = '{$post_id}'";
        $result = $Database->query($sql);
        $num = $Database->fetch_data($result)->num;
        if($num > $Settings->max_allowed_total_post_comment()) return "Sorry! Commenting is closed on this post";


        $sql  = "INSERT INTO ".$this->comments_table." (post_id_fk, added_by, comment, date_added) ";
        $sql .= "VALUES ('{$post_id}', '{$user_id}', '{$comment}', '{$date}')";

        return ($Database->query($sql) === true) ? "Comment added successfully! <br> We will publish it once it has been confirmed." : "Ooops! error adding comment";
    }
}
$Comments = new Comments();
?>