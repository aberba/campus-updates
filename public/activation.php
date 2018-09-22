<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
	redirect_to("/offline/");
}

if (!isset($_GET['user_id']) || !isset($_GET['key'])) {
	redirect_to("/notfound/");
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Account Activation</title>
</head>
<body>
		<section style="padding: 50px 0; text-align: center;"> 
<?php
$output = "";
$user_id = (int)$Database->clean_data($_GET['user_id']);
$key     = $Database->clean_data($_GET['key']);

if (strlen($key) != 32) {
	$output .= "<p>Error, invalid activation key.</p>";
} else {
	if (!$Session->activate_user($user_id, $key)) {
		$output .= "<p>Activation failed! <br> Invalid credentials</p>
		            <p><a href='".SITE_URI."/home/'> &laquo; Back to home </a></p>";
	} else {
        $output .= "<p>Congratulations! <br>You account has been successfully activated. 
                    You can now <a href='".SITE_URI."/home/'>login</a> and start getting active.</p>";
	}
}

echo $output;
?>	   
		</section>
</body>
</html>

