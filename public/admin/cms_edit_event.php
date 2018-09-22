<?php
$event_id = ( isset($_GET['event_id'])) ? (int) $_GET['event_id'] : null;

if(!$event_id) exit("Undefined event ID");

require_once('../../files_admin/includes/initialize.php');

$event= $Events->find_by_id($event_id);
if(!$event) exit("No results was found for post ID: ". $event_id);

$css        = "cms_edit_event.css";
$js         = "cms_edit_event.js";
$page_title = "Edit: ".$event->title;

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">
	 <section class="edit-section">
      <section class="tabs-section">
          <ul>
             <li><a id="information" class="button current" href="#">INFORMATION</a></li>
             <li><a id="images"      class="button" href="#">IMAGES</a></li>
          </ul>
      </section>

      <section class="form-section">
          <form class="information-form form">
              <label for="title">Event Title: </label>
              <input type="text" name="title" maxlength="200" placeholder="Enter event title" value="<?php echo $event->title; ?>">

              <label for="owner_name">Event Owner's Name: </label>
              <input type="text" name="owner_name" maxlength="50" placeholder="Event Owner's Name" value="<?php echo $event->owner_name; ?>">

              <label for="contact">Contact Information: </label>
              <input type="text" name="contact" maxlength="200" placeholder="Enter contact information for event" value="<?php echo $event->contact; ?>">

              <label for="priority">Event Priority Number (1 - 99): </label>
              <input type="text" name="priority" maxlength="2" placeholder="Enter event priority (1 - 99)" value="<?php echo $event->priority; ?>">


              <label for="post_content">Event Content:  @IMAGE2@</label>
              <textarea name="content" placeholder="Event content here"><?php echo $event->content; ?></textarea>

              <label for="tags">Tags: </label>
              <div class="tag-list">
                 <?php
                 $tag_list = $Tags->fetch_tags("event", $event_id);
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
                 $all_tags = $Tags->sort_fetch("event");
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

              <input type="hidden" name="event_id" value="<?php echo $event->event_id; ?>">
              <input type="hidden" name="save_event_information" value="yes">

              <button type="button" class="save button">Save</button>
              <button type="button" class="cancel button">Cancel</button>
          </form>

          <form class="images-form form">
             <label for="image">UPDATE POST IMAGES</label>
               
             <div class="current-images clearfix">
                 <img class="image-one image"   src="../uploads/events/<?php echo $event->image_one; ?>" title="Image One" alt="Image One">
                 <img class="image-two image"   src="../uploads/events/<?php echo $event->image_two; ?>" title="Image Two" alt="Image Two">
                 <img class="image-three image" src="../uploads/events/<?php echo $event->image_three; ?>" title="Image Three" alt="Image Three">
             </div>
            
             <label for="image-type">SELECT IMAGE TYPE BELOW</label>
             <select name="image_type">
                <option selected="selected" disabled="disabled">SELECT TYPE</option>
                <option value="1">ONE</option>
                <option value="2">TWO</option>
                <option value="3">THREE</option>
             </select>

             <input type="file" name="file" id="file">
             <input type="hidden" name="event_id" value="<?php echo $event->event_id; ?>">
             <div class="image-preview"></div>
             <button type="button" name="upload-btn" class="button">UPLOAD</button>
             <button type="button" name="remove-btn" class="button">REMOVE</button>
             <button type="button" name="preview-btn" class="button">PREVIEW</button>
          </form>
      </section>

	 </section>
</section>

<?php include_template("footer.php"); ?>