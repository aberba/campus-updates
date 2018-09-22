<?php
class Advertisements {
    private $advertisements_table = "advertisements";

    public function count_all() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->advertisements_table;
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }


    public function add($post=null, $files=null) {
        global $Database;

        $sql = "".$this->advertisements_table;
        return ($Database->query($sql) === true) ? "" : "";
    }    

    public function fetch_all() {
        global $Database;

        $sql = "SELECT * FROM ".$this->advertisements_table." ORDER BY date_added DESC";

        $result = $Database->query($sql);
        if ($Database->num_rows($result) < 1) return false;

        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
    }

    public function changad_status($ad_id=0) {
        global $Database;
        
        $ad_id = (int)$Database->clean_data($ad_id);
        $sql = "SELECT publish FROM ".$this->advertisements_table." WHERE advertisement_id = '{$ad_id}' LIMIT 1";

        $result = $Database->query($sql);
        $status = $Database->fetch_data($result)->publish;
        $new_status = ($status == 1) ? 0 : 1;
        $message    = ($status == 1) ? "Advertisement is now hidden" : "Advertisement is now shown in public";
        $sql = "UPDATE ".$this->advertisements_table." SET publish = '{$new_status}' WHERE advertisement_id = '{$ad_id}' LIMIT 1";
        return ($Database->query($sql) === true) ? $message : "Oops! error changing advertisement status.";
    }
}

$Advertisements = new Advertisements();
?>