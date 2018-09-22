<?php
class Uploads {
     private $users_table = "users";
     private $allowed_image_extensions = array("png", "jpeg", "jpg", "gif");

     public function upload_avatar($files=null) {
          global $Database, $Session, $Settings;
          
          $file_name  = $Database->clean_data($files['file']['name']);
          $tmp_name   = $files['file']['tmp_name'];
          $file_size  = (int)$files['file']['size'];
          $file_error = (int)$files['file']['error'];

          $ext        = $this->get_file_extension($file_name);

          if (!in_array($ext, $this->allowed_image_extensions)) {
               return "File extension is not supported.";
          }

          if ($file_size > $Settings->max_upload_size()) {
               return "File size must not exceed ".$this->gen_size_unit($Settings->max_upload_size());
          }

          if(empty($tmp_name)) return "Error retrieving uploaded file on the serverside";  

          $new_filename = $this->generate_file_name($ext);
          $dir          = $this->get_dir("avatar");
          if(!is_dir($dir)) mkdir($dir, 0755, true);
          $path         = $dir. DS .$new_filename;

          $user_id      = $Session->user()['id'];

          //select current avatar filename an delete
          $sql    = "SELECT profile_photo AS pro FROM ".$this->users_table." WHERE user_id = '{$user_id}' LIMIT 1";
          $result = $Database->query($sql);
          $avatar_filename = $Database->fetch_data($result)->pro;
          $current_avatar  = $dir. DS. $avatar_filename;
           
          $Settings->transaction_start(); // start transaction

          $sql = "UPDATE ".$this->users_table." SET profile_photo = '{$new_filename}' WHERE user_id = '{$user_id}' LIMIT 1";
          if (!$Database->query($sql)) {
               $Settings->transaction_rollback();
               return "Oops!, an error occured whilst saving your avatar";
          }

          if (!move_uploaded_file($tmp_name, $path)) {
               $Settings->transaction_rollback();
               return "Oops!, an error occured whilst moving new avatar to server";
          }
     
          $Settings->transaction_commit();
          if (is_file($current_avatar)) unlink($current_avatar);
          return "Avatar uploaded successfully!";
     }

     protected function get_file_extension($file_name="") {
          $exts_array = explode(".", $file_name);
          return $exts_array[count($exts_array)-1];
     }
     
     //returns dir path of an image file using the types such as post, capture, ...
     protected function get_dir($type="post") {
          switch ($type) {
               case 'post':
                    return POSTS_DIR;
                    break;
               case 'event':
                    return EVENTS_DIR;
                    break;
               case 'capture':
                    return CAPTURE_DIR;
                    break;
               case 'docs':
                    return DOCS_DIR;
                    break;
               case 'users_uploads':
                    return USERS_UPLOADS_DIR;
                    break;
               case 'avatar':
                    return AVATARS_DIR;
                    break;
               default:
                    return false;
                    break;
          }
     }
     
     //generates a random unique name for images
     protected function generate_file_name($extension="") {
          return time()."_".md5(uniqid(mt_rand(), true)).".".$extension;
     }
 
     // generates file size form bytes to string format
     public function gen_size_unit($file_size=0) {
          $file_size = $file_size;
         
          $GB = 1073741824;
          $MB = 1048576; 
          $KB = 1024;
          $size = null;
          $unit = null;

          if ($file_size >= $GB) {
               $size = number_format(($file_size / $GB), 2);
               $unit = "GB";
          } elseif ($file_size >= $MB) {
                $size = number_format(($file_size / $MB), 2);
                $unit = "MB";
          } elseif ($file_size >= $KB) {
                $size = number_format(($file_size / $KB), 2);
                $unit = "KB";
          } elseif ($file_size > 0) {
               $size = $file_size;
               $unit = "B";
          }
          return $size.$unit;
   }
}

$Uploads = new Uploads();
?>