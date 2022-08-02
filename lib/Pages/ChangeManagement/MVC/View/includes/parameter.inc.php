<?php
/** (c) Joachim Göddel . RLMS */
# Session starten, wenn noch nicht geschehen
if (session_status() == PHP_SESSION_NONE) session_start();

if(!empty($_GET)):
    // Es wurde ein Parameter übergeben
    if(isset($_GET['monat'])): // Der Monat wurde übergeben
        $_SESSION['GET']['monat'] = $_GET['monat'];
    endif;
    if(isset($_GET['jahr'])): // Das Jahr wurde übergeben
        $_SESSION['GET']['jahr'] = $_GET['jahr'];
        if(!isset($_SESSION['GET']['monat'])):
            $_SESSION['GET']['monat'] = $_SESSION['parameter']['monat'];
        endif;
    endif;
else:
    // Keine Parameter wurden übergeben. Es sind die Standardwerte zu verwenden
    $_SESSION['GET']['monat'] = $_SESSION['parameter']['monat'];
    $_SESSION['GET']['jahr'] = $_SESSION['parameter']['jahr'];
endif;
// Wenn das ausgewählte Jahr nicht das aktuelle Jahr ist
if($_SESSION['GET']['jahr'] != $_SESSION['parameter']['jahr']):
    $dspMonat = 12;
else:
    $dspMonat = $_SESSION['parameter']['monat'] * 1;
endif;

// Berechtigungsebene
$ebene = '5';
// Leserechte
if(isset($_SESSION['rechte'][''.$ebene.'.1']) && $_SESSION['rechte'][''.$ebene.'.1'] == 1):
    $seitelesen = 1;
else:
    $seitelesen = 0;
endif;
// Schreibrechte
if(isset($_SESSION['rechte'][''.$ebene.'.0']) && $_SESSION['rechte'][''.$ebene.'.0'] == 1): // Admin Rechte
    $seiteschreiben = 1;
    $seitelesen = 1;
    $seiteadmin = 1;
else:
    if(isset($_SESSION['rechte'][''.$ebene.'.2']) && $_SESSION['rechte'][''.$ebene.'.2'] == 1): // Schreibrechte
        $seiteschreiben = 1;
        $seitelesen = 1;
    else:
        $seiteschreiben = 0;
    endif;
endif;

// Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';

// Berechtigungsebene
$ebene = '9';
// Leserechte
if(isset($_SESSION['rechte'][''.$ebene.'.1']) && $_SESSION['rechte'][''.$ebene.'.1'] == 1):
    $apqplesen = 1;
else:
    $apqplesen = 0;
endif;
// Schreibrechte
if(isset($_SESSION['rechte'][''.$ebene.'.0']) && $_SESSION['rechte'][''.$ebene.'.0'] == 1): // Admin Rechte
    $apqpschreiben = 1;
    $apqplesen = 1;
    $apqpadmin = 1;
else:
    if(isset($_SESSION['rechte'][''.$ebene.'.2']) && $_SESSION['rechte'][''.$ebene.'.2'] == 1): // Schreibrechte
        $apqpschreiben = 1;
        $apqplesen = 1;
    else:
        $apqpschreiben = 0;
    endif;
endif;