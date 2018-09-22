<?php
require_once('../../../files_admin/includes/initialize.php');

if(!is_ajax_request()) exit();

if(isset($_POST['authenticate'])) {
	$result = $Session->authenticateAction($_POST['pass']);
	echo $result;
    exit();
}

if(isset($_POST['change_account_freeze'])) {
	$result = $Session->change_account_freeze($_POST['user_id']);
	echo $result;
    exit();
}
?>