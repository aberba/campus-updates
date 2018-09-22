<?php
require_once('../../files_admin/includes/initialize.php');

$css        = "cms_posts.css";
$js         = "cms_posts.js";
$page_title = "Posts";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">

	 <section class="posts-section">
      <h3>Record Of Posts</h3>

<?php
$posts = $Posts->fetch_all();
if($posts) {
   foreach ($posts as $post => $value) {
    
      $image_one = "../uploads/posts/".$value->image_one;
      if (!is_file(POSTS_DIR . DS . $value->image_one)) {
         $image_one = "./img/templates/not-yet-edited.png";
      }

      $publish_status    = ($value->publish == 1) ? "Unpublish" : "Publish";
      $commentting_status = ($value->block_comments == 1) ? "Unblock Comments" : "Block Comments";
      $published = ($value->publish == 1) ? "yes"       : "No";
      $name      = $value->first_name. " ". $value->last_name;

      $tags      = $Posts->fetch_tags($value->post_id);
      $tags_html = "";
      if($tags) {
         foreach ($tags as $tag => $val) {
            $tags_html .= "<a href='cms_posts.php?tag=".$val->tag_id."'> ".$val->tag_name. " </a> ";
         }
      }
?>
      <section id="post<?php echo $value->post_id; ?>" class="post clearfix">
            <div class="post-options">
                <ul>
                   <li><a href="cms_edit_post.php?post_id=<?php echo $value->post_id; ?>"> Edit <a/></li>
                   <li><a class="publish" href="#"> <?php echo $publish_status; ?> </a></li>
                   <li><a class="block-comments" href="#"> <?php echo $commentting_status; ?> </a></li>
                   <li><a class="delete" href="#"> Delete </a></li>
                   <li><a class="preview" href="#"> Preview </a></li>
                   <li><a class="view-comments" href="#"> View Comments </a></li>
                </ul>
            </div>

            <div class="post-information">
                <h3><?php echo htmlentities($value->title); ?></h3>

                <table class="table">    
                    <tr>
                       <td>Tag</td>
                       <td><?php echo $tags_html; ?></td>
                    </tr>
                    <tr>
                       <td>By</td>
                       <td><?php echo $name; ?></td>
                    </tr>
                    <tr>
                       <td>Readers</td>
                       <td><?php echo $value->num_readers; ?></td>
                    </tr>
                    <tr>
                       <td>Published  Yes</td>
                       <td>Block Comments  No</td>
                    </tr>
                    <tr>
                       <td>Date</td>
                       <td><?php echo $Dates->date_with_time($value->num_readers); ?></td>
                    </tr>
                </table>
            </div>

            <figure>
                <img src="<?php echo $image_one; ?>">
            </figure>
      </section>

<?php      
   }
} else {
  echo "<p>No posts are added yet</p>";
}

?>       
	 </section>
</section>

<?php include_template("footer.php"); ?>