<?php
require_once('../../files_admin/includes/initialize.php');

if (!$Session->is_super_admin()) {
   exit("Sorry, you donnot have permission to undertake this action.<br>
         <a href='cms_dashboard.php'>&laquo; Back to dashboard</a>");
}

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    exit("Undefined user ID");
}

$user_id = (int)$Database->clean_data($_GET['user_id']);
$user = $Users->find_by_id($user_id);
if (!$user) {
   exit("No record of user found");
}

$css        = "cms_edit_user.css";
$js         = "cms_edit_user.js";
$page_title = "Edit User";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">
	 <section class="edit-section">
         <h3>Edit User &raquo; <?php echo $user->first_name ." ". $user->last_name; ?></h3>
         <form class="edit-form form">
            <label for="role"> Users Role: </label>
            <select name="role">
            <?php 
            $role = (int)$user->role;
            $output = "";
            if ($role == 0) {
               $output .= '<option selected="selected" value="0"> Member </option>';
            } else {
               $output .='"<option value="0"> Member </option>';
            }

            if ($role == 1) {
               $output .= '<option selected="selected" value="1"> Moderator </option>';
            } else {
               $output .= '<option value="1"> Moderator </option>';
            }

            if ($role == 2) {
               $output .= '<option selected="selected" value="2"> Admin </option>';
            } else {
               $output .= '<option value="2"> Admin </option>';
            }

            if ($role == 3) {
               $output .= '<option selected="selected" value="3"> Super Admin </option>';
            } else {
               $output .= '<option value="3"> Member </option>';
            }
            $output .= '</select>';

            echo $output;
            ?>
            </select>


            <label for="block"> Block User: </label>
            <?php
            $output = "";

            if ($user->blocked == 1) {
               $output .= '<input type="radio" name="block" value="0">NO
                           <input type="radio" name="block" checked="checked" value="1">YES';
            } else {
               $output .= '<input type="radio" name="block" checked="checked" value="0">NO
                           <input type="radio" name="block" value="1">YES';
            }
            echo $output;
            ?>

            <label for="resume_date"> Date of Resume: </label>
            <?php
               echo $Dates->generate_date_select(date("d", time()), date("m", time()), date("Y", time()));
            ?>

            <input type="hidden" name="save_user" value="yes">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <button type="button" class="save-btn button"> Save </button>
         </form>
	 </section>
</section>

<?php include_template("footer.php"); ?>