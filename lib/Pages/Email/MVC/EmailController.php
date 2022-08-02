<?php
/** (c) Joachim Göddel . RLMS */

namespace App\Pages\Email\MVC;

use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Cron\CronDatabase;
use App\Pages\Home\IndexDatabase;
use App\PHPMailer\Exception;
use App\PHPMailer\PHPMailer;
use JetBrains\PhpStorm\Pure;

require_once Functions::getBaseUrl() . "lib/PHPMailer/PHPMailer.php";
require_once Functions::getBaseUrl() . "lib/PHPMailer/SMTP.php";

class EmailController extends PHPMailer
{
    private string $_host = "cau-mailrelay.service.rhs.zz";

    public function __construct($exceptions = true)
    {
        $this->isSMTP();
        $this->isHTML(true);
        $this->Host = $this->_host;
        $this->CharSet = "UTF-8";
        parent::__construct($exceptions);
    }

    # Mail Basics zusammenstellen
    public static function emailHTML($bild,$part,$nr,$teil,$var1 = '', $var2 = '', $var3 = '', $var4 = '', $var5 = ''): string
    {
        $txt = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
         <head>
          <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
          <title>Rhenus Automotive Services GmbH & Co. KG</title>
          <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
          <style type="text/css">
            a { color: #084997 !important; }
          </style>
        </head>
        <body style="margin: 0; padding: 0; background-color:#F0F2F3; font-family: Calibri, Arial, sans-serif;">
         <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 30px;">
          <tr>
           <td>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="800" style="border-collapse: collapse;">
             <tr>
              <td>
               <table align="center" border="0" cellpadding="0" cellspacing="0" width="800">
                 <tr>
                  <td colspan="3" align="left" bgcolor="#F0F2F3" style="padding: 0 0 10px 0;">
                   <img src="' . Functions::getBaseURL() . 'lib/Pages/Email/MVC/View/rhenus_logo.png" alt="Rhenus Logo" style="display: block">
                  </td>
                 </tr>
                 <!-- Ende Header -->
                 '. self::emailPicture($bild) .'
                 <!-- Ende Bild -->
                 '. self::emailBody($part,$nr,$teil,$var1) .'
                 <!-- Ende Inhalt -->
                 '. self::emailFooter() .'
                 <!-- Ende Footer -->
                </table>
              </td>
             </tr>
            </table>
           </td>
          </tr>
         </table>
        </body>
        </html>
        ';
        return $txt;
    }
    #[Pure] public static function emailHeader(): string
    {
        $txt = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
         <head>
          <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
          <title>Rhenus Automotive Services GmbH & Co. KG</title>
          <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        </head>
        <body style="margin: 0; padding: 0; background-color:#F0F2F3; font-family: Calibri, Arial, sans-serif;">
         <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 30px;">
          <tr>
           <td>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="800" style="border-collapse: collapse;">
             <tr>
              <td>
               <table align="center" border="0" cellpadding="0" cellspacing="0" width="800">
                 <tr>
                  <td colspan="3" align="left" bgcolor="#F0F2F3" style="padding: 0 0 10px 0;">
                   <img src="' . Functions::getBaseURL() . 'lib/Pages/Email/MVC/View/rhenus_logo.png" alt="Rhenus Logo" style="display: block">
                  </td>
                 </tr>
        ';
        return $txt;
    }
    #[Pure] public static function emailPicture($bild): string
    {
        $txt = '
        <tr>
            <td colspan="3" bgcolor="#FFFFFF">
                <img src="' . Functions::getBaseURL() . 'lib/Pages/Email/MVC/View/header_'.$bild.'.jpg" alt="Change Management" width="800" height="300" style="display: block">
            </td>
        </tr>
        ';
        return $txt;
    }
    public static function emailBody($part,$nr,$teil,$var1 = '', $var2 = '', $var3 = '', $var4 = '', $var5 = ''): string
    {
        if($part == 'CMNeu') { # Neuer Eintrag Change Management
            $txt = '
            <tr>
                <td colspan="3" bgcolor="#FFFFFF" style="padding: 20px;">
                    <h1 style="font-weight: 100; margin: 0; padding: 0; margin-bottom: 12px">' . $_SESSION['text']['b_cm'] . '</h1>
                    <p style="border-bottom: 1px dotted #333333; padding-bottom: 12px; margin-bottom: 12px;">' . $_SESSION['text']['m_tcmNeu'] .'</p>
                    <h2 style="font-weight:600; margin: 0; padding: 0;">'.$nr.' . '.$teil.'</h2>
                    <p style="margin: 0; padding: 0; padding-top: 12px; margin-top: 12px; border-top: 1px dotted #333333;">' . $_SESSION['text']['m_tLinkIntranet'] .'</p>
                </td>
            </tr>
            ';
        }
        if($part == 'Daily') { # Tägliche Mail mit der Anzahl der offenen Einträge
            $txt = '
            <tr>
                <td colspan="3" bgcolor="#FFFFFF" style="padding: 20px;">
                    <h1 style="font-weight: 100; margin: 0; padding: 0; border-bottom: 1px dotted #333333; padding-bottom: 12px; margin-bottom: 12px;">' . $_SESSION['text']['b_cm'] . '</h1>
                    <p style="margin: 0; padding: 0;">Hallo '.$var1.',<br><br>aktuell ist/sind noch '.$teil.' offene Anfrage(n) für den Standort '.$nr.' in unserer Datenbank.</p>
                    <p style="margin: 0; padding: 0; padding-top: 12px; margin-top: 12px; border-top: 1px dotted #333333; color:#999999; font-size: 12px">' . $_SESSION['text']['m_tLinkIntranet'] .'</p>
                </td>
            </tr>
            ';
        }

        return $txt;
    }
    #[Pure] public static function emailFooter(): string
    {
        $txt = '
        <tr>
            <td bgcolor="#F0F2F3" width="30%" style="color: #666666; padding-bottom: 20px; padding-top: 10px;">
                <p style="font-size: 11px; padding-top: 5px;">Rhenus Automotive Services GmbH & Co. KG<br>Carl-Zeiß-Str. 27<br>66740 Saarlouis GERMANY</p>
            </td>
            <td bgcolor="#F0F2F3" width="30%" style="color: #666666; padding-bottom: 20px;">
                <p style="font-size: 11px; padding-top: 5px;">Joachim Göddel<br>IT<br><a href="mailto:joachim.goeddel@de.rhenus.com" style="text-decoration: none; color:#000000">joachim.goeddel@de.rhenus.com</a></p>
            </td>
            <td bgcolor="#F0F2F3" align="right" width="40%" style="color: #666666; padding-bottom: 20px;">
                <img src="' . Functions::getBaseURL() . 'lib/Pages/Email/MVC/View/rhenus_slogan_blau.png" alt="Rhenus Logo" style="display: block">
            </td>
        </tr>
        ';
        return $txt;
    }
    public static function emailEnd(): string
    {
        $txt = '
        </table>
              </td>
             </tr>
            </table>
           </td>
          </tr>
         </table>
        </body>
        </html>
        ';
        return $txt;
    }

    # Neuer Changemanagement Eintrag

    /**
     * @throws Exception
     */
    public function sendEmailCMNeu()
    {
        foreach ($_POST as $key => $value) {
            $$key = $value;
        }

        $x = explode("|", $titel);

        $mail_sent = false;
        $mail = new PHPMailer;
        $mail->Host = $this->_host;
        $mail->isSMTP();
        $mail->isHTML(true);
        $mail->CharSet = "UTF-8";

        # Mail Header
        $mail->setFrom('joachim.goeddel@de.rhenus.com', 'Change Management Intranet');
        $mail->addReplyTo('joachim.goeddel@de.rhenus.com', 'Change Management Intranet');

        # An wen geht die Mail
        $mail->addAddress('joachim.goeddel@de.rhenus.com', 'RLMS IT');

        # Bilder
        # $mail->addEmbeddedImage("".Functions::getBaseURL() . "lib/Pages/Email/MVC/View/header_".$bild.".jpg","headerImage","header_".$bild.".jpg");

        # Es müssen alle Mitarbeiter mit der Berechtigung CM (36) sowie dem zugehörigen Standort abgerufen werden
        $a = AdministrationDatabase::getEmailUserID($citycode);
        foreach($a as $b):
            # E-Mail Adresse abrufen
            $email = AdministrationDatabase::getMaiAddresslUser($b->mid);
            #$mail->addAddress($email); # Kommentierung bei Live Schaltung entfernen !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        endforeach; # $a

        # Betreff
        $mail->Subject = $_SESSION['text']['h_CMNeu'];

        # Mail Content wird aus mehreren Dateien zusammengestellt
        $mailContent = self::emailHTML("cm","CMNeu","{$x[0]}","{$x[1]}");
        $mail->Body = $mailContent;

        # Testausgabe Browser
        echo $mail->Body;

        # Mail versenden
        if ($mail->send()) $mail_sent = true;

    }


    /**
     * @throws Exception
     */
    public function sendEmailCMDaily()
    {
        foreach(IndexDatabase::getCMCitycode() AS $cc) {
            $open = CronDatabase::getOpenCM("$cc->citycode");
            if ($open > 0) {
                # Alle Mitarbeiter des Standortes abrufen
                foreach(AdministrationDatabase::selectMaCC($cc->citycode) AS $user){
                    if($user->email) {
                        $mail_sent = false;
                        $mail = new PHPMailer;
                        $mail->Host = $this->_host;
                        $mail->isSMTP();
                        $mail->isHTML(true);
                        $mail->CharSet = "UTF-8";
                        # Mail Header
                        $mail->setFrom("joachim.goeddel@de.rhenus.com", "Change Management Intranet");
                        $mail->addReplyTo("joachim.goeddel@de.rhenus.com", "Change Management Intranet");
                        # TODO: NACHFOLGENDE ZEILE BEI LIVE VERSION SCHARF SCHALTEN UND DIE DARUNTER LÖSCHEN
                        #$mail->addAddress("$user->email", "$cc->citycode");
                        $mail->addAddress("joachim.goeddel@de.rhenus.com", "RLMS IT");
                        # Betreff
                        $mail->Subject = $_SESSION['text']['h_changeManagement'];
                        # Mail Content wird aus mehreren Dateien zusammengestellt
                        $mailContent = self::emailHTML("cm","Daily","$cc->citycode","$open","$user->vorname");
                        $mail->Body = $mailContent;
                        # Testausgabe Browser
                        echo $mail->Body;
                        # Mail versenden
                        if ($mail->send()) $mail_sent = true;
                    }
                }
            } # Es sind keine offenen Anfragen in der Datenbank, also muss auch nichts versendet werden
        }
    }

}