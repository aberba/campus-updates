<?php
require_once('../files_public/includes/initialize.php');
echo phpinfo();

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}

$css              = "forum.css";
$js               = "forum.js";
$page_description = "Forum";
$page_title       = "Forum";


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->
    <section class="intro-section">
          <h3>Forum for discussion</h3>
          <p>Our forums are categorized. Please click on any to participate.</p>
    </section>

    <section class="categories-section">
<?php
$categories = $Forum->fetch_all_forums();
if ($categories) {
	$output = "";

	foreach ($categories as $c => $value) {
        
        $output .= "<section class='category clearfix'>
    	     <figure>
	    	     <img src=uploads/forum/".$value->file_name.">
    	     </figure>

    	 	 <h3><a href='forum_threads.php?thread_id=". $value->forum_id ."'>". htmlentities($value->name) ."</a></h3>
    	 	 <p>". $value->description ."</p>
    	 	 <p>Since ". $Dates->date_only($value->date_added) .", <a href='forum_threads.php?forum_id=". $value->forum_id ."'>View Category</a></p>
    	 </section><br>";
	}

	echo $output;
}
?>
    </section>
</section>


<lo
<aside id="aside"> <!-- aside -->
<?php include_template('aside_raw.php'); ?>
</aside>


<?php include_template('footer.php'); ?>