<?php
//NB: This class extends the posts class

class Comments {
    private $posts_table    = "posts";
    private $comments_table = "posts_comments";
    private $replies_table  = "replies";
    private $users_table    = "users";

    //Allowed tags in commenting
    private $allowed_tags = "<a><strong><b><em><i><span><br>";

     public function count_all() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->comments_table;
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function count_published() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->comments_table." WHERE publish = '1'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function count_unpublished() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->comments_table." WHERE publish = '0'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }
    
    //fetches an sinlge comment
    public function find_by_id($comment_id=0) {
        global $Database;
        
        $comment_id = (int)$Database->clean_data($comment_id);

        $sql = "SELECT * FROM ".$this->comments_table." WHERE comment_id = '{$comment_id}' LIMIT 1";
        $result = $Database->query($sql);
        return $Database->fetch_data($result);
    }

    //Fetch comments and user infomation of a post using the post ID
    public function find_by_post_id($post_id=0) {
        global $Database, $Dates;
        
        $post_id = (int)$Database->clean_data($post_id);

        $sql  = "SELECT C.*, U.* FROM ".$this->comments_table." C, ".$this->users_table." U WHERE C.post_id_fk = '{$post_id}' ";
        $sql .= "AND C.added_by = U.user_id ORDER BY C.date_added ASC";
        $result = $Database->query($sql);
        if($Database->num_rows($result) < 1) return false;

        $output = array();
        while($row = $Database->fetch_data($result)) {
            $output[] = array(
                            "comment_id" => $row->comment_id,
                            "comment"    => $row->comment,
                            "user_id"    => $row->added_by,
                            "user_name"  => ($row->show_real_name === 1) ? $row->first_name." ".$row->last_name : $row->user_name,
                            "avatar"     => $row->profile_photo,
                            "post_id"    => $row->post_id_fk,
                            "date" => $Dates->date_with_time($row->date_added),
                            "publish" => $row->publish
                        );
        }
        return $output;
    }
    
    //Saves an edited comment
    public function save($post=null) {
        global $Database, $Logs;

        $comment_id = (int)$Database->clean_data($post['comment_id']);
        $comment    = $Database->clean_data($post['comment'], $this->allowed_tags);

        $sql = "UPDATE ".$this->comments_table." SET comment = '{$comment}' WHERE comment_id = '{$comment_id}' LIMIT 1";

        //Record log
        $Logs->log("Edited and saved comment with an ID of => ". $comment_id);
        return ($Database->query($sql) === true) ? "Comment saved successfully" : "Ooops!, error saving comment: ".$comment_id;
    }

    //Changes status of a comment
    public function change_status($comment_id=0) {
        global $Database, $Logs;
        
        $comment_id = (int)$Database->clean_data($comment_id);

        //select current status of the comment
        $sql = "SELECT publish FROM ".$this->comments_table." WHERE comment_id = '{$comment_id}' LIMIT 1";
        $result = $Database->query($sql);
        $status = $Database->fetch_data($result)->publish;

        $new_status = ($status == 1) ? 0 : 1;
        $message    = ($new_status == 1) ? "Comment is now shown in public" : "Comment is now hidden from public";

        $sql = "UPDATE ".$this->comments_table." SET publish = '{$new_status}' WHERE comment_id = '{$comment_id}' LIMIT 1";

        //Record log
        $log_msg = ($new_status == 1) ? "Published" : "Unpublished";
        $Logs->log($log_msg. " comment with an ID of => ". $comment_id);
        return ($Database->query($sql) === true) ? $message : "Ooops!, an error occured whilst changing comment: ".$comment_id." status";
    }   

    //For deleting a comment
    public function delete($comment_id=0) {
        global $Database, $Logs;
        
        $comment_id = (int)$Database->clean_data($comment_id);

        //select post id
        $sql = "SELECT post_id_fk FROM ".$this->comments_table." WHERE comment_id = '{$comment_id}' LIMIT 1";
        $result  = $Database->query($sql);
        $post_id = $Database->fetch_data($result)->post_id_fk;

        //Delete comment an reduce num_comments in post
        $sql = "DELETE FROM ".$this->comments_table." WHERE comment_id = '{$comment_id}' LIMIT 1";
        
        //Record Log
        $Logs->log("Deleted comment with an ID of => ". $comment_id);
        return ($Database->query($sql) === true) ? "Comment removed successfully!" : "Ooops!, an error occured whilst decrementing \"num_comment\" of post: ".$post_id;
    }

     //Inserts a new Comment
    public function add_new($post=null) {
        global $Database, $Session, $Logs;
        
        if(!$Session->logged_in()) return "Please login to post a comment.";

        $post_id    = (int)$Database->clean_data($post['post_id']);
        $user_id    = $Session->user()['id'];
        $comment      = $Database->clean_data($post['comment'], $this->allowed_tags);
        $date       = time();

        //Check for repeatition
        $sql  = "SELECT comment_id FROM ".$this->comments_table." WHERE added_by = '{$user_id}' AND ";
        $sql .= "post_id_fk = '{$post_id}' AND comment = '{$comment}' LIMIT 1";
        $result = $Database->query($sql);
        if($Database->num_rows($result) == 1) return "You have already posted this comment";

        $sql  = "INSERT INTO ".$this->comments_table." (post_id_fk, added_by, comment, date_added) ";
        $sql .= "VALUES ('{$post_id}', '{$user_id}', '{$comment}', '{$date}')";
        if($Database->query($sql) !== true) return "Ooops!, error an error occured whilst adding comment";

        //Increase num_comments by 1
        $sql = "UPDATE ".$this->posts_table." SET num_comments = num_comments + 1 WHERE post_id = '{$post_id}' LIMIT 1";

        //record log
        $Logs->log("Commented on post with an ID of => ". $post_id);
        return ($Database->query($sql) === true) ? "Comment added successfully! <br> We will publish it once it has been confirmed." : "Ooops! error adding comment";
    }
    



/************ Comment Replies to Be Added Later   *********/

/*
    //Inserts a new reply
    public function add_reply($post=null) {
        global $Database, $Session;
        
        if(!$Session->logged_in()) return "Please login to post a comment.";

        $comment_id = (int)$Database->clean_data($post['comment_id']);
        $user_id    = (int)$Database->clean_data($post['user_id']);
        $reply      = $Database->clean_data($post['reply'], $this->allowed_tags);
        $date       = time();

        $sql  = "INSERT INTO ".$this->replies_table." (comment_id_fk, user_id_fk, reply, date_added) ";
        $sql .= "VALUES ('{$comment_id}', '{$user_id}', '{$reply}', '{$date}')";
        return ($Database->query($sql) === true) ? "Reply added successfully! We will publish it once it has been confirmed." : "Ooops! error adding reply";
    } 

    //Fetch replies of a comment
    public function fetch_replies($comment_id=0) {
        global $Database;
        
        $comment_id = (int)$Database->clean_data($comment_id);

        $sql  = "SELECT R.*, U.* FROM ".$this->replies_table." R, ".$this->users_table." U WHERE R.comment_id_fk = '{$comment_id}' ";
        $sql .= "AND R.added_by = U.user_id ORDER BY date_added ASC LIMIT ".REPLIES_LIMIT;
        $result = $Database->query($sql);
        if($Database->num_rows($result) < 1) return false;

        $output = array();
        while($row = $Database->query($sql)) {
            $output[] = $row;
        }
        return $output;
    }
*/

}
$Comments = new Comments();
?>