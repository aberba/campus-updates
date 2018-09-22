<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}

$event_id = (isset($_GET['event_id']) && !empty($_GET['event_id'])) ? (int)$Database->clean_data($_GET['event_id']) : 0;
$E = $Events->find_by_id($event_id);

if(!$E) {
    redirect_to("/notfound");
    exit();
}

$css              = "read_event.css";
$js               = "read_event.js";
$page_description = $Database->clean_data(substr($E->content, 0, 150));
$page_title       = $E->title;

$images_path      = "/uploads/events/";

include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

    <section class="event-section">
    	 <h2 class="event-title"><?php echo htmlentities($E->title); ?></h2>

         <section class="introduction-image clearfix">
              <figure class="introduction-figure">
                   <img src="/uploads/events/<?php echo $E->image_one; ?>">
              </figure>
         </section>

         <section class="event-content clearfix">
              <?php echo $Events->insert_images($E->content); ?>
         </section>

         <section class="event-information">
             <ul>
                 <li class="clearfix"><img src="/img/icons/edit.png" alt="Author: "><a href="#"> <?php echo htmlentities($E->owner_name); ?> </a></li>
                 <li class="clearfix"><img src="/img/icons/watch.png" alt="Date: "> <?php echo $Dates->date_only($E->date_added); ?></li>
                 <li class="clearfix"><img src="/img/icons/glasses.png" alt="Viewers: "> <?php echo $E->num_readers; ?></li>

                 <?php 
                  if (!empty($E->contact)) {
                     echo "<li class='clearfix'><img src='/img/icons/phone.png' alt='Contact: ''>". $E->contact ."</li>";
                  }
                 ?>
             </ul>
         </section>

         <form>
             <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
         </form>
    </section>         

<?php
/************ Events Tags ********************/
$event_tags = $Tags->fetch_tags("event", $event_id);
if($event_tags) {
    $output = "<section class='tags-section'> 
                  <p><img class='tag-image' src='/img/icons/label.png' alt='Tags: ' title='Tags'>";
    foreach ($event_tags as $tag => $value) {
        $output .= "<a href='/tags/events/" .urlencode(strtolower($value->tag_name)). "/' title='" .htmlentities($value->tag_name). "'>" .htmlentities($value->tag_name). "</a> ";
    }
    $output .= "</p> </section>";
    echo $output;
}





/************* Related Events ******************/
$record_related = $Events->fetch_related($event_id);
if ($record_related) {
    $output = "<section class='event-section'>
                   <h3>You May Also Like: </h3>";
                   
    foreach ($record_related as $e => $value) {
        $output .= "<section id='event". $value->event_id ."' class='event'>
                            <figure>
                                <a href='/events/". $value->event_id ."/". urlencode($value->title) ."'>
                                   <img src='/uploads/events/".$value->image_one."'>
                                </a>
                                
                                <figcaption><a href='/events/". $value->event_id ."/". urlencode($value->title) ."'>".$value->title."</a></figcaption>
                            </figure>
                    </section>
                    ";
    }
    $output .= "</section>";
    echo $output;
}
?>
         

</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_events.php'); ?>
</aside>


<?php include_template('footer.php'); ?>