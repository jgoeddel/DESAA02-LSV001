<?php
# Seitenparameter
use App\Functions\Functions;
use App\Pages\Rotationsplan\RotationsplanDatabase;

$_SESSION['seite']['id'] = 10;
$subid = 0;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung(10);

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

# Array löschen
$_SESSION['beginn'] = array();
$_SESSION['ende'] = array();

# Array setzen
rotationsplanDatabase::zs2array();

# Angezeigte Werte (array)
$dspzz = array();

# Ausgabe der Arbeitszeiten
if ($zzone == 1):
    $dspzz[0] = $_SESSION['beginn'][0] . " bis ". $_SESSION['ende'][0] . " Uhr";
    $dspzz[1] = $_SESSION['beginn'][1] . " bis ". $_SESSION['ende'][1] . " Uhr";
    $dspzz[2] = $_SESSION['beginn'][2] . " bis ". $_SESSION['ende'][3] . " Uhr";
    $zschiene = array(1, 2, 3);
else:
    $dspzz[0] = $_SESSION['beginn'][4] . " bis ". $_SESSION['ende'][4] . " Uhr";
    $dspzz[1] = $_SESSION['beginn'][5] . " bis ". $_SESSION['ende'][5] . " Uhr";
    $dspzz[2] = $_SESSION['beginn'][6] . " bis ". $_SESSION['ende'][7] . " Uhr";
    $zschiene = array(4, 5, 6);
endif;

if(!isset($_SESSION['wrk']['datum'])) $_SESSION['wrk']['datum'] = DATE('Y-m-d');
$aD = date_create($_SESSION['wrk']['datum']);
$anzeigeDatum = date_format($aD, 'd.m.Y');

# Logfile schreiben
#Functions::logfile('Rotationsplan Verwaltung', '', '', 'Seite aufgerufen');
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
Functions::dspParallaxSmall("RHENUS LMS GmbH", $_SESSION['text']['h_rotationsplan']);
?>
<main class="bg__white w-100">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <?php if($plan > 0): ?>
        <div class="row py-3">
            <?php
            Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "pe-3");
            ?>
            <h5 class="oswald font-weight-300">
                Mitarbeiter der Schicht <?= $_SESSION['user']['wrk_schicht'] ?>
                <span class="float-end font-size-11 italic text-muted pt-3"><?= $anzeigeDatum ?></span>
            </h5>

            <div id="dspPersonal">
                <?php Functions::warten(); ?>
            </div>

            <?php
            Functions::htmlCloseDiv();
            Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "px-3");
            ?>
            <h5 class="oswald font-weight-300">
                <?= $dspzz[0] ?>
            </h5>
            <div class="" id="dspTableZeitschiene<?= $zschiene[0] ?>">
                <?php Functions::warten(); ?>
            </div>
            <?php
            Functions::htmlCloseDiv();
            Functions::htmlOpenDiv(3, "right", "dotted", "", "", "", "px-3");
            ?>
            <h5 class="oswald font-weight-300">
                <?= $dspzz[1] ?>
            </h5>
            <div class="" id="dspTableZeitschiene<?= $zschiene[1] ?>">
                <?php Functions::warten(); ?>
            </div>
            <?php
            Functions::htmlCloseDiv();
            Functions::htmlOpenDiv(3, "", "", "", "", "", "ps-3");
            ?>
            <h5 class="oswald font-weight-300">
                <?= $dspzz[2] ?>
            </h5>
            <div class="" id="dspTableZeitschiene<?= $zschiene[2] ?>">
                <?php Functions::warten(); ?>
            </div>
            <?php
            Functions::htmlCloseDiv();
            ?>
        </div><!-- fluid -->
        <?php else: ?>
            <div class="container-fluid text-center" style="padding-top: 10%">
                <h1 class="oswald text-primary font-weight-300 text-center" style="font-size: 7rem">
                    Keine Daten!
                </h1>
                <p class="font-size-14 text-gray">An dem von Ihnen eingetragenen Datum wurde kein Rotationsplan erstellt.</p>
            </div>
        <?php endif; ?>
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
        dspTablePersonalArchiv();
        dspTableZeitschieneArchiv(<?= $zschiene[0] ?>, '<?= $_SESSION['wrk']['datum'] ?>');
        dspTableZeitschieneArchiv(<?= $zschiene[1] ?>, '<?= $_SESSION['wrk']['datum'] ?>');
        dspTableZeitschieneArchiv(<?= $zschiene[2] ?>, '<?= $_SESSION['wrk']['datum'] ?>');
    })
</script>
</body>
</html>