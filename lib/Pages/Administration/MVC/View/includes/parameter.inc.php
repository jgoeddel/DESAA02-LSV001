<?php
/** (c) Joachim Göddel . RLMS */

// Berechtigungsebene
$ebene = '1';
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