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

    public function fetch_all($sort="aside") {
        global $Database;

        $sort = ($sort == "aside") ? "aside" : "top";

        $sql  = "SELECT * FROM ".$this->advertisements_table." ";
        $sql .= "WHERE publish = '1' AND placement = '{$sort}' ORDER BY RAND()";

        $result = $Database->query($sql);
        if ($Database->num_rows($result) < 1) return false;

        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
    }
}

$Advertisements = new Advertisements();
?>