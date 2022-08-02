<?php
/** (c) Joachim Göddel . RLMS */
use App\Pages\Rotationsplan\RotationsplanDatabase;

# Datenbank
$db = new \App\Pages\Rotationsplan\MVC\View\functions\Functions();
# Springer
$springer = $db->getSpringerID();
# Datum
$datum = $_SESSION['wrk']['datum'];

?>
<table class="table table-bordered table-sm table-hover bg-white font-size-12">
    <thead class="bg__blue-gray--50">
    <tr>
        <th colspan="2">Mitarbeiter</th>
        <th class="text-center">E</th>
        <th class="text-center">Q</th>
        <th class="text-center">1</th>
        <th class="text-center">2</th>
        <th class="text-center">3</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($ma as $anwesend):
        # Mitarbeiter anwesend
        $mitarbeiterAnwesend = ($db->getAnwesendMa($anwesend->id) === true) ? '<i class="fa fa-user-circle fa-fw text-success float-end ms-1 pointer" onclick="showTR(\'u'.$anwesend->id.'\');"></i>' : '';
        # Mitarbeiter abwesend
        $mitarbeiterAbwesend = ($db->getAbwesend($anwesend->id) === true) ? '<i class="fa fa-user-circle text-danger fa-fw float-end ms-1"></i>' : '';
        # Mitarbeiter im Training
        $mitarbeiterTraining = ($db->getTraining($anwesend->id) > 0) ? '<i class="fa fa-user-circle fa-fw text-info float-end ms-1"></i>' : '';
        # Mitarbeiter mit Handicap
        $mitarbeiterHandicap = ($db->getHandicap($anwesend->id) > 0) ? '<i class="fa fa-user-circle fa-fw text-warning float-end ms-1"></i>' : '';
        # Anzahl Qualifikationen
        $mitarbeiterQualifikation = $db->getAnzahlQualiMa($anwesend->id);
        # Anzahl Einsätze
        $mitarbeiterEinsaetze = $db->getAnzahlEinsatzGesamt($anwesend->id);
        # Klasse Mitarbeiter
        $classMa = ($anwesend->id == $uid) ? 'badge badge-success font-size-12 font-weight-300' : 'pointer';
        # Station möglich ?
        $stn = $db->getQualiMaStation($anwesend->id, $sid);
        $icon = ($stn === true) ? '<i class="fa fa-caret-right text-warning ps-2"></i>' : '';
        # Rote Station ?
        $redBadge = ($db->getMaRedStation($anwesend->id) > 0) ? "<span class='badge badge-danger'>$anwesend->id</span>" : "$anwesend->id";
        # MA Training Stationen
        $trStn = $db->getStnMaTraining($anwesend->id);
        # MA Handicap Stationen
        $hcStn = $db->getStnMaHandicap($anwesend->id);
        ?>
        <tr>
            <td class="text-end"><?= $redBadge ?></td>
            <td class="">
                <span class="<?= $classMa ?>" onclick="showPossStation(<?= $zschiene[0] ?>, <?= $anwesend->id ?>, '<?= $_SESSION['wrk']['datum'] ?>')">
                <?php
                # Name des Mitarbeiters
                echo RotationsplanDatabase::getNameMAFormat($anwesend->id);
                echo $icon;
                echo "<span id='spin$anwesend->id'></span>";
                echo "</span>";
                echo $mitarbeiterAnwesend;
                echo $mitarbeiterTraining;
                echo $mitarbeiterHandicap;
                echo $mitarbeiterAbwesend;
                ?>
            </td>
            <td class="text-center">
                <?= $mitarbeiterEinsaetze ?>
            </td>
            <td class="text-center">
                <?= $mitarbeiterQualifikation ?>
            </td>
            <?php
            for ($i = 0; $i < 3; $i++):
                $z = $i+1;
                $db->maZeitschieneAktiv($anwesend->id, $zschiene[$i],$springer,$z);
            endfor;
            ?>
        </tr>
        <tr id="u<?= $anwesend->id ?>" class="dspnone w-100">
            <td colspan="4">
                <?php
                if($trStn){
                    foreach($trStn AS $sn){
                        RotationsplanDatabase::badgeStation($sn->sid,'info');
                    }
                }
                if($hcStn){
                    foreach($hcStn AS $sn){
                        RotationsplanDatabase::badgeStation($sn->sid,'warning');
                    }
                }
                ?>
            </td>
            <?php
            for ($i = 0; $i < 3; $i++):
                $z = $i+1;
                $db->maZeitschieneAnwesend($anwesend->id,$zschiene[$i],$z);
            endfor;
            ?>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>
<p class="font-size-10 italic">E = Anzahl der Einsätze heute (alle Zeitschienen) | Q = Anzahl der
    Qualifikationen</p>
