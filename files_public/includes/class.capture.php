<?php
class Capture {
    private $table = "captures";

    public function count_all() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->table." WHERE publish = '1'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function search($keyword="") {
        global $Database;

        $keyword = $Database->clean_data($keyword);

        $sql = "SELECT * FROM ".$this->table." WHERE caption LIKE '%{$keyword}%' AND publish = '1' ORDER BY date_added DESC LIMIT 12";
        $result = $Database->query($sql);
        if($Database->num_rows($result) < 1) return false;

        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
    }

    public function fetch_all($offset=0, $limit=0) {
    	global $Database;

        $offset = (int)$Database->clean_data($offset);
        $limit  = (int)$Database->clean_data($limit);

    	$sql = "SELECT * FROM ".$this->table." WHERE publish = '1' ORDER BY date_added DESC LIMIT {$limit} OFFSET {$offset}";
    	$result = $Database->query($sql);
    	if($Database->num_rows($result) < 1) return false;

    	$output = array();
    	while ($row = $Database->fetch_data($result)) {
    		$output[] = $row;
    	}
    	return $output;
    }
}

$Capture = new Capture();
?>