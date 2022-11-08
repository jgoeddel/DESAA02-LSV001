<?php
/** (c) Joachim Göddel . RLMS */


    $j = 0;
    $sw = array(120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 133);
    $ca = count($sw);
    for ($x = 0; $x < $ca; $x++):
        $host = "172.16.33." . $sw[$x];

        $artikel = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.1.0");
        $artikel = str_replace("STRING: ","", $artikel);
        $artikel = str_replace("\"","", $artikel);
        $snr = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.2.0");
        $snr = str_replace("STRING: ","", $snr);
        $snr = str_replace("\"","", $snr);
        $name = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.4.0");
        $name = str_replace("STRING: ","", $name);
        $name = str_replace("\"","", $name);
        $deviceName = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.5.0");
        $deviceName = str_replace("STRING: ","", $deviceName);
        $deviceName = str_replace("\"","", $deviceName);
        $deviceLocation = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.6.0");
        $deviceLocation = str_replace("STRING: ","", $deviceLocation);
        $deviceLocation = str_replace("\"","", $deviceLocation);
        $hex = strpos($deviceLocation, "Hex-53 ");
        if($hex !== false):
            $deviceLocation = str_replace("Hex-53 ","",$deviceLocation);
            $deviceLocation = "-";
        endif;
        $ring = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.8.0");
        $ring = str_replace("STRING: ","", $ring);
        $ring = str_replace("\"","", $ring);
        $zeit = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.11.0");
        $tm = explode("(", $zeit);
        $tmz = explode(")", $tm[1]);
        $ringStatus = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.7.10.1.6.1");
        $ringStatus = str_replace("INTEGER: ","", $ringStatus);
        $ringStatus = str_replace("\"","", $ringStatus);
        $temp = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.9.0");
        $temp = str_replace("INTEGER: ","", $temp);
        $temp = str_replace("\"","", $temp);
        $ports = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.3.1.0");
        $ports = str_replace("INTEGER: ","", $ports);
        $ports = str_replace("\"","", $ports);
        for($i = 1; $i <= $ports; $i++) {
            $sts = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.3.10.1.3.$i");
            $sts = str_replace("INTEGER: ", "", $sts);
            $sts = str_replace("\"", "", $sts);
            $speed = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.3.10.1.4.$i");
            $speed = str_replace("INTEGER: ", "", $speed);
            $speed = str_replace("\"", "", $speed);
            $speed = match ($speed) {
                '3' => '1000',
                '2' => '100',
                '1' => '10',
                default => '-'
            };
            $port .= "
                <status$i>$sts</status$i>
                <speed$i>$speed</speed$i>
            ";
        }
        $XMLS.= "
        <switch>
            <artikel>$artikel</artikel>
            <snr>$snr</snr>
            <zeit>$tmz[0]</zeit>
            <name>$name</name>
            <ip>$sw[$x]</ip>
            <deviceName>$deviceName</deviceName>
            <deviceLocation>$deviceLocation</deviceLocation>
            <ring>$ring</ring>
            <ringStatus>$ringStatus</ringStatus>
            <temp>$temp</temp>
            <ports>$ports</ports>
            $port
        </switch>";

    endfor;
$XML = "<?xml version='1.0' encoding='UTF-8'?>
<network>$XMLS</network>";
header('Content-Type: application/xml; charset=utf-8');
echo $XML;