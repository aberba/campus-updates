<?php
require_once('../../files_admin/includes/initialize.php');

$css        = "cms_manager.css";
$js         = "cms_manager.js";
$page_title = "Manager";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">
	 <section class="notification-section">
	    <h3 class="toggle">Notifications</h3>
        
        <div>
        	
        </div>
	 </section>

    <section class="tags-section">
        <h3 class="toggle">Tags On Posts And Events</h3>
        
        <button class="show-form-btn button"> + Add a new tag </button>
        <form class="tag-form form">
            <label name="tag_name">Tag Name: </label>
        	<input type="text" name="tag_name" placeholder="Enter tag name">

        	<label for="tag_type"> Select tag Type: </label>
            <select name="tag_type">
            	<option selected="selected" disabled="disabled"> Select tag type</option>
            	<option value="post"> Post </option>
            	<option value="event"> Event </option>
            </select>

            <input type="hidden" name="add_new_tag" value="yes">
        	<button type="button" class="add-btn button"> + Add </button>
        	<button type="button" class="cancel-btn button"> Cancel </button>
        </form>
<?php   
$tag_records = $Tags->fetch_all();
if ($tag_records) {
	$output = "<table class='tags-table table'>
	        	<tr>
	        		<th> Tag Name &nbsp;&nbsp;&nbsp;<a class='edit' href='#'>Edit</a></th>
	        		<th> Category </th>
	        		<th> Option </th>
	        	</tr>";  

	foreach ($tag_records as $t => $value) {
		$output .= "<tr id='tag". $value->tag_id ."'>
		        		<td class='edit-name'>". $value->tag_name ."</td>
		        		<td>". $value->type ."</td>
		        		<td><a href='#' id='tag". $value->tag_id ."' class='delete'> X </a></td>
		        	</tr>";
	}
	$output .= "</table>";
	echo $output;
}   else {
  echo "<p>No tags are added yet</p>";
}
?>
    </section>

    <section class="template-images-section">
        
    </section>
</section>

<?php include_template("footer.php"); ?>