<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}

$css              = "not_found.css";
$js               = "not_found.js";
$page_description = "";
$page_title       = "Resource Not Found";


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

    <section class="message-section">
       <figure>
           <img src="/img/templates/brainy-smurf.png" alt="Not foung robot">
       </figure>

       <p>Sorry, the resource you requested could not be found. <br>It may have been removed or you provided an invalid request address.</p>
       <p>&laquo; Back to <a href="/home/">homepage</a></p>
   </section> 
   
</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_raw.php'); ?>
</aside>


<?php include_template('footer.php'); ?>