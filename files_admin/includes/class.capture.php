<?php
class Capture {
    private $captures_table = "captures";
    private $allowed_tags   = "<a>";

    public function count_all() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->captures_table;
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function count_published() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->captures_table." WHERE publish = '1'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function count_unpublished() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->captures_table." WHERE publish = '0'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function fetch_all() {
    	global $Database;

    	$sql = "SELECT * FROM ".$this->captures_table." ORDER BY date_added DESC";
    	$result = $Database->query($sql);
    	if($Database->num_rows($result) < 1) return false;

    	$output = array();
    	while ($row = $Database->fetch_data($result)) {
    		$output[] = $row;
    	}
    	return $output;
    }

    public function find_by_id($capture_id=0) {
        global $Database;

        $capture_id = (int)$Database->clean_data($capture_id);
        $sql = "SELECT * FROM ".$this->captures_table." WHERE capture_id = '{$capture_id}' LIMIT 1";
        $result = $Database->query($sql);
        if($Database->num_rows($result) != 1) return false;

        return $Database->fetch_data($result);
    }

    public function save($post=null) {
        global $Database, $Logs;

        $capture_id = (int)$Database->clean_data($post['capture_id']);
        $caption    = $Database->clean_data($post['caption'], "<a>");

        $sql = "UPDATE ".$this->captures_table." SET caption = '{$caption}' WHERE capture_id = '{$capture_id}' LIMIT 1";

        //Record log
        $Logs->log("Added a capture with caption => ". $caption);
        return ($Database->query($sql) === true) ? "Capture saved successfully!" : " Ooops!, an error occured whilst saving capture";
    }

    public function change_status($capture_ids=null) {
        global $Database, $Logs;

        $num_ids = count($capture_ids);
        $result  = null;
        $id      = null;

        for($i = 0; $i < $num_ids; $i++) {
            $id = (int)$Database->clean_data($capture_ids[$i]);
           
            //Query current status
            $sql = "SELECT publish, caption FROM ".$this->captures_table." WHERE capture_id = '{$id}' LIMIT 1";
            $result  = $Database->query($sql);
            $row     = $Database->fetch_data($result);
            $status  = $row->publish;
            $caption = $row->caption; // for log message

            $new_status = ($status == 1) ? 0 : 1;

            //Change current status to the new status
            $sql = "UPDATE ".$this->captures_table." SET publish = '{$new_status}' WHERE capture_id = '{$id}' LIMIT 1";
            
            //Record log
            $log_msg = ($new_status == 1) ? "Published" : "Unpublished";
            $Logs->log($log_msg. " a capture with caption => ". $caption);
            $result = ($Database->query($sql) === true) ? "Status of the selected items have been changed successfully!" : "Ooops!, error changing status";
        }
        return $result;
    }
    
    public function deleteBulk($capture_ids=null) {
        global $Database, $Uploads, $Logs;

        $num_ids = count($capture_ids);
        $result  = null;

        for ($i = 0; $i < $num_ids; $i++) {
            $id = $capture_ids[$i];  //will be sanitized in remove_image() method

            $data = array(
                        "type" => "capture",
                        "capture_id" => $id,
                    );
            $result = $Uploads->remove_image($data);
        }
        
        $Logs->log("Deleted one or more captures from DB");
        return $result;
    }
}

$Capture = new Capture();
?>