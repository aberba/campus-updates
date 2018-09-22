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

          $sql = "SELECT * FROM ".$this->settings_table;
          $result = $Database->query($sql);

          $output = array();
          while ($row = $Database->fetch_data($result)) {
              $output[] = $row;
          }
          return $output;
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
     	return (int)$this->settings['pagination_per_page'];
     }

     public function smtp_user_name() {
          return $this->settings['smtp_user_name'];
     }

     public function smtp_password() {
          return $this->settings['smtp_password'];
     }

     public function smtp_host() {
          return $this->settings['smtp_host'];
     }

     public function smtp_port() {
          return $this->settings['smtp_port'];
     }

     public function site_admin_email() {
          return $this->settings['site_admin_email'];
     }

     public function site_public_email() {
          return $this->settings['site_public_email'];
     }
     

     public function max_upload_size() {
          return (int)$this->settings['max_upload_size'];
     }

     public function min_upload_size() {
          return (int)$this->settings['min_upload_size'];
     }

     public function max_allowed_comments_on_post() {
          return (int)$this->settings['max_allowed_comments_on_post'];
     }

     public function max_allowed_total_post_comment() {
          return (int)$this->settings['max_allowed_total_post_comment'];
     }

 
     public function user_upload_limit() {
          return (int)$this->settings['user_upload_limit'];
     }

     public function site_facebook_url() {
          return urlencode($this->settings['site_facebook_url']);
     }

     public function site_twitter_url() {
          return urlencode($this->settings['site_twitter_url']);
     }

     public function site_youtube_url() {
          return urlencode($this->settings['site_youtube_url']);
     }

     public function site_phone_number() {
          return $this->settings['site_phone_number'];
     }     
}

$Settings = new Settings();
?>