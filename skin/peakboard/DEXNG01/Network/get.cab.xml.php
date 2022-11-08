<?php
/** (c) Joachim Göddel . RLMS */

/*
$j = 0;
$sw = array(80);
$ca = count($sw);
for ($i = 0; $i < $ca; $i++):
    $host = "172.16.90." . $sw[$i];
    # Name
    $a = snmp2_get($host, "public", "iso.3.6.1.2.1.1.9.1.3.3");

    $XMLS .= "
<switch>
    <name>$a</name>
    <ipaddress>$host</ipaddress>
</switch>";
    $j++;
endfor;

# Ausgabe XML Datei
$XML = "<?xml version='1.0' encoding='UTF-8'?>
<network>$XMLS
</network>";
header('Content-Type: application/xml; charset=utf-8');
echo $XML;
*/
$a = snmp2_real_walk("127.16.33.121", "public", "");
echo "<pre>"; print_r($a); echo "</pre>";
foreach ($a as $val) {
    echo "$val\n";
}