<?php
require_once('../../files_admin/includes/initialize.php');

$css        = "cms_capture.css";
$js         = "cms_capture.js";
$page_title = "Captures";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">

	 <section class="captures-section">
      <section class="options">
          <select name="action">
              <option value="none" disabled="disabled" selected="selected">Bulk Action</option>
              <option value="publish">Publish</option>
              <option value="delete">Delete</option>
          </select>

          <button class="apply button"> Apply </button>
          <button class="add button"> + Add </button>
      </section>

<?php
$captures = $Capture->fetch_all();
if($captures) {
   foreach ($captures as $capture => $value) {
    $status = ($value->publish == 1) ? "Unpublish" : "Publish";
?>
     <figure id="capture<?php echo $value->capture_id; ?>" class="capture">
         <input type="checkbox" name="capture" value="<?php echo $value->capture_id; ?>">
         &nbsp;&nbsp;&nbsp; <span class="status"><?php echo $status; ?></span>
         &nbsp;&nbsp;&nbsp; <a class="edit" href="#">Edit</a>
         <img src="../uploads/captures/<?php echo $value->file_name; ?>" alt="<?php echo htmlentities($value->caption); ?>">
     </figure>

<?php   
   }

} else {
  echo "<p>No capture is uploaded yet</p>";
}
?>        
        
	 </section>
</section>

<?php include_template("footer.php"); ?>