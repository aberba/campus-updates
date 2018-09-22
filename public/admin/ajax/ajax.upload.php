<?php

require_once('../../../files_admin/includes/initialize.php');

/********* Posts *************/
if(isset($_POST['upload_post_image'])) {
    $result = $Uploads->upload_image($_POST, $_FILES);
    echo $result;
    exit();
}

if(isset($_POST['upload_post_attachment'])) {
    $result = $Uploads->upload_post_attachment($_POST, $_FILES);
    echo $result;
    exit();
}

/********* Capture *************/
if(isset($_POST['upload_new_capture'])) {
    $result = $Uploads->upload_image($_POST, $_FILES);
    echo $result;
    exit();
}

/********* Event *************/
if(isset($_POST['upload_event_image'])) {
    $result = $Uploads->upload_image($_POST, $_FILES);
    echo $result;
    exit();
}


/************** adverts **************/
if(isset($_POST['upload_advertisement'])) {
    $result = $Uploads->upload_advertisement($_POST, $_FILES);
    echo $result;
    exit();
}
?>