<?php
require_once('../../files_admin/includes/initialize.php');

$css        = "cms_events.css";
$js         = "cms_events.js";
$page_title = "Events";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">

	 <section class="events-section">
	   <h3>Record Of Events</h3>
<?php 
$events_list = $Events->fetch_all();
if($events_list) {
	foreach ($events_list as $item => $value) {
		$image_one = "../uploads/events/".$value->image_one;
	    if (!is_file(EVENTS_DIR . DS . $value->image_one)) {
	       $image_one = "./img/templates/not-yet-edited.png";
	    }

		$status   = ($value->publish == 1) ? "Unpublish" : "Publish";
		$confirmation = ($value->confirmed == 1) ? "Unconfirm" : "Confirm";
		$publised = ($value->publish == 1) ? "Yes" : "No";
?>
		<section id="event<?php echo $value->event_id; ?>" class="event clearfix">
		    <div class="options">
		    	<ul>
		    		<li><a href="cms_edit_event.php?event_id=<?php echo $value->event_id; ?>"> Edit </a></li>
		    		<li><a class="publish" href="#"> <?php echo $status; ?> </a></li>
		    		<li><a class="confirm" href="#"> <?php echo $confirmation; ?> </a></li>
		    		<li><a class="preview" href="#"> Preview </a></li>
		    		<li><a class="delete" href="#"> Delete </a></li>
		    	</ul>
		    </div>

			<div class="event-information">
			    <h3><?php echo $value->title; ?></h3>
				<table class="table">
					<tr>
						<td>Date Added</td>
						<td><?php echo $value->date_added; ?></td>
					</tr>
					<tr>
						<td>Published: </td>
						<td><?php echo $publised; ?></td>
					</tr>
					<tr>
						<td>Added By: </td>
						<td><?php echo $value->first_name." ".$value->last_name; ?></td>
					</tr>
					<tr>
						<td>Date Added</td>
						<td><?php echo $value->date_added; ?></td>
					</tr>
				</table>
			</div>

			<figure><img src="<?php echo $image_one; ?>"></figure>
		</section>
<?php
	}
}else {
  echo "<p>No event has been added yet</p>";
}
?>
	 </section>
</section>

<?php include_template("footer.php"); ?>