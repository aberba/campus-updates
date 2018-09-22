<?php
class Session {
    private $login_table = "login";
    private $users_table = "users";

    private $logged_id  = false;
    private $activated  = false;
    private $user_id    = null;
    private $user_name  = null;
    private $user_email = null;
    private $fullname   = null;
    private $user_role  = null;
    
    function __construct() {
        session_start();
        $this->check_login();
    }

    public function check_login() {
        global $Database;

        if (isset($_COOKIE['u_id'])) {
            $_SESSION['u_id'] = (int)$_COOKIE['u_id'];
        }

        if(isset($_SESSION['u_id'])) {
            $uid    =  (int)$Database->clean_data($_SESSION['u_id']);
            
            // Validate users record in login table
            $sql  = "SELECT * FROM ".$this->login_table." WHERE user_id_fk = '{$uid}' LIMIT 1";
            $result = $Database->query($sql);
            if($Database->num_rows($result) != 1) return false;
            $row       = $Database->fetch_data($result); 
            $email     = $row->email;
            $activated = ($row->activation == null) ? true : false;
            
            //Fecth users in fo and set seesion
            $sql = "SELECT * FROM ".$this->users_table." WHERE user_id = '{$uid}' LIMIT 1";
            $result = $Database->query($sql);
            $row    = $Database->fetch_data($result);
            $this->user_id    = $row->user_id;
            $this->user_name  = ((int)$row->show_real_name == 1) ? $row->first_name." ".$row->last_name : $row->user_name;
            $this->user_email = $email;
            $this->user_role  = $row->role;
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
  
    public function set_search_category($category="posts") {
        global $Database;
     
        $category = $Database->clean_data($category);
        $cat = null;

        switch ($category) {
            case 'posts':
                $cat = "posts";
                break;
            case 'events':
                $cat = "events";
                break;
            case 'capture':
                $cat = "capture";
                break;
            
            default:
                $cat = "posts";
                break;
        }

        $_SESSION['search_category'] = $cat;
    }

    public function search_category_is_set() {
        $options = array("posts", "events", "capture");
        return ( isset($_SESSION['search_category']) && in_array($_SESSION['search_category'], $options)) ? true : false;
    }

    public function query_search_category() {
        return isset($_SESSION['search_category']) ? $_SESSION['search_category'] : "posts";
    }

    public function authenticate($post=null, $keep_me_logged_in=false) {
        global $Database, $Secure;

        $login_try = (isset($_SESSION['login_try'])) ? (int)$_SESSION['login_try'] : 0;

        //Block login if users has tried an failed more than allowed
        if ($login_try > 7) return "Sorry, you have tried logging in several times and failed. <br>".
                                   "You are therefore suspended temporally for security reasons.";

        $email  = $Database->clean_data($post['email']);
        $sql    = "SELECT * FROM ".$this->login_table." WHERE email = '{$email}' LIMIT 1";
        $result = $Database->query($sql);

        //Increment sesssion login tries if fails a
        if($Database->num_rows($result) != 1) {
            $_SESSION['login_try'] = (isset($_SESSION['login_try'])) ? ( (int)$_SESSION['login_try'] + 1 ) : 1;
            return "Invalid e-mail and password combination";
        }

        $row = $Database->fetch_data($result);
        
        // Validate password with the hashed copy
        if(!$Secure->password_check($post['password'], $row->password)) {
            $_SESSION['login_try'] = (isset($_SESSION['login_try'])) ? ( (int)$_SESSION['login_try'] + 1 ) : 1;
            return "Invalid e-mail and password combination";
        }
        
        //validate account blockage ie. when users account is blocked
        if ((int)$row->blocked == 1) return "Sorry, your account has been blocked temporally. <br>".
                                            "Please contact system administrators for more information.";

         //validate account frozen ie. when users frozen
        if ((int)$row->frozen == 1) return "Sorry, your account has been frozen. <br>".
                                               "Please contact system administrators for more information.";

        return ($this->signin($row->user_id_fk, $keep_me_logged_in) === true ) ? "Login successful" : "Ooops! an error occured whilst logging you in";
    }

    private function signin($user_id=0, $keep_me_logged_in=false) {
        $user_id = (int)$user_id;
        if(empty($user_id) || $user_id == 0) return false;

        $_SESSION['u_id']    = $user_id;
        if ($keep_me_logged_in === true) {
            setcookie('u_id', $user_id, time() + (60 * 60 * 24 * 2), "/");
        }

        $this->log_login($user_id);
        return true;
    }

    public function change_password($post=null) {
        global $Database, $Secure;

        $cpass = $post['cpass']; // current password
        $pass1 = $post['pass1'];
        $pass2 = $post['pass2'];

        //check new passwords
        if(!isset($cpass[7])) return "Invalid current password";

        if($cpass === $pass1) return "Your new and current password cannot be the same";

        if($pass1 !== $pass2) return "The two new passwords donnot match";

        $result = $Secure->check_password_strength($pass1);
        if($result !== "OK") return $result;

        //validate current password from BD
        $user_id = (int)$this->user()['id'];

        $sql    = "SELECT * FROM ".$this->login_table." WHERE user_id_fk = '{$user_id}' LIMIT 1";
        $result = $Database->query($sql);
        if($Database->num_rows($result) != 1) return "Oops!, an error occured whilst validating your session";
        $row = $Database->fetch_data($result);
        
        // Validate current password with the hashed copy
        if(!$Secure->password_check($cpass, $row->password)) return "Access denied! <br>Invalid current password.";

        $hashed_password = $Secure->secure_password($pass1);
        $sql = "UPDATE ".$this->login_table." SET password = '{$hashed_password}' WHERE user_id_fk = '{$user_id}' LIMIT 1";
        return ($Database->query($sql) === true) ? "Password has been changed successfully!" : "Oops!, an error occured whilst changing your password";
    }

    private function log_login($user_id=0) {
        global $Database;

        $user_id   = (int) $Database->clean_data($user_id);
        $time         = time();
        $sql  = "UPDATE ".$this->login_table." SET last_login = '{$time}' WHERE user_id_fk = '{$user_id}' LIMIT 1";
        $Database->query($sql);
    }
    
    public function logout() {
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), "", time() - (3600 * 60 * 24), "/");     
        }

        
        setcookie('u_id', null, time() - (3600 * 60 * 60 * 24), "/");     
        setcookie('u_id', null, time() - (3600 * 60 * 60 * 24), "/ajax/");     
        unset($_SESSION['u_id']);
        session_destroy();

        $this->user_id    = null; 
        $this->user_name  = null; 
        $this->user_role  = null;
        $this->logged_id  = false;    
    }

    public function logged_in() {
        return $this->logged_id;
    }
    
    public function send_recovery($email="") {
        global $Database, $Users, $Email, $Settings, $Secure;
        
        $email = $Database->clean_data($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "Please enter a valid email address.";

        //Validate multiple try failure store i session
        $try = (isset($_SESSION['recovery_try'])) ? (int)$_SESSION['recovery_try'] : 1;
         if ($try > 5) return "Sorry!, you have tried multiple times and failed. ".
                              "You are temporally suspended from additional tries for security reasons. ".
                              "<br>Please try again later.";

        $sql = "SELECT user_id_fk FROM ".$this->login_table." WHERE email = '{$email}' LIMIT 1";
        $result = $Database->query($sql);
        if ($Database->num_rows($result) != 1) {
            ++$try;
            $_SESSION['recovery_try'] = $try;
            return "No record of your email address was found. <br> Please make sure it is correct.";
        }
        $_SESSION['recovery_try'] = $try;

        $user_id = $Database->fetch_data($result)->user_id_fk;

        $to       = $Users->query_email($user_id);
        $subject  = $Settings->site_name()." account password recovery";
        $tmp_password = substr($to, 0, 3) ."T". time();
        $tmp_password = substr(md5($tmp_password), 0, 10)."_".date("Y", time());

        $tmp_password_hash = $Secure->secure_password($tmp_password);

        if (!$to) return "An error retrieving you email address";

        $message = "<html><head><title>{$subject}</title></head>
                        <body>
                            <div class='width: 100%; padding:20px; text-align: center;'>
                                <p>Hello, you sent a request to recover your account password. Please use the following temporal credentials to sign into your account.<p>
                                <p>Please make sure to change your password to a more secure one immediately you sign in.</p>
                                <p><strong>Email Address</strong>: Your accounht email address<br>
                                   <strong>Password: </strong> ". $tmp_password. "</p>
                            </div>
                        </body>
                    </html>";

        $Settings->transaction_start();
           $sql    = "UPDATE ".$this->login_table." SET password = '{$tmp_password_hash}' WHERE user_id_fk = '{$user_id}' LIMIT 1";
           if (!$Database->query($sql)) {
              $Settings->transaction_rollback();
              return "Oops! an error occured whilst procesing your information.";
           }

           if ($Email->send($to, $subject, $message)) return "Oops1, an error occured whilst send recovery message.";
        $Settings->transaction_commit(); //commit if everything was OK

        return "A recovery email message was sent successfully to ". $email.
               ". Please check your inbox to recover your account.";
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
        $user = array("id"         => (int)$this->user_id,
                       "name"      => $this->user_name,
                       "fullname"  => $this->fullname,
                       "email"     => $this->user_email,
                       "level"     => $this->user_role
                      );
        return $user; 
    }

    public function signup($post=null) {
        global $Database, $Secure, $Email, $Settings;
         
        if(!isset($post['agreed'])) return "You must agree to the terms and conditions to sign up";
        if(!isset($post['gender'])) return "Please select your gender";

        $fname = $Database->clean_data($post['fname']);
        $lname = $Database->clean_data($post['lname']);
        $gender = ($Database->clean_data($post['gender']) == "M") ? "M" : "F";
        $uname = $Database->clean_data($post['uname']);
        $email = $Database->clean_data($post['email']);
        $pass1 = $post['pass1']; //not sanatized since it will be hashed
        $pass2 = $post['pass2'];

        $result = $Secure->validate_name($fname, "fname");
        if($result !== "OK") return $result;

        $result = $Secure->validate_name($fname, "lname");
        if($result !== "OK") return $result;

        $result = $Secure->validate_email($email);
        if($result !== "OK") return $result;

        $result = $Secure->validate_user_name($uname);
        if($result !== "OK") return $result;
        
        if($pass1 !== $pass2) return "The two passwords donnot match";

        $result = $Secure->check_password_strength($pass1);
        if($result !== "OK") return $result;
        $hashed_password = $Secure->secure_password($pass1);


        //Insert data into users table
        $Settings->transaction_start();

        $date = time();
        $sql  = "INSERT INTO ".$this->users_table." (user_name, first_name, last_name, gender, date_registered) ";
        $sql .= "VALUES ('{$uname}', '{$fname}', '{$lname}', '{$gender}', '{$date}')";
        if (!$Database->query($sql)) {
            $Settings->transaction_rollback();
            return "Oops!, error inserting account information";
        }

        $last_id = $Database->insert_id();
        $activation_key = md5($fname.$email.time()); 
        
        //insert login data into login table
        $sql  = "INSERT INTO ".$this->login_table." (user_id_fk, email, password, activation) ";
        $sql .= "VALUES ('{$last_id}', '{$email}', '{$hashed_password}', '{$activation_key}')";

        if (!$Database->query($sql)) {
            $Settings->transaction_rollback();
            return "Oops!, an error occured whilst creating your account. <br />Please try again later.";
        }
        $Settings->transaction_commit();


        // Send account activation email

        //if all credentials are valid, send mail activation email
        $site_name = $Settings->site_name();
        $subject   = $site_name. " Account Activation";
        
        $activation_uri = SITE_URI. "/activation/" .$last_id. "/".$activation_key;
        $message = "<!doctype HTML>
                    <html lang='en'>
                    <head>
                        <meta charset='utf-8'>
                        <title>{$subject}</title>
                    </head>
                    <body style='background: #ffffff'>
                        <div style='width: 100%; text-align: center; font-size: 16px;'>
                            <h1>{$site_name} Account Activation</h1>

                            <p style='margin: 30px 0'><strong>Hello Dear</strong>, your account on {$site_name} is 
                            awaiting activation. <br />Click on the link bellow to activate your account.</p>

                            <p style='margin: 30px 0'>Delete this message if you did not make any registration as such.</p>
                            
                            <br /><br /><br />
                            <p> <a href='{$activation_uri}' target='_blank'>Activate my account now</a> </p><br />

                            <h3>{$site_name}</h3>
                            <p><small>Super genuine information</small></p>
                        </div>
                    </body>
                    <html>";

        $Email->send($email, $subject, $message);

        return "Account created successfully! <br />A confirmation message has been sent to " .$email. 
               ". <br />Please check your inbox to activate your account.";
    }

    public function resend_activation() {
        global $Database, $Email, $Users, $Settings;

        $user      = $this->user();
        $user_id   = $user['id'];
        $user_name = $user['name'];

        $sql = "SELECT * FROM ".$this->login_table." WHERE user_id_fk = '{$user_id}' LIMIT 1";
        $result = $Database->query($sql);
        $row    = $Database->fetch_data($result);

        //if all credentials are valid, send mail activation email
        $site_name = $Settings->site_name();
        $to        = $Users->query_email($user_id); // since email might be contact_email and not login email
        $subject   = $site_name. " Account Activation";
        $activation_key = $row->activation;
        $activation_uri = SITE_URI. "/activation/". $user_id ."/". $activation_key;

        $message = "<!doctype HTML>
                    <html lang='en'>
                    <head>
                        <meta charset='utf-8'>
                        <title>{$subject}</title>
                    </head>
                    <body style='background: #ffffff'>
                        <div style='width: 100%; text-align: center; font-size: 16px;'>
                            <h1>{$site_name} Account Activation</h1>

                            <p style='margin: 30px 0'><strong>Hello Dear</strong>, your account on {$site_name} is 
                            awaiting activation. <br />
                            Click on the link bellow to activate your account.</p>

                            <p style='margin: 30px 0'>Delete this message if you did not make any registration as such.</p>
                            
                            <br />
                            <p> <a href='{$activation_uri}' target='_blank'>Activate my account now</a> </p><br />

                            <h3>{$site_name}</h3>
                            <p><small>Super genuine information</small></p>
                        </div>
                    </body>
                    <html>";
        echo $message;

        // Send account activation email
        if (!$Email->send($to, $subject, $message)) {
            return "Ooops! error sending activation message. <br>".
                   " Make sure your contact email is correct";
        } else {
            return "An confirmation message has been sent to " .$to. 
               ". <br />Please check your inbox to activate your account.";
        }
    }

    public function activate_user($user_id=0, $key="") {
        global $Database;

        $user_id = (int)$Database->clean_data($user_id);
        $key     = $Database->clean_data($key);

        $sql = "SELECT * FROM ".$this->login_table." WHERE user_id_fk = '{$user_id}' AND activation = '{$key}' LIMIT 1";
        $result = $Database->query($sql);
        if ($Database->num_rows($result) != 1) return false;

        $sql = "UPDATE ".$this->login_table." SET activation = NULL WHERE user_id_fk = '{$user_id}' LIMIT 1";
        return ($Database->query($sql) === true) ? true : false;
    }
}

$Session = new Session();
?>