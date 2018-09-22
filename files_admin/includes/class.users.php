<?php
class Users {
      private $users_table = "users";
      private $login_table = "login";

    public function count_all() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->users_table;
        $result = $Database->query($sql);
        return (int)$Database->fetch_data($result)->num;
    }

    public function count_activated() {
        global $Database;
        $sql = "SELECT COUNT(*) as num FROM ".$this->login_table." WHERE activation IS NULL";
        $result = $Database->query($sql);
        return (int)$Database->fetch_data($result)->num;
    }

    public function count_unactivated() {
        global $Database;
        $sql = "SELECT COUNT(*) as num FROM ".$this->login_table." WHERE activation IS NOT NULL";
        $result = $Database->query($sql);
        return (int)$Database->fetch_data($result)->num;
    }


    public function count_super_admin() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->login_table." WHERE level = '3'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function count_admin() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->login_table." WHERE level = '2'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function count_members() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->users_table." WHERE role = '0'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }


    public function count_moderators() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->users_table." WHERE role = '1'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }

    public function count_administrators() {
        global $Database;

        $sql = "SELECT COUNT(*) as num FROM ".$this->users_table." WHERE role = '2'";
        $result = $Database->query($sql);
        return $Database->fetch_data($result)->num;
    }


    public function fetch_all() {
        global $Database, $Dates;

        $sql  = "SELECT * FROM ".$this->users_table." U LEFT JOIN ".$this->login_table." L ";
        $sql .= "ON U.user_id = L.user_id_fk ORDER BY U.first_name ASC, U.last_name ASC";
        $data = $Database->query($sql);
        if($Database->num_rows($data) < 1) return false;

        $output = array();
        while($row = $Database->fetch_data($data)) {
        	$output[] = $row;
        }
        return $output;
    }

    public function find_by_id($user_id=0) {
        global $Database, $Session;

        $user_id = ((int)$user_id != 0) ? (int)$Database->clean_data($user_id) : (int)$Session->user()['id'];
        $sql  = "SELECT * FROM ".$this->users_table." U LEFT JOIN ";
        $sql .= $this->login_table." L ON U.user_id = L.user_id_fk WHERE user_id = '{$user_id}' LIMIT 1";
        $result = $Database->query($sql);
        if($Database->num_rows($result) != 1) return false;
        return $Database->fetch_data($result);
    }

    public function save($post=null) {
        global $Database, $Session, $Secure, $Settings, $Dates;

        $user_id = (int)$Database->clean_data($post['user_id']);
        $role    = (int)$Database->clean_data($post['role']);

        // Deny if user has not activated his account
        if ( !$Session->is_activated($user_id) && ($role > 0) ) return "Sorry, you cannot modify an unactivated user's role.";

        $blocked = (int)$Database->clean_data($post['block']);
        $day     = (int)$Database->clean_data($post['day']);
        $month   = (int)$Database->clean_data($post['month']);
        $year    = (int)$Database->clean_data($post['year']);

        if (!$Dates->validate_date($day, $month, $year)) return "Resume date is invalid";
        $resume_date = $Dates->gen_mysql_date_format($day, $month, $year);
        
        //start transaction
        $Settings->transaction_start();

        //add login info
        $sql  = "UPDATE ".$this->login_table." SET blocked = '{$blocked}', block_resume_date = UNIX_TIMESTAMP('{$resume_date}') ";
        $sql .= " WHERE user_id_fk = '{$user_id}' LIMIT 1";

        if (!$Database->query($sql)) {
            $Settings->transaction_rollback();
            return "Oops! error saving data into login table";
        }

        // users table data
        $sql = "UPDATE ".$this->users_table." SET role = '{$role}' WHERE user_id = '{$user_id}' LIMIT 1";
       

        if(!$Database->query($sql)) {
            $Settings->transaction_rollback();
            return "Oops! error saving data into users table";
        } 
        $Settings->transaction_commit();
        return "Information saved successfully!";
    }

    public function remove($victim_id="") {
        global $Database, $Session;

        $admin_id = (int)$Database->clean_data($victim_id);
        if(!$Session->is_admin()) return "You donnot have permission to delete a user";

        // Deny delete if admin has added products 
        $sql  = "SELECT * FROM products WHERE added_by = '{$admin_id}' OR ";
        $sql .= "edited_by = '{$admin_id}' LIMIT 1";
        $result = $Database->query($sql);
        if($Database->num_rows($result) == 1) return "User has added or edited products, thus cannot be removed";

        //Deny delete if admin has added or edited services
        $sql  = "SELECT * FROM services WHERE added_by = '{$admin_id}' OR ";
        $sql .= "edited_by = '{$admin_id}' LIMIT 1";
        if($Database->num_rows($result) == 1) return "User has added or edited services, thus cannot be removed";

        $sql = "DELETE FROM ".$this->login_table." WHERE admin_id = '{$admin_id}' LIMIT 1";
        $Database->query($sql);
        return ($Database->affected_rows() == 1) ? "User deleted successfully!" : "Ooops! error deleting user";
    }

    public function get_role($level_code="") {
    	if($level_code == 3) {
    		$level = "Sup.";
    	} elseif($level_code == 2) {
    		$level = "Adm.";
    	} elseif($level_code == 1) {
    		$level = "Mod.";
    	} else {
            $level = "Mem.";
        }
    	return $level;
    } 


        /* public function check_username($user_name="") {
        global $Database;

        $user_name = $Database->clean_data($user_name);
        $sql       = "SELECT * FROM ".$this->login_table." WHERE user_name = '{$user_name}' LIMIT 1";
        $result    = $Database->query($sql);
        return ($Database->num_rows($result) == 1) ? false : true;
    }

    public function update_profile($post="") {
        global $Database, $Session;
        
        $user_id = (int)$Session->user()['id'];
        $fname = $Database->clean_data($post['fname']);
        $lname = $Database->clean_data($post['lname']);
        $email = $Database->clean_data($post['email']);
        
        if(empty($fname) || empty($lname) || empty($email)) return "Please complete the form";
        if(!isset($fname[2])) return "Enter a valid first name";
        if(!isset($lname[2])) return "Enter a valid last name";
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return "Invalid email address";

        $sql  = "UPDATE ".$this->users_table." SET first_name = '{$fname}', last_name = '{$lname}', email = '{$email}' ";
        $sql .= "WHERE admin_id_fk = '{$user_id}' LIMIT 1";
        return ($Database->query($sql) === true) ? "Profile information updated successfully!" : "Ooops! eror saving information";
    }

      // for validating admin password update
    public function change_pass($post="") {
        global $Database, $Session, $Secure;
        
        $session_u    = $Session->user();
        $session_id   = $session_u['id'];
        $pass1        = $post['pass1'];
        $pass2        = $post['pass2'];
        $current_pass = $post['current_pass'];

        if(empty($pass1) || empty($pass2) || empty($current_pass)) {
            $message = "Please complete the form";
        }if($pass1 != $pass2) {
            return "The two passwords donnot match";
        }

        // Validate pass strenght Using Secure.class.php and return error message or 'OK' if success
        $strength = $Secure->check_password_strength($pass1);
        if($strength != "OK") return $strength;
       
        // Fetch session user's current password
        $sql    = "SELECT * FROM ".$this->login_table." WHERE admin_id = '{$session_id}' LIMIT 1";
        $result = $Database->query($sql);
        $row    = $Database->fetch_data($result);
        if(!$Secure->password_check($current_pass, $row->password)) {
            return "Invalid current password";
        }
        
        //update pass if there are no errors
        $new_pass = $Secure->password_secure($pass1);
        $sql      = "UPDATE ".$this->login_table." SET password = '{$new_pass}' WHERE admin_id = '{$session_id}' LIMIT 1";
        return ($Database->query($sql) === true) ? "Password changed successfully!" : "Ooops! error changing password";
    }

   public function add($user_name="", $password="", $level="") {
        global $Database, $Secure, $Settings;   
    }*/
}

$Users = new Users();
?>