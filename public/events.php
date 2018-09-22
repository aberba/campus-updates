<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
  redirect_to("/offline/");
}

$css              = "events.css";
$js               = "events.js";
$page_description = "Campus updates";
$page_title       = "Events";


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

    <section class="events-section clearfix">

<?php
$current_page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = ceil($Settings->records_per_pagination() * 2);
$total_records    = $Events->count_all();
$Pagination       = new Pagination($current_page, $records_per_page, $total_records);

$E = $Events->fetch_all($Pagination->offset(), $records_per_page);
if($E) {
     $output = "";
     foreach ($E as $evt => $value) {
          $output .= "<section id='event". $value->event_id ."' class='event'>
                            <figure>
                                <a href='/events/". $value->event_id ."/". urlencode($value->title) ."'>
                                   <img src='/uploads/events/".$value->image_one."' alt='". htmlentities($value->title) ."'>
                                </a>
                                
                                <figcaption><a href='/events/". $value->event_id ."/". urlencode($value->title) ."'>".$value->title."</a></figcaption>
                            </figure>
                    </section>
                    ";
     }
     echo $output;
} else {
     echo "<p>No event is published yet</p>";
}
?>
    </section>

<?php
/***********************  Pagination Section   *****************/

//if there are more than one record, the evaluate pagination
if($Pagination->total_pages() > 1) { 
   
   $output = "<section class='pagination-section clearfix'>";

    if($Pagination->has_previous_page()) {
        $output .= " <a class='previous' href='/events/";
        $output .= $Pagination->previous_page();
        $output .= "/'>&laquo; Previous</a>";
   }

   // Shows pages Numbers
   for($i =1; $i <= $Pagination->total_pages(); $i++) {
     //NB: $i is the pages number
      if($i == $current_page) {
          $output .= "<span class='page-numbers'> <span class='current'>{$i}</span>";
      } else {
        $output .= "<a href='/events/{$i}/'>{$i}</a> </span>";
      }
   }

   if($Pagination->has_next_page()) {
        $output .= " <a class='next' href='/events/";
        $output .= $Pagination->next_page();
        $output .= "/'>Next &raquo; </a>";
   }
         
   $output .= "</section>";
   echo $output;
} // end of if there are more than a page e

?>

</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_events.php'); ?>
</aside>


<?php include_template('footer.php'); ?>