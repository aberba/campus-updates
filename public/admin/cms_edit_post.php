<?php
$post_id = ( isset($_GET['post_id'])) ? (int) $_GET['post_id'] : null;

if(!$post_id) exit("Undefined post ID");

require_once('../../files_admin/includes/initialize.php');

$post = $Posts->find_by_id($post_id);
if(!$post) exit("No results was found for post ID: ". $post_id);

$css        = "cms_edit_post.css";
$js         = "cms_edit_post.js";
$page_title = "Edit: ".$post->title;

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">
	 <section class="edit-section">
      <section class="tabs-section">
          <ul>
             <li><a id="information" class="button current" href="#">INFORMATION</a></li>
             <li><a id="images"      class="button" href="#">IMAGES</a></li>
             <li><a id="attachments" class="button" href="#">ATTACHMENTS</a></li>
          </ul>
      </section>

      <section class="form-section">
          <form class="information-form form">
              <label for="title">POST TITLE: </label>
              <input type="text" name="title" maxlength="200" placeholder="Enter post title" value="<?php echo $post->title; ?>">

              <label for="owner_name">POST OWNER'S FULLNAME: </label>
              <input type="text" name="owner_name" maxlength="50" placeholder="Enter post Owner's fullname" value="<?php echo $post->owner_name; ?>">

              <label for="owner_url_address">OWNER'S URL ADDRESS: </label>
              <input type="text" name="owner_url_address" maxlength="300" placeholder="Owner's url address eg. google plus profile link" value="<?php echo $post->owner_url_address; ?>">

              <label for="post_content">POST CONTENT:  @IMAGE2@</label>
              <textarea name="content" placeholder="Post content here"><?php echo $post->content; ?></textarea>

              <label for="tags">Tags: </label>
              <div class="tag-list">
                 <?php
                 $tag_list = $Tags->fetch_tags("post", $post_id);
                 if ($tag_list) {
                     $output = "<ul>";
                     foreach ($tag_list as $t => $value) {
                         $output .= "<li class='tag' id='".$value->tag_id."'>".$value->tag_name." <a class='delete' href='#' title='Delete tag'> x </a> </li>";
                     }
                     $output .= "</u>";
                     echo $output;
                 }
                 ?>

                 <?php
                 $all_tags = $Tags->sort_fetch("post");
                 if ($all_tags) {
                    $output = "<select name='tags'>
                                    <option selected disabled> Select tag </option>";
                     foreach ($all_tags as $allt => $value) {
                        $output .= "<option value='".$value->tag_id."'>". $value->tag_name ."</option>";
                     }
                     $output .= "
                              </select>

                              <button type='button' class='add-tag button'> + Add </button>
                              ";
                     echo $output;
                 }

                 ?>
              </div>

              <input type="hidden" name="post_id" value="<?php echo $post->post_id; ?>">
              <input type="hidden" name="save_post_information" value="yes">
              
              <br>
              <button type="button" class="save-information button">Save</button>
              <button type="button" class="cancel-information button">Cancel</button>
          </form>

          <form class="images-form form">
             <label for="image">UPDATE POST IMAGES</label>
               
             <div class="current-images clearfix">
                 <img class="image-one image"   src="../uploads/posts/<?php echo $post->image_one; ?>" title="Image One" alt="Image One">
                 <img class="image-two image"   src="../uploads/posts/<?php echo $post->image_two; ?>" title="Image Two" alt="Image Two">
                 <img class="image-three image" src="../uploads/posts/<?php echo $post->image_three; ?>" title="Image Three" alt="Image Three">
                 <img class="image-four image"  src="../uploads/posts/<?php echo $post->image_four; ?>" title="Image Four" alt="Image Four">
                 <img class="image-five image"  src="../uploads/posts/<?php echo $post->image_five ?>" title="Image Five" alt="Image Five">
                 <img class="image-six image"   src="../uploads/posts/<?php echo $post->image_six; ?>" title="Image Six" alt="Image Six">
             </div>
            
             <label for="image-type">SELECT IMAGE TYPE BELOW</label>
             <select name="image_type">
                <option selected="selected" disabled="disabled">SELECT TYPE</option>
                <option value="1">ONE</option>
                <option value="2">TWO</option>
                <option value="3">THREE</option>
                <option value="4">FOUR</option>
                <option value="5">FIVE</option>
                <option value="6">SIX</option>
             </select>

             <input type="file" name="file" id="file">
             <input type="hidden" name="post_id" value="<?php echo $post->post_id; ?>">
             <div class="image-preview"></div>
             <button type="button" name="upload-btn" class="button">UPLOAD</button>
             <button type="button" name="remove-btn" class="button">REMOVE</button>
             <button type="button" name="preview-btn" class="button">PREVIEW</button>
          </form>

          <form class="attachment-form form" id="<?php echo $post_id; ?>">
               <table class="attachment-table table">
                  <tr>
                     <td>File One</td>
                     <td class="file_one">
                      <?php 
                      if(!empty($post->file_one)) {
                          echo "<a href='../uploads/attachments/". $post->file_one. "' target='_blank'>" .$post->file_one. "</a>";
                      } else { echo  "No file uploaded"; }
                      ?>
                    </td>
                  </tr>
                  <tr>
                     <td>File Two</td>
                     <td class="file_two">
                      <?php 
                      if(!empty($post->file_two)) {
                          echo "<a href='../uploads/attachments/". $post->file_two. "' target='_blank'>" .$post->file_two. "</a>";
                      } else { echo  "No file uploaded"; }
                      ?>
                     </td>
                  </tr>
                  <tr>
                     <td>File Three</td>
                     <td class="file_three">
                      <?php 
                      if(!empty($post->file_three)) {
                          echo "<a href='../uploads/attachments/". $post->file_three. "' target='_blank'>" .$post->file_three. "</a>";
                      } else { echo  "No file uploaded"; }
                      ?>
                     </td>
                  </tr>
               </table>
                   
               <label for="attachment">SELECT ATTACHMENT FILE TYPE: </label>
               <select name="attachment">
                   <option selected="selected" disabled="disabled">SELECT ATTACHMENT TYPE</option>
                   <option value="1">ONE</option>
                   <option value="2">TWO</option>
                   <option value="3">THREE</option>
               </select>
               
               <br>
               <label for="file">SELECT ATTACHMENT FILE (zip): </label>
               <input type="file" name="file">
               <br>
               
               <input type="hidden" name="post_id" value="<?php echo $post->post_id; ?>">
               <button type="button" name="upload-btn" class="button">UPLOAD</button>
               <button type="button" name="delete-btn" class="button">DELETE</button>
          </form>
      </section>

        
	 </section>
</section>

<?php include_template("footer.php"); ?>