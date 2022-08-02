<?php

use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\PHPMailer\PHPMailer;

require Functions::getBaseURL() . "lib/PHPMailer/src/Exception.php";
require Functions::getBaseURL() . "lib/PHPMailer/src/PHPMailer.php";
require Functions::getBaseURL() . "lib/PHPMailer/src/SMTP.php";

# Neue Mail
$mail = new PHPMailer();
# SMTP
$mail->IsSMTP();
$mail->Host = "cau-mailrelay.service.rhs.zz";

# HTML
$mail->isHTML(true);
# Charset
$mail->CharSet ="UTF-8";

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
# $mail->addAddress('1f177b8f.rhenusglobal.onmicrosoft.com@emea.teams.ms', 'Kundenänderungen LMS - Rhenus LMS Intranet');
# Alle User mit Berechtigungen abrufen um die Mail zu versenden
# Es müssen alle Mitarbeiter mit der Berechtigung CM (36) sowie dem zugehörigen Standort abgerufen werden
$a = AdministrationDatabase::getEmailUserID($citycode);
foreach($a as $b):
    # E-Mail Adresse abrufen
    $email = AdministrationDatabase::getMaiAddresslUser($b->id);
echo $email."<br>";
    #$mail->addAddress($email); # Kommentierung bei Live Schaltung entfernen !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
endforeach; # $a
# Mail an Teams in einfachem Text versenden.

# $mail->addCC('cc1@example.com', 'Elena');
# $mail->addBCC('bcc1@example.com', 'Alex');
# Betreff
$mail->Subject = 'Einsatz von Kundenänderungen';

# Mail Inhalt
$mailContent = '';
include_once "head.tmpl.php"; # HTML HEAD
include_once "message.ka.einsatz.tmpl.php"; # HTML E-MAIL INHALT


$mail->Body = $mailContent;

# Externer Mail Inhalt !
# $mail->msgHTML(file_get_contents('contents.html'), __DIR__);

# Mail versenden
if($mail->send()){

}else{

}