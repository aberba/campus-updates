<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
  redirect_to("/offline/");
}

$css              = "capture.css";
$js               = "capture.js";
$page_description = "Capture";
$page_title       = "Capture";


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

    <section class="capture-section clearfix">
<?php
$current_page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = ceil($Settings->records_per_pagination() * 3);
$total_records    = $Capture->count_all();
$Pagination       = new Pagination($current_page, $records_per_page, $total_records);

$capture_records = $Capture->fetch_all($Pagination->offset(), $records_per_page);
$output = "";
if($capture_records) {
  
	foreach ($capture_records as $capture => $value) {
      $output .= "
                  <section id='capture". $value->capture_id ."' class='capture'>
                       <figure>
                       	<img src='/uploads/captures/". $value->file_name ."' alt='". htmlentities($value->caption) ."'>
                       	<figcaption>". htmlentities($value->caption) ."</figcaption>
                       </figure>
              	  </section>";
	}
} else {
    $output .= "<p>No capture has been added yet</p>";
}
echo $output;
?>	
    </section>


<?php
/***********************  Pagination Section   *****************/

//if there are more than one record, the evaluate pagination
if($Pagination->total_pages() > 1) { 
   
   $output = "<section class='pagination-section clearfix'>";

    if($Pagination->has_previous_page()) {
        $output .= " <a class='previous' href='/capture/";
        $output .= $Pagination->previous_page();
        $output .= "/'>&laquo; Previous</a>";
   }

   // Shows pages Numbers
   for($i =1; $i <= $Pagination->total_pages(); $i++) {
     //NB: $i is the pages number
      if($i == $current_page) {
          $output .= "<span class='page-numbers'> <span class='current'>{$i}</span>";
      } else {
        $output .= "<a href='/capture/{$i}/'>{$i}</a> </span>";
      }
   }

   if($Pagination->has_next_page()) {
        $output .= " <a class='next' href='/capture/";
        $output .= $Pagination->next_page();
        $output .= "/'>Next &raquo; </a>";
   }
         
   $output .= "</section>";
   echo $output;
} // end of if there are more than a page
?>

</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_main.php'); ?>
</aside>


<?php include_template('footer.php'); ?>