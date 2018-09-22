<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
  redirect_to("/offline/");
}

$css              = 'home.css';
$js               = "home.js";
$page_description = "Welcome to compus updates. This is where you get to know what is happening on other campuses.";
$page_title       = 'Home';


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

      <section class="posts-section">

<?php
$P = $Posts->fetch_all(0, 2); // 0 for no offset
if($P) {
    foreach ($P as $post => $value) {

    $image_file = "/uploads/posts/".$value->image_one;
?>
          <section class="post clearfix">
              <figure>
                   <a href="<?php echo "/posts/".$value->post_id."/".gen_url_param($value->title)."/"; ?>">
                   <img src="<?php echo $image_file; ?>" alt="<?php echo htmlentities($value->title); ?> image">
                 </a>
              </figure>

              <div class="introduction">
                  <h4><?php echo htmlentities($value->title); ?></h4>
                  <p>
                    <?php  
                    //use title length and content length to substring the introduction content
                    $max_length = 210;
                    $title_length = strlen($value->title);
                    $remaining_length = (int)($max_length - $title_length);
                    
                    echo substr($Database->clean_data($value->content, "<a>"), 0, $remaining_length). " ...";
                    ?>
                  </p>
                  <a class="read-more" href="<?php echo "/posts/".$value->post_id."/".gen_url_param($value->title)."/"; ?>">Read More &raquo;</a>
              </div>
          </section>
<?php       
    }
} else {
   echo "<h3>No post are added yet</h3>";
}
?>
    </section>


<?php
$event_list = $Events->fetch_all(0, 4);
if($event_list) {
    $output = "<section class='events-section clearfix'>
                   <h3>Most Recent Events</h3>";
    foreach ($event_list as $evt => $value) {
        $output .= "<section class='event'>
                        <figure>
                            <a href='/events/". $value->event_id ."/". urlencode($value->title) ."'>
                               <img src='/uploads/events/" .$value->image_one. "' alt='". htmlentities($value->title) ."' />
                            </a>

                        </figure>
                        <figcaption><a href='/events/". $value->event_id ."/". urlencode($value->title) ."'>". $value->title ."</a></figcaption>
                    </section>
                   ";
    }
    $output .= "</section>";
    echo $output;

} 

?>



<?php
$capture_records = $Capture->fetch_all(0, 6);
if($capture_records) {
    $output = "<section class='captures-section clearfix'>
                  <div><h3>Most Recent Capture</h3></div>";
    foreach ($capture_records as $cap => $value) {
        $output .= "
                    <section class='capture' id='capture". $value->capture_id ."'>
                       <figure>
                           <img src='/uploads/captures/". $value->file_name ."' alt='".htmlentities($value->caption)."'>
                       </figure>
                    </section>
                    ";
    }
    $output .= "</section>";

    echo $output;
}
?>


<?php
/************** Recent comments  ********************/

$comment_records = $Comments->fetch_recent(3);
if ($comment_records) {
    $output = "<section class='comments-section'>
                  <h3>Most Recent Comments</h3>
              ";
    foreach ($comment_records as $c => $value) {
         $avatar_url = "/uploads/avatars/". $value->profile_photo;
         if (!is_file(AVATARS_DIR. DS. $value->profile_photo)) {
            $url = "/img/templates/";
            $avatar_url = ($value->gender == "M") ? $url. "default_male.png" : $url. "default_female.png";
         }

         $name = ($value->show_real_name == 1) ? $value->first_name." ".$value->last_name : $value->user_name;
         $output .= "
                    <section class='comment clearfix'>
                        <figure>
                            <img src='". $avatar_url ."' alt='Avatar'>
                        </figure>
                       
                        <div class='comment-text'>
                            <h4><a href='/posts/". $value->post_id ."/".urlencode($value->title)."/'>". htmlentities($value->title) ."</a></h4>
                            <p>". $value->comment ."</p>
                            <p class='comment-information'><a class='name' id='". $value->user_id ."' href='#'>". $name ."</a> ". $Dates->date_with_time($value->date_added) ."</p>
                        </div> 
                        
                   </section>";
    }
    $output .= "</section>";
    echo $output;
} 
?>

</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_main.php'); ?>
</aside>


<?php include_template('footer.php'); ?>