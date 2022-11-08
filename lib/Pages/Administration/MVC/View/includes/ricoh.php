<?php
/** (c) Joachim Göddel . RLMS */


        $host = "172.27.49.104";
        $panel = snmpget("$host", "public", ".1.3.6.1.4.1.10642.200.19.27.4.0");

        $XMLS.= "
<switch>
    <dauer6>$display</dauer6>
    <dauer6>$panel</dauer6>
</switch>";
$XML = "<?xml version='1.0' encoding='UTF-8'?>
<network>$XMLS</network>";
header('Content-Type: application/xml; charset=utf-8');
echo $XML;