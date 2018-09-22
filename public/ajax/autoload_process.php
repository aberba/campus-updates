<?php

require_once('../../files_public/includes/initialize.php');

if($_POST) {
	//sanitize post value
	$group_number = filter_var($_POST["group_no"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
	
	//throw HTTP error if group number is not valid
	if(!is_numeric($group_number)){
		header('HTTP/1.1 500 Invalid number!');
		exit();
	}
	$items_per_group = 2;
	//get current starting point of records
	$position = ($group_number * $items_per_group);
	
	//Limit our results within a specified range. 
	$query= "SELECT * FROM updates WHERE confirm = '1' ORDER BY date_posted DESC " .
    "LIMIT $position, $items_per_group";
    $results = mysqli_query($dbc, $query);
	
	if(mysqli_num_rows($results) >= 1) { 
		//output results from database
		
		while($row = mysqli_fetch_assoc($results)) {
		  $update_idu = $row['update_id'];
		  $query_num = "SELECT * FROM comments WHERE update_id = ";
          $query_num .= "'$update_idu'";
          $data_num = mysqli_query($dbc, $query_num);
          $num = mysqli_num_rows($data_num);
          
	echo '
    <div id="story-div">  
      <h1 id="story-heading">' .$row['update_title']. '</h1>
        
      <div id="story-picture">  
      <figure>
      <img src="' .GALLERY_IMG_PATH.$row['update_photo']. '" />
       </figure>
      <figcaption>by '.$row['update_by']. '</figcaption>
      </div>
      
      <div id="story-text"><p>' .substr($row['update_text'], 0, 325). ' ....</p>
    </div>
      
      <p id="comments_number">'.
      $num. '<img = src="img/comments_icon.png" width="30" 
      alt="comments"/></p><br /><br />
      
      <div id="botton">
        <a href="read_update.php?upd_id=' .$row['update_id']. '">read more</a>
      </div>
      
   </div><br /> 
        '; 
       }
    
  }else {
        //no updates posted
    //echo '<p>No updates posted for now</p>';
    
  }
  unset($row);	
}
?>