<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}
    
$Session->logout();
redirect_to("/home/");   
exit();
?>