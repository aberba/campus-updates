<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}

if($Session->logged_in()) {
    redirect_to("/home/");
    exit();
}

$css              = "password_recovery.css";
$js               = "password_recovery.js";
$page_description = "Campus updates";
$page_title       = "Password Recovery";


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

    <section class="recovery-section">
          <h3>Account Password Recovery</h3>

          <form class="welcome-form form">
          	  <p>Hello, welcome to the password recovery section. 
              Continue if only you already have an account, but have lost or forgotten your password. To get access to your account safely, please follow this short procedure .</p>
              <p>Please click next to proceed.</p>
          	  <button type="button" class="welcome-btn button"> Next &raquo; </button>
          </form>

          <form class="email-form form">
          	  <label for="email">Enter your account email address: </label>
          	  <input type="email" name="email" placeholder=" eg. johndoe@mail.com ">

          	  <button type="button" class="email-btn button"> Continue &raquo; </button>
          </form>
    </section>
</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_raw.php'); ?>
</aside>


<?php include_template('footer.php'); ?>