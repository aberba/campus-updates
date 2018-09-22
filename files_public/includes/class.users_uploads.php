<?php
class UsersUploads extends Uploads {
    private $users_uploads_table      = "users_uploads";
    private $uploads_categories_table = "users_uploads_categories";
    private $users_table              = "users";

    private $allowed_file_extensions  = array("zip");
    protected $allowed_images_extensions = array("png", "jpg", "gif", "x-png", "jpeg");

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

    public function count_all() {
        global $Database;
        
        $sql = "SELECT COUNT(*) AS num FROM ".$this->users_uploads_table;
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function fetch_categories() {
        global $Database;

        $sql = "SELECT * FROM ".$this->uploads_categories_table." WHERE publish = '1'";
        $result = $Database->query($sql);
        if($Database->num_rows($result) < 1) return false;

        $output = array();
        while ($row = $Database->fetch_data($result)) {
            $output[] = $row;
        }
        return $output;
    }

    public function upload_user_content($post=null, $files=null) {
        global $Database, $Session, $Settings;
        
        $file_name = $Database->clean_data($files['file']['name']);
        $ext       = $this->get_file_extension($file_name);
         
        if(!in_array($ext, $this->allowed_file_extensions)) return "File extension must be in zip format";
         
        $new_name = $this->generate_file_name($ext);
        $tmp_name = $files['file']['tmp_name'];
        $file_error = $files['file']['error'];
        $file_size  = (int)$files['file']['size'];

        if($file_size > $Settings->max_upload_size()) return "File size must not exceed ".$this->gen_size_unit($Settings->max_upload_size());

        if(empty($tmp_name)) return "Error retrieving uploaded file on the serverside";  
        
        $dir = $this->get_dir("users_uploads");
        if(!is_dir($dir)) mkdir($dir);
        $upload_path = $dir. DS. $new_name;

        //insert Upload record into DB
        $user_id = (int)$Session->user()['id'];
        $cat_id  = (int)$Database->clean_data($post['category']);
        $subject = $Database->clean_data($post['subject']);
        $desc    = $Database->clean_data($post['description'], "<a>");
        $date    = time();

         
        // Validate to see if users has not exceeded his the max upload limit
        $sql = "SELECT COUNT(*) AS num FROM ".$this->users_uploads_table." WHERE user_id_fk = '{$user_id}'";
        $result = $Database->query($sql);
        $num = (int)$Database->fetch_data($result)->num;

        if($num > $Settings->user_upload_limit()) return "You have reached the maximum upload limit";

        $sql = "BEGIN WORK";
        $Database->query($sql);

        $sql  = "INSERT INTO ".$this->users_uploads_table." (category_id_fk, user_id_fk, subject, description, file_name, date_added) ";
        $sql .= "VALUES ('{$cat_id}', '{$user_id}', '{$subject}', '{$desc}', '{$new_name}', '{$date}')";
        if (!$Database->query($sql)) return "Oops! an error occured whilst sumitting file";

        if (!move_uploaded_file($tmp_name, $upload_path)) {
            $sql = "ROLLBACK";
            $Database->query($sql);
            return "Oops! error moving file to server";
        }
        
        $sql = "COMMIT";
        $Database->query($sql);
        return $this->upload_errors[$file_error];      
    }
}

$UsersUploads = new UsersUploads();
?>
