<?php
session_start();
  
require_once('includes/initialize.php');

if(logged_in()) {
    redirect_to("logout.php");
}

$style = 'password_recovery.css';
$page_title = 'Password Recovery';

include('includes/header.php');


// Initialize variables
$user_name = "";
$_SESSION['rec_user_name'] = "";

if(isset($_POST['user_name'])) {
    $user_name = clean_string($_POST['user_name']);
    
    // set user name as session to make it static
    $_SESSION['rec_user_name'] = $user_name;
}

if(isset($_POST['email'])) {
    $user_name = clean_string($_POST['email']);
}

// Initialize username form

$user_name_form  = <<<HTML
  <form method="post" class="form-users" action="{$_SERVER['PHP_SELF']}">
  <br /><br />
  <p>
    <label for="user_name">Please enter your User Name:</label><br />
    <input type="text" name="user_name" autocomplete="off" 
    required="required" pattern="[@a-zA-Z0-9_-]+" title="Invalid Characters" />
    <br />
    
    <br /><br />
    <input type="submit" name="submit" value="Proceed" />
  </form>    
HTML;

$email_form = <<<HTML
   <br /><br />
     <form method="post" class="form-users" 
     action="{$_SERVER['PHP_SELF']}">
    
     <p>
       <label for="user_name">Please enter your Email Address:</label><br />
       <input type="email" name="email" autocomplete="off" required="required" />
     </p>
     <br /><br />
       <input type="hidden" name="user_name" 
       value="{$_SESSION['rec_user_name']}" />
       
       <input type="submit" name="submit" value="Continue" />
     </form>
HTML;
?>

<section id="content"> <!-- left content -->
<h1 id="pageHeader">Password Recovery &raquo; <?php echo $site_name; ?></h1>

<section >
<?php
// Print messages if any
$messenger->message_success();
$messenger->message_error();





// When page first loads
if(($_SERVER['REQUEST_METHOD'] == "GET") && !isset($_GET['result'])) {
   echo $user_name_form;                             
}

// If user name is posted for proccessing
if(isset($_POST['user_name']) && !isset($_POST['email'])) {
   $user_name = clean_string($_POST['user_name']);
   $query = "SELECT * FROM users WHERE user_name = '$user_name' LIMIT 1";
   $data  = mysqli_query($dbc, $query);
   
   if(mysqli_num_rows($data) == 1) {
      echo $email_form;
      
   }else {
      echo '<p class="error">Invalid User ssssName</p>';
      echo $user_name_form;
   }  
}

// If user name and email are set
if(isset($_POST['user_name']) && isset($_POST['email']) &&
 !empty($_POST['user_name']) && !empty($_POST['email'])) {
   $user_name = clean_string($_POST['user_name']);
   $email     = clean_string($_POST['email']); 
   
   $query  = "SELECT * FROM users WHERE user_name = '$user_name' ";
   $query .= "AND email = '$email' LIMIT 1";
   $data  = mysqli_query($dbc, $query);
   
   if(mysqli_num_rows($data) == 1) {
      $temp_pass = $user_name.substr(md5($user_name.time()), 0, 5);
      $db_temp_pass = sha1(md5($temp_pass));
      
      $query  = "UPDATE users SET password = '$db_temp_pass' WHERE ";
      $query .= "user_name = '$user_name' AND email = '$email' LIMIT 1";
      $result = mysqli_query($dbc, $query);
      
      if($result) {
        //unset the $_SESSION['rec_user_name'] - used for the recovery prccess
         unset($_SESSION['rec_user_name']);
         
        // Send email with temporal password
         $subject = "$site_name; Password Recovery";
      
         $message = <<<HTML
      <!DOCTYPE html>
      <html lang="en">
         <head>
            <meta charset="uft-8" />
            <title>$subject</title>
         </head>
         <body>
            <div>
               <h1>$subject</h1><br />
               <p>Hi, <br />
               We recieved a request from you to recover your password.<br /> 
               Your temporal password for $site_name; is now:
               <strong>$temp_pass</strong>. <br />You can now log into your 
               account using this password. We recommend you change your pasword
               to a more secure one, in your account settings, as soon as you 
               login. </p>
               <p>You can contact us from our website if you have any 
               difficulties.</p> <br /><br /><br />
               <h1>$site_name;<br /><small>super genuine info</small></h1>
            </div>
         </body>
      </html>
HTML;
         //send email
         $sent = $Email->email($email, $subject, $message);
         
         // set message to be printed on page
         $msg = "A password recovery message has been sent to <em>$email</em>.<br /> 
         Please check your inbox to recover your password";
         $messenger->message_success($msg);
         redirect_to("password_recovery.php?result=sent");
         exit();
         
      }else {
       $messenger->message_error("Ooops! Error saving data, please try again later.");
         redirect_to("password_recovery.php?result=error");
         exit();
         
      }// End of save temporal password
       
   }else {
      echo '<p class="error">Invalid Email Address</p>';
      $user_name = @$_POST['user_name'];
      echo $email_form;
   } // End of if valid user name and password
    
} // End of post user name and password

?>
</section>

</section>



<aside id="aside"> <!-- aside -->
<?php
include('includes/aside.php');	
?>
</aside>




<?php
include('includes/footer.php');
?>
