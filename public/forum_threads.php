<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}

$forum_id = isset($_GET['forum_id']) ? (int)$Database->clean_data($_GET['forum_id']) : 0;

$forum_info = $Forum->find_forum_by_id($forum_id);
if (!$forum_info) {
    redirect_to("/notfound");
}

$current_page     = isset($_GET['page']) ? (int)$Database->clean_data($_GET['page']) : 1;
$records_per_page = ceil($Settings->records_per_pagination() * 2);
$total_records    = $Forum->count_threads($forum_id);
$Pagination       = new Pagination($current_page, $records_per_page, $total_records);
$threads          = $Forum->fetch_threads($forum_id, $Pagination->offset(), $records_per_page);

$css              = "forum_threads.css";
$js               = "forum_threads.js";
$page_description = htmlentities($forum_info->description);
$page_title       = "Forum &raquo; ". htmlentities($forum_info->name);


include_template('header.php'); 
?>

<section id="content">
    <section class="forum-info-section">
        <h3>Threads posted under <em><?php echo htmlentities($forum_info->name); ?></em> </h3>
    </section>


    <section class="threads-section">
<?php
if ($threads) {
    $output = "";
    foreach ($threads as $t => $value) {
         $num_comments = $Forum->count_thread_comments($value->thread_id);

         $output .= "<section class='thread'>
                        <h3><a href='forum_read_thread.php?thread_id=". $value->thread_id ."'>". htmlentities($value->title) ."</a></h3>
                        <p>". $value->thread ."</p>
                        
                        <p class='info'>Posted By: <a>". $value->first_name." ".$value->last_name ."</a>, &nbsp;&nbsp; Comments: <a>". $num_comments ."</a> &nbsp;&nbsp;&nbsp; &nbsp; Since ". $Dates->date_only($value->date_added) ."</p>
                        <p><a class='tag' href='#'>Relief</a> <a class='tag' href='#'>Behaviour</a> <a class='tag' href='#'>National Development</a></p>
                    </section>";
    }
    echo $output;
} else {
    echo "<p>No thread is posted in this forum.</p>";
}
?>
    </section>
</section>



<aside id="aside"> 
<?php include_template('aside_raw.php'); ?>
</aside>


<?php include_template('footer.php'); ?>