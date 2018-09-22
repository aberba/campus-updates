<?php
class Session {
    private $login_table = "login";
    private $users_table = "users";

    private $logged_id  = false;
    private $activated  = false;
    private $user_id    = null;
    private $user_name  = null;
    private $user_email = null;
    private $user_role  = null;
    private $fullname   = null;
    
    function __construct() {
        session_start();
        $this->check_login();
    }

    public function check_login() {
        global $Database;

        if(isset($_SESSION['u_id'])) {
            $uid    =  (int) $Database->clean_data($_SESSION['u_id']);
            
            // Validate users record in login table
            $sql  = "SELECT * FROM ".$this->login_table." WHERE user_id_fk = '{$uid}' AND activation IS NULL LIMIT 1";
            $result = $Database->query($sql);
            if($Database->num_rows($result) != 1) return false;
            $row       = $Database->fetch_data($result);
            $email     = $row->email; // stores email address
            $activated = ($row->activation == null) ? true : false;
            
            //Fecth users in fo and set seesion
            $sql = "SELECT * FROM ".$this->users_table." WHERE user_id = '{$uid}' LIMIT 1";
            $result = $Database->query($sql);
            $row    = $Database->fetch_data($result);
            $this->user_id    = $row->user_id;
            $this->user_name  = ((int)$row->show_real_name == 1) ? $row->first_name." ".$row->last_name : $row->user_name;
            $this->user_email = $email;
            $this->user_role  = (int)$row->role;
            $this->logged_id  = true; 
            $this->activated  = $activated;
            $this->fullname   = $row->first_name. " " .$row->last_name;
        }
    }

   //Authenticate users action such as delete, edit, etc. that require permissions
    public function authenticateAction($password="") {
        global $Database, $Secure, $Session;

        $user_id  = $Session->user()['id'];
        $sql    = "SELECT password FROM ".$this->login_table." WHERE user_id_fk = '{$user_id}' LIMIT 1";
        $result = $Database->query($sql);
        $row = $Database->fetch_data($result);
        
        // Validate passsword with the encrypted copy
        if(!$Secure->password_check($password, $row->password)) {
            return "Access denied";
        } else {
            return "Access granted";
        }
    }

    //Authenticate users action such as delete, edit, etc. that require permissions
    public function change_account_freeze($user_id=0) {
        global $Database, $Session;

        $user_id = (int)$Database->clean_data($user_id);
        $sql     = "SELECT role FROM ".$this->users_table." WHERE user_id = '{$user_id}' LIMIT 1";
        $result  = $Database->query($sql);
        $role    = (int)$Database->fetch_data($result)->role;
        
        if (!$Session->is_admin()) return "Sorry, you donnot have permission to undertake this action";
       
        $session_id = $Session->user()['id'];
        $sql     = "SELECT role FROM ".$this->users_table." WHERE user_id = '{$session_id}' LIMIT 1";
        $result  = $Database->query($sql);
        $session_role  = (int)$Database->fetch_data($result)->role;

        //Deny if victim is admin and action taker is not a Super admin i.e.
        // Admin cannot delete Admin, but Super user can delete admin and Another Super user
        if ($session_role <= $role) return "Sorry, you donnot have permission to delete this user";

        //now select users freeze status and change
        $sql = "SELECT frozen FROM ".$this->login_table." WHERE user_id_fk = '{$user_id}' LIMIT 1";
        $result = $Database->query($sql);
        $frozen = (int)$Database->fetch_data($result)->frozen;

        $new_frozen = ($frozen == 1) ? 0 : 1;
        $message    = ($frozen == 1) ? "User's account has been unfrozen successfully!" :  "User's account is now frozen!";
        $sql = "UPDATE ".$this->login_table." SET frozen = '{$new_frozen}' WHERE user_id_fk = '{$user_id}' LIMIT 1"; 
        return ($Database->query($sql) === true) ? $message : "Oops! error changing user's frozen status";
    }

    public function logged_in() {
        return $this->logged_id;
    }

    public function is_super_admin() {
        return ($this->user_role == 3) ? true : false;
    }

    public function is_admin() {
        return ($this->user_role >= 2) ? true : false;
    }

    public function is_moderator() {
        return ($this->user_role >= 1) ? true : false;
    }

    public function is_activated($user_id=0) {
        global $Database;

        $user_id = (int)$Database->clean_data($user_id);
        if ($user_id == 0) return $this->activated;

        $sql = "SELECT activation FROM ".$this->login_table." WHERE user_id_fk = '{$user_id}' LIMIT 1";
        $result = $Database->query($sql);
        return ($Database->fetch_data($result)->activation == null) ? true : false;
    }
    
    public function user() {
        $user = array( "id"       => (int)$this->user_id,
                       "fullname" => $this->fullname,
                       "name"     => $this->user_name,
                       "email"    => $this->user_email,
                       "level"    => $this->user_role
                      );
        return $user; 
    }


      /*
    function login($user_id=0) {
        $user_id = (int)$user_id;
        if(empty($user_id)) return false;

        $_SESSION['u_id']    = $user_id;
        $this->log_login($user_id);
        return true;
    }

    public function log_login($user_id=0) {
        global $Database;

        $user_id   = (int) $Database->clean_data($user_id);
        $time         = time();
        $sql  = "UPDATE ".$this->login_table." SET last_login = '{$time}' WHERE user_id_fk = '{$user_id}' LIMIT 1";
        $Database->query($sql);
    }
    
    public function logout() {
        if(isset($_COOKIE[session_name()])) setcookie(session_name(), "", time()-3600);     
        unset($_SESSION['u_id']);
        session_destroy();

        $this->user_id    = null; 
        $this->user_name  = null; 
        $this->user_role  = null;
        $this->logged_id  = false;    
    }*/
}

$Session = new Session();
?>