<?php
class Forum {
    private $forum_table = "forum";

    public function fetch_threads() {
        global $Database;

        $sql = "";
    }

     public function find_by_id($thread_id=0) {
        global $Database;

        $sql = "";
    }    

    public function fetch_comment($thread_id=0) {
        global $Database;

        $sql = "";
    }    

    public function post_comment($post=null) {
        global $Database;

        $sql = "";
    }    
}

$Forum = new Forum();
?>