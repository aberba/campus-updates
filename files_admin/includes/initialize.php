<?php

//error_reporting(0);

/*******************  Constants           *************************************/
defined("DS")        ? null : define('DS', DIRECTORY_SEPARATOR);
defined("SITE_ROOT") ? null : define('SITE_ROOT', dirname(dirname(dirname(__FILE__))));



/*******************  PHP FILES DIR       *************************************/
defined("INC_DIR")     ? null : define('INC_DIR', dirname(__FILE__));
defined("TEM_DIR")     ? null : define('TEM_DIR', dirname(dirname(__FILE__)).DS."templates");
defined("LOGS_DIR")    ? null : define('LOG_DIR', dirname(dirname(__FILE__)).DS."logs");



/******************   Assets Directories   ********************************/
defined("UPLOADS_DIR") ? null : define('UPLOADS_DIR', dirname(dirname(dirname(__FILE__))).DS."public".DS."uploads");
defined("AVATARS_DIR") ? null : define("AVATARS_DIR", UPLOADS_DIR.DS."avatars");
defined("POSTS_DIR") ? null : define("POSTS_DIR", UPLOADS_DIR.DS."posts");
defined("EVENTS_DIR") ? null : define("EVENTS_DIR", UPLOADS_DIR.DS."events");
defined("CAPTURE_DIR") ? null : define("CAPTURE_DIR", UPLOADS_DIR.DS."captures");
defined("DOCS_DIR") ? null : define("DOCS_DIR", UPLOADS_DIR.DS."docs");
defined("ATTACHMENT_DIR") ? null : define("ATTACHMENT_DIR", UPLOADS_DIR.DS."attachments");
defined("USERS_UPLOADS_DIR") ? null : define("USERS_UPLOADS_DIR", UPLOADS_DIR.DS."users_uploads");
defined("ADVERTISEMENTS_DIR") ? null : define("ADVERTISEMENTS_DIR", UPLOADS_DIR.DS."advertisements");



/***************  ENV CONSTANTS   *****************/
if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
	$uri = 'https://';
} else {
	$uri = 'http://';
}
	$uri .= $_SERVER['HTTP_HOST'];

defined("SITE_URI") ? null : define("SITE_URI", $uri);


/***************  Develpment OR Production Environment Settings  **********/
defined("DEV_ENV")  ? null : define("DEV_ENV", true); //set this to 'false' when in producttion

if (DEV_ENV) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 'Off');
    ini_set('log_errors', 'On');
    ini_set('error_log', SITE_ROOT.DS."logs".DS."errors.log");   
}


/**************  Initialize Calss Libraries *******************/
// Require classes functions, etc.
require_once(INC_DIR.DS.'campus_updates_connect.php');

// Helper Classes
require_once(INC_DIR.DS.'class.database.php');
require_once(INC_DIR.DS.'functions.php');
require_once(INC_DIR.DS.'class.dates.php');
require_once(INC_DIR.DS.'class.secure.php');
require_once(INC_DIR.DS.'class.session.php');
require_once(INC_DIR.DS.'class.emails.php');
require_once(INC_DIR.DS.'class.notifications.php');
require_once(INC_DIR.DS.'class.settings.php');

// Content Manipulating Classes
require_once(INC_DIR.DS.'class.posts.php');
require_once(INC_DIR.DS.'class.tags.php');
require_once(INC_DIR.DS.'class.comments.php');
require_once(INC_DIR.DS.'class.events.php');
require_once(INC_DIR.DS.'class.capture.php');
require_once(INC_DIR.DS.'class.users.php');
require_once(INC_DIR.DS.'class.uploads.php');
require_once(INC_DIR.DS.'class.users_uploads.php');
require_once(INC_DIR.DS.'class.logs.php');
require_once(INC_DIR.DS.'class.advertisements.php');

?>