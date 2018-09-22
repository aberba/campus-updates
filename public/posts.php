<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
    redirect_to("/offline/");
}

$total_records          = $Posts->count_all();
$records_per_pagination = $Settings->records_per_pagination();

$total_groups = ceil($total_records/$records_per_pagination);
//echo $total_groups." ".$total_records;

$css              = 'posts.css';
$js               = "posts.js";
$page_description = "Posts";
$page_title       = 'Posts';


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

    <section class="posts-section">
<?php
$current_page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = ceil($Settings->records_per_pagination() / 2);
$total_records    = $Posts->count_all();
$Pagination       = new Pagination($current_page, $records_per_page, $total_records);

$P = $Posts->fetch_all($Pagination->offset(), $records_per_page);
if($P) {
    $output = "";
    foreach ($P as $pst => $value) {

         //use title length and content length to substring the introduction content
          $max_length = 210;
          $title_length = strlen($value->title);
          $remaining_length = $max_length - $title_length;

          $output .= "<section class='post clearfix'>
              <figure>
                   <a href='/posts/" .$value->post_id ."/". urlencode($value->title) ."/'>
                   <img src='/uploads/posts/". $value->image_one ."' alt='". htmlentities($value->title) ." image'>
                 </a>
              </figure>

              <div class='introduction'>
                  <h4>". htmlentities($value->title) ."</h4>
                  <p>". substr($Database->clean_data($value->content, "<p>"), 0, $remaining_length). " ...</p>
                  <a class='read-more' href='/posts/". $value->post_id ."/". urlencode($value->title) ."/'>Read More &raquo;</a>
              </div>
          </section>
          ";
    }
    echo $output;
} else {
    echo "<p>No post is published yet</p>";
}
?>

    </section>


<?php
/***********************  Pagination Section   *****************/

//if there are more than one record, the evaluate pagination
if($Pagination->total_pages() > 1) { 
   
   $output = "<section class='pagination-section clearfix'>";

    if($Pagination->has_previous_page()) {
        $output .= " <a class='previous' href='/posts/";
        $output .= $Pagination->previous_page();
        $output .= "/'>&laquo; Previous</a>";
   }

   // Shows pages Numbers
   for($i =1; $i <= $Pagination->total_pages(); $i++) {
     //NB: $i is the pages number
      if($i == $current_page) {
          $output .= "<span class='page-numbers'> <span class='current'>{$i}</span>";
      } else {
        $output .= "<a href='/posts/{$i}/'>{$i}</a> </span>";
      }
   }

   if($Pagination->has_next_page()) {
        $output .= " <a class='next' href='/posts/";
        $output .= $Pagination->next_page();
        $output .= "/'>Next &raquo; </a>";
   }
         
   $output .= "</section>";
   echo $output;
} // end of if there are more than a page
?>
</section>


<aside id="aside"> <!-- aside -->
<?php include_template('aside_posts.php'); ?>
</aside>


<?php include_template('footer.php'); ?>