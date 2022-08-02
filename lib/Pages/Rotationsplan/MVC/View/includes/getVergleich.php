<?php
/** (c) Joachim Göddel . RLMS */

use App\Pages\Rotationsplan\MVC\View\functions\Functions;
use App\Pages\Rotationsplan\RotationsplanDatabase;

# Datenbank
$db = new Functions();

# Anzahl der zu vergleichenden Mitarbeiter
$anz = count($u);
# Wenn kein Datum gesetzt ist die Werte setzen
if(empty($start)) $start = RotationsplanDatabase::getPlanDates('ASC');
if(empty($ende)) $ende = RotationsplanDatabase::getPlanDates('DESC');
?>
<table class="table table-bordered table-striped table-sm font-size-12">
    <thead class="bg__white oswald text-uppercase">
    <tr>
        <th class="font-weight-300" rowspan="2">Mitarbeiter</th>
        <th class="text-center font-weight-300" rowspan="2">T</th>
        <th class="text-center font-weight-300" rowspan="2">E</th>
        <?php
        foreach ($q as $c):
            ?>
            <th class="text-center font-weight-300"><?= $c->station ?></th>
        <?php
        endforeach;
        ?>
    </tr>
    <tr>
        <?php
        foreach ($q as $c):
            ($c->mitarbeiter == 1) ? $iu = 'fa-user' : $iu = 'fa-users';
            ?>
            <th class="text-center oswald font-weight-300"><i class="fa <?=$iu?> me-2 font-size-10"></i><?= $c->mitarbeiter ?></th>
        <?php
        endforeach;
        ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $color = array('#00469b', '#fabb00', '#007cb5');
    for($i = 0; $i < $anz; $i++):
        $es = $db->getAnzahlEinsatzGesamt($u[$i]->id);
        $tg = $db->getSummeAnwesend($u[$i]->id);
    ?>
    <tr>
        <td><i class="fa fa-square-full me-2" style="color: <?=$color[$i]?>"></i><?= $u[$i]->name ?>, <?= $u[$i]->vorname ?></td>
        <td class="text-center"><?=$tg?></td>
        <td class="text-center"><?=$es?></td>
        <?php
        foreach($q as $c):
            $as = $db->getAnzahlEinsatzZeitraum($u[$i]->id, $c->id, $start, $ende);
            $e = (!empty($as)) ? number_format(($as/$es)*100,2, ',', '.').'%' : '0';
            $ql = $db->getQualiMaStation($u[$i]->id,$c->id);
            if($ql !== true) $e = '';
        ?>
        <td class="text-center oswald"><?= $e ?></td>
        <?php
        endforeach;
        ?>
    </tr>
    <?php
    endfor;
    ?>
    </tbody>
</table>
