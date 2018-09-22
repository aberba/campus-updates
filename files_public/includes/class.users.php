<?php
class Users {
    private $users_table       = "users";
    private $login_table = "login";


     public function fetch_moderators() {
        global $Database, $Dates;

        $sql  = "SELECT * FROM ".$this->users_table." U LEFT JOIN ".$this->login_table." L ";
        $sql .= "ON U.user_id = L.user_id_fk WHERE U.role = '1' ORDER BY U.date_registered DESC";
        $data = $Database->query($sql);
        if($Database->num_rows($data) < 1) return false;

        $output = array();
        while($row = $Database->fetch_data($data)) {
            $output[] = $row;
        }
        return $output;
    }

    public function query_information() {
        global $Database, $Session;

        $user_id = (int)$Session->user()['id'];
        $sql  = "SELECT * FROM ".$this->users_table." U LEFT JOIN ";
        $sql .= $this->login_table." L ON U.user_id = L.user_id_fk ";
        $sql .= "WHERE U.user_id = '{$user_id}' LIMIT 1";

        $result = $Database->query($sql);
        if($Database->num_rows($result) != 1) return false;
        return $Database->fetch_data($result);
    }

    public function query_avatar() {
        global $Database, $Session;

        $user_id = (int)$Session->user()['id'];
        $sql  = "SELECT profile_photo AS pro FROM ".$this->users_table." WHERE user_id = '{$user_id}' LIMIT 1";
        $result = $Database->query($sql);
        if($Database->num_rows($result) != 1) return false;
        return $Database->fetch_data($result)->pro;
    }

    //Returns logged in user's email if argument is 0 ie. default
    public function query_email($user_id=0) {
        global $Database, $Session;

        $user_id = ($Session->logged_in()) ? (int)$Session->user()['id'] : (int)$Database->clean_data($user_id);

        $sql  = "SELECT contact_email FROM ".$this->users_table." WHERE user_id = '{$user_id}' LIMIT 1";
        $result = $Database->query($sql);
        if($Database->num_rows($result) != 1) return false;
        $email = $Database->fetch_data($result)->contact_email;

        if (!empty($email)) return $email;

        $sql  = "SELECT email FROM ".$this->login_table." WHERE user_id_fk = '{$user_id}' LIMIT 1";
        $result = $Database->query($sql);
        if($Database->num_rows($result) != 1) return false;
        $email =  $Database->fetch_data($result)->email;
        return (!empty($email)) ? $email : false;
    }

    public function save_account_settings($post=null) {
        global $Database, $Session;
        
        $user_id = (int)$Session->user()['id'];
        $gender  = ($post['gender'] == "M") ? "M": "F";
        $show_real_name = ((int)$post['show_real_name'] == 1) ? 1 : 0;

        $sql = "UPDATE ".$this->users_table." SET gender = '{$gender}', show_real_name = '{$show_real_name}' WHERE user_id = '{$user_id}' LIMIT 1";
        return ($Database->query($sql) === true) ? "Settings saved successfully!" : "Oops! an error occured whilst saving settings.";
    }


    public function update_profile($post=null) {
        global $Database, $Session, $Secure;
        
        $user_id = (int)$Session->user()['id'];
        $field   = $Database->clean_data($post['field']);
        $value   = $Database->clean_data($post['value']);

        $field_name = false;
        switch ($field) {
            case 'fname':
                if ( !isset($value[2])) {
                    return "Please enter a valid first name";
                } elseif (!preg_match('/([a-zA-Z]+)/', $value)) {
                    return "First name must contain only alphabets";
                }

                $field_name = "first_name";
                break;
            case 'lname':
                if ( !isset($value[2])) {
                    return "Please enter a valid last name";
                } elseif (!preg_match('/([a-zA-Z]+)/', $value)) {
                    return "Last name must contain only alphabets";
                }

                $field_name = "last_name";
                break;
            case 'uname':
                if ( !isset($value[2])) {
                    return "Please enter a valid first name";
                } elseif (!preg_match('/([a-zA-Z0-9_]+)/', $value)) {
                    return "Username can only contain alphanumeric characters, and an underscore";
                }
                $field_name = "user_name";
                break;
            case 'contact_email':
                $result = $Secure->validate_email($value, "users");
                if ($result !== "OK") return $result;
                $field_name = "contact_email";
                break;
            case 'phone_number':
                if(!isset($value[9])) {
                    return "Phone number is invalid";
                } elseif (!preg_match('/([0-9]+)/', $value)) {
                    return "Phone number must contain only numeric characters";
                }
                $field_name = "phone_number";
                break;
            default:
                break;
        }

        if (!$field_name) return "Invalid field argument";
        
        $sql  = "UPDATE ".$this->users_table." SET {$field_name} = '{$value}' WHERE user_id = '{$user_id}' LIMIT 1";
        return ($Database->query($sql) === true) ? "Updated successfully!" : "Oops!, sorry error saving information";
    }

   public function get_role($level_code="") {
        if ($level_code == 3) {
            $level = "Super Administrator";
        } elseif($level_code == 2) {
            $level = "Administrator";
        } elseif($level_code == 1) {
            $level = "Moderator";
        } else {
            $level = "Member";
        }
        return $level;
    } 
}

$Users = new Users();
?>