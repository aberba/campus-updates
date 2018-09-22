<?php
require_once('../../files_public/includes/initialize.php');

if(!is_ajax_request()) exit();

if(isset($_POST['add_reader'])) {
    if($_POST['type'] == "post") {
        $Posts->add_reader($_POST['id']);
    } elseif ($_POST['type'] == "event") {
        $Events->add_reader($_POST['id']);
    }
    exit();
}

if(isset($_POST['add_comment'])) {
    $result = $Comments->add_new($_POST);

    if($result && is_array($result)) {
       echo json_encode($result);
    } else {
       echo $result;
    }
    exit();
}

if(isset($_POST['update_profile'])) {
    $result = $Users->update_profile($_POST);
    echo $result;
    exit();
}

if(isset($_POST['save_account_settings'])) {
    $result = $Users->save_account_settings($_POST);
    echo $result;
    exit();
}

?>