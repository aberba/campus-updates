<?php
global $Database, $Session, $Settings, $page_title, $css, $js;
if(!$Session->logged_in()) {
    redirect_to("/signin/");
}

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" type="image/ico" href="./img/icons/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    
    <link rel="stylesheet" href="./css/normalize.css" />
    <link rel="stylesheet" href="./css/cms_global.css" />
    <link rel="stylesheet" href="./css/<?php echo @$css; ?>" />
    
    <title><?php echo @$page_title." | ".$Settings->site_name(); ?></title>
</head>

<body>

<article id="wrapper">
    <header id="header" class="blue">
        <h2 class="title">Campus Updates CMS</h2>
        
        <section class="status-section">
             <ul>            
                <li><a href='/home/'> Go To Site</a></li>
                <li><a href='/logout/'> Logout (<?php echo $Session->user()['name']; ?>) </a></li>

                <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
                <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
                <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>

                <li><a href="cms_add_post.php"> + Add Post </a></li>
                <li><a href="cms_add_event.php"> + Add Event </a></li>
                <li><a href="cms_capture.php"> + Add Capture </a></li>     
             </ul>
        </section>
  
    </header>
  


