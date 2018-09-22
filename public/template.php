<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}

$css              = "";
$js               = "";
$page_description = "Campus updates";
$page_title       = "";


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

    <section class="">
          
    </section>
</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_raw.php'); ?>
</aside>


<?php include_template('footer.php'); ?>