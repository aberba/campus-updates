<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}

$category = (isset($_GET['category'])) ? $Database->clean_data($_GET['category']) : "";
$tag_name = (isset($_GET['tag']))      ? urldecode($Database->clean_data($_GET['tag'])) : "";
$tag_id   = $Tags->fetch_tag_id_with_name($tag_name);

$current_page = (isset($_GET['page']))     ? (int)$Database->clean_data($_GET['page']) : 1;
$cat_title    = $category;
$cat_title[0] = strtoupper($cat_title[0]); //capitalize category name for page title

$records_per_page = $Settings->records_per_pagination();
$total_records    = null; //calculated in switch statement below
$Pagination       = null;

$css              = "tags.css";
$js               = "tags.js";
$page_description = "Campus updates";
$page_title       = $cat_title ." &raquo; ". $tag_name;


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

    <section class="tagged-section">
 <?php
$output = "";
switch ($category) {
		case 'posts':
            $total_records = $Tags->count_tagged("posts", $tag_id);
        
		    $Pagination    = new Pagination($current_page, $records_per_page, $total_records);
			$post_results  = $Posts->find_by_tag_name($tag_name, $records_per_page, $Pagination->offset());

			if ($post_results) {
				$output .= "<section class='posts-section'>";
				$output .= "<p class='search-title'>Results for posts tagged <em>". $tag_name . "</em></p>";

				foreach ($post_results as $p => $value) {
					$image_url = "/uploads/posts/".$value->image_one;

					//use title length and content length to substring the introduction content
                    $max_length = 210;
                    $title_length = strlen($value->title);
                    $remaining_length = (int)($max_length - $title_length);

					$output .= "
                                <section class='post clearfix'>
					                <figure>
					                     <a href='/posts/".$value->post_id."/".urlencode($value->title)."/'>
					                       <img src='".$image_url ."' alt='". htmlentities($value->title) ." image'>
					                     </a>
					                </figure>

					                <div class='introduction'>
					                    <h4>". htmlentities($value->title) ."</h4>
					                    <p>". substr($Database->clean_data($value->content, "<a>"), 0, $remaining_length). " ...</p>
					                    <a class='read-more' href='/posts/". $value->post_id ."/". urlencode($value->title) ."/'>Read More &raquo;</a>
					                </div>
					            </section>  
					           ";
				}
				$output .= "</section>";
			} else {
				$output .= "<p class='search-title'>No results for posts tagged <em>". $tag_name ."</em></p>";
			}
			break;

		case 'events':
		    $total_records = $Tags->count_tagged("events", $tag_id);
		    
		    $Pagination    = new Pagination($current_page, $records_per_page, $total_records);
			$event_results = $Events->find_by_tag_name($tag_name, $records_per_page, $Pagination->offset());
		  
		    if ($event_results) {
		    	$output .= "<section class='events-section clearfix'>";
		    	$output .= "<p class='search-title'>Results for events tagged <em>". $tag_name . "</em></p>";

		    	foreach ($event_results as $e => $value) {
		    		$output .= "
		    		           <section id='event". $value->event_id ."' class='event'>
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
		    	
		    } else {
		    	$output .= "<p class='search-title'>No results for events tagged <em>". $tag_name ."</em></p>";
		    }
		    break;

		default:
			redirect_to("/notfound/");
			break;
	}

echo $output;



/***********************  Pagination Section   *****************/

//if there are more than one record, the evaluate pagination
if($Pagination->total_pages() > 1) { 
   
   $output = "<section class='pagination-section clearfix'>";

    if($Pagination->has_previous_page()) {
        $output .= " <a class='previous' href=/tags/{$category}/{$tag_name}/";
        $output .= $Pagination->previous_page();
        $output .= "/'>&laquo; Previous</a>";
   }

   // Shows pages Numbers
   for($i =1; $i <= $Pagination->total_pages(); $i++) {
     //NB: $i is the pages number
      if($i == $current_page) {
          $output .= "<span class='page-numbers'> <span class='current'>{$i}</span>";
      } else {
        $output .= "<a href='/tags/{$category}/{$tag_name}/{$i}/'>{$i}</a> </span>";
      }
   }

   if($Pagination->has_next_page()) {
        $output .= " <a class='next' href='/tags/{$category}/{$tag_name}/";
        $output .= $Pagination->next_page();
        $output .= "/'>Next &raquo; </a>";
   }
         
   $output .= "</section>";
   echo $output;
} // end of if there are more than a page


?>
     
    </section>
</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_raw.php'); ?>
</aside>


<?php include_template('footer.php'); ?>