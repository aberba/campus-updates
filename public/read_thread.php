<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}


$thread_id = isset($_GET['thread_id']) ? (int)$Database->clean_data($_GET['thread_id']) : 0;

$thread_info = $Forum->find_thread_by_id($thread_id);
if (!$thread_info) {
    redirect_to("/notfound");
}


$css              = "read_thread.css";
$js               = "read_thread.js";
$page_description = htmlentities(substr($thread_info->thread, 0, 250));
$page_title       = "Threads &raquo; ". htmlentities($thread_info->title);


include_template('header.php'); 
?>

<section id="content"> 
    
    <section class="thread-section">
<?php
echo "
    	<figure>
    		<img src='uploads/avatars/1414186617_013ecc8bb1c0e84f6dc7f6fd52fcbfbd.jpg' alt='User Avatar'>
    	</figure>

    	<h3>". $thread_info->title. "</h3>
    	<p>". $thread_info->thread ."</p>
    ";
?>
    </section>

    <section class="">
    </section>
</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_raw.php'); ?>
</aside>


<?php include_template('footer.php'); ?>