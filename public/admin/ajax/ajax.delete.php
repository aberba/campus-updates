<?php
require_once('../../../files_admin/includes/initialize.php');

if(!is_ajax_request()) exit();

// Process requests that fetches data only (no insert or delete)

/***************** Post   *****************/
if(isset($_POST['delete_comment'])) {
    $result = $Comments->delete($_POST['comment_id']);
    echo $result;
    exit();
}

if(isset($_POST['delete_post'])) {
    $result = $Posts->deletePost($_POST['post_id']);
    echo $result;
    exit();
}

if(isset($_POST['remove_item_tag'])) {
    $result = $Tags->remove_from($_POST);
    echo $result;
    exit();
}

if(isset($_POST['remove_attachment'])) {
    $result = $Uploads->remove_post_attachment($_POST);
    echo $result;
    exit();
}


/************* Capture ****************/

if(isset($_POST['delete_captures'])) {
    $result = $Capture->deleteBulk($_POST['capture_id']);
    echo $result;
    exit();
}

/************* Event ********************/
if(isset($_POST['delete_event'])) {
    $result = $Events->delete($_POST['event_id']);
    echo $result;
    exit();
}

if(isset($_POST['remove_user_upload'])) {
	//print_r($_POST);
    $result = $UsersUploads->remove($_POST['upload_id']);
    echo $result;
    exit();
}

//For deleting all images based on image type
if(isset($_POST['remove_image'])) {
    $result = $Uploads->remove_image($_POST);
    echo $result;
    exit();
}


/************* Settings **************/
if(isset($_POST['delete_setting'])) {
    $result = $Settings->delete($_POST['setting_id']);
    echo $result;
    exit();
}

/******************** Logs ******************/
if(isset($_POST['delete_log'])) {
    $result = $Logs->delete($_POST['log_id']);
    echo $result;
    exit();
}

/************ Advertisements  ****************/
if(isset($_POST['delete_ad'])) {
    $result = $Uploads->delete_ad($_POST['ad_id']);
    echo $result;
    exit();
}


/****************** Tags *************/
if(isset($_POST['delete_tag'])) {
    $result = $Tags->delete_tag($_POST['tag_id']);
    echo $result;
    exit();
}
?>