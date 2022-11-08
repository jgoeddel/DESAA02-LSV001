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
    $sw = array(170,171,172,173,174,175,176,177);
    $ca = count($sw);
    for ($i = 0; $i < $ca; $i++):
        $host = "172.16.33." . $sw[$i];
        $x = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.11.0");
        $a = explode(" ", $x);
        $b = count($a);
        if($b > 4) {
            $c = explode(":", $a[4]);
            $x = $a[2] . " Tag(e), " . $c[0] . " Stunde(n) und " . $c[1] . " Minute(n)";
            $realcolor = '';
        } else {
            $c = explode(":", $a[2]);
            $x = $c[0] . " Stunde(n) und " . $c[1] . " Minute(n)";
            $a[2] = $c[0]."<small class='font-size-12'>STD</small>";
            $realcolor = 'danger text-white';
        }
        $y = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.5.0");
        $y = str_replace("STRING: ", "", $y);
        $y = str_replace("\"", "", $y);
        $l = snmp2_get($host, "public", ".1.3.6.1.4.1.3181.10.3.1.6.0");
        $l = str_replace("STRING: ", "", $l);
        $l = str_replace("\"", "", $l);
        $l = utf8_encode($l);
        // Farben
        if($a[2] <= 15): $color = 'warning'; endif;
        if($a[2] <= 5): $color = 'danger text-white'; endif;
        if($a[2] > 15): $color = 'primary text-white'; endif;
        $color = ($realcolor != '') ? $realcolor : $color;
        ?>
        <tr onclick="window.open('http://<?= $host ?>');" class="pointer">
            <td class="col-2"><?= $y ?></td>
            <?php if (isset($a[4])): ?>
                <td class="bg__<?= $color ?> oswald text-center col-2"><?= $a[2] ?></td>
            <?php else: ?>
                <td class="bg__<?= $color ?> oswald text-center col-2"><?= $x ?></td>
            <?php endif; ?>
            <td class="col-2"><?= $host ?></td>
            <td><?= $l ?></td>
        </tr>
        <?php
        $j++;
    endfor;
    ?>
    </tbody>
</table>