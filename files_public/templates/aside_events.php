<?php
global $Advertisements, $Posts, $Events, $Capture;

/*********** Aside Advertisement Thumbs ************/
$ads = $Advertisements->fetch_all("aside");

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


/***********  Popular Events   ***********************/
$event_records = $Events->fetch_popular(3);
if ($event_records) {
	$output = "<section class='popular-events aside-section'>
	            <h3>Popular Events</h3>";
    foreach ($event_records as $e => $value) {
    	$output .= "<section class='event-item clearfix'>
    	               <figure>
    	                   <a href='/events/". $value->event_id. "/" .urlencode($value->title). "'>
    	                      <img src='/uploads/events/". $value->image_one ."' alt='". htmlentities($value->title) ."'>             
    	                   </a>
    	               </figure>

    	               <p><a href='/events/". $value->event_id. "/" .urlencode($value->title). "'>". $value->title ."</a><p>
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