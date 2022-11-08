<?php
/** (c) Joachim Göddel . RLMS */
$XMLS = '';
// Daten einlesen
for ($i = 1; $i < 5; $i++):
    $context = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
    $url = 'http://172.16.101.101:813' . $i . '/xml/alarms';
    $xml = file_get_contents($url, false, $context);
    $xml = simplexml_load_string($xml);
    foreach ($xml->alarm as $x):
        $y[] = $x;
    endforeach;
endfor;

$abc = array("", "a", "b", "c", "d");
for ($s = 1; $s < 5; $s++):
    for ($abc[$s] = 0; $abc[$s] < count($y); $abc[$s]++):
        $str[$s] = explode("'", $y[$abc[$s]]);
        $info[$s] = explode(".", $str[$s][1]);
        if ($info[$s][0] == 'Stationen'):
            //echo "<pre>"; print_r($str[$s]); echo "</pre>";
            $st = explode("_", $info[$s][1]);
            $info[$s][1] = str_replace("S", "", $st[1]);
            $info[$s][0] = $st[0];
            if (empty($str[$s][3])):
                $str[$s][3] = $info[$s][2] . " " . $info[$s][3];
            else:
                $str[$s][3] = str_replace("Station", "", $str[$s][3]);
                $str[$s][3] = str_replace("S" . $info[$s][1] . "", "", $str[$s][3]);
                $str[$s][3] = str_replace("_", "", $str[$s][3]);
                $str[$s][3] = preg_replace('/(\d+)/', '', $str[$s][3]);
            endif;
        //$color[$s] = 'text-warning';
        else:
            $info[$s][1] = str_replace("S", "", $info[$s][1]);
        endif;
        if ($info[$s][0] == 'SPS' . $s . ''):
            $station[$s][] = $s . "|" . $info[$s][1] . "|" . $str[$s][3];
        endif;
    endfor;
    if (!empty($station[$s])) {

        $x = array_unique($station[$s]);
        #var_dump($x);
        sort($x);
        foreach ($x as $row):
            $y = explode("|", $row);
            $XMLS .= "
            <station>
                <meldung>SPS: $y[0] - Station: $y[1] -  ". trim($y[2]) ."</meldung>
            </station>";
        endforeach;
    }
endfor;
$XML = "<?xml version='1.0' encoding='UTF-8'?>
<network>$XMLS</network>";
header('Content-Type: application/xml; charset=utf-8');
echo $XML;
