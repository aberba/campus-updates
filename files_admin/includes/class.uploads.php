<?php
class Uploads {
     private $posts_table            = "posts";
     private $posts_images_table     = "posts_images";
     private $posts_attachment_table = "posts_attachments";
     private $events_table           = "events";
     private $events_images_table    = "events_images";
     private $captures_table         = "captures";
     private $advertisements_table    = "advertisements";

     protected $allowed_images_extensions = array("png", "jpg", "gif", "x-png", "jpeg");
     protected $allowed_attachment_extensions = array("zip");

     protected $upload_errors = array(
           UPLOAD_ERR_OK           => "File uploaded successfully.",
           UPLOAD_ERR_INI_SIZE     => "File is larger than upload maximum size.",
           UPLOAD_ERR_FORM_SIZE    => "File is larger than upload maximum size.",
           UPLOAD_ERR_PARTIAL      => "THE upload was incomplete.",
           UPLOAD_ERR_NO_FILE      => "No file was selected.",
           UPLOAD_ERR_NO_TMP_DIR   => "No temporal directory.",
           UPLOAD_ERR_CANT_WRITE   => "Can't write to disk.",
           UPLOAD_ERR_EXTENSION    => "File upload stopped by extension."
     );


     //Mother method for uploading all images: post, captures events, etc
     public function upload_image($post=null, $files=null) {
   	      global $Database, $Settings;
      
   	      $upload_type = $Database->clean_data($post['type']);
   	      $file_name   = $Database->clean_data($files['file']['name']);
          $file_size   = (int)$files['file']['size'];
          $ext         = $this->get_file_extension($file_name);
         
          if(!in_array($ext, $this->allowed_images_extensions)) return "Image file extension is not supported";
          if($file_size > $Settings->max_upload_size()) return "File size must not be more than ".$Settings->gen_size_unit($Settings->max_upload_size());
         
          $new_name = $this->generate_file_name($ext);
          $tmp_name = $files['file']['tmp_name'];
          $file_error = $files['file']['error'];
          if(empty($tmp_name)) return "No Temp Dir was set";

          // Process images based on upload type: post, capture, events, etc
         	switch ($upload_type) {
         	 	case 'post':
         	 		    $post_id = (int)$Database->clean_data($post['post_id']);
         	 		    $column  = (int)$Database->clean_data($post['column']);

                  $dir     = $this->get_dir("post");
                  if(!is_dir($dir)) mkdir($dir, 0755, true);
                  $path = $dir.DS.$new_name;

                  if(!$this->update_db_post_info($post_id, $column, $new_name)) return "Oops!, error updating file in the database";
                  if(!move_uploaded_file($tmp_name, $path)) return "Oops!, error uploading file";
                  return $this->upload_errors[$file_error];
         	 		    break;

            case "capture":
                  $caption = $Database->clean_data($post['caption'], "<a>");
                  $dir     = $this->get_dir("capture");
                  if(!is_dir($dir)) mkdir($dir, 0755, true);
                  $path    = $dir.DS.$new_name;

                  if(!$this->update_db_capture_info($new_name, $caption)) return "Oops!, error saving file info into DB";
                  if(!move_uploaded_file($tmp_name, $path)) return "Oops!, error uploading image file";
                  return $this->upload_errors[$file_error];
                  break;

            case "event":
                  $event_id = (int)$Database->clean_data($post['event_id']);
                  $column  = (int)$Database->clean_data($post['column']);

         	 	      $dir     = $this->get_dir("event");
                  if(!is_dir($dir)) mkdir($dir, 0744, true);
                  $path = $dir.DS.$new_name;
                  
                  if(!$this->update_db_event_info($event_id, $column, $new_name)) return "Oops!, error updating file in the database";
                  if(!move_uploaded_file($tmp_name, $path)) return "Oops!, error uploading file";
                  return $this->upload_errors[$file_error];
                  break;
         	 	default:
         	          return false;
         	 		break;
         	}
     }


