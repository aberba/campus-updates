<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
	redirect_to("/offline/");
}

$css              = "contact.css";
$js               = "contact.js";
$page_description = "Campus updates";
$page_title       = "Contact Us";


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

    <section class="contact-section clearfix">
         <h1>Contact Campus Updates</h1>
         
         <table class="table">
         	 <tr>
         	 	<td>Site Name</td>
         	 	<td><?php echo $Settings->site_name(); ?></td>
         	 </tr>
         	 <tr>
         	 	<td>E-mail Address</td>
         	 	<td><?php echo $Settings->site_public_email(); ?></td>
         	 </tr>
         	 <tr>
         	 	<td>Phone Number</td>
         	 	<td><?php echo $Settings->site_phone_number(); ?></td>
         	 </tr>
         	 <tr>
         	 	<td>Connnect With Us</td>
         	 	<td>
         	 		<ul>
		               <li><a href="//<?php echo $Settings->site_facebook_url(); ?>" target="_blank"><img src="/img/icons/facebook.png" alt="Facebook"></a></li>
		               <li><a href="//<?php echo $Settings->site_twitter_url(); ?>" target="_blank"><img src="/img/icons/twitter.png" alt="Twitter"></a></li>
	                   <li><a href="//<?php echo $Settings->site_youtube_url(); ?>" target="_blank"><img src="/img/icons/youtube.png" alt="Youtube"></a></li>
	                </ul>
         	 	</td>
         	 </tr>
         </table>
    </section>


<?php
$moderators = $Users->fetch_moderators();
if ($moderators) {
   $output = "<section class='moderators'>
                  <section id='moderators' class='moderators-section'>
                     <h1>Moderators of Campus Updates</h1>
                     <p>You can also contact any of the following moderators.</p>";
   foreach ($moderators as $m => $value) {
      $avatar_url = "/uploads/avatars/".$value->profile_photo;

      $social_media = "#";
      if (!empty($value->social_media_profile)) {
         $social_media = "//".$value->social_media_profile;
      }

      $output .= "<section class='moderator clearfix'>
                      <figure>
                         <img src='". $avatar_url ."' alt='Avatar' />
                      </figure>

                      <div class='info'>
                          <p><a href='". $social_media ."'>".$value->first_name ." ". $value->last_name ."</a></p>
                          <p>Since ".date("M d, Y", $value->date_registered)."</p>
                      </div>
                  </section>
                  ";
   }
   $output .= "     </section>
               </section>";
   echo $output;
}
?> 
       </section>
    </section>
</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_raw.php'); ?>
</aside>


<?php include_template('footer.php'); ?>