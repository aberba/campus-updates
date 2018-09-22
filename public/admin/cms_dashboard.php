<?php
require_once('../../files_admin/includes/initialize.php');

$css        = "cms_dashboard.css";
$js         = "cms_dashboard.js";
$page_title = "Dashboard";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">
	 <section class="welcome-section blue">
        <h3>Welcome to <?php echo $Settings->site_name(); ?></h3>
        <p>You are logged in as <span><?php echo $Session->user()['fullname']; ?></span></p>
	 </section>


   <section class="status-section clearfix">
        <div class="group">
           <h3>Posts</h3>
           <ul>
              <li><?php echo $Posts->count_all(); ?> Total</li>
              <li><?php echo $Posts->count_published(); ?> Published</li>
              <li><?php echo $Posts->count_unpublished(); ?> Unpublished</li>
           </ul>
        </div>

        <div class="group">
           <h3>Capture</h3>
           <ul>
              <li><?php echo $Capture->count_all(); ?> Total</li>
              <li><?php echo $Capture->count_published(); ?> Published</li>
              <li><?php echo $Capture->count_unpublished(); ?> Unpublished</li>
           </ul>
        </div>

        <div class="group">
           <h3>Events</h3>
           <ul>
              <li><?php echo $Events->count_all(); ?> Total</li>
              <li><?php echo $Events->count_published(); ?> Published</li>
              <li><?php echo $Events->count_unpublished(); ?> Unpublished</li>
              <li><?php echo $Events->count_confirmed(); ?> Confirmed</li>
              <li><?php echo $Events->count_unconfirmed(); ?> Unconfirmed</li>
           </ul>
        </div>

        <div class="group">
           <h3>Comments</h3>
           <ul>
              <li><?php echo $Comments->count_all(); ?> Total</li>
              <li><?php echo $Comments->count_published(); ?> Published</li>
              <li><?php echo $Comments->count_unpublished(); ?> Unpublished</li>
           </ul>
        </div>

        <div class="group">
           <h3>Users</h3>
           <ul>
              <li><?php echo $Users->count_all(); ?> Users registered</li>
              <li><?php echo $Users->count_activated(); ?> Users Activated</li>
              <li><?php echo $Users->count_unactivated(); ?> Users Unactivated</li>
              <li><?php echo $Users->count_moderators(); ?> Moderators</li>
              <li><?php echo $Users->count_administrators(); ?> Administrators</li>
           </ul>
        </div>

        <div class="group">
           <h3>Others</h3>
           <ul>
               <li><?php echo $UsersUploads->count_all(); ?> User Uploads</li>
              <li><?php echo $Advertisements->count_all(); ?> Advertisements Added</li>
              <li><?php echo $Logs->count_all(); ?> Logs Recorded</li>
           </ul>
        </div>
   </section>

   <section class="logs-section">
        <h3>Most Recent Logs</h3>
        <ul>

<?php
$recent_logs = $Logs->fetch_recent(5);
if ($recent_logs) {
    $output = "";
    foreach ($recent_logs as $log => $value) {
        $output .= "<li><a class='name' href='#'>".$value->first_name." ".$value->last_name."</a> ".$value->message." on ".$value->date_added."</li>";
    }
    echo $output;
} else {
    echo "<p>No log has been recorded</p>";
}
?>
        </ul>
   </section>
</section>

<?php include_template("footer.php"); ?>