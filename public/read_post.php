<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}

$post_id = (isset($_GET['post_id']) && !empty($_GET['post_id'])) ? (int)$Database->clean_data($_GET['post_id']) : 0;
$P = $Posts->find_by_id($post_id);

if(!$P) {
    redirect_to("/notfound/");
    exit();
}

//$Posts->add_reader($post_id);

$css              = "read_post.css";
$js               = "read_post.js";
$page_description = $Database->clean_data(substr($P->content, 0, 150));
$page_title       = $P->title;

$images_path      = "/uploads/posts/";
$num_comments     = $Comments->count_all($post_id);

include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

    <section class="post-section">
    	 <h2 class="post-title"><?php echo htmlentities($P->title); ?></h2>

         <section class="introduction-section clearfix">
              <figure class="introduction-figure">
                   <img src="/uploads/posts/<?php echo $P->image_one; ?>">
              </figure>

              <div class="post-information">
                   <ul>
                       <li class="clearfix"><img src="/img/icons/edit.png" alt="Author: "><a href="<?php echo $P->owner_url_address; ?>" target="_blank"> <?php echo $P->owner_name; ?> </a></li>
                       <li class="clearfix"><img src="/img/icons/watch.png" alt="Date: "> <?php echo $Dates->date_only($P->date_added); ?></li>
                       <li class="clearfix"><img src="/img/icons/glasses.png" alt="Readers: "> <?php echo $P->num_readers; ?></li>
                       <li class="clearfix"><img src="/img/icons/comments.png" alt="Comments: "> <?php echo $num_comments; ?></li>
                   </ul>

                   <ul class="social-media-buttons">
                      <li>
                         <div id="fb-root" data-id=""></div>
                          <script>(function(d, s, id) {
                            var js, fjs = d.getElementsByTagName(s)[0];
                            if (d.getElementById(id)) return;
                            js = d.createElement(s); js.id = id;
                            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
                            fjs.parentNode.insertBefore(js, fjs);
                          }(document, 'script', 'facebook-jssdk'));</script>

                          <div class="fb-share-button" data-width="60"></div>
                       </li>
                       <li>
                         <a href="https://twitter.com/share" class="twitter-share-button" data-via="campus_updates">Tweet</a>
                         <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                       </li>
                       <li>
                         <!-- Place this tag in your head or just before your close body tag. -->
                         <script src="https://apis.google.com/js/platform.js" async defer></script>

                         <!-- Place this tag where you want the share button to render. -->
                         <div class="g-plus" data-action="share" data-annotation="none"></div>
                       </li>
                   </ul>
              </div>
         </section>

         <section class="post-content clearfix">
              <?php echo $Posts->insert_images($P->content); ?>
         </section>

         
<?php
if (!empty($P->file_one) || !empty($P->file_two) || !empty($P->file_three)) {
    $output = "<section class='attachments-section blue'>";

    if (!$Session->logged_in()) {
        $output .= "<p>Please <strong><a href='/login/'>sign in</a><s/trong> to gain access to attached files</p>";

    } elseif ($Session->logged_in() && !$Session->is_activated()) {
        $output .= "<p>Please <strong><a href='/account/'> activate </a></strong> your account to gain access to attached files</p>";

    } else {
        $output .= "
                 <h3>Files Attached To Post </h3>
                 <ul>";

        if (!empty($P->file_two)) {
            $output .= "<li>One: <a href='/uploads/attachments/".$P->file_two."' target='_blank'> download </a></li>";
        }

        if (!empty($P->file_one)) {
            $output .= "<li>Two: <a href='/uploads/attachments/".$P->file_one."' target='_blank'> download </a></li>";
        }

        if (!empty($P->file_three)) {
            $output .= "<li>Three: <a href='/uploads/attachments/".$P->file_three."' target='_blank'> download </a></li>";
        }
    } 

    $output .= "</ul></section>";
    echo $output;
}

?>

    </section>         

<?php
$post_tags = $Tags->fetch_tags("post", $post_id);
if($post_tags) {
    $output = "<section class='tags-section'> 
                 <p>
                   <img class='tag-image' src='/img/icons/label.png' alt='Tags: ' title='Tags' />";
    foreach ($post_tags as $tg => $value) {
        $output .= "<a href='/tags/posts/" .urlencode(strtolower($value->tag_name)). "/' title='" .htmlentities($value->tag_name). "'>" .htmlentities($value->tag_name). "</a>";
    }
    $output .= "</p> </section>";
    echo $output;
}
?>
         

         
<?php
$related = $Posts->fetch_related($post_id);
if($related) {
     $output = "<section class='related-posts-section clearfix'>
              <h3>You May Also Like: </h3>";

     foreach ($related as $post => $value) {
     $image_path = "/uploads/posts/". $value->image_one;
     $url = "/posts/" .$value->post_id. "/". urlencode($value->title). "/";

     $output .= "<section class='post'>
                    <figure>
                        <a href='". $url ."'>
                           <img src='".$image_path ."' alt='". $value->title. " image'>
                        </a>

                        <figcaption><a href='". $url ."'>". htmlentities($value->title) ."</a></figcaption>
                    </figure>
                 </section>";
     }
     $output .= "</section>";
     echo $output;
}
?>
    
    


    <section class="comments-section">
<?php  
if($num_comments < 1) { //Comments

     echo "<p>Be the first to post a comment.</p>";
     
} else {
     echo "<h4>Comment(s): <strong>". $num_comments. "</strong> </h4>";
     $comment_records = $Comments->find_by_post_id($post_id);

     foreach ($comment_records as $comment => $value) {

     $name = ($value->show_real_name == 1) ? $value->first_name." ".$value->last_name : $value->user_name;

     // Show default avatar if users has not uploaded a profile photo
    $avatar_url = "/uploads/avatars/". $value->profile_photo;
    if (!is_file(AVATARS_DIR. DS. $value->profile_photo)) {
       $url = "/img/templates/";
       $avatar_url = ($value->gender == "M") ? $url. "default_male.png" : $url. "default_female.png";
    }
?>
         
         <section class="comment clearfix">
              <figure>
                  <img src="<?php echo $avatar_url; ?>">
              </figure>
             
              <div class="comment-text">
                  <div>
                       <p><?php echo $value->comment; ?></p>
                       <p class="comment-information"><a class="name" id="<?php echo $value->user_id; ?>" href="#"><?php echo htmlentities($name); ?></a> <?php echo $Dates->date_with_time($value->date_added); ?></p>
                  </div>
              </div> 
         </section> 
<?php
     } //End of foreach comments loop
     

} // End 'if thre are comments'
?>              

      </section> <!-- end of comments section -->


      <section class="comment-form-section">
           <form id="comment-form<?php echo $post_id; ?>" class="comment-form form">
                <label for="comment">Post your comment:</label>

                <p class="arrow"></p>
                <textarea name="comment" maxlength="250"></textarea>
                <button class="comment-btn button" type="button">Post Comment</button>
           </form>
      </section>


</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_posts.php'); ?>
</aside>


<?php include_template('footer.php'); ?>