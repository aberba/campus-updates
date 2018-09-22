<?php
require_once('../../files_admin/includes/initialize.php');

$css        = "cms_users_uploads.css";
$js         = "cms_users_uploads.js";
$page_title = "Users Uploads";

include_template("header.php");
?>

<aside id="aside"><?php include_template('aside.php'); ?></aside>


<section id="content">
	 <section class="uploads-section">

<?php  
$uploads_list = $UsersUploads->fetch_all();
if($uploads_list) {
   foreach ($uploads_list as $item => $value) {

?>
      <section id="upload<?php echo $value->upload_id; ?>" class="upload blue">
         <div class="options">
            <ul>
               <li><a class="toggle" href="#"> Toggle </a></li>
               <li><a class="delete" href="#"> Delete </a></li> 
            </ul>
         </div>
         <p><strong>Category:</strong> <?php echo $value->category_name; ?> &nbsp;&nbsp;&nbsp;&nbsp; <strong>Subject:</strong> <?php echo $value->subject; ?></p>

         <div class="content">
            <div>
               <p><strong>Description:</strong> <?php echo $value->description; ?></p>
               <p><a class="name" href="../uploads/users_uploads/<?php echo $value->file_name; ?>"><?php echo $value->file_name; ?></a></p>
            </div>

            <div class="upload-info clearfix">
               <figure class="avatar"><img src="../uploads/avatars/<?php echo $value->profile_photo; ?>"></figure>
               <p>
                 <a class="name" href="#"><?php echo $value->first_name." ".$value->last_name; ?></a>
                 <span><?php echo $value->date_added; ?></span>
               </p>
            </div>
         </div>
      </section>

<?php
      
   }
}else {
  echo "<p>No files are uploaded yet</p>";
}
?>
	 </section>
</section>

<?php include_template("footer.php"); ?>