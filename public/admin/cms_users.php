<?php
require_once('../../files_admin/includes/initialize.php');

if (!$Session->is_admin()) {
    echo " 
         <div style='background: #eeeeee; padding: 20px; margin: 100px auto; width: 600px; text-align: center;'>
            <p>Access Denied!</p>
            <p>Sorry, you donnot have permission to view this content</p>
            <p><a href='cms_dashboard.php'>&laquo; Back to Dasboard</a></p>
         </div>";
         exit();
}

$css        = "cms_users.css";
$js         = "cms_users.js";
$page_title = "Users";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">
	 <section class="users-section">
<?php
$users = $Users->fetch_all();

if($users) {
   $output = "<h3>Users of ". $Settings->site_name() ."campusupdates4u@gmail.com</h3>
              <table class='users-table table'>
                  <tr>
                      <th>Avt.</th>
                      <th>Fullname</th>
                      <th title='Gender'>G.</th>
                      <th title='User's Role>R.</th>
                      <th title='Last Login'>Login</th>
                      <td title='Blocked - Activated - Frozen'>B-A-F</th>
                      <th>Option</th>
                  </tr>";
   $session_id = $Session->user()['id'];
   foreach ($users as $user => $value) {
       $activation = ($value->activation == null) ? "Y" : "N";
       $blocked    = ($value->blocked    == 1) ?    "Y" : "N";
       $frozen     = ($value->frozen     == 1) ?    "Y" : "N";
       $freeze_btn = ($value->frozen     == 1) ?    "U F." : "F";

       $opt_btn    = "<a href='cms_edit_user.php?user_id=". $value->user_id ."' class='".$value->user_id."' title='edit'> Edit </a>
                      &nbsp;<a href='#' class='freeze' title='Freeze Action'>".$freeze_btn."</a>";

       if ($value->user_id == $session_id) $opt_btn = "";
    
       $output .= "<tr id='user".$value->user_id ."'>
                      <td><img src='../uploads/avatars/".$value->profile_photo."' alt='Photo' /></td>
                      <td>".$value->first_name. " ". $value->last_name."</td>
                      <td>".$value->gender."</td>
                      <td>".$Users->get_role($value->role)."</td>
                      <td>".$Dates->date_abbr($value->last_login)."</td>
                      <td>
                         <strong>".$activation."</strong>-
                         <strong>".$blocked."</strong>- 
                         <strong>".$frozen."</strong> 
                      </td>
                      <td>". $opt_btn ."</td>
                  </tr>";
   }
   $output .= "</table>";
   echo $output;
}else {
  echo "<p>No user has registered yet</p>";
}

?>
	 </section>
</section>

<?php include_template("footer.php"); ?>