<?php
require_once('../../files_public/includes/initialize.php');

if(!is_ajax_request()) exit();

// Process requests that fetches data only (no insert or delete)

//fetches all captures for lightbox display
if(isset($_GET['show_capture_lightbox'])) {
    $result = $Capture->fetch_all(0, 1000);

    if($result && is_array($result)) {
       echo json_encode($result);
    } else {
       echo 0;
    }
    exit();
}

if(isset($_GET['query_avatar'])) {
    $result = $Users->query_avatar();
    if ($result) {
    	echo $result;
    } else {
    	echo 0;
    }
    exit();
}
?>