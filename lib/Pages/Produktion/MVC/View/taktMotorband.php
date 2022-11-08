<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\Produktion\ProduktionDatabase;

$_SESSION['seite']['id'] = 53;
$_SESSION['seite']['name'] = 'taktMotorband';
$subid = 0;
$n_suche = '';
$dspKalender = true;
$dspTag = true;

# WRK Datum
$_SESSION['wrk']['datum'] = $_SESSION['wrk']['jahr']."-".$_SESSION['wrk']['monat']."-".$_SESSION['wrk']['tag'];

# Sommer- oder Winterzeit
$n = new DateTime($_SESSION['wrk']['datum'], new DateTimeZone('Europe/Berlin'));
$z = $n->format('I')*1;

# Datum umschreiben
try {
    $wrkdatum = Functions::germanDateFormat($_SESSION['wrk']['datum']);
} catch (Exception $e) {
}
# Produktionszahlen
$anfangFrueh = 6+$z;
$endeFrueh = 13+$z;
$anfangMittag = 14+$z;
$endeMittag = 22+$z;
$fabFrueh = ProduktionDatabase::getAbrufeFabStunde($_SESSION['wrk']['datum'],''.$anfangFrueh.':00:00', ''.$endeFrueh.':59:59')*3;
$fabMittag = ProduktionDatabase::getAbrufeFabStunde($_SESSION['wrk']['datum'],''.$anfangMittag.':00:00', ''.$endeMittag.':59:59')*3;
$ptsFrueh = ProduktionDatabase::getSummeSchicht($_SESSION['wrk']['datum'],$anfangFrueh, $endeFrueh);
$ptsMittag = ProduktionDatabase::getSummeSchicht($_SESSION['wrk']['datum'],$anfangMittag, $endeMittag);
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
<body class="d-flex flex-column h-100 ford" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation('', 'index', '', 1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE &bull; $wrkdatum", "{$_SESSION['text']['h_taktzeit']}");
?>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid mt-3">
        <div class="row border__bottom--dotted-gray pb-1 mb-4">
            <div class="col-10">
                <h3 class="text-center">Frühschicht </h3>
            </div>
            <div class="col-1">
                <h3 class="text-end font-weight-300"><span class="text-black badge badge-light">FAB:  <?=$fabFrueh?></span></h3>
            </div>
            <div class="col-1">
                <h3 class="text-end font-weight-300"><span class="text-black badge badge-light">PTS:  <?=$ptsFrueh?></span></h3>
            </div>
        </div>
        <div class="row">
            <?php
            for($i = 6; $i < 14; $i++):
                echo '<div class="col">';
                echo '<div class="px-2">';
                echo '<h5 class="oswald text-center border__bottom--dotted-gray mb-2 pb-2">'.str_pad($i,2,0,STR_PAD_LEFT).':00  bis '.str_pad($i,2,0,STR_PAD_LEFT).':59 <i class="fa fa-caret-down text-muted ps-3 pointer" onclick="$(\'#d'.$i.'\').toggle(500)"></i></h5>';
                $vorgabe = 860;
                $maxFzg = $vorgabe/7/2;
                $minuten = 60;
                switch($i){
                    case 8:
                        $minuten = 40;
                        break;
                    case 11:
                        $minuten = 30;
                        break;
                    case 12:
                        $minuten = 50;
                        break;
                }
                $max[$i] = round($maxFzg/60*$minuten);
                $geplant = array_sum($max);
                # Sommerzeit
                $sz = Functions::sommerzeit($_SESSION['parameter']['jahr'],$_SESSION['wrk']['datum']);
                $az = (Functions::sommerzeit($_SESSION['parameter']['jahr'],$_SESSION['wrk']['datum']) == 1) ? $i+1 : $i;
                $eingesteuert = ProduktionDatabase::countCallOffs($_SESSION['wrk']['datum'],''.$i.':00:00',''.$i.':59:59');
                $abgerufen = ProduktionDatabase::getAbrufeFabStunde($_SESSION['wrk']['datum'],''.$az.':00:00',''.$az.':59:59')*3;
                $pts = ProduktionDatabase::getSummeStunde($_SESSION['wrk']['datum'],''.$az.'');
                $pr = $abgerufen/$max[$i]*100;
                $prz[$i] = number_format($pr,2,',','.');
                $warning = ($pr > 100) ? '<i class="fa fa-exclamation-triangle text-warning"></i>' : '';
                $caret = ($pts >= $abgerufen) ? '<i class="fa fa-caret-up text-success ps-2"></i>' : '<i class="fa fa-caret-down text-danger ps-2"></i>';
                $caret2 = ($max[$i] <= $eingesteuert) ? '<i class="fa fa-exclamation-triangle text-warning ps-2"></i>' : '';
                $zahl = ($max[$i] <= $eingesteuert) ? "(".$eingesteuert-$max[$i].")" : '';
                ?>
                <div class="container font-size-11">
                    <div class="row pb-2 mb-2 border__bottom--dotted-gray">
                        <div class="col-9">Produktionszeit (M):</div>
                        <div class="col-3 text-end"><?= $minuten ?></div>
                    </div>
                    <div class="row pb-2 mb-2 border__bottom--dotted-gray">
                        <div class="col-9">Maximal (FZG):</div>
                        <div class="col-3 text-end"><?= $max[$i] ?></div>
                    </div>
                    <div class="row pb-2 border__bottom--solid-gray">
                        <div class="col-9">Abrufe (FAB):</div>
                        <div class="col-3 text-end"><?= $abgerufen ?></div>
                    </div>
                    <div class="row py-2 mb-2 border__bottom--solid-gray bg__blue-gray--6">
                        <div class="col-9">Produktionsauslastung: <?= $warning ?></div>
                        <div class="col-3 text-end"><?= $prz[$i] ?>%</div>
                    </div>
                    <div class="row pb-2 mb-2 border__bottom--dotted-gray">
                        <div class="col-9">Eingesteuert: <?= $caret2 ?> <?= $zahl ?></div>
                        <div class="col-3 text-end"><?= $eingesteuert ?></div>
                    </div>
                    <div class="row pb-2 mb-2 border__bottom--dotted-gray">
                        <div class="col-9">PTS (Overzicht Variants): <?= $caret ?></div>
                        <div class="col-3 text-end"><?= $pts ?></div>
                    </div>
                </div>
                <?php
                # Details abrufen
                $a = ProduktionDatabase::getCallOffs($_SESSION['wrk']['datum'],''.$i.':00:00',''.$i.':59:59');
                $aus = "00:00:00";
                $cnt = 1;
                echo '<div class="dspnone" id="d'.$i.'">';
                foreach($a AS $row):
                    $zeit[$row->sequence] = $row->lastchange;
                    $vgl = ($row->sequence > 1) ? $row->sequence-1 : $row->sequence;
                    if($row->sequence > 1):
                        $aktuell = new DateTime(''.$row->lastchange.'');
                        $letzte = new DateTime(''. $zeit[$vgl].'');
                        $diff = $letzte->diff($aktuell);
                        $clr = ($diff->s < 58 && $diff->i == 0) ? 'danger' : 'success';
                        $aus = str_pad($diff->h,2,0,STR_PAD_LEFT).":".str_pad($diff->i,2,0,STR_PAD_LEFT).":".str_pad($diff->s,2,0,STR_PAD_LEFT);
                    endif;
                    ?>
                <div class="row border__bottom--dotted-gray mb-2 pb-2">
                    <div class="col-2"><small><?= $cnt ?>.</small></div>
                    <div class="col-2 oswald"><?= $row->sequence ?></div>
                    <div class="col"><small><?= $row->tag ?> <?= $row->zeit ?></small><br><small class="text-<?= $clr ?>"><?= $aus ?></small></div>
                </div>
                <?php
                $cnt++;
                endforeach;
                echo "</div>";
                echo "</div>";
                echo "</div>";
            endfor;
            ?>
        </div>
        <!--
        <div class="bg__blue-gray--6 font-size-11 italic px-2 py-1">
            Maximal möglich: <b>xx</b> &bull; Eingesteuert: <b>xx</b>
        </div>
        -->

        <div class="row border__bottom--dotted-gray pb-1 my-4">
            <div class="col-10">
                <h3 class="text-center">Mittagschicht</h3>
            </div>
            <div class="col-1">
                <h3 class="text-end font-weight-300"><span class="text-black badge badge-light">FAB:  <?=$fabMittag ?></span></h3>
            </div>
            <div class="col-1">
                <h3 class="text-end font-weight-300"><span class="text-black badge badge-light">PTS:  <?=$ptsMittag ?></span></h3>
            </div>
        </div>
        <div class="row mb-5">
            <?php
            for($i = 14; $i < 22; $i++):
                echo '<div class="col">';
                echo '<div class="px-2">';
                echo '<h5 class="oswald text-center border__bottom--dotted-gray mb-2 pb-2">'.str_pad($i,2,0,STR_PAD_LEFT).':00  bis '.str_pad($i,2,0,STR_PAD_LEFT).':59 <i class="fa fa-caret-down text-muted ps-3 pointer" onclick="$(\'#d'.$i.'\').toggle(500)"></i></h5>';
                $vorgabe = 860;
                $maxFzg = $vorgabe/7/2;
                $minuten = 60;
                switch($i){
                    case 16:
                        $minuten = 40;
                        break;
                    case 18:
                    case 19:
                        $minuten = 45;
                        break;
                    case 20:
                        $minuten = 50;
                        break;
                }
                $max[$i] = round($maxFzg/60*$minuten);
                $geplant = array_sum($max);
                # Sommerzeit
                $sz = Functions::sommerzeit($_SESSION['parameter']['jahr'],$_SESSION['wrk']['datum']);
                $az = (Functions::sommerzeit($_SESSION['parameter']['jahr'],$_SESSION['wrk']['datum']) == 1) ? $i+1 : $i;
                $eingesteuert = ProduktionDatabase::countCallOffs($_SESSION['wrk']['datum'],''.$i.':00:00',''.$i.':59:59');
                $abgerufen = ProduktionDatabase::getAbrufeFabStunde($_SESSION['wrk']['datum'],''.$az.':00:00',''.$az.':59:59')*3;
                $pts = ProduktionDatabase::getSummeStunde($_SESSION['wrk']['datum'],''.$az.'');
                $pr = $abgerufen/$max[$i]*100;
                $prz[$i] = number_format($pr,2,',','.');
                $warning = ($pr > 100) ? '<i class="fa fa-exclamation-triangle text-warning"></i>' : '';
                $caret = ($pts >= $abgerufen) ? '<i class="fa fa-caret-up text-success ps-2"></i>' : '<i class="fa fa-caret-down text-danger ps-2"></i>';
                $caret2 = ($max[$i] <= $eingesteuert) ? '<i class="fa fa-exclamation-triangle text-warning ps-2"></i>' : '';
                $zahl = ($max[$i] <= $eingesteuert) ? "(".$eingesteuert-$max[$i].")" : '';
                ?>
                <div class="container font-size-11">
                    <div class="row pb-2 mb-2 border__bottom--dotted-gray">
                        <div class="col-9">Produktionszeit (M):</div>
                        <div class="col-3 text-end"><?= $minuten ?></div>
                    </div>
                    <div class="row pb-2 mb-2 border__bottom--dotted-gray">
                        <div class="col-9">Maximal (FZG):</div>
                        <div class="col-3 text-end"><?= $max[$i] ?></div>
                    </div>
                    <div class="row pb-2 border__bottom--solid-gray">
                        <div class="col-9">Abrufe (FAB):</div>
                        <div class="col-3 text-end"><?= $abgerufen ?></div>
                    </div>
                    <div class="row py-2 mb-2 border__bottom--solid-gray bg__blue-gray--6">
                        <div class="col-9">Produktionsauslastung: <?= $warning ?></div>
                        <div class="col-3 text-end"><?= $prz[$i] ?>%</div>
                    </div>
                    <div class="row pb-2 mb-2 border__bottom--dotted-gray">
                        <div class="col-9">Eingesteuert: <?= $caret2 ?> <?= $zahl ?></div>
                        <div class="col-3 text-end"><?= $eingesteuert ?></div>
                    </div>
                    <div class="row pb-2 mb-2 border__bottom--dotted-gray">
                        <div class="col-9">PTS (Overzicht Variants): <?= $caret ?></div>
                        <div class="col-3 text-end"><?= $pts ?></div>
                    </div>
                </div>
                <?php
                # Details abrufen
                $a = ProduktionDatabase::getCallOffs($_SESSION['wrk']['datum'],''.$i.':00:00',''.$i.':59:59');
                $aus = "00:00:00";
                $cnt = 1;
                echo '<div class="dspnone" id="d'.$i.'">';
                foreach($a AS $row):
                    $zeit[$row->sequence] = $row->lastchange;
                    $vgl = ($row->sequence > 1) ? $row->sequence-1 : $row->sequence;
                    if($row->sequence > 1):
                        $aktuell = new DateTime(''.$row->lastchange.'');
                        $letzte = new DateTime(''. $zeit[$vgl].'');
                        $diff = $letzte->diff($aktuell);
                        $clr = ($diff->s < 58 && $diff->i == 0) ? 'danger' : 'success';
                        $aus = str_pad($diff->h,2,0,STR_PAD_LEFT).":".str_pad($diff->i,2,0,STR_PAD_LEFT).":".str_pad($diff->s,2,0,STR_PAD_LEFT);
                    endif;
                    ?>
                    <div class="row border__bottom--dotted-gray mb-2 pb-2">
                        <div class="col-2"><small><?= $cnt ?>.</small></div>
                        <div class="col-2 oswald"><?= $row->sequence ?></div>
                        <div class="col"><small><?= $row->tag ?> <?= $row->zeit ?></small><br><small class="text-<?= $clr ?>"><?= $aus ?></small></div>
                    </div>
                    <?php
                    $cnt++;
                endforeach;
                echo "</div>";
                echo "</div>";
                echo "</div>";
            endfor;
            ?>
        </div>

    </div><!-- container-fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>/skin/plugins/other/progressbar.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
    });
</script>
</body>
</html>