<?php
global $Database, $Session, $Settings, $page_title, $css, $js, $Advertisements;
?>
<!DOCTYPE HTML>
<html lang="en" manifest="/cfiles.appcache">
<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="/img/templates/fav.ico" />
    <link rel="shortcut icon" type="image/ico" href="img/templates/fav.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	 
	<meta name="author" content="https://plus.google.com/104154811790535154300" />
    <meta name="description" content="Campus updates is an open news and articles site that promote information sharing and news across the various campuses of institutions across the globe." />
    <meta name="keywords" content="Leaks, rumors, updates, news, campus, university, college, stories, latest" />
    
    <meta property="og:type" content="blog" />
    <meta property="og:url" content="<?php echo @$site_url; ?>" />
    <meta property="og:description" content="" />
	 
    
    <link rel="stylesheet" href="css/normalize.css" />
    <link rel="stylesheet" href="css/global.css" />
    <link rel="stylesheet" href="css/<?php echo @$css; ?>" />
    
    <title><?php echo @$page_title." | ".$Settings->site_name(); ?></title>
</head>

<body>
<article id="wrapper">

    <header id="header">
        <section class="banner-section clearfix">
            <figure>
                <a href="/home/"><img src="img/templates/campus_updates_logo.png" alt="Campus Updates Logo"></a>
            </figure>

            

<?php
$ads_recors = $Advertisements->fetch_all("aside");

if ($ads_recors) {

    $output = "<div class='slider'>";
    foreach ($ads_recors as $a => $value) {
        $url = "uploads/advertisements/". $value->file_name;
        if (!is_file(ADVERTISEMENTS_DIR. DS .$value->file_name)) {
              $url = $value->file_url;
        }

        $output .= "<div class='slide'>
                        <a href='//". $value->ad_url ."' target='_blank'>
                            <img src='". $url ."' alt='". $value->alt ."' />
                        </a>
                    </div>";
    }
    $output .= "</div>";
    echo $output;
}


?>
        </section>


        <nav class="navigation clearfix">
            <div class="navmenu"> 
                 <ul class="clearfix">
                    <li class='close'><a href="#"> x </a></li>
                    <li><a href="/home/"> Home</a></li>
                    <li><a href="/posts/"> Posts </a></li>
                    <li><a href="/events/"> Events </a></li>
                    <li><a href="/capture/"> Capture </a></li>
                    <li><a href="/about/"> About </a></li>
                 </ul>
            </div>

            <div class="navlinks">
              <ul>
                <li><a href="/home/"> Home </a></li>
                <li><a href="/posts/"> Posts </a></li>
                <li><a href="/events/"> Events </a></li>
                 <li><a href="/capture/"> Capture </a></li>
                 <li><a href="/about/"> About </a></li>
              </ul>
            </div>
            
            <form class="search-form" action="search.php" method="get">
                <input type="search" name="s" placeholder="Search ..." value="<?php echo @$_GET['s']; ?>"/>
            </form>
        </nav>  <!-- End of navigation -->
        
        <section class="status-section blue clearfix">
             <ul>
                <?php
                   if($Session->logged_in()) {
                       echo "<li><a href='/account/'> My Account </a></li>
                             <li><a href='/logout/'> Sign Out </a></li>";
                   } else {
                       echo "<li><a href='/signin/'> Sign In </a></li>
                             <li><a href='/signup/'> Sign Up </a></li>";
                   }
                ?>   


                <li class="search-category">
                    <form>
                        <select name="category">
                            <option <?php if (isset($_SESSION['search_category']) && ($_SESSION['search_category'] == "posts")) echo "selected='selected'"; ?> value="posts"> Posts </option>
                            <option <?php if (isset($_SESSION['search_category']) && ($_SESSION['search_category'] == "events")) echo "selected='selected'"; ?> value="events"> Events </option>
                            <option <?php if (isset($_SESSION['search_category']) && ($_SESSION['search_category'] == "capture")) echo "selected='selected'"; ?> value="capture"> Capture </option>
                        </select>
                    </form>
                </li>                 
             </ul>
        </section>
    </header>