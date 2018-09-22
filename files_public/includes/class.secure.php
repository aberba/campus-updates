<?php
class Secure {
    private $login_table = "login";
    private $users_table = "users";
    
    // Check passwors strenght and returns 'OK' iwhen succeeds or erroe maeesge if fails
    public function check_password_strength($password="") {
        // Validate
        if(!isset($password[7])) {
            return "Password must be at least 8 characters"; // validate length
        } elseif(!preg_match('/([a-z]+)/', $password)) {
            return "Password must include a lower case alphabet"; // validate lowercase
        } elseif(!preg_match('/([A-Z]+)/', $password)) {
            return "Password must include an upper case alphabet"; // validate uppercase
        } elseif(!preg_match('/([0-9]+)/', $password)) {
            return "Password must include a numeric character"; // validate numeric charaters
        } elseif(!preg_match('/([^a-zA-Z0-9]+)/', $password)) {
            return "Password must include a symbol"; // validate symbols
        } 
        return "OK";
    }

    public function validate_name($name="", $type="fname") {
         $name_type = ($type === "fname") ? "First Name" : "Last Name";

         if(empty($name)) {
             return "Please enter your ".strtolower($name_type);
         } elseif (isset($name[19])) {
             return "Your ". $name_type. " is too long";
         } elseif (!preg_match('/([a-zA-Z]+)/', $name)) {
             return $name_type. " must contain only alphabets";
         } elseif (!isset($name[2])) {
             return $name_type." must be at least 3 alphabets";
         }
         return "OK";
    }

    public function validate_email($email="", $table="login") {
         global $Database;

         $table  = ($table == "login") ? $this->login_table : $this->users_table;
         $column = ($table == "login") ? "email" : "contact_email";

         if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Your email address is invalid";
         } elseif (isset($email[39])) {
             return "Email address is too long";
         }

         //validate for duplicate in database
         $sql = "SELECT * FROM ".$table." WHERE ".$column." = '{$email}' LIMIT 1";
         $result = $Database->query($sql);
         if($Database->num_rows($result) == 1) return "Your email address is already in use.";
         return "OK";
    }

    public function validate_user_name($user_name="") {
        global $Database;

        $user_name = $Database->clean_data($user_name);

        if (!preg_match('/([a-zA-Z]+)/', $user_name)) {
            return "Username must contain at least a single alphabet";
        } elseif (!preg_match('/([a-zA-Z0-9_-]+)/', $user_name)) {
            return "Username must contain only alphanumeric character and or underscore";
        }

        $sql    = "SELECT * FROM ".$this->users_table." WHERE user_name = '{$user_name}' LIMIT 1";
        $result = $Database->query($sql);
        if ($Database->num_rows($result) == 1) return "Username is already in use";
        return "OK";
    }

     
    // Validate users permisiion using password (used when deleting user)
    public function validate_permission($victim_id=0, $admin_password="") {
       global $Database, $Session;
       
       $session_u     = $Session->user();
       $session_id    = $session_u['id'];
       $session_level = $session_u['level'];
       $victim_id     = (int)$Database->clean_data($victim_id);
       
       // Fetch session user's level
       $sql     = "SELECT * FROM ".$this->login_table." WHERE admin_id = '{$session_id}' ";
       $sql    .= "LIMIT 1";
       $result  = $Database->query($sql);
       $row_session = $Database->fetch_data($result);
       
       //Fetch victim's level
       $sql    = "SELECT level FROM ".$this->login_table." WHERE admin_id = '{$victim_id}' ";
       $sql   .= "LIMIT 1";
       $result = $Database->query($sql);

       if($Database->num_rows($result) != 1) return 0;
       $row_victim   = $Database->fetch_data($result);

       // Check user's permission to delete victim
       if(($session_level <= $row_victim->level) && ($session_level != PERMS_SUPER_ADMIN)) return 2; 
       return ($this->password_check($admin_password, $row_session->password) === true) ? 1 : 0;
    }

    //for authenticating users permission to take an action
    public function authenticate($password="", $level=3) {
        global $Database, $Session;

        $session_u     = $Session->user();
        $session_id    = $session_u['id'];
        $session_level = $session_u['level'];
        if(empty($password)) return "Access denied";
       
        // Fetch session user's level
        $sql    = "SELECT * FROM ".$this->login_table." WHERE admin_id = '{$session_id}' ";
        $sql   .= "LIMIT 1";
        $result = $Database->query($sql);
        $row    = $Database->fetch_data($result);
        $level = (int)$Database->clean_data($level);
        if($row->level < $level) return "Access denied. You donnot have permission";
        return ($this->password_check($password, $row->password) === true) ? "Access granted" : "Access denied";
    }

    public function gen_salt($length=22) {
        $length = (int)$length;
        $len    = ($length >= 22) ? $length : 22;
        
        $unique_str = md5(uniqid(mt_rand(), true));
        $base64     = base64_encode($unique_str);
        $modified   = str_replace("+", ".", $base64);
        return substr($modified, 0, $len);
    }
    
    // returns an encryptes password
    public function secure_password($password="") {
        $hash_type     = "$2y$11$";
        $salt          = $hash_type.$this->gen_salt(22);
        return crypt($password, $salt);
    }
    
    public function password_check($password="", $hash_password="") {
        return (crypt($password, $hash_password) === $hash_password) ? true : false;
    }
}

$Secure = new Secure();
?>