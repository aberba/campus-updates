<?php
require_once('../../files_admin/includes/initialize.php');

$css        = "cms_edit_event.css";
$js         = "cms_add_event.js";
$page_title = "Add Event";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">
	 <section class="edit-section">
    
      <section class="form-section">
          <h3>Add a New Event</h3>

          <form class="information-form form">
              
              <label for="title">Post title: </label>
              <input type="text" name="title" maxlength="200" placeholder="Enter event title">

              <label for="post_content">Post Content: </label>
              <textarea name="content" placeholder="Event content here"></textarea>

              <input type="hidden" name="add_new_event" value="yes">

              <button type="button" class="save button">Save</button>
              <button type="button" class="cancel button">Cancel</button>
          </form>

      </section>

	 </section>
</section>

<?php include_template("footer.php"); ?>