     public function upload_post_attachment($post=null, $files=null) {
         global $Database, $Settings;

         $file_name = $Database->clean_data($files['file']['name']);
         $file_size = $files['file']['size'];
         $ext       = $this->get_file_extension($file_name);
         
         if(!in_array($ext, $this->allowed_attachment_extensions)) return "Attachment file extension is not supported";
         if($file_size > $Settings->max_upload_size()) return "File size must not be more than ".$this->gen_size_unit($Settings->max_upload_size());
         if($file_size < $Settings->min_upload_size()) return "File size must not be less than ".$this->gen_size_unit($Settings->min_upload_size());
         
         $new_name = $this->generate_file_name($ext);
         $tmp_name = $files['file']['tmp_name'];
         $file_error = $files['file']['error'];
         if(empty($tmp_name)) return "No Temp Dir was set";
         
         $post_id = (int)$Database->clean_data($post['post_id']);
         $column  = (int)$Database->clean_data($post['column']);
  
         if (!$this->update_db_post_attachment_info($post_id, $column, $new_name)) return "Oops!, error inserting file infomation into the database";

         // select current 
         $dir     = $this->get_dir("attachment");
         if(!is_dir($dir)) mkdir($dir, 0755, true);
         $path = $dir.DS.$new_name;
         if (!move_uploaded_file($tmp_name, $path)) return "Oops!, error moving file to server";
         return "Attachment file uploaded successfully!";
     }

     
     public function upload_advertisement($post=null, $files=null) {
         global $Database, $Settings, $Dates;
          
         $file_name = null;
         if (isset($files['file']['name'])) {
             $file_name = $Database->clean_data($files['file']['name']);
         }

         $file_size = null;
         $ext       = null; 
         $new_name  = null;
         $tmp_name  = null;
         $file_error = null;

         if ($file_name) {
             $file_size = (int)$files['file']['size'];
             $ext       = $this->get_file_extension($file_name);
             
             if(!in_array($ext, $this->allowed_images_extensions)) return "Attachment file extension is not supported";
             if($file_size > $Settings->max_upload_size()) return "File size must not be more than ".$this->gen_size_unit($Settings->max_upload_size());
             if($file_size < $Settings->min_upload_size()) return "File size must not be less than ".$this->gen_size_unit($Settings->min_upload_size());
             
             $new_name = $this->generate_file_name($ext);
             $tmp_name = $files['file']['tmp_name'];
             $file_error = $files['file']['error'];
             if(empty($tmp_name)) return "No Temp Dir was set";
         }
         
         $placement = $Database->clean_data($post['placement']);
         $file_url  = $Database->clean_data($post['file_url']);
         $ad_url    = $Database->clean_data($post['ad_url']);
         $alt       = $Database->clean_data($post['alt']);
         $day       = (int)$Database->clean_data($post['day']);
         $month     = (int)$Database->clean_data($post['month']);
         $year      = (int)$Database->clean_data($post['year']);

         if (!$Dates->validate_date($day, $month, $year)) return "Expire date is invalid";
         $exp_date  = $Dates->gen_mysql_date_format($day, $month, $year);
         $date      = time();

         if (empty($file_name) && empty($file_url)) return "Please select file from local disk OR enter file source URL"; 
  
         $sql  = "INSERT INTO ".$this->advertisements_table." (placement, file_name, file_url, ad_url, alt, date_to_expire, date_added) ";
         $sql .= "VALUES ('{$placement}', '{$new_name}', '{$file_url}', '{$ad_url}', '{$alt}', UNIX_TIMESTAMP('{$exp_date}'), '{$date}')";
        
         if (!$Database->query($sql)) return "Oops!, error inserting file infomation into the database";

         // select current 
         $dir     = $this->get_dir("advertisement");
         if(!is_dir($dir)) mkdir($dir, 0755, true);
         $path = $dir.DS.$new_name;

         if ($tmp_name) {
            if (!move_uploaded_file($tmp_name, $path)) return "Oops!, error moving file to server";
         }
         return "Attachment file uploaded successfully!";
     }



/********************************************************************************
*************************  Database Updating Functions  ********************************
*********************************************************************************/
     private function update_db_post_info($post_id=0, $column=0, $new_file_name="") {
          global $Database;
          
          $post_id   = (int)$Database->clean_data($post_id);
          $column    = (int)$Database->clean_data($column);
          $new_file_name = $Database->clean_data($new_file_name);
          
           //save file into db
          $column_name = $this->get_post_image_column($column);
          if(!$column_name) return false;

          //delete current image first
          $sql = "SELECT {$column_name} AS col FROM ".$this->posts_images_table." WHERE post_id_fk = '{$post_id}' LIMIT 1";
          $result = $Database->query($sql);
          $fname  = $Database->fetch_data($result)->col;
          $file   = $this->get_dir("post").DS.$fname;
          if(is_file($file)) unlink($file);
          
          $sql = "UPDATE ".$this->posts_images_table." SET {$column_name} = '{$new_file_name}' WHERE post_id_fk = '{$post_id}' LIMIT 1";
          return ($Database->query($sql) === true) ? true : false;
     }

