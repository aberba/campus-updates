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

$css        = "cms_settings.css";
$js         = "cms_settings.js";
$page_title = "Settings";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">
	 <section class="settings-section">
        <h3>Site Settings</h3>

        <form class="settings-form form">
        <input type="hidden" name="save_settings" value="yes">
<?php
$S = $Settings->fetch_all();
if($Settings) {
   $output = "";
   foreach ($S as $setting => $value) {
       $setting_name = str_replace("_", " ", $value->setting_name);
       $output .= "<p class='setting' id='setting".$value->setting_id."'>
                     <label for='".$value->setting_name."'> ".$setting_name." <a class='delete' id='".$value->setting_id."' href='#'> x </a></label>
                     <input type='text' id='".$value->setting_id."' name='".$value->setting_id."' value='".$value->setting_value."' >
                  </p>
                  ";
   }

   echo $output;
} else {
   echo "<p> No setting have been added</p>";
}
?>  
            <button type="button" class="save button"> Save </button>
            <button type="button" class="add button"> + Add </button>
        </form>
	 </section>
</section>

<?php include_template("footer.php"); ?>