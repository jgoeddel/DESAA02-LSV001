<?php
/** (c) Joachim Göddel . RLMS */

?>
<table class="table table-bordered table-sm font-size-12">
    <?php
    include __DIR__ . "/inc.thead.netzwerk.php";
    ?>
    <tbody>
    <?php
    $j = 0;
    // Array mit Switches
    $sw = array(10, 20, 30, 61, 52, 50, 11, 25);
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
        // Farben
        if ($a[2] <= 15): $color = 'warning'; endif;
        if ($a[2] <= 5): $color = 'danger text-white'; endif;
        if ($a[2] > 15): $color = 'primary text-white'; endif;
        // Korrekturen
        if ($host == '172.16.33.57'): $l = 'Büro IT'; endif;
        ?>
        <tr onclick="window.open('http://<?= $host ?>');" class="pointer">
            <td><?= $y ?></td>
            <?php if (isset($a[4])): ?>
                <td class="bg__<?= $color ?> oswald text-center"><?= $a[2] ?></td>
            <?php else: ?>
                <td class="bg__<?= $color ?> oswald text-center"><?= $x ?></td>
            <?php endif; ?>
            <td><?= $host ?></td>
            <td><?= $l ?></td>
        </tr>
        <?php
        $j++;
    endfor;
    ?>
    </tbody>
</table>
<p class="font-size-10 italic">
    <b>STA001:</b> SWI008 + SWI009 &bull;
    <b>STA002:</b> SWI010 + SWI011 &bull;
    <b>STA003:</b> SWI012 + SWI013 &bull;
    <b>STA004:</b> SWI014 + SWI016<br>
    <b>STA005:</b> SWI017 + SWI018 &bull;
    <b>STA006:</b> SWI019 + SWI020 &bull;
    <b>STA007:</b> SWI027 + SWI028 &bull;
    <b>STA008:</b> SWI032 + SWI026
</p>
