<?php
require_once('../../../files_admin/includes/initialize.php');

if(!is_ajax_request()) exit();


/************ Post & Comments ******************/

if(isset($_POST['save_post_information'])) {
    $result = $Posts->save($_POST);
    echo $result;
    exit();
}

if(isset($_POST['add_post'])) {
    $result = $Posts->add($_POST);
    echo $result;
    exit();
}

if(isset($_POST['change_post_state'])) {
    $result = $Posts->change_publish_status($_POST['post_id']);
    echo $result;
    exit();
}

//Allows or disallow commenting on a post
if(isset($_POST['change_commenting_status'])) {
    $result = $Posts->change_commenting_status($_POST['post_id']);
    echo $result;
    exit();
}

if(isset($_POST['save_comment'])) {
    $result = $Comments->save($_POST);
    echo $result;
    exit();
}

//Change the status of a comment( publish or hide )
if(isset($_POST['change_comment_status'])) {
    $result = $Comments->change_status($_POST['comment_id']);
    echo $result;
    exit();
}

//Change the status of a comment( publish or hide )
if(isset($_POST['tag_item'])) {
    $result = $Tags->add_to($_POST);
    echo $result;
    exit();
}

/************************ Events ****************************/
if(isset($_POST['add_new_event'])) {
    $result = $Events->add($_POST);
    echo $result;
    exit();
}

if(isset($_POST['save_event_information'])) {
    $result = $Events->save($_POST);
    echo $result;
    exit();
}

if(isset($_POST['change_event_status'])) {
    $result = $Events->change_status($_POST['event_id']);
    echo $result;
    exit();
}

if(isset($_POST['change_event_confirmation'])) {
    $result = $Events->change_confirmation($_POST['event_id']);
    echo $result;
    exit();
}



/*******************  Capture   ********************/
if(isset($_POST['change_captures_status'])) {
    $result = $Capture->change_status($_POST['capture_id']);
    echo $result;
    exit();
}

if(isset($_POST['save_edited_capture'])) {
    $result = $Capture->save($_POST);
    echo $result;
    exit();
}


/*******************  Settings ***********************/
if(isset($_POST['save_settings'])) {
    $result = $Settings->save($_POST);
    echo $result;
    exit();
}

if(isset($_POST['add_setting'])) {
    $result = $Settings->add($_POST);
    echo $result;
    exit();
}


/************** Users ********************/
if(isset($_POST['save_user'])) {
    $result = $Users->save($_POST);
    echo $result;
    exit();
}

/*************  Advertisements ************/
if(isset($_POST['changad_ad_status'])) {
    $result = $Advertisements->changad_status($_POST['ad_id']);
    echo $result;
    exit();
}


/********************** Tags ***************************/
if(isset($_POST['update_tag_name'])) {
    $result = $Tags->update_tag_name($_POST);
    echo $result;
    exit();
}

if(isset($_POST['add_new_tag'])) {
    $result = $Tags->add_new($_POST);
    echo $result;
    exit();
}
?>