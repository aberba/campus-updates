<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
    redirect_to("/offline/");
    exit();
}

if($Session->logged_in()) {
    redirect_to("/home/");
    exit();
}

//echo $Secure->password_secure('aberba1313');

$css              = "signin.css";
$js               = "signin.js";
$page_description = "Sign into your account";
$page_title       = "Sign In";

//echo $Secure->password_secure("aberba1313");

include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

    <section class="form-section">
         
         <form class="login-form form">
            <fieldset><legend>Sign Into Your Account</legend>
           	    <p>
                   <label for="email">E-mail Address: </label>
                   <input type="email" name="email" maxlength="40" placeholder=" Your account e-mail address ">
                </p>

                <p>
                   <label for="password">Password: </label>
                   <input type="password" name="password" placeholder=" Your account password ">
                </p>

                <p>
                   <input type="checkbox" name="keep_me_logged_in" value="yes">
                   <span>Keep me looged in</span>
                </p>

                <div class="options">
                	<p><a href="/signup/">You dont't have an account?</a></p>
                	<p><a href="/passwordrecovery/">I forgot my password.</a></p>
                </div>
                
                <p>
                    <input type="hidden" name="signin" value="yes">
                    <button type="button" class="login-btn button"> Sign In </button>
                </p>
            </fieldset>
         </form>

    </section>

    <section class="show-section">
         <div>
             <img src="/img/templates/image.png" alt="image">
         </div>
    </section>
</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_raw.php'); ?>
</aside>


<?php include_template('footer.php'); ?>