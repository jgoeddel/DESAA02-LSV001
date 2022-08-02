<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

$_SESSION['seite']['id'] = 50;
$_SESSION['seite']['name'] = 'bandsicherung';
$subid = 0;
$n_suche = '';
$dspKalender = false;
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

# Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';
# Neuer Buchstabe
if($row->position == 16): $position = 1; else: $position = $row->position + 1; endif;
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
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_bandsicherung']}");
?>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid py-3">
        <div class="row">
            <div class="col-2 border__right--dotted-gray">
                <div class="pe-3">
                    <h3 class="oswald font-weight-100 pb-3 mb-3 border__bottom--dotted-gray"><?= $_SESSION['text']['h_taeglSicherung'] ?></h3>
                    <?php Functions::alert($_SESSION['text']['t_taeglSicherung']); ?>
                    <div class="row mb-3">
                        <div class="col-4 border__right--dotted-gray">
                            <div class="p-3 text-center">
                                <span class="badge badge-primary oswald font-size-30 font-weight-600">
                                    <?= $_SESSION['parameter']['tapeletters'][$position] ?>
                                </span>
                            </div><!-- pe-3 -->
                        </div><!-- col -->
                        <div class="col-8">
                            <div class="p-3">
                                <p class="text-muted font-size-10 italic text-center p-0 m-0"><?= $_SESSION['text']['t_eintragFuer'] ?></p>
                                <?php Functions::invisibleInput("date", "datum", "font-size-24 text-gray", "{$_SESSION['parameter']['heuteSQL']}"); ?>
                            </div>
                        </div><!-- col -->
                    </div><!-- row -->
                    <?php Functions::alert($_SESSION['text']['t_passTape']); ?>

                </div><!-- pe-3 -->
            </div><!-- col -->
            <div class="col-10">
                <div class="ps-3">
                    <h3 class="oswald font-weight-100 pb-3 mb-3 border__bottom--dotted-gray"><?= $_SESSION['text']['h_erlSicherung'] ?></h3>
                    <table class="table table-sm table-striped font-size-12 dTable">
                        <thead class="bg__blue-gray--6">
                        <tr>
                            <th class="text-end"><?= $_SESSION['text']['h_nr'] ?></th>
                            <th><?= $_SESSION['text']['h_wochentag'] ?></th>
                            <th class="text-center"><?= $_SESSION['text']['h_datum'] ?></th>
                            <th class="text-center"><?= $_SESSION['text']['h_buchstabe'] ?></th>
                            <th><?= $_SESSION['text']['h_mitarbeiter'] ?></th>
                            <th><?= $_SESSION['text']['h_datum'] ?></th>
                            <th><?= $_SESSION['text']['h_zeit'] ?></th>
                            <th><?= $_SESSION['text']['h_unterschrift'] ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach($cel AS $row):
                        ?>
                            <tr>
                                <td class="text-end"><?= $row->id ?></td>
                                <td><?= Functions::germanTag($row->wota) ?></td>
                                <td class="text-center"><?= FUnctions::germanMonat($row->tag) ?></td>
                                <td class="text-center"><span class="badge badge-primary font-size-12 oswald"><?= $row->letter ?></span></td>
                                <td><?= $row->mitarbeiter ?></td>
                                <td><?= Functions::germanDate($row->etag) ?></td>
                                <td><?= $row->zeit ?> Uhr</td>
                                <td class="kristi font-size-16"><?= $row->mitarbeiter ?></td>
                            </tr>
                        <?php
                        endforeach;
                        ?>
                        </tbody>
                    </table>
                </div>
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
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>/skin/plugins/other/dataTables/datatables.min.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>/skin/plugins/other/dataTables/date-de.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>/skin/plugins/other/moment.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>/skin/plugins/other/datetime-moment.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
    });
    // Data Table
    $.fn.dataTable.moment( 'DD.MM.YYYY' );
    $(".dataTables_filter").hide();
    var table = $('.dTable').DataTable({
        "dom": '<"top">rt<"bottom"p>',
        "fixedHeader": {
            header: true,
            footer: true,
        },
        "order": [],
        "columnDefs": [{
            "targets": 'no-sort',
            "orderable": false,
        }],
        "language": {
            "url": "../template/plugins/dataTables/dataTables.german.lang.json",
            "decimal": ",",
            "thousands": "."
        },
        "pageLength": 25
    });
    $('#dtsuche').keyup(function () {
        table.search($(this).val()).draw();
    });
</script>
</body>
</html>