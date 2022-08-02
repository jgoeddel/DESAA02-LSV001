<?php
# Seitenparameter
use App\App\Container;
use App\Formular\Formular;
use App\Functions\Functions;
use App\Pages\Rotationsplan\RotationsplanDatabase;

$_SESSION['seite']['id'] = $ebene;
$subid = 28;
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
# Datum
$rpldate = $db->getNewDate();
# Logfile schreiben
# Functions::logfile('Rotationsplan', '', '', 'Seite aufgerufen');
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
Functions::dspParallaxNeuerUser("RHENUS LMS GmbH &bull; $wrkabteilung", $_SESSION['text']['h_rotationsplan']);
?>
<main class="bg__white w-100" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <h3 class="oswald font-weight-100 py-3 border__bottom--dotted-gray">Aktive Mitarbeiter</h3>
        <div class="row mb-4">
            <?php
            foreach ($ma as $user):
                $iv = mb_substr($user->vorname, 0, 1);
                $in = mb_substr($user->name, 0, 1);
                $abteilung = RotationsplanDatabase::getAbteilungRotationsplan($user->abteilung);
                $rfid = ($user->rfid) ? '<i class="fa fa-rss text-primary ps-1"></i>' : '';
                $clr = 'primary';
                $info = ($db->getTraining($user->id) > 0) ? '<i class="fa fa-square text-info ms-1"></i>' : '';
                $warning = ($db->getHandicap($user->id) > 0) ? '<i class="fa fa-square text-warning ms-1"></i>' : '';
                $danger = ($db->getAbwesend($user->id) > 0) ? '<i class="fa fa-square text-danger ms-1"></i>' : '';
                $vletter = mb_substr($user->vorname, 0, 1);
                $mid = Functions::encrypt($user->id);
                ?>


                <div class="col-2 pointer all filter<?= $in ?>"
                     onclick="top.location.href='/rotationsplan/mitarbeiterDetails?id=<?= $mid ?>'">
                    <div class="mx-2">
                        <div class="kommentar row border__bottom--dotted-gray m-0 p-0 mb-2">
                            <div class="col-12 font-size-14 pt-2 pe-2">
                                <p class="p-0 m-0 pt-2 oswald"><?= $user->vorname ?> <?= $user->name ?></p>
                                <p class="text-warning font-size-11 p-0 m-0 mb-2">
                                    <?= $abteilung ?> &bull; Schicht <?= $user->schicht ?>
                                    <span class="float-end">
                                        <?= $rfid ?><?= $info ?><?= $warning ?><?= $danger ?>
                                    </span>
                                </p>
                                <div class="row m-0 p-0 pb-2 text-center bg-light-lines border__top--dotted-gray">
                                    <div class="col-md-4">
                                        <p class="p-0 m-0"><span
                                                    class="text-muted font-size-10 italic">Qualifikationen:</span><br><b
                                                    class="oswald font-weight-300"><?= $db->getAnzahlQualiMa($user->id) ?></b>
                                        </p>
                                    </div><!-- col-4 -->
                                    <div class="col-md-4">
                                        <p class="p-0 m-0"><span class="text-muted font-size-10 italic">Anwesend:</span><br><b
                                                    class="oswald font-weight-300"><?= $db->getSummeAnwesend($user->id) ?></b>
                                        </p>
                                    </div><!-- col-4 -->
                                    <div class="col-md-4">
                                        <p class="p-0 m-0"><span
                                                    class="text-muted font-size-10 italic">Eingesetzt:</span><br><b
                                                    class="oswald font-weight-300"><?= $db->getAnzahlEinsatzGesamt($user->id) ?></b>
                                        </p>
                                    </div><!-- col-4 -->
                                </div><!-- row -->
                            </div>
                        </div>
                    </div>
                </div>
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
</script>
</body>
</html>