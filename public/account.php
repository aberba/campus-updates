<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
  redirect_to("/offline/");
}

if(!$Session->logged_in()) {
     redirect_to("/signin/");
}

$css              = "account.css";
$js               = "account.js";
$page_description = "Campus updates";
$page_title       = "My Account";


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->
    <section class="profile-section">
       <h3 class="toggle">Your Profile</h3>

<?php 
/********   Query Account Information   ********/
 $data = $Users->query_information();
 
// Show default avatar if users has not uploaded a profile photo
$avatar_url = "/uploads/avatars/". $data->profile_photo;
if (!is_file(AVATARS_DIR. DS. $data->profile_photo)) {
   $url = "/img/templates/";
   $avatar_url = ($data->gender == "M") ? $url. "default_male.png" : $url. "default_female.png";
}

$gender = ($data->gender == "M") ? "Male" :"Female";

if (!$Session->is_activated()) {
?>
       <section class="activation-section">
            <p>A confirmation message was sent to your e-mail when you created 
            your account. Please check your inbox and activate your account to gain 
            full access to our content.</p><br>
           
            <div class="button-section">
               <p>You got no such message ?</p>
               <button class="resend-btn button"> Resend Activation Message </button>
            </div>
       </section>

<?php
}
?>

          <table class="profile-table table">
          	  <tr>
          	  	 <th>Attribute</th> <th>Information &nbsp;&nbsp;&nbsp;&nbsp;<a class="edit" href="#">Edit</a> </th> 
          	  </tr>
          	  <tr>
          	  	 <td>Avatar</td> 
                 <td class="avatar-box">
                    <img class="avatar" src="<?php echo $avatar_url; ?>">
                    <form class="avatar-form">
                        <input type="file" name="avatar">
                    </form>
                    <div class="btn">Upload an image</div>
                 </td> 
          	  </tr>
          	  <tr>
          	  	 <td>First Name</td> <td class="fname" title="Your first name"><?php echo $data->first_name; ?></td> 
          	  </tr>
          	  <tr>
          	  	 <td>Last Name</td> <td class="lname" title="Your last name"><?php echo $data->last_name; ?></td> 
          	  </tr>

          	   <tr>
          	  	 <td>Username</td> <td class="uname" title="Your account username"><?php echo $data->user_name ?></td> 
          	  </tr> 

              <tr>
                 <td>Contact E-mail Address</td> <td class="contact_email" title="Your email for recieving e-mails from us"><?php echo $data->contact_email; ?></td> 
              </tr>

              <tr>
                 <td>Phone Number</td> <td class="phone_number" title="Your phone number"><?php echo $data->phone_number; ?></td> 
              </tr>

              <tr>
                 <td>Account E-mail Address</td> <td class="email" title="Your email address for login"><?php echo $data->email; ?></td> 
              </tr>
              
          	  <tr>
          	  	 <td>Gender</td> <td title="Your gender"><?php echo $gender; ?></td> 
          	  </tr>
          	  <tr>
          	  	 <td>Activited</td> 
                 <td title="Account Status">
                    <?php echo ($Session->is_activated() === true) ? "Yes" : "No" ?>
                 </td> 
          	  </tr>
          </table>
    </section>

    <section class="form-section">
          <div>
              <h3 class="toggle blue clearfix" title="Click to toggle">More Settings</h3>
              <form class="settings-form form">
                  

<?php
$output = "<label for='gender'>Gender: </label>";
if ($data->gender == "M")  {
      $output .= "<input type='radio' checked='checked' name='gender' value='M'>Male
            <input type='radio' name='gender' value='F'>Female";
} else {
      $output .= "<input type='radio' name='gender' value='M'>Male
            <input type='radio' checked='checked' name='gender' value='F'>Female";
}

$output .= "<label for='show_real_name'>Show Real Name ?</label>";
if ($data->show_real_name == "1")  {
      $output .= "<input type='radio' checked='checked' name='show_real_name' value='1'> Yes
                 <input type='radio' name='show_real_name' value='0'> No";
} else {
      $output .= "<input type='radio' name='show_real_name' value='1'> Yes
                   <input type='radio' checked='checked' name='show_real_name' value='0'> No";
}         
echo $output;                      
?>              
                  <input type="hidden" name="save_account_settings" value="yes">
                  <button type="button" class="save-settings button"> Save Settings </button>
              </form>
          </div>

          <div>
              <h3 class="toggle blue clearfix" title="Click to toggle">Change Your Account Password</h3>
              <form class="password-form form">
                  <label for="current-pass">Current Password: </label>
                  <input type="password" name="current-pass" placeholder="Your current password">

                  <label for="current-pass">New Password: </label>
                  <input type="password" name="pass1" placeholder="New password">

                  <label for="current-pass">Confirm Password: </label>
                  <input type="password" name="pass2" placeholder="Confirm password">

                  <button type="button" class="change-pass-btn button"> Change Password </button>
              </form>
          </div>
    

         <div>
             <h3 class="toggle blue clearfix" title="Click to toggle">Submit Content For Publishing</h3>
        	   <form class="upload-form form">
                 <label for="upload-type">Select Upload Type: </label>

                <?php
                 // fetch uploads categories and generate html
                 $categories = $UsersUploads->fetch_categories();
                 if($categories) {
                     $output = "<select name='category'>
                                      <option disabled='disabled' checked='checked'> Click to Select Category </option>
                                      ";

                     foreach ($categories as $cat => $value) {
                          $output .= "  
                                      <option value='".$value->category_id."'> ".htmlentities($value->category_name)." </option>                              
                                     ";
                     }
                     $output .= "</select>";
                     echo $output;
                 } else {
                    echo "<p>Sorry!, no upload category is added yet</p>";
                 }
                ?>

                 <label for="file">Select File To Upload (zip format only): </label>
                 <input type="file" name="file"> 

                 <label for="subject">Subject: </label>
                 <input type="text" name="subject" placeholder="Subject of the content">

                 <label for="description">Content Description: </label>
                 <textarea name="description" placeholder="A brief description of the file content"></textarea>

                 <button type="button" class="upload-btn button"> Submit Content </button>
             </form>
         </div>
    </section>
</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_raw.php'); ?>
</aside>


<?php include_template('footer.php'); ?>