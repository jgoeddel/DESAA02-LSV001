<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\Produktion\ProduktionDatabase;

$_SESSION['seite']['id'] = 49;
$_SESSION['seite']['name'] = 'fabMotorband';
$subid = 0;
$n_suche = '';
$dspKalender = true;
$dspTag = true;

# WRK Datum
$_SESSION['wrk']['datum'] = $_SESSION['wrk']['jahr']."-".$_SESSION['wrk']['monat']."-".$_SESSION['wrk']['tag'];

# Sommer- oder Winterzeit
$n = new DateTime($_SESSION['wrk']['datum'], new DateTimeZone('Europe/Berlin'));
$z = $n->format('I')*1;

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung($_SESSION['seite']['id']);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

# Keine Berechtigung, dann zurück auf die Startseite
if (!isset($seitelesen) || $seitelesen != 1):
    header(header: 'Location: /');
endif;
# Datum umschreiben
try {
    $wrkdatum = Functions::germanDateFormat($_SESSION['wrk']['datum']);
} catch (Exception $e) {
}
// Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';
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
    Functions::dspNavigation($dspedit, 'index', '', 1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE &bull; $wrkdatum", "{$_SESSION['text']['h_fab']}");
?>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="table-responsive">
        <table class="table table-bordered table-sm font-weight-100 font-size-12">
            <thead class="bg__blue-gray--6">
            <tr class="class="">
            <th class="text-end"><?= $_SESSION['text']['h_zeit'] ?></th>
            <?php for ($i = 6; $i < 22; $i++):
                ?>
                <th class="text-center"><?php echo substr($_SESSION['parameter']['anfangszeit'][$i], 0, 5); ?></th>
            <?php endfor; ?>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="text-end"><?= $_SESSION['text']['h_abrufe'] ?></td>
                <?php for ($i = 6; $i < 22; $i++):
                    # Sommerzeit berücksichtigen
                    $x = $i + $z;
                    ?>
                    <td class="text-center">
                        <?= ProduktionDatabase::getAbrufeFabStunde('' . $_SESSION['wrk']['datum'] . '', '' . $_SESSION['parameter']['anfangszeit'][$x] . '', '' . $_SESSION['parameter']['endzeit'][$x] . '') ?>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="text-end"><?= $_SESSION['text']['h_fab'] ?></td>
                <?php for ($i = 6; $i < 22; $i++):
                    # Sommerzeit berücksichtigen
                    $x = $i + $z;
                    ?>
                    <td class="text-center">
                        <?= ProduktionDatabase::getAbrufeFabStunde('' . $_SESSION['wrk']['datum'] . '', '' . $_SESSION['parameter']['anfangszeit'][$x] . '', '' . $_SESSION['parameter']['endzeit'][$x] . '')*3 ?>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="text-end"><?= $_SESSION['text']['h_taktzeit'] ?></td>
                <?php for ($i = 6; $i < 22; $i++):
                    # Sommerzeit berücksichtigen
                    $x = $i + $z;
                    $anzahl = ProduktionDatabase::getAbrufeFabStunde('' . $_SESSION['wrk']['datum'] . '', '' . $_SESSION['parameter']['anfangszeit'][$x] . '', '' . $_SESSION['parameter']['endzeit'][$x] . '');
                    $takt[$i] = ($anzahl > 0) ? ProduktionDatabase::getTaktzeit($_SESSION['parameter']['anfangszeit'], $anzahl) : 0;
                    ?>
                    <td class="text-center">
                        <?= Functions::clockalize($takt[$i]) ?>
                    </td>
                <?php endfor; ?>
            </tr>
            </tbody>
        </table>
    </div><!-- table-responsive -->
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xxl-4 border__right--dotted-gray">
                <div class="px-3">
                    <h3 class="oswald text-primary font-weight-100 pb-3 border__bottom--dotted-gray"><?= $_SESSION['text']['h_uFruehschicht'] ?></h3>
                    <div class="chart-container p-1 pb-3 border__bottom--dotted-gray" style="position: relative; height:20vh;">
                        <canvas id="chart1"></canvas>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-sm font-size-11">
                            <thead class="bg__blue-gray--6">
                            <tr>
                                <th class="text-end"><?= $_SESSION['text']['h_zeit'] ?></th>
                                <?php for ($i = 6; $i < 14; $i++):
                                    ?>
                                    <th class="text-center"><?php echo substr($_SESSION['parameter']['anfangszeit'][$i], 0, 5); ?></th>
                                <?php endfor; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-end"><?= $_SESSION['text']['h_abrufe'] ?></td>
                                <?php for ($i = 6; $i < 14; $i++):
                                    # Sommerzeit berücksichtigen
                                    $x = $i + $z;
                                    ?>
                                <td class="text-center">
                                    <?= ProduktionDatabase::getAbrufeFabStunde('' . $_SESSION['wrk']['datum'] . '', '' . $_SESSION['parameter']['anfangszeit'][$x] . '', '' . $_SESSION['parameter']['endzeit'][$x] . '') ?>
                                </td>
                                <?php endfor; ?>
                            </tr>
                            <tr>
                                <td class="text-end"><?= $_SESSION['text']['h_fab'] ?></td>
                                <?php for ($i = 6; $i < 14; $i++):
                                    # Sommerzeit berücksichtigen
                                    $x = $i + $z;
                                    $fab[$i] = ProduktionDatabase::getAbrufeFabStunde('' . $_SESSION['wrk']['datum'] . '', '' . $_SESSION['parameter']['anfangszeit'][$x] . '', '' . $_SESSION['parameter']['endzeit'][$x] . '')*3;
                                    ?>
                                <td class="text-center">
                                    <?= $fab[$i] ?>
                                </td>
                                <?php endfor; ?>
                            </tr>
                            <tr>
                                <td class="text-end"><?= $_SESSION['text']['h_taktzeit'] ?></td>
                                <?php for ($i = 6; $i < 14; $i++):
                                    # Sommerzeit berücksichtigen
                                    $x = $i + $z;
                                    $anzahl = ProduktionDatabase::getAbrufeFabStunde('' . $_SESSION['wrk']['datum'] . '', '' . $_SESSION['parameter']['anfangszeit'][$x] . '', '' . $_SESSION['parameter']['endzeit'][$x] . '');
                                    $takt[$i] = ($anzahl > 0) ? ProduktionDatabase::getTaktzeit($_SESSION['parameter']['anfangszeit'], $anzahl) : 0;
                                    ?>
                                <td class="text-center">
                                    <?= Functions::clockalize($takt[$i]) ?>
                                </td>
                                <?php endfor; ?>
                            </tr>
                            <tr>
                                <td class="text-end"><?= $_SESSION['text']['h_pts'] ?></td>
                                <?php for ($i = 6; $i < 14; $i++):
                                    # Sommerzeit berücksichtigen
                                    $x = $i + $z;
                                    $pts[$i] = ProduktionDatabase::getSummeStunde('' . $_SESSION['wrk']['datum'] . '', '' . $x . '');
                                    ?>
                                    <td class="text-center">
                                        <?= $pts[$i] ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            <tr>
                                <td class="text-end"><?= $_SESSION['text']['h_differenz'] ?></td>
                                <?php for ($i = 6; $i < 14; $i++):
                                    # Sommerzeit berücksichtigen
                                    $x = $i + $z;
                                    $dif[$i] = $pts[$i] - $fab[$i];
                                    $txtcolor = ($dif[$i] >= 0) ? 'text-success' : 'text-danger';
                                    ?>
                                    <td class="text-center <?= $txtcolor ?>">
                                        <b><?= $dif[$i] ?></b>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            </tbody>
                        </table>

                        <h3 class="oswald text-primary font-weight-100 py-3 border__bottom--dotted-gray"><?= $_SESSION['text']['h_verteilungModel'] ?></h3>
                        <table class="table table-bordered table-sm font-size-11">
                            <thead class="bg__blue-gray--6">
                            <tr>
                                <?php
                                $start = ($z == 0) ? 6 : 7;
                                $ende = ($z == 0) ? 14 : 15;
                                foreach(ProduktionDatabase::getModelle($_SESSION['wrk']['datum'],$start,$ende) AS $row):
                                    ?>
                                    <th class="text-center"><?= $row->typ ?></th>
                                <?php endforeach; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach(ProduktionDatabase::getModelle($_SESSION['wrk']['datum'],$start,$ende) AS $row):
                                ?>
                                <td class="text-center">
                                    <?= ProduktionDatabase::getAnzahlModelle($_SESSION['wrk']['datum'],$start,$ende,''.$row->typ.'') ?>
                                </td>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div><!-- table-responsive -->
                </div><!-- px-3 -->
            </div><!-- col -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xxl-4 border__right--dotted-gray">
                <div class="px-3">
                    <h3 class="oswald text-primary font-weight-100 pb-3 border__bottom--dotted-gray"><?= $_SESSION['text']['h_uMittagschicht'] ?></h3>
                    <div class="chart-container p-1 pb-3 border__bottom--dotted-gray" style="position: relative; height:20vh;">
                        <canvas id="chart2"></canvas>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-sm font-size-11">
                            <thead class="bg__blue-gray--6">
                            <tr>
                                <th class="text-end"><?= $_SESSION['text']['h_zeit'] ?></th>
                                <?php for ($i = 14; $i < 22; $i++): ?>
                                    <th class="text-center"><?php echo substr($_SESSION['parameter']['anfangszeit'][$i], 0, 5); ?></th>
                                <?php endfor; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-end"><?= $_SESSION['text']['h_abrufe'] ?></td>
                                <?php for ($i = 14; $i < 22; $i++):
                                    # Sommerzeit berücksichtigen
                                    $x = $i + $z;
                                    ?>
                                    <td class="text-center">
                                        <?= ProduktionDatabase::getAbrufeFabStunde('' . $_SESSION['wrk']['datum'] . '', '' . $_SESSION['parameter']['anfangszeit'][$x] . '', '' . $_SESSION['parameter']['endzeit'][$x] . '') ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            <tr>
                                <td class="text-end"><?= $_SESSION['text']['h_fab'] ?></td>
                                <?php for ($i = 14; $i < 22; $i++):
                                    # Sommerzeit berücksichtigen
                                    $x = $i + $z;
                                    $fab[$i] = ProduktionDatabase::getAbrufeFabStunde('' . $_SESSION['wrk']['datum'] . '', '' . $_SESSION['parameter']['anfangszeit'][$x] . '', '' . $_SESSION['parameter']['endzeit'][$x] . '')*3;
                                    ?>
                                    <td class="text-center">
                                        <?= $fab[$i] ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            <tr>
                                <td class="text-end"><?= $_SESSION['text']['h_taktzeit'] ?></td>
                                <?php for ($i = 14; $i < 22; $i++):
                                    # Sommerzeit berücksichtigen
                                    $x = $i + $z;
                                    $anzahl = ProduktionDatabase::getAbrufeFabStunde('' . $_SESSION['wrk']['datum'] . '', '' . $_SESSION['parameter']['anfangszeit'][$x] . '', '' . $_SESSION['parameter']['endzeit'][$x] . '');
                                    $takt[$i] = ($anzahl > 0) ? ProduktionDatabase::getTaktzeit($_SESSION['parameter']['anfangszeit'], $anzahl) : 0;
                                    ?>
                                    <td class="text-center">
                                        <?= Functions::clockalize($takt[$i]) ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            <tr>
                                <td class="text-end"><?= $_SESSION['text']['h_pts'] ?></td>
                                <?php for ($i = 14; $i < 22; $i++):
                                    # Sommerzeit berücksichtigen
                                    $x = $i + $z;
                                    $pts[$i] = ProduktionDatabase::getSummeStunde('' . $_SESSION['wrk']['datum'] . '', '' . $x . '');
                                    ?>
                                    <td class="text-center">
                                        <?= $pts[$i] ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            <tr>
                                <td class="text-end"><?= $_SESSION['text']['h_differenz'] ?></td>
                                <?php for ($i = 14; $i < 22; $i++):
                                    # Sommerzeit berücksichtigen
                                    $x = $i + $z;
                                    $dif[$i] = $pts[$i] - $fab[$i];
                                    $txtcolor = ($dif[$i] >= 0) ? 'text-success' : 'text-danger';
                                    ?>
                                    <td class="text-center <?= $txtcolor ?>">
                                        <b><?= $dif[$i] ?></b>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            </tbody>
                        </table>
                        <h3 class="oswald text-primary font-weight-100 py-3 border__bottom--dotted-gray"><?= $_SESSION['text']['h_verteilungModel'] ?></h3>
                        <table class="table table-bordered table-sm font-size-11">
                            <thead class="bg__blue-gray--6">
                            <tr>
                                <?php
                                $start = ($z == 0) ? 14 : 15;
                                $ende = ($z == 0) ? 22 : 23;
                                foreach(ProduktionDatabase::getModelle($_SESSION['wrk']['datum'],$start,$ende) AS $row):
                                    ?>
                                    <th class="text-center"><?= $row->typ ?></th>
                                <?php endforeach; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach(ProduktionDatabase::getModelle($_SESSION['wrk']['datum'],$start,$ende) AS $row):
                                ?>
                                <td class="text-center">
                                    <?= ProduktionDatabase::getAnzahlModelle($_SESSION['wrk']['datum'],$start,$ende,''.$row->typ.'') ?>
                                </td>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div><!-- table-responsive -->
                </div><!-- px-3 -->
            </div><!-- col -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xxl-4">
                <div class="px-3">
                    <h3 class="bg__blue-gray--6 oswald font-size-20 text-center p-2 font-weight-300"><?= $_SESSION['text']['h_fab'] ?></h3>
                    <div class="row m-0 p-0 text-center mb-3 pb-3">
                        <div class="col-4 border__right--dotted-gray">
                            <small><?= $_SESSION['text']['h_summe'] ?></small><br>
                            <span class="oswald font-size-50">
                                <?= ProduktionDatabase::getAbrufeFabStunde($_SESSION['wrk']['datum'],'00:00:00', '23:59:59')*3 ?>
                            </span>
                        </div><!-- col -->
                        <div class="col-4 border__right--dotted-gray">
                            <small><?= $_SESSION['text']['h_fruehschicht'] ?></small><br>
                            <span class="oswald font-size-50">
                                <?php
                                if($z == 0):
                                    echo ProduktionDatabase::getAbrufeFabStunde($_SESSION['wrk']['datum'],'06:00:00', '13:59:59')*3;
                                else:
                                    echo ProduktionDatabase::getAbrufeFabStunde($_SESSION['wrk']['datum'],'07:00:00', '14:59:59')*3;
                                endif;
                                ?>
                            </span>
                        </div><!-- col -->
                        <div class="col-4 ">
                            <small><?= $_SESSION['text']['h_mittagschicht'] ?></small><br>
                            <span class="oswald font-size-50">
                                <?php
                                if($z == 0):
                                    echo ProduktionDatabase::getAbrufeFabStunde($_SESSION['wrk']['datum'],'14:00:00', '21:59:59')*3;
                                else:
                                    echo ProduktionDatabase::getAbrufeFabStunde($_SESSION['wrk']['datum'],'15:00:00', '22:59:59')*3;
                                endif;
                                ?>
                            </span>
                        </div><!-- col -->
                    </div><!-- row -->
                    <h3 class="bg__blue-gray--6 oswald font-size-20 text-center p-2 font-weight-300"><?= $_SESSION['text']['h_pts'] ?></h3>
                    <div class="row m-0 p-0 text-center mb-3 pb-3">
                        <div class="col-4 border__right--dotted-gray">
                            <small><?= $_SESSION['text']['h_summe'] ?></small><br>
                            <span class="oswald font-size-50">
                                <?= ProduktionDatabase::getSummeSchicht($_SESSION['wrk']['datum'],0, 23) ?>
                            </span>
                        </div><!-- col -->
                        <div class="col-4 border__right--dotted-gray">
                            <small><?= $_SESSION['text']['h_fruehschicht'] ?></small><br>
                            <span class="oswald font-size-50">
                                <?php
                                if($z == 0):
                                    echo ProduktionDatabase::getSummeSchicht($_SESSION['wrk']['datum'],6, 13);
                                else:
                                    echo ProduktionDatabase::getSummeSchicht($_SESSION['wrk']['datum'],7, 14);
                                endif;
                                ?>
                            </span>
                        </div><!-- col -->
                        <div class="col-4 ">
                            <small><?= $_SESSION['text']['h_mittagschicht'] ?></small><br>
                            <span class="oswald font-size-50">
                                <?php
                                if($z == 0):
                                    echo ProduktionDatabase::getSummeSchicht($_SESSION['wrk']['datum'],14, 21);
                                else:
                                    echo ProduktionDatabase::getSummeSchicht($_SESSION['wrk']['datum'],15, 22);
                                endif;
                                ?>
                            </span>
                        </div><!-- col -->
                    </div><!-- row -->

                    <h3 class="bg__blue-gray--6 oswald font-size-20 text-center p-2 font-weight-300"><?= $_SESSION['text']['h_verteilungModel'] ?></h3>
                    <table class="table table-bordered table-sm font-size-11">
                        <thead class="bg__blue-gray--6">
                        <tr>
                            <?php
                            $start = ($z == 0) ? 6 : 7;
                            $ende = ($z == 0) ? 22 : 23;
                            foreach(ProduktionDatabase::getModelle($_SESSION['wrk']['datum'],$start,$ende) AS $row):
                                ?>
                                <th class="text-center"><?= $row->typ ?></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach(ProduktionDatabase::getModelle($_SESSION['wrk']['datum'],$start,$ende) AS $row):
                            ?>
                            <td class="text-center">
                                <?= ProduktionDatabase::getAnzahlModelle($_SESSION['wrk']['datum'],$start,$ende,''.$row->typ.'') ?>
                            </td>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div><!-- px-3 -->
            </div><!-- col -->
        </div><!-- row -->
    </div><!-- container-fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>skin/plugins/chart.min.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>/skin/plugins/other/progressbar.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
    });
    const ctx = $('#chart1'); // Frühschicht
    const lineChart1 = new Chart(ctx, {
        type: 'line',
        data: {
            <?php
            $l = '';
            for($i = 6; $i < 14; $i++):
                $v = $_SESSION['parameter']['anfangszeit'][$i];
                $l.= "\"".substr($_SESSION['parameter']['anfangszeit'][$i],0,-3)."\",";
            endfor;
            $l = substr($l, 0, -1);
            ?>
            labels: [<?= $l ?>],
            datasets: [
                {
                    label: "<?= $_SESSION['text']['h_fab'] ?>",
                    <?php $dt = ''; for($i = 6; $i < 14; $i++):
                        # Sommerzeit berücksichtigen
                        $x = $i + $z;
                        $dt.= (ProduktionDatabase::getAbrufeFabStunde('' . $_SESSION['wrk']['datum'] . '', '' . $_SESSION['parameter']['anfangszeit'][$x] . '', '' . $_SESSION['parameter']['endzeit'][$x] . '') * 3).",";
                    endfor;
                    $dt = substr($dt,0,-1);
                    ?>
                    data: [<?= $dt ?>],
                    backgroundColor: [
                        'rgba(0,70,155,1)',
                    ],
                    borderColor: [
                        'rgba(0,70,155, 1)',
                    ],
                    borderWidth: 2,
                    fill: false,
                    lineTension: 0
                },
                {
                    label: "PTS",
                    <?php $dt = ''; for($i = 6; $i < 14; $i++):
                        # Sommerzeit berücksichtigen
                        $x = $i + $z;
                        $dt.= ProduktionDatabase::getSummeStunde('' . $_SESSION['wrk']['datum'] . '', '' . $x . '').",";
                    endfor;
                    $dt = substr($dt,0,-1);
                    ?>
                    data: [<?= $dt ?>],
                    backgroundColor: [
                        'rgba(250,187,0,1)',
                    ],
                    borderColor: [
                        'rgba(250,187,0, 1)',
                    ],
                    borderWidth: 2,
                    fill: false,
                    lineTension: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    suggestedMin: 0,
                    suggestedMax: 120
                }
            }
        }
    });
    const cty = $('#chart2'); // Frühschicht
    const lineChart2 = new Chart(cty, {
        type: 'line',
        data: {
            <?php
            $l = '';
            for($i = 14; $i < 22; $i++):
                $v = $_SESSION['parameter']['anfangszeit'][$i];
                $l.= "\"".substr($_SESSION['parameter']['anfangszeit'][$i],0,-3)."\",";
            endfor;
            $l = substr($l, 0, -1);
            ?>
            labels: [<?= $l ?>],
            datasets: [
                {
                    label: "<?= $_SESSION['text']['h_fab'] ?>",
                    <?php $dt = ''; for($i = 14; $i < 22; $i++):
                        # Sommerzeit berücksichtigen
                        $x = $i + $z;
                        $dt.= (ProduktionDatabase::getAbrufeFabStunde('' . $_SESSION['wrk']['datum'] . '', '' . $_SESSION['parameter']['anfangszeit'][$x] . '', '' . $_SESSION['parameter']['endzeit'][$x] . '') * 3).",";
                    endfor;
                    $dt = substr($dt,0,-1);
                    ?>
                    data: [<?= $dt ?>],
                    backgroundColor: [
                        'rgba(0,70,155,1)',
                    ],
                    borderColor: [
                        'rgba(0,70,155, 1)',
                    ],
                    borderWidth: 2,
                    fill: false,
                    lineTension: 0
                },
                {
                    label: "PTS",
                    <?php $dt = ''; for($i = 14; $i < 22; $i++):
                        # Sommerzeit berücksichtigen
                        $x = $i + $z;
                        $dt.= ProduktionDatabase::getSummeStunde('' . $_SESSION['wrk']['datum'] . '', '' . $x . '').",";
                    endfor;
                    $dt = substr($dt,0,-1);
                    ?>
                    data: [<?= $dt ?>],
                    backgroundColor: [
                        'rgba(250,187,0,1)',
                    ],
                    borderColor: [
                        'rgba(250,187,0, 1)',
                    ],
                    borderWidth: 2,
                    fill: false,
                    lineTension: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    suggestedMin: 0,
                    suggestedMax: 120
                }
            }
        }
    });
</script>
</body>
</html>