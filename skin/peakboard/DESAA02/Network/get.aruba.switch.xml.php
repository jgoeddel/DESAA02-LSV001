<?php
/** (c) Joachim Göddel . RLMS */
$j = 0; $g = 1;
$sw = array(10, 20, 30, 61, 52, 50, 11, 25, 31, 32, 33, 34, 35, 36, 38, 100, 55, 56, 110, 111, 16, 15, 57 );
$ca = count($sw);
for ($i = 0; $i < $ca; $i++):
    $host = "172.16.33." . $sw[$i];
    $x = snmp2_get($host, "public", ".1.3.6.1.2.1.1.3.0");
    $a = explode(" ", $x);
    $b = count($a);
    $y = snmp2_get($host, "public", ".1.3.6.1.2.1.1.5.0");
    $y = str_replace("STRING: ", "", $y);
    $y = str_replace("\"", "", $y);
    $z = snmp2_get($host, "public", ".1.3.6.1.2.1.2.1.0");
    $z = str_replace("INTEGER: ", "", $z);
    $l = snmp2_get($host, "public", ".1.3.6.1.2.1.1.6.0");
    $l = str_replace("STRING: ", "", $l);
    $l = str_replace("\"", "", $l);
    if (isset($a[4])):
        $c = explode(":", $a[4]);
        $x = $a[2] . " Tag(e), " . $c[0] . " Stunde(n) und " . $c[1] . " Minute(n)";
    else:
        $c = explode(":", $a[2]);
        $x = $c[0] . ":" . $c[1];
    endif;
    # Korrekturen
    if ($host == '172.16.33.57'): $l = 'Büro IT'; endif;
    $f = (isset($a[4])) ? $a[2] : $x;
    $y = str_replace("DESAA02-","",$y);
    $n = substr($y,0,3);

    $XMLS.= "
    <switch>
        <typ>$n</typ>
        <asset>$y</asset>
        <online>$f</online>
        <host>$host</host>
        <dauer>$x</dauer>
        <bezeichnung>$l</bezeichnung>
        <anzahl>$g</anzahl>
    </switch>";
    $j++; $g++;
endfor;
# Ausgabe XML Datei
$XML = "<?xml version='1.0' encoding='UTF-8'?>
<network>$XMLS
</network>";
header('Content-Type: application/xml; charset=utf-8');
echo $XML;