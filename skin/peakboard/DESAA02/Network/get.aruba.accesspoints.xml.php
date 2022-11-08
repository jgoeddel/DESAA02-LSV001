<?php
/** (c) Joachim Göddel . RLMS */
$j = 0;
$sw = array(201,202,203,204,205,206,207,208,209,210,211,212,213,214,220,224,225,226,227,228,240,241,242,243,245,246,247,248,249,252,254);
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
        <anzahl>$j</anzahl>
    </switch>";
    $j++;
endfor;
# Ausgabe XML Datei
$XML = "<?xml version='1.0' encoding='UTF-8'?>
<network>$XMLS
</network>";
header('Content-Type: application/xml; charset=utf-8');
echo $XML;