     private function update_db_post_attachment_info($post_id=0, $column=0, $new_file_name="") {
          global $Database;
          
          $post_id   = (int)$Database->clean_data($post_id);
          $column    = (int)$Database->clean_data($column);
          $new_file_name = $Database->clean_data($new_file_name);
          
           //save file into db
          $column_name = $this->get_post_attachment_column($column);
          if(!$column_name) return false;

          //delete current image first
          $sql = "SELECT {$column_name} AS col FROM ".$this->posts_attachment_table." WHERE post_id_fk = '{$post_id}' LIMIT 1";
          $result = $Database->query($sql);
          $fname  = $Database->fetch_data($result)->col;
        
          $file   = $this->get_dir("attachment").DS.$fname;
          if(is_file($file)) unlink($file);
          
          $sql = "UPDATE ".$this->posts_attachment_table." SET {$column_name} = '{$new_file_name}' WHERE post_id_fk = '{$post_id}' LIMIT 1";
          return ($Database->query($sql) === true) ? true : false;
     }

     private function update_db_event_info($event_id=0, $column=0, $new_file_name="") {
          global $Database;
          
          $event_id   = (int)$Database->clean_data($event_id);
          $column    = (int)$Database->clean_data($column);
          $new_file_name = $Database->clean_data($new_file_name);
          
           //save file into db
          $column_name = $this->get_post_image_column($column);
          if(!$column) return false;

          //delete current image first
          $sql = "SELECT {$column_name} AS col FROM ".$this->events_images_table." WHERE event_id_fk = '{$event_id}' LIMIT 1";
          $result = $Database->query($sql);
          $fname  = $Database->fetch_data($result)->col;
          $file   = $this->get_dir("event").DS.$fname;
          if(is_file($file)) unlink($file);
          
          $sql = "UPDATE ".$this->events_images_table." SET {$column_name} = '{$new_file_name}' WHERE event_id_fk = '{$event_id}' LIMIT 1";
          return ($Database->query($sql) === true) ? true : false;
     }

     private function update_db_capture_info($new_file_name="", $caption="") {
          global $Database, $Session;

          $new_file_name = $Database->clean_data($new_file_name);
          $caption   = $Database->clean_data($caption);
          $user_id  = $Session->user()['id'];
          $date      = time();

          $sql  = "INSERT INTO ".$this->captures_table." (file_name, caption, edited_by, added_by, date_added) ";
          $sql .= "VALUES('{$new_file_name}', '{$caption}', '{$user_id}', '{$user_id}', '{$date}')";
          return ($Database->query($sql) === true) ? true : false;
     }


/********************************************************************************
*************************  Files Deleting Functions  ********************************
*********************************************************************************/
     // used for deleting images based on type: post, capture, events, etc
     public function remove_image($post=null) {
          global $Database;

          $type = $Database->clean_data($post['type']);

          switch ($type) {
               case 'post':
                    $post_id = (int)$Database->clean_data($post['post_id']);
                    $column  = (int)$Database->clean_data($post['column']);

                    //delete image file and remove record from Database
                    return ($this->remove_post_image($post_id, $column) === true) ? "Post image deleted successfully" : "File is already deleted or an error occured";                  
                    break;

               case 'event':
                    $event_id = (int)$Database->clean_data($post['event_id']);
                    $column  = (int)$Database->clean_data($post['column']);

                    //delete image file and remove record from Database
                    return ($this->remove_event_image($event_id, $column) === true) ? "Event image deleted successfully" : "File is already deleted or an error occured";                  
                    break;

               case "capture":
                    $capture_id = (int)$Database->clean_data($post['capture_id']);
                    return $this->remove_capture_image($capture_id);
                    break;
               
               default:
                    return false;
                    break;
          }   

     }

     //Used for deleting posts images only
     private function remove_post_image($post_id, $column=0) {
          global $Database;
          
          $post_id = (int)$Database->clean_data($post_id);
          $column  = (int)$Database->clean_data($column);
          $column_name = $this->get_post_image_column($column);

          //Select file name from DB and delete
          $sql = "SELECT {$column_name} as col FROM ".$this->posts_images_table." WHERE post_id_fk = {$post_id} LIMIT 1";
          $result = $Database->query($sql);
          $file_name = $Database->fetch_data($result)->col;

          //Generate file path
          $file = $this->get_dir("post").DS.$file_name;
          if(is_file($file)) unlink($file);

          //Empty image file column inthe db
          $sql = "UPDATE ".$this->posts_images_table." SET {$column_name} = NULL WHERE post_id_fk = {$post_id} LIMIT 1";
          $Database->query($sql);
          return ($Database->affected_rows() == 1) ? true : false; 
     }

      //Used for deleting posts images only
     public function remove_post_attachment($post=null) {
          global $Database;
          
          $post_id = (int)$Database->clean_data($post['post_id']);
          $column  = (int)$Database->clean_data($post['column']);
          $column_name = $this->get_post_attachment_column($column);

          //Select file name from DB and delete
          $sql = "SELECT {$column_name} as col FROM ".$this->posts_attachment_table." WHERE post_id_fk = {$post_id} LIMIT 1";
          $result = $Database->query($sql);
          $file_name = $Database->fetch_data($result)->col;

          //Generate file path
          $file = $this->get_dir("attachment").DS.$file_name;
          if(is_file($file)) unlink($file);

          //Empty image file column inthe db
          $sql = "UPDATE ".$this->posts_attachment_table." SET {$column_name} = NULL WHERE post_id_fk = {$post_id} LIMIT 1";
          $Database->query($sql);
          return ($Database->affected_rows() == 1) ? "Attachment removed successfully!" : "Oops!, error removing attachment"; 
     }

      //Used for deleting event images only
     private function remove_event_image($event_id, $column=0) {
          global $Database;
          
          $event_id = (int)$Database->clean_data($event_id);
          $column  = (int)$Database->clean_data($column);
          $column_name = $this->get_post_image_column($column);

          //Select file name from DB and delete
          $sql = "SELECT {$column_name} as col FROM ".$this->events_images_table." WHERE event_id_fk = {$event_id} LIMIT 1";
          $result = $Database->query($sql);
          $file_name = $Database->fetch_data($result)->col;

          //Generate file path
          $file = $this->get_dir("event").DS.$file_name;
          if(is_file($file)) unlink($file);

          //Empty image file column inthe db
          $sql = "UPDATE ".$this->events_images_table." SET {$column_name} = NULL WHERE event_id_fk = {$event_id} LIMIT 1";
          $Database->query($sql);
          return ($Database->affected_rows() == 1) ? true : false; 
     }

     //Used for deleting capture images
     private function remove_capture_image($capture_id=0) {
         global $Database;

         //select image file name and delete
         $sql = "SELECT file_name FROM ".$this->captures_table." WHERE capture_id = '{$capture_id}' LIMIT 1";
         $result    = $Database->query($sql);
         $file_name = $Database->fetch_data($result)->file_name;

         $file = $this->get_dir("capture").DS.$file_name;
         if(is_file($file)) unlink($file);

         //Now delete file record form DB
         $sql = "DELETE FROM ".$this->captures_table." WHERE capture_id = '{$capture_id}' LIMIT 1";
         $Database->query($sql);
         return ($Database->affected_rows() == 1) ? "Selected items have been deleted successfully!" : "Oops!, an error occured whilst deleting items";
     }

      //Used for deleting posts images only
     public function delete_ad($ad_id=0) {
          global $Database;
          
          $ad_id = (int)$Database->clean_data($ad_id);
          
          //Select file name from DB and delete
          $sql = "SELECT file_name FROM ".$this->advertisements_table." WHERE advertisement_id = '{$ad_id}' LIMIT 1";
          $result = $Database->query($sql);
          $file_name = $Database->fetch_data($result)->file_name;

          //Generate file path
          $file = $this->get_dir("advertisement").DS.$file_name;
          if(is_file($file)) unlink($file);

          //Empty image file column inthe db
          $sql = "DELETE FROM ".$this->advertisements_table." WHERE advertisement_id = '{$ad_id}' LIMIT 1";
          $Database->query($sql);
          return ($Database->affected_rows() == 1) ? "Advertisement removed successfully!" : "Oops!, error removing advertisement"; 
     }



/***************************************************************************************************
**************  Function For retriving paths, extensions and unique file names, etc  *****************
****************************************************************************************************/
     //return post image column name with column position number such as 1,2 3 ...
     private function get_post_image_column($column=0) {
          $column_name = null;
          $column = (int)$column;

          switch ($column) {
               case 1:
                    return "image_one";
                    break;
               case 2:
                    return "image_two";
                    break;
               case 3:
                    return "image_three";
                    break;
               case 4:
                    return "image_four";
                    break;
               case 5:
                    return "image_five";
                    break;
               case 6:
                    return "image_six";
                    break;
               
               default:
                    return false;
                    break;
          }
     }

     private function get_post_attachment_column($column=0) {
          $column_name = null;
          $column = (int)$column;

          switch ($column) {
               case 1:
                    return "file_one";
                    break;
               case 2:
                    return "file_two";
                    break;
               case 3:
                    return "file_three";
                    break;

               default:
                    return false;
                    break;
          }
     }

     protected function get_file_extension($file_name="") {
          $exts_array = explode(".", $file_name);
          return $exts_array[count($exts_array)-1];
     }
     
     //returns dir path of an image file using the types such as post, capture, ...
     public function get_dir($type="post") {
          switch ($type) {
               case 'post':
                    return POSTS_DIR;
                    break;
               case 'attachment':
                    return ATTACHMENT_DIR;
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
               case 'advertisement':
                    return ADVERTISEMENTS_DIR;
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