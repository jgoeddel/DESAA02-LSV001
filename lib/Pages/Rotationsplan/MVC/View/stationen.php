<?php
# Seitenparameter
use App\Functions\Functions;
use App\Pages\Rotationsplan\RotationsplanDatabase;

$_SESSION['seite']['id'] = $ebene;
$subid = 29;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung($ebene);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

# Keine Berechtigung, dann zurück auf die Startseite
if (!isset($seitelesen) || $seitelesen != 1):
    header(header: 'Location: /?e='.$ebene.'');
endif;

# Möglichkeit zum Ändern setzen
$dspedit = ($seiteschreiben == 1) ? '' : 'dspnone';

# Datenbank
$db = new \App\Pages\Rotationsplan\MVC\View\functions\Functions();

# Logfile schreiben
# Functions::logfile('Rotationsplan Stationen', '', '', 'Seite aufgerufen');
$wrkabteilung = RotationsplanDatabase::showWrkParameter();
?>
<!doctype html>
<html lang="de" class="h-100">
<head>
    <head>
        <?php
        # Basis-Head Elemente einbinden
        Functions::getHeadBase();
        ?>
        <title><?= $_SESSION['page']['version'] ?></title>
    </head>
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
        <h3 class="oswald text-primary font-weight-100 py-3 border__bottom--dotted-gray">Aktive Stationen</h3>
        <div class="row mb-5">
        <?php
        foreach($stn AS $station):
            $eid = Functions::encrypt($station->id);
            $anz = $db->getAnzahlQualiStation($station->id);
            $abteilung = RotationsplanDatabase::getAbteilungRotationsplan($station->abteilung);
            if($anz < 10) $clr = 'danger';
            if($anz > 10 && $anz < 20) $clr = 'warning';
            if($anz > 20) $clr = 'success';
            if($station->qps > 0):
                //$qps = getQPS($station->qps,$_SESSION['user']['wrk_abteilung']);
                $dspQPS = '<i class="fa fa-file-pdf text-danger"></i>';
            else:
                $dspQPS = '-';
            endif;
            $ergo = match($station->ergo){
                'success' => '<i class="fa fa-square-full text-success font-size-12"></i>',
                'danger' => '<i class="fa fa-square-full text-danger font-size-12"></i>',
                'warning' => '<i class="fa fa-square-full text-warning font-size-12"></i>',
            }
        ?>
            <div class="col-3" id="<?= $station->id ?>" onclick="top.location.href='/rotationsplan/stationDetails?id=<?= $eid ?>'">
                <div class="mx-2 pointer">
                    <div class="row border__bottom--dotted-gray mb-2">
                        <div class="col-12">
                            <p class="p-0 m-0 pt-2 oswald"><small class="me-1 font-size-11 text-muted"><?=$station->id?></small> <?= $station->station ?></p>
                            <p class="text-warning font-size-11 p-0 m-0"><?= $abteilung ?></p>
                            <p class="text-muted font-size-11 italic p-0 m-0 pb-1"><?= $station->bezeichnung ?></p>

                            <div class="row m-0 p-0 pb-2 text-center bg-light-lines border__top--dotted-gray">
                                <div class="col-md-3">
                                    <p class="p-0 m-0"><span class="text-muted font-size-10 italic">Mitarbeiter:</span><br><b class="oswald font-weight-300"><?= $station->mitarbeiter ?></b></p>
                                </div><!-- col-4 -->
                                <div class="col-md-3">
                                    <p class="p-0 m-0"><span class="text-muted font-size-10 italic">QPS:</span><br><b class="oswald font-weight-300"><?= $dspQPS ?></b></p>
                                </div><!-- col-4 -->
                                <div class="col-md-3">
                                    <p class="p-0 m-0"><span class="text-muted font-size-10 italic">Ergonomie:</span><br><b class="oswald font-weight-300"><?= $ergo ?></b></p>
                                </div><!-- col-4 -->
                                <div class="col-md-3">
                                    <p class="p-0 m-0"><span class="text-muted font-size-10 italic">Qualifikationen:</span><br>
                                        <b class="oswald font-weight-300"><?= $anz ?></b>
                                        <?php
                                        if($anz < 10) echo '<i class="fa fa-exclamation-triangle ms-1 text-warning"></i>';
                                        ?>
                                    </p>
                                </div><!-- col-4 -->
                            </div><!-- row -->

                        </div><!-- col-9 -->
                    </div><!-- row -->
                </div><!-- m-4 -->
            </div><!-- grid-item-->
        <?php
        endforeach;
        ?>
        </div><!-- grid -->
    </div><!-- fluid -->
</main>

<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<!-- Zusätzliche Javascript Dateien -->
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>skin/js/base.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/action.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/view.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/submit.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

    })
    // Isotope Filter Navigation
    const $grid = $('.grid').isotope({
        itemSelector: '.grid-item-20',
    });
    $('.filter-button-group').on( 'click', 'li', function() {
        const filterValue = $(this).attr('data-filter');
        $grid.isotope({ filter: filterValue });
    });
</script>
</body>
</html>