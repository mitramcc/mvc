<?php
class Email_Model extends Model{

    public function __construct(){
        parent::__construct();
        $this->mail = new Email();
    }

    public function sendMail($to, $to_name, $subject, $msg){

        $this->mail->Subject = utf8_decode($subject);
        $this->mail->AltBody = "Para ver esta mensagem por favor use um visualizador de emails com supporte para emails em HTML.";

        $this->mail->MsgHTML($msg, dirname(__FILE__));
        $this->mail->AddAddress($to, $to_name);

        return $this->mail->Send();
    }

    public function sendWithStringAttachment($arayTo, $attachment, $message){

        $this->mail->Subject    = "Assunto";
        $this->mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        $msg = $message;
        $this->mail->MsgHTML($msg);
        foreach($arayTo as $to){
            $this->mail->AddAddress($to['email'], $to['name']);
        }

        $this->mail->AddStringAttachment($attachment['data'], "Encomenda - ".$attachment['encomenda'].$attachment['ext'], 'base64', $attachment['type']);

        return $this->mail->Send();
    }

    /**
    * Contact Form
    */
    public function sendContact($data)
    {
        $subject = "Contacto site - ".$data["nome"];
        $msg =  $data["msg"]."\n\n <br><br> ".
                "Nome: ".$data["nome"]." \n\n <br><br>".
                "Email: ".$data["email"];

        return $this->sendMail(SEND_TO_MAIL, "Contacto Site", $subject, $msg);

    }

    private function _setTemplate($Utilizador='', $mensagem="")
    {
        $template = file_get_contents("../emails/basic_template.html", FILE_USE_INCLUDE_PATH);

        $template = preg_replace("/::IMG_PATH::/", IMAGES_URL, $template);
        $template = preg_replace("/::UTILIZADOR::/", $Utilizador, $template);
        $template = preg_replace("/::MENSAGEM::/", $mensagem, $template);
        $template = preg_replace("/::CONTACTOS_URL::/", BASE_URL."#contactos", $template);

        return $template;
    }

}
?>