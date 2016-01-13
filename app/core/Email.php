<?php
require_once("Email/class.phpmailer.php");
require_once("Email/class.smtp.php");
require_once("Email/class.pop3.php");
class Email extends PHPMailer{

    public function __construct(){
        parent::__construct();
        $this->IsSMTP();                        // telling the class to use SMTP
        $this->Host       = SMTP_HOST;        // SMTP server
        $this->SMTPDebug  = 0;                  // 0 = no messages
                                                // 1 = errors and messages
                                                // 2 = messages only
        $this->SMTPSecure = SMTP_SECURITY;
        $this->SMTPAuth   = true;               // enable SMTP authentication
        $this->Port       = SMTP_PORT;          // set the SMTP port for the GMAIL server
        $this->Username   = MAIL_USERNAME;      // SMTP account username
        $this->Password   = MAIL_PASSWORD;      // SMTP account password
        $this->SetFrom(FROM_MAIL, FROM_NAME);
        $this->AddReplyTo(REPLY_TO_MAIL, REPLY_TO_NAME);
/*


        $this->IsSMTP();                        // telling the class to use SMTP

        $this->Host       = "ASPMX.L.GOOGLE.COM";   // SMTP server
        #$this->Host       = "localhost";   // SMTP server
        $this->SMTPDebug  = 0;                  // 0 = no messages
                                                // 8 = errors and messages
                                                // 2 = messages only
        $this->SMTPAuth   = false;               // enable SMTP authentication
        $this->Port       = '25';          // set the SMTP port for the GMAIL server
        $this->SetFrom("no-reply@pneuscruzeiro.com.pt", FROM_NAME);
        $this->AddReplyTo("no-reply@pneuscruzeiro.com.pt", REPLY_TO_NAME);
*/

    }
}
?>
