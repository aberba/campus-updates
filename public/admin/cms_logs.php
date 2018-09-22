<?php
require_once('../../files_admin/includes/initialize.php');

if (!$Session->is_admin()) {
    echo " 
         <div style='background: #eeeeee; padding: 20px; margin: 100px auto; width: 600px; text-align: center;'>
            <p>Access Denied!</p>
            <p>Sorry, you donnot have permission to view this content</p>
            <p><a href='cms_dashboard.php'>&laquo; Back to Dasboard</a></p>
         </div>";
         exit();
}

$css        = "cms_logs.css";
$js         = "cms_logs.js";
$page_title = "Logs";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">
	 <section class="logs-section">

    <h1>Recorded Logs</h1>
        

<?php
$L = $Logs->fetch_all();
if($L) {
   $output = " <table class='table'>
                  <tr>
                     <th></th>
                     <th>Fullname</th>
                     <th>Log Message</th>
                     <th>Date</th>
                     <th></th>
                  </tr>";
                  
   foreach ($L as $log => $value) {
       $output .= "<tr id='log".$value->log_id."' class='log'>
                     <td><img src='../uploads/avatars/".$value->profile_photo."' alt='Profile Photo'></td>
                     <td><a class='name' href=''>".$value->first_name." ".$value->last_name."</td>
                     <td>".$value->message."</td>
                     <td>".$value->date_added."</td>
                     <td><a id='".$value->log_id."' class='delete' title='Delete Log' href='#'> X </a></td>
                  </tr>
                  ";
   }
   echo $output;
} else {
   echo "<p>No log has been recorded yet</p>";
}
            
?>

         </table>
	 </section>
</section>

<?php include_template("footer.php"); ?>