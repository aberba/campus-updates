<?php


require_once('../../files_admin/includes/initialize.php');

$css        = "cms_add_post.css";
$js         = "cms_add_post.js";
$page_title = "Add new Post";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">
	 <section class="edit-section">

      <section class="form-section">

          <form class="information-form form">
              <p>Add a new post</p>

              <label for="title">Post title: </label>
              <input type="text" name="title" maxlength="200" placeholder="Post title here">

              <label for="post_content">Post Content:</label>
              <textarea name="content" placeholder="Post content here"></textarea>

              <input type="hidden" name="post_id" value="<?php echo $post->post_id; ?>">
              <input type="hidden" name="add_post" value="yes">

              <button type="button" class="save button">Save</button>
              <button type="button" class="cancel button">Cancel</button>
          </form>

      </section>

	 </section>
</section>

<?php include_template("footer.php"); ?>