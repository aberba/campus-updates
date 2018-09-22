<?php
global $Advertisements, $Posts, $Events, $Capture;

/*********** Aside Advertisement Thumbs ************/
$ads = $Advertisements->fetch_all();

if ($ads) {
    $output = "
              <section class='advertisement-box aside-section'>
                  <h3>Advertisement</h3>
                  <div class='slider'>";

    foreach ($ads as $a => $value) {
        $url = "/uploads/advertisements/". $value->file_name;
        if (!is_file(ADVERTISEMENTS_DIR. DS .$value->file_name)) {
            $url = $value->file_url;
        }
        
        $output .= "<div class='slide'>
                       <a href='//". $value->ad_url ."' target='_blank'>
                          <img src='". $url ."' alt='". $value->alt ."' />
                       </a>
                    </div>";
    }

    $output .= "</div>
              </section>";
    echo $output;
}


/***********  Popular Posts   ***********************/
$post_records = $Posts->fetch_popular(3);
if ($post_records) {
	$output = "<section class='popular-posts aside-section'>
	            <h3>Popular Posts</h3>";
    foreach ($post_records as $p => $value) {
    	$output .= "<section class='post-item clearfix'>
    	               <figure>
    	                   <a href='/posts/". $value->post_id. "/" .urlencode($value->title). "'>
    	                      <img src='/uploads/posts/". $value->image_one ."' alt='". htmlentities($value->title) ."'>             
    	                   </a>
    	               </figure>

    	               <p><a href='/posts/". $value->post_id. "/" .urlencode($value->title). "'>". $value->title ."</a><p>
    	           </section>";
    }
	$output .= "</section>";
	echo $output;
}
?>


<section class="aside-section">
<!-- Twitter Feeds-->
<a class="twitter-timeline" href="https://twitter.com/campus_updates" data-widget-id="523169400916946944">Tweets by @campus_updates</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</section>