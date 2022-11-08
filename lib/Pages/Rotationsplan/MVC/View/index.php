<?php
# Seitenparameter
use App\Formular\Formular;
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
# Datum
$rpldate = $db->getNewDate();
# Logfile schreiben
# Functions::logfile('Rotationsplan', '', '', 'Seite aufgerufen');
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
<main class="bg__white w-100" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <form action="/rotationsplan/setAnwesenheit" id="anwesenheitDatum" method="post" class="needs-validation">
            <div class="row bg-light-lines border__bottom--dotted-gray_50">
                <div class="col-md-4">
                    <p class="p-0 m-0 pe-2 pt-1 font-weight-400 font-size-12 text-end"><?= $_SESSION['text']['i_auswahlDatum'] ?></p>
                </div><!-- col-4 -->
                <div class="col-md-1">
                    <?php Functions::invisibleInput("date", "newdate", "font-size-12", "$rpldate", "onchange = \"planExists(this.value)\"", "required"); ?>
                </div><!-- col-1 -->
                <div class="col-md-7 dspnone" id="warning">
                    <?= $_SESSION['text']['e_achtungPlan'] ?>
                </div><!-- col-7 -->
            </div><!-- row -->

            <div class="row p-0 m-0 font-size-12 pt-3">
                <?php
                for($t = 1; $t <= $_SESSION['parameter']['zeitschienen']; $t++):
                    ?>
                    <div class="col-md-4 m-0 p-0">
                        <div class="pe-3">
                            <table class="table table-bordered table-striped table-sm anwesenheit">
                                <thead class="bg__blue-gray--50">
                                <tr>
                                    <th class="text-end">ID</th>
                                    <th><?= $_SESSION['text']['h_name'] ?></th>
                                    <th class="text-center"></th>
                                    <th class="text-center">A</th>
                                    <th><?= $_SESSION['text']['h_station'] ?></th>
                                </tr>
                                </thead>
                                <tbody id="tbody<?=$t?>">
                                    <tr>
                                        <td colspan="4" class="text-center bg-light-lines text-gray">
                                            <i class="fa fa-cog fa-spin me-2"></i> <?= $_SESSION['text']['i_tabelleGeneriert'] ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- col-4 -->
                <?php
                endfor;
                ?>
            </div><!-- row -->
            <div class="row">
                <div class="col-md-8 font-size-12">
                    <?= sprintf($_SESSION['text']['t_erforderlicheMitarbeiter'], $erforderliche_mitarbeiter, $mitarbeiter_schicht); ?>
                </div>
                <div class="col-md-4 text-end mb-5">
                    <div class="pe-3">
                        <input id="submit" type="submit" class="btn btn-lg btn-primary font-size-16" value="<?= $_SESSION['text']['b_setAnwesenheit'] ?>" onclick="dspHinweis()">
                    </div>
                </div>
            </div>


        </form>
    </div><!-- fluid -->
</main>

<?php
# Modal
Functions::modalHinweis();
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
/*
 TODO: Wenn für einen Mitarbeiter eine Abwesenheit eingetragen wird, muss er aus allen künftigen Rotationsplänen gelöscht werden.
 */
# Update der Datenbank -> Anpassen der Einsätze in der Tabelle c_qualifikation
# RotationsplanDatabase::updateQualifikationDB();
?>
<!-- Zusätzliche Javascript Dateien -->
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>skin/js/base.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/action.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/view.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/submit.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
        const datum = $('#newdate').val();
        planExists(datum);
        dspTableAnwesenheit('tbody1','1');
        dspTableAnwesenheit('tbody2','2');
        dspTableAnwesenheit('tbody3','3');
    })
</script>
</body>
</html>