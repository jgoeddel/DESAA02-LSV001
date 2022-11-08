<?php
/** (c) Joachim Göddel . RLMS */

$prt = array("172.27.49.104");
for ($i = 0; $i < count($prt); $i++):
    $host = $prt[$i];
    $panel = snmpget("$host", "public", ".1.3.6.1.4.1.10642.200.19.27.4.0");
    $panel = str_replace("STRING: ", "", $panel);
    $panel = str_replace("\"", "", $panel);

    $online = snmpget("$host", "public", ".1.3.6.1.4.1.10642.200.19.20.0");
    $online = str_replace("STRING: ", "", $online);
    $online = str_replace("\"", "", $online);

    $job = snmpget("$host", "public", ".1.3.6.1.4.1.10642.30.3.1.1.1");
    $job = str_replace("STRING: ", "", $job);
    $job = str_replace("\"", "", $job);

    $jobzeit = snmpget("$host", "public", ".1.3.6.1.4.1.10642.30.3.1.2.1");
    $jobzeit = str_replace("STRING: ", "", $jobzeit);
    $jobzeit = str_replace("\"", "", $jobzeit);
    $jobzeit = str_replace(" hours ",":", $jobzeit);
    $jobzeit = str_replace(" mins ",":", $jobzeit);
    $jobzeit = str_replace(" secs","", $jobzeit);
    $j = explode(":", $jobzeit);
    $j1 = str_pad($j[0],2,0,STR_PAD_LEFT);
    $j2 = str_pad($j[1],2,0,STR_PAD_LEFT);
    $j3 = str_pad($j[2],2,0,STR_PAD_LEFT);
    $jobzeit = $j1.":".$j2.":".$j3;

    $drucker = snmpget("$host", "public", ".1.3.6.1.4.1.10642.1.1.0");
    $drucker = str_replace("STRING: ", "", $drucker);
    $drucker = str_replace("\"", "", $drucker);

    $str = snmpget("$host", "public", ".1.3.6.1.4.1.10642.200.12.2.0");
    $str = pack("H*", $str);

    $panel = str_replace("<FRONT-PANEL><LCD>","", $panel);
    $panel = str_replace("</FRONT-PANEL>","", $panel);
    $panel = str_replace("</LCD>","", $panel);
    $panel = str_replace("<LEDS>","", $panel);
    $panel = str_replace("</LEDS>","", $panel);

    $XMLS .= "
    <printer>
        <drucker>$drucker</drucker>
        <host>$host</host>
        <online>$online</online>
        <job>$job</job>
        <jobzeit>$jobzeit</jobzeit>
        $panel
    </printer>";
endfor;

$XML = "<?xml version='1.0' encoding='UTF-8'?>
<network>$XMLS</network>";
header('Content-Type: application/xml; charset=utf-8');
echo $XML;