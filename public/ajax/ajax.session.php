<?php
require_once('../../files_public/includes/initialize.php');

if(!is_ajax_request()) exit();

if(isset($_POST['signin'])) {
    $keep_logged_in = isset($_POST['keep_me_logged_in']);
	$result = $Session->authenticate($_POST, $keep_logged_in);
    echo $result;
    exit();
}

if(isset($_POST['signup'])) {
    $result = $Session->signup($_POST);
    echo $result;
    exit();
}

if(isset($_POST['change_password'])) {
    $result = $Session->change_password($_POST);
    echo $result;
    exit();
}

if(isset($_POST['send_recovery'])) {
    $result = $Session->send_recovery($_POST['email']);
    echo $result;
    exit();
}

if(isset($_POST['resend_activation'])) {
    $result = $Session->resend_activation();
    echo $result;
    exit();
}


if(isset($_POST['set_search_category'])) {
    $Session->set_search_category($_POST['category']);
    exit();
}

/*************** Authentication ****************/
if(isset($_POST['authenticate_action'])) {
    $result = $Session->authenticateAction($_POST['password']);
    echo $result;
    exit();
}
?>