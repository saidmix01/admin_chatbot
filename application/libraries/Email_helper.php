<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(APPPATH . 'libraries/PHPMailer/PHPMailer.php');
require_once(APPPATH . 'libraries/PHPMailer/SMTP.php');
require_once(APPPATH . 'libraries/PHPMailer/Exception.php');

class Email_helper
{
    protected $mail;
    protected $email = "noreply@webcolsoluciones.com.co";
    protected $passwd = "9C{67iWAbQEYR#O[";
    protected $web_name = "WebColSoluciones";

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // Configuración SMTP
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $this->email;
        $this->mail->Password   = $this->passwd; // usa app password si es Gmail
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port       = 587;

        $this->mail->setFrom($this->email, $this->web_name);
        $this->mail->isHTML(true);
    }

    public function send_mail($to, $subject, $msn, $alternative = '')
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $msn;
            $this->mail->AltBody = $alternative ?: strip_tags($msn);
            $this->mail->CharSet = 'UTF-8';
            $this->mail->Encoding = 'base64';
            return $this->mail->send();
        } catch (Exception $e) {
            echo $e;
            log_message('error', 'Error sending email: ' . $this->mail->ErrorInfo);
            return false;
        }
    }
}
