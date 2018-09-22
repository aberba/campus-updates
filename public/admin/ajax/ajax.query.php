<?php
require_once('../../../files_admin/includes/initialize.php');

if(!is_ajax_request()) exit();

// Process requests that fetches data only (no insert or delete)

/*************** Post & Comments ****************/
if(isset($_GET['fetch_post'])) {
    $result = $Posts->find_by_id($_GET['post_id']);
    if($result && count($result) > 0) {
       echo json_encode($result);
    } else {
       echo 0;
    }
    exit();
}

//.fetches all comments for preview
if(isset($_GET['fetch_comments'])) {
    $result = $Comments->find_by_post_id($_GET['post_id']);
    
    if($result && count($result) > 0) {
       echo json_encode($result);
    } else {
       echo 0;
    }
    exit();
}

//fetches a single comment to be edited 
if(isset($_GET['query_comment'])) {
    $result = $Comments->find_by_id($_GET['comment_id']);
    
    if($result && count($result) > 0) {
       echo json_encode($result);
    } else {
       echo 0;
    }
    exit();
}


/*********   Event *********************/
if(isset($_GET['fetch_event'])) {
    $result = $Capture->find_by_id($_GET['event_id']);
print_r($result);
    if($result && is_array($result)) {
       echo json_encode($result);
    } else {
       echo 0;
    }
    exit();
}



/******************  Capture *****************/
if(isset($_GET['show_capture_lightbox'])) {
    $result = $Capture->fetch_all();

    if($result && is_array($result)) {
       echo json_encode($result);
    } else {
       echo 0;
    }
    exit();
}

//fetches a single capture for editing
if(isset($_GET['fetch_capture'])) {
    $result = $Capture->find_by_id($_GET['capture_id']);
    
    if($result && count($result) > 0) {
       echo json_encode($result);
    } else {
       echo 0;
    }
}
?>