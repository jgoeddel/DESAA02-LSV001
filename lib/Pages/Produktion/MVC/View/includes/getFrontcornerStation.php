<?php
/** (c) Joachim Göddel . RLMS */

$x = 0;

    foreach ($y[$id]->attributes() as $d => $e):
        $$d[$id] = $e;
    endforeach;

    $color[$id] = (!isset($rot[$id]) || $rot[$id] == '') ? 'text-muted' : 'text-black'; # Schriftfarbe Station
    $fehler[$id] = ($fault[$id] == 'false') ? 'text-success' : 'text-danger'; # Schriftfarbe linker Kreis
    $opreq[$id] = ($opreq[$id] == 'false') ? 'text-white' : 'text-info'; # Schriftfarbe rechter Kreis

echo $color[$id]."|".$fehler[$id]."|&nbsp;".$rot[$id]."&nbsp;|&nbsp;".$vin[$id]."&nbsp;|".$name[$id]."|".$mode[$id]."|".$opreq[$id]."|".$fault[$id];