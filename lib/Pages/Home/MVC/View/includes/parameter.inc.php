<?php
/** (c) Joachim Göddel . RLMS */

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