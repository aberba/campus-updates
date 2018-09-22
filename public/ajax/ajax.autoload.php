<?php

require_once('../../files_public/includes/initialize.php');

if(isset($_GET['fetch_more_post_records'])) {
  	//sanitize post value
  	$group_number = (int)$Database->clean_data($_GET["current_group_no"]);
  	
  	//throw HTTP error if group number is not numeric
  	if(!is_numeric($group_number)){
  		header('HTTP/1.1 500 Invalid number!');
  		exit();
  	}
  	$records_per_group = $Settings->records_per_pagination();

  	//get current starting point of records
  	$current_position = ($group_number * $records_per_group);
  	
  	//Limit our results within a specified range. 
  	$results = $Posts->fetch_only($current_position);
  	
  	if($results && is_array($results) && count($results) > 0) { 
        echo json_encode($results);
  	} else {
        echo 0;
    }
    exit();
}
?>