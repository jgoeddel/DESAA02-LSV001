<?php
# Seitenparameter
use App\App\Container;
use App\Formular\Formular;
use App\Functions\Functions;
use App\Pages\Rotationsplan\RotationsplanDatabase;

$_SESSION['seite']['id'] = $ebene;
$subid = 30;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung($ebene);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

# Keine Berechtigung, dann zurück auf die Startseite
if (!isset($seitelesen) || $seitelesen != 1):
    header(header: 'Location: /');
endif;

# Möglichkeit zum Ändern setzen
$dspedit = ($seiteschreiben == 1) ? '' : 'dspnone';

# Datenbank
$db = new \App\Pages\Rotationsplan\MVC\View\functions\Functions();

# Logfile schreiben
#Functions::logfile('Rotationsplan Auswertung', '', '', 'Seite aufgerufen');
$wrkabteilung = RotationsplanDatabase::showWrkParameter();
?>
<!doctype html>
<html lang="de" class="h-100">
<head>
    <?php
    # Basis-Head Elemente einbinden
    Functions::getHeadBase();
    ?>
    <title><?= $_SESSION['page']['version'] ?></title>
</head>
<body class="d-flex flex-column h-100 rotationsplan" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation($dspedit, 'index', '', 1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("RHENUS LMS GmbH &bull; $wrkabteilung", $_SESSION['text']['h_rotationsplan']);
?>
<main class="bg__white w-100">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <h3 class="oswald font-weight-100 py-3 border__bottom--dotted-gray">Auswertung</h3>

        <table class="table table-bordered table-hover table-sm dTable">
            <thead class="bg-light-lines-primary text-white font-weight-300 font-size-10">
            <tr>
                <th class="text-end">ID</th>
                <th>Mitarbeiter</th>
                <th class="text-center">Q</th>
                <th class="text-center">A</th>
                <th class="text-center">M</th>
                <th class="text-center">E</th>
                <th class="text-center">D</th>
                <?php
                foreach($stn AS $a):
                    $lght = ($a->ergo == 'danger') ? 'bg-light-lines-danger' : '';
                    ?>
                    <th class="text-center <?= $lght ?>"><?=$a->station?></th>
                <?php
                endforeach;
                ?>
            </tr>
            </thead>
            <tbody class="font-size-12">
            <?php
            foreach($ma AS $b):
                ?>
                <tr>
                    <td class="text-end"><?=$b->id?></td>
                    <td class="pointer" onclick="top.location.href='personal.detail.php?id=<?=$id?>'"><?=$b->name?>, <?=$b->vorname?></td>
                    <?php
                    $quali = $db->getAnzahlQualiMa($b->id);
                    $eins = $db->getAnzahlEinsatzGesamt($b->id);
                    $da = $db->getSummeAnwesend($b->id);
                    $m = $da * 3;
                    $d = $m - $eins;
                    ($d == 0) ? $ad = "<i class='fa fa-check-circle text-success'></i>" : $ad = $d;
                    ?>
                    <td class="text-center"><?=$quali?></td>
                    <td class="text-center"><?=$da?></td>
                    <td class="text-center"><?=$m?></td>
                    <td class="text-center"><?=$eins?></td>
                    <td class="text-center"><?=$ad?></td>
                    <?php
                    foreach($stn AS $a):
                        $c = $db->getAnzahlEinsatz($b->id,$a->id);
                        if($c == 0) $c = '';
                        $ql = $db->getQualiMaStation($b->id,$a->id);
                        ($ql === true) ? $bgc = 'bg-light-lines-success-2' : $bgc = '';
                        ?>
                        <td class="text-center <?=$bgc?>"><?=$c?></td>
                    <?php
                    endforeach;
                    ?>
                </tr>
            <?php
            endforeach;
            ?>
            </tbody>
        </table>
        <p class="font-size-11">Q = Anzahl Qualifikationen &bull; A = Wie oft war der Mitarbeiter anwesend &bull; M = Maximale Anzahl an möglichen Einsaätzen &bull; E = Tatsächliche Anzahl an Einsätzen &bull; D = Differenz zwischen tatsächlien und möglichen Einsätzen</p>
    </div><!-- fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<!-- Zusätzliche Javascript Dateien -->
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>skin/js/base.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>skin/plugins/other/isotope.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/action.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/view.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/submit.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

    })

</script>
</body>
</html>