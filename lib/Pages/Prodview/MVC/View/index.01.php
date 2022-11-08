<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;
use App\Pages\Prodview\ProdviewDatabase;

$_SESSION['seite']['id'] = 3;
$_SESSION['seite']['name'] = 'index';
$subid = 0;
$n_suche = '';
$dspKalender = true;
$dspTag = false;

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
<body class="d-flex flex-column h-100 ford" id="body" data-spy="scroll" data-target=".navbar" data-offset="183">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation($dspedit, 'index', '', 1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_produktion']}");
?>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid p-4">

        <h3 class="font-weight-300 pb-3 mb-3 border__bottom--dotted-gray">Linien DEXNG01 <small
                    class="font-size-14 text-muted">(24.06.2022)</small></h3>
        <div class="row border__bottom--dotted-gray mb-3 pb-3">
            <?php
            $linien = count($ln);
            $i = 1;
            foreach ($ln as $linie):
                $border = ($i < $linien) ? 'border__right--dotted-gray' : '';
                $anz = ProdviewDatabase::getSumStationLine($linie->LineId);
                ?>
                <div class="col <?= $border ?>">
                    <div class="px-3">
                        Line ID: <?= $linie->LineId ?> - <?= $linie->Line ?> <?= $linie->Description ?> <small
                                class="font-size-11 text-muted">(<?= $anz ?>)</small>
                        <div class="border__top--dotted-gray pt-2 mt-3">
                            <?php
                            if ($anz > 0):
                                foreach (ProdviewDatabase::getStationLine($linie->LineId) as $station):
                                    # Anzahl IO
                                    $io = ProdviewDatabase::getSumAuftrag($station->Id, '2022-06-24', 1);
                                    # Anzahl NIO
                                    $nio = ProdviewDatabase::getSumAuftrag($station->Id, '2022-06-24', 2);
                                    ?>
                                    <div class="border__bottom--dotted-gray pb-2 mb-2 font-size-12 row">
                                        <div class="col-8 border__right--dotted-gray">
                                            <div class="pe-3">
                                                <?= $station->Id ?>. <?= $station->StationName ?><br><small
                                                        class="font-size-10 text-muted"><?= $station->Description ?></small>
                                            </div>
                                        </div>
                                        <div class="col-2 text-center border__right--dotted-gray">
                                            <?= $io ?>
                                        </div>
                                        <div class="col-2 text-center">
                                            <div class="ps-3 text-danger">
                                                <?= $nio ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                $i++;
            endforeach;
            ?>
        </div>

        <table class="table table-striped">
            <thead class="font-weight-300 font-size-11">
            <tr>
                <th>Id</th>
                <th>Citycode</th>
                <th>Value 1</th>
                <th>Value 2</th>
                <th>Station</th>
                <th>Status</th>
                <th>Timestamp</th>
            </tr>
            </thead>
            <tbody class="font-size-12">
            <?php
            foreach ($tb as $row):
                # Datum umschreiben
                $dt = new DateTime($row->TimeStamp);
                $datum = $dt->format('d.m.Y H:i:s');
                ?>
                <tr>
                    <td><?= $row->Id ?></td>
                    <td><?= $row->CityCode ?></td>
                    <td><?= $row->Value1 ?></td>
                    <td><?= $row->Value2 ?></td>
                    <td><?= $row->sid ?>. <?= $row->Description ?></td>
                    <td><?= $row->IdStatus ?></td>
                    <td><?= $datum ?></td>
                </tr>
            <?php
            endforeach;
            ?>
            </tbody>
        </table>

    </div>
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

</script>
</body>
</html>