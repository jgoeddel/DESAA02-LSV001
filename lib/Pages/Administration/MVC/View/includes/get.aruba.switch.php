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
    $sw = array(31, 32, 33, 34, 35, 36, 38, 100, 55, 56, 110, 111, 16, 15, 57);
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
