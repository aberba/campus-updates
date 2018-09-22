<?php
require_once('../files_public/includes/initialize.php');

if (!$Session->search_category_is_set()) {
	$Session->set_search_category("posts");
}

$category = $Session->query_search_category(); // fetches search category from $_SESSION superglobal
$keyword  = (isset($_GET['keyword'])) ?   $_GET['keyword'] : "";

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}

$css              = "search.css";
$js               = "search.js";
$page_description = "Campus updates";
$page_title       = "Search";


include_template('header.php'); 
?>

<section id="content"> 

    <section class="search-section">
<?php
$output = "";

if (isset($keyword[3])) {

	switch ($category) {
		case 'posts':
			$post_results = $Posts->search($keyword);
			if ($post_results) {
				$output .= "<section class='posts-section'>";
				$output .= "<p class='search-title'>Search results for <em>". $keyword . "</em> in posts</p>";

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
				$output .= "<p class='search-title'>No results for <em>". $keyword ."</em> in posts</p>";
			}
			break;

		case 'events':
		    $event_results = $Events->search($keyword);
		    if ($event_results) {
		    	$output .= "<section class='events-section clearfix'>";
		    	$output .= "<p class='search-title'>Search results for <em>". $keyword . "</em> in events</p>";

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
		    	$output .= "<p class='search-title'>No results for <em>". $keyword ."</em> in events</p>";
		    }
		    break;

		case 'capture':
		    $capture_results = $Capture->search($keyword);
		    if ($capture_results) {
		    	$output .= "<section class='capture-section clearfix'>
		    	";
		    	$output .= "<p class='search-title'>Search results for <em>". $keyword . "</em> in capture</p>";

		    	foreach ($capture_results as $c => $value) {
		    		$output .= "
                                <section id='capture". $value->capture_id ."' class='capture'>
						            <figure>
						               <img src='/uploads/captures/". $value->file_name ."'>
						               <figcaption>". htmlentities($value->caption) ."</figcaption>
						            </figure>
						    	</section>
		    		           ";
		    	}
		    	$output .=    "
		    	         </section>";

		    } else {
		    	$output .= "<p class='search-title'>No results for <em>". $keyword ."</em> in capture</p>";
		    }
		    break;
		
		default:
			// Do nothing since default category is set to posts
			break;
	}


	echo $output; //Finally print results

} else {
	echo "<p class='search-title'>Please enter a keyword to search</p>";
}
?>
          
    </section>
</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_main.php'); ?>
</aside>


<?php include_template('footer.php'); ?>