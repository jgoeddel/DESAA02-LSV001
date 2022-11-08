<?php
/** (c) Joachim Göddel . RLMS */

$j = 0;
$sw = array(80);
$ca = count($sw);
for ($i = 0; $i < $ca; $i++):
    $host = "172.16.88." . $sw[$i];
    # Name
    $a = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.5.0");
    # Standort
    $b = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.6.0");
    # Anzahl Ports
    $c = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.3.1");
    $anzahlPorts = str_replace("INTEGER: ","",$c);
    $d = 1;
    $prt[$d] = snmp2_get($host, "public",".1.3.6.1.4.1.3181.10.3.3.10.1.3.$d");

    # Portstatus
    for($d = 1; $d <= $anzahlPorts; $d++){
        $prt[$d] = snmp2_get($host, "public",".1.3.6.1.4.1.3181.10.3.3.10.1.3.$d");
        $prt[$d] = str_replace("INTEGER: ","",$prt[$d]);
        $dspPort.= "<port$d>$prt[$d]</port$d>
        ";
    }
    /*
    # VLAN
    $vlan = snmp2_get($host, "public",".1.3.6.1.4.1.3181.10.3.4.10.1.3.1");
    $vlan2 = snmp2_get($host, "public",".1.3.6.1.4.1.3181.10.3.4.10.1.3.2");
    $vlan3 = snmp2_get($host, "public",".1.3.6.1.4.1.3181.10.3.4.10.1.3.3");
    $vlan4 = snmp2_get($host, "public",".1.3.6.1.4.1.3181.10.3.4.10.1.3.4");
    $vlan5 = snmp2_get($host, "public",".1.3.6.1.4.1.3181.10.3.4.10.1.3.5");
    $vlan6 = snmp2_get($host, "public",".1.3.6.1.4.1.3181.10.3.4.10.1.3.6");
    $vlan7 = snmp2_get($host, "public",".1.3.6.1.4.1.3181.10.3.4.10.1.3.7");
    $vlan8 = snmp2_get($host, "public",".1.3.6.1.4.1.3181.10.3.4.10.1.3.8");
    $vlan9 = snmp2_get($host, "public",".1.3.6.1.4.1.3181.10.3.4.10.1.3.9");
    $vlan10 = snmp2_get($host, "public",".1.3.6.1.4.1.3181.10.3.4.10.1.3.10");
    */
    $XMLS .= "
<switch>
    <name>$a</name>
    <standort>$b</standort>
    <ports>$anzahlPorts</ports>
    $dspPort
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
