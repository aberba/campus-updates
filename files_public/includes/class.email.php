<?php
require_once("PHPMailer/class.phpmailer.php");
require_once("PHPMailer/class.smtp.php");

$Mailer = new PHPMailer();
$SMTP   = new SMTP();

class Email {
  
    //Sends email using SMTP credentials
    function send($to="", $subject="", $message="") {
        global $Mailer, $SMTP, $Settings;

        $Mailer->isSMTP();
        $Mailer->Host     = $Settings->smtp_host();
        $Mailer->Port     = $Settings->smtp_port();
        $Mailer->SMTPAuth = true;
        $Mailer->Username = $Settings->smtp_user_name();
        $Mailer->Password = $Settings->smtp_password();

        $Mailer->FromName = $Settings->site_name();
        $Mailer->From     = $Settings->site_public_email();
        $Mailer->addAddress($to);
        $Mailer->Subject  = $subject;
        $message = wordwrap($message, 70);
        $Mailer->Body     = $message;

        //For mail
        $from    = $Settings->site_name()." <". $Settings->site_public_email().">";
        $headers = "From: {$from}\n";
        $headers .= "X-Mailer: PHP/".phpversion()."\n";
        $headers .= "MIME-Version: 1.0\n"; 
        $headers .= "Content-Type: text/html; charset=utf-8";


        if ( $Mailer->send() ) {
            return true;
        } elseif (mail($to}, $subject, wordwrap($message, 70), $headers) ) {
            return true;
        } else {
            return false;
        }
    }
}
$Email = new Email();
?>