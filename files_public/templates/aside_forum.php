<?php
global $Advertisements;

/*********** Aside Advertisement Thumbs ************/
$ads = $Advertisements->fetch_all("aside");

if ($ads) {
  $output = "
            <section class='advertisement-box aside-section'>
                  <h3>Advertisement</h3>
                  <div class='slider'>";

    foreach ($ads as $a => $value) {
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

    $output .= "</div>
              </section>";
    echo $output;
}



?>


<section class="aside-section">
<!-- Twitter Feeds-->
<a class="twitter-timeline" href="https://twitter.com/campus_updates" data-widget-id="523169400916946944">Tweets by @campus_updates</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</section>