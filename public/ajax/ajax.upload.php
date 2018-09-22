<?php
require_once('../../files_public/includes/initialize.php');

if(!is_ajax_request()) exit();

// For uploading file in publis view

// for uploading files submitted by users for publishing
if(isset($_POST['submit_upload'])) {
    $result = $UsersUploads->upload_user_content($_POST, $_FILES);
    echo $result;
    exit();
}

if(isset($_POST['upload_avatar'])) {
    $result = $Uploads->upload_avatar($_FILES);
    echo $result;
    exit();
}
?>