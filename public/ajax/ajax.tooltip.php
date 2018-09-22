<?php
require_once('../../files_public/includes/initialize.php');

if(!is_ajax_request()) exit();

if(isset($_GET['user_id'])) {
    $user_id = mysqli_real_escape_string($dbc, $_GET['user_id']);
    $query = "SELECT * FROM users WHERE user_id = '$user_id' LIMIT 1";
    $data  = mysqli_query($dbc, $query);
    if($data) {
      $row   = mysqli_fetch_assoc($data);
      
      $campus      = $row['campus'];
      $gender     = $row['gender'];
           
      if($gender == "M") {
            $sex = "Male";
      }else {
            $sex = "Female";
      } 
      
      echo '<div id="tooltip" class="tooltip">
             <p>'.$sex.'</p>
             <p>'.$campus.'</p>
            </div>';
    }
}

?>