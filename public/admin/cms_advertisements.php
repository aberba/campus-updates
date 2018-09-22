<?php
require_once('../../files_admin/includes/initialize.php');

$css        = "cms_advertisements.css";
$js         = "cms_advertisements.js";
$page_title = "Advertisements";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">
    <section>
       <button type="button" class="toggle-form-btn button"> + Add New Advertisement </button>

       <form class="add-form form">
          <label for="placement"> Placement: </label>
          <select name="placement">
              <option disabled="disabled" selected="selected"> Select placement for banner </option>
              <option value="top"> Top </option>
              <option value="aside"> Aside </option>
          </select>

          <label for="file"> Select Banner Image OR Enter Banner Source URL: </label>
          <input type="file" name="file">
          <input type="text" name="file_url" placeholder=" Banner file source url">

          <label for="ad_url"> Ad client URL: </label>
          <input type="text" name="ad_url" placeholder=" Enter ad client url">


          <label for="alt"> Alt text for banner: </label>
          <input type="text" name="alt" placeholder=" Alt text for banner image">

          <label for="day"> Select of expire: </label>
         <?php
          $date_select = $Dates->generate_date_select(date("d", time()), date("m", time()), date("Y", time()));
          echo $date_select;
         ?>

          <button type="button" class="add-btn button"> + Add </button>
          <button type="button" class="cancel-btn button"> Cancel </button>
       </form>
    </section>

	 <section class="advertisement-section">
<?php
$advert_records = $Advertisements->fetch_all();
if ($advert_records) {
   $output = "";
   foreach ($advert_records as $a => $value) {
       $status = ($value->publish == 1) ? "Unpublish" : "Publish";

       $url = "/uploads/advertisements/". $value->file_name;
       if (!is_file(ADVERTISEMENTS_DIR. DS .$value->file_name)) {
            $url = "//".$value->file_url;
       }

       $output .= "<div id='advertisement". $value->advertisement_id ."' class='advertisement'>
                       <ul>
                          <li><a class='publish' href='#'> ". $status ." </a></li>
                          <li><a class='edit' href='#'> Edit </a></li>
                          <li>Expires: ". $Dates->date_only($value->date_to_expire) ."</li>
                          <li>Added on: ". $Dates->date_only($value->date_added) ."</li>
                          <li><a class='delete' href='#'> Delete </a></li>
                       </ul>
                       <figure>
                          <a href='//". $value->ad_url ."' target='_blank'>
                             <img src='".$url."' alt='advertisement image'>
                          </a>
                       </figure>

                       <table class='table'>
                          <tr>
                             <td>Placement: </td>
                             <td>". $value->placement ."</td>
                          </tr>
                          <tr>
                             <td>Ad URL: </td>
                             <td title='URL for client advert'>". $value->ad_url ."</td>
                          </tr>
                          <tr>
                             <td>Alt: </td>
                             <td>". $value->alt ."</td> 
                          </tr>
                       </table>
                   </div>";
   }
   echo $output;
} else {
  echo "<p>No advertisement added yet</p>";
}
?>
	 </section>
</section>

<?php include_template("footer.php"); ?>