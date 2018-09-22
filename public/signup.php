<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}

if($Session->logged_in()) {
    redirect_to("/home/");
    exit();
}

$css              = "signup.css";
$js               = "signup.js";
$page_description = "Create an account";
$page_title       = "Sign Up";


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

    <section class="form-section">
        <h3>Fill in this form to create an account</h3>
        
        <form class="signup-form form">
        	<fieldset><legend>Personal Information</legend>
        	    <p><a href="/about/#privacy">Privacy</a></p>

                <p>
	        	   <label for="fname">Name: </label>
	               <input type="text" name="fname" maxlength="20" placeholder=" First name">
	               <input type="text" name="lname" maxlength="20" placeholder=" Last name">
	        	</p>

	        	<p>
	               <label for="gender">Select Your Gender: </label>
	               <input type="radio" name="gender" value="M">Male 
	               <input type="radio" name="gender" value="F">Female
	        	</p>
        	</fieldset>

        	<fieldset><legend>Account Information</legend>
        		<p>
	        	   <label for="uname">Username: </label>
	               <input type="text" name="uname" maxlength="20" placeholder=" Enter a username ">
	        	</p>

	        	<p>
	        	   <label for="email">E-mail Address: </label>
	               <input type="email" name="email" maxlength="40" placeholder=" Your e-mail address ">
	        	</p>

	        	<p>
	        	   <label for="pass1">Password: </label>
	               <input type="password" name="pass1" placeholder=" Password ">
	               <input type="password" name="pass2" placeholder=" Confirm password ">
	        	</p>
        	</fieldset>

        	<fieldset><legend>Terms Of Use Agreement</legend>
        		<input type="checkbox" name="agreed" value="yes"> <span>I solely agree to the <a href="/about/#terms-of-use">terms of use</a>. </span>
        	</fieldset>
            
            <input type="hidden" name="signup" value="yes">
        	<button type="button" class="signup button"> Sign Up </button>
        </form>
    </section>
</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_raw.php'); ?>
</aside>


<?php include_template('footer.php'); ?>