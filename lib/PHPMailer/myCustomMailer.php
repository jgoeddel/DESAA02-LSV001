<?php
/** (c) Joachim Göddel . RLMS */

namespace App\PHPMailer;

use App\Functions\Functions;

require_once "PHPMailer.php";
require_once "SMTP.php";

class MyCustomMailer extends PHPMailer
{
    private string $_host = "cau-mailrelay.service.rhs.zz";

    public function __construct($exceptions = true)
    {
        $this->isSMTP();
        $this->isHTML(true);
        $this->Host = $this->_host;
        $this->CharSet ="UTF-8";

        parent::__construct($exceptions);
    }

    # Neuer Changemanagement Eintrag
    public static function sendEmailCMNeu(): bool
    {
        $mail_sent = false;
        try {
            $mail = new PHPMailer;
            # Bilder
            $mail->AddEmbeddedImage("rhenus_logo.png","rhenus_logo","rhenus_logo.png");
            $mail->AddEmbeddedImage("rhenus_slogan_blau.png","rhenus_slogan","rhenus_slogan_blau.png");
            $mail->AddEmbeddedImage("header_ka.jpg","header_ka","header_ka.jpg");
            $mail->AddEmbeddedImage("rhenus_mail_ecke.png","header_ecke","rhenus_mail_ecke.png");
            # Mail Header
            $mail->setFrom('joachim.goeddel@de.rhenus.com', 'RLMS IT');
            $mail->addReplyTo('joachim.goeddel@de.rhenus.com', 'RLMS IT');
            # An wen geht die Mail
            $mail->addAddress('joachim.goeddel@de.rhenus.com', 'RLMS IT');
            # Betreff
            $mail->Subject = 'Einsatz von Kundenänderungen';

            # Mail Inhalt
            $mailContent = '';
            include_once "head.tmpl.php"; # HTML HEAD
            include_once "message.ka.einsatz.tmpl.php"; # HTML E-MAIL INHALT
            $mail->Body = $mailContent;

            # Mail versenden
            if($mail->send()) $mail_sent = true;
        }
        catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: { $mail->ErrorInfo }";
        }
        return $mail_sent;
    }
}

myCustomMailer::sendEmailCMNeu();