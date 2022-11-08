<?php
/** (c) Joachim Göddel . RLMS */


    $j = 0;
    $sw = array(80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 96, 97);
    $ca = count($sw);
    for ($i = 0; $i < $ca; $i++):
        $host = "172.16.33." . $sw[$i];
        $x = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.11.0");
        $a = explode(" ", $x);
        $b = count($a);
        if($b > 4) {
            $c = explode(":", $a[4]);
            $x = $a[2] . " Tag(e) " . $c[0] . " Stunde(n) " . $c[1] . " Minute(n)";
            $realcolor = '';
        } else {
            $c = explode(":", $a[2]);
            $x = $c[0] . " Stunde(n) " . $c[1] . " Minute(n)";
            $a[2] = $c[0]."<small class='font-size-12'>STD</small>";
            $realcolor = 'danger';
        }
        $y = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.5.0");
        $y = str_replace("STRING: ", "", $y);
        $y = str_replace("\"", "", $y);
        $l = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.6.0");
        $l = str_replace("STRING: ", "", $l);
        $l = str_replace("\"", "", $l);
        $l = utf8_encode($l);
        // Farben
        if($a[2] <= 15): $color = 'warning'; endif;
        if($a[2] <= 5): $color = 'danger text-white'; endif;
        if($a[2] > 15): $color = 'primary text-white'; endif;
        $color = ($realcolor != '') ? $realcolor : $color;
        $f = (isset($a[4])) ? $a[2] : $x;

        $XMLS.= "
<switch>
    <asset>$y</asset>
    <online>$f</online>
    <host>$host</host>
    <dauer>$x</dauer>
</switch>";
        $j++;
    endfor;
$XML = "<?xml version='1.0' encoding='UTF-8'?>
<network>$XMLS</network>";
header('Content-Type: application/xml; charset=utf-8');
echo $XML;