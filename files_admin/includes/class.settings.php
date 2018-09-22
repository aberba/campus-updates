<?php
class Settings {
	private $settings_table = "settings";
     private $settings = array();

     function __construct() {
     	$this->set_all();
     }
      
     //pack all settings in $settings array for easy retrieval
     public function set_all() {
     	global $Database;

     	$sql = "SELECT * FROM ".$this->settings_table;
     	$result = $Database->query($sql);

     	$output = array();
     	while ($row = $Database->fetch_data($result)) {
              $output[$row->setting_name] = $row->setting_value;
          }
          $this->settings = $output;
     }

     public function fetch_all() {
          global $Database;

          $sql = "SELECT * FROM ".$this->settings_table. " ORDER BY setting_name ASC";
          $result = $Database->query($sql);

          $output = array();
          while ($row = $Database->fetch_data($result)) {
              $output[] = $row;
          }
          return $output;
     }

     public function save($post=null) {
          global $Database;
              $result = "";
          foreach ($post as $item => $value) {
              $id = (int)$Database->clean_data($item);
              $value = $Database->clean_data($value);

              $sql = "UPDATE ".$this->settings_table." SET setting_value = '{$value}' WHERE setting_id = '{$id}' LIMIT 1";
              $result =($Database->query($sql) === true) ? "Settings saved successfully!" : "Error saving settings";
          }
          return $result;
     }

     public function add($post) {
          global $Database;

          $name  = $Database->clean_data($post['name']);
          $value = $Database->clean_data($post['value']);
          $sql   = "INSERT INTO ".$this->settings_table." (setting_name, setting_value) VALUES ('{$name}', '{$value}')";
          return ($Database->query($sql) === true) ? "Setting added successfully!" : "Error!, please check for duplication or connection to server";
     }

     public function delete($setting_id=0) {
          global $Database;

          $setting_id = (int)$Database->clean_data($setting_id);
          $sql = "DELETE FROM ".$this->settings_table." WHERE setting_id = '{$setting_id}' LIMIT 1";
          $Database->query($sql);
          return ($Database->affected_rows() == 1) ? "Setting deleted successfully!" : "Oops!, error deleting setting: ".$setting_id;
     }

      //transaction methods
     public function transaction_start() {
          global $Database;
          $sql = "BEGIN WORK";
          $Database->query($sql);
     }

     public function transaction_rollback() {
          global $Database;
          $sql = "ROLLBACK";
          $Database->query($sql);
     }

     public function transaction_commit() {
          global $Database;
          $sql = "COMMIT";
          $Database->query($sql);
     }
    
     public function site_online() {
          return ((int)$this->settings['site_online'] == 1) ? true : false;
     }

     public function site_name() {
     	return htmlentities($this->settings['site_name']);
     }

     public function records_per_pagination() {
     	return $this->settings['pagination_per_page'];
     }

     public function max_upload_size() {
          return (int)$this->settings['max_upload_size'];
     }

     public function min_upload_size() {
          return (int)$this->settings['min_upload_size'];
     }
}

$Settings = new Settings();
?>