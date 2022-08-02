<?php
# Seitenparameter
use App\App\Container;
use App\Formular\Formular;
use App\Functions\Functions;
use App\Pages\Rotationsplan\RotationsplanDatabase;

$_SESSION['seite']['id'] = $ebene;
$subid = 33;
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
<main class="bg__white w-100" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <div class="p-3">
        <div class="row">
            <?php
            Functions::htmlOpenDiv(2, "right", "dotted", "", "", "", "pe-3");
            ?>
            <h3 class="oswald font-weight-100 py-3 border__bottom--dotted-gray">Vergleich</h3>
            <form class="needs-validation mb-3" id="vergleich" method="post" action="?">
                <?php
                Formular::input("hidden","hma","","","");
                Functions::alert("Bitte wählen Sie aus dem nachfolgenden Select Feld nach und nach maximal 3 Mitarbeiter und die entsprechende Einsatzzeit aus. Wenn Sie kein Datum angeben werden die Werte von Beginn der Aufzeichnung bis heute angezeigt.");
                ?>
                <div class="border__bottom--dotted-gray mb-3 pb-3 row">
                    <?php
                    Functions::htmlOpenDiv(6, "right", "dotted", "", "", "", "pe-3");
                    Formular::labelInvisibleInput($_SESSION['text']['h_startdatum'], "date", "start", "", "", "");
                    Functions::htmlCloseDiv();
                    ?>
                    <?php
                    Functions::htmlOpenDiv(6, "", "", "", "", "", "ps-3");
                    Formular::labelInvisibleInput($_SESSION['text']['h_enddatum'], "date", "ende", "", "", "");
                    Functions::htmlCloseDiv();
                    ?>
                </div><!-- row -->
                
                <div class="border__bottom--dotted-gray pb-3 mb-3">
                    <label class='form-label font-size-10 text-muted italic p-0 m-0'>Mitarbeiter</label><br>
                    <select name="ma" id="ma" class="invisible-formfield">
                        <option value="">Bitte wählen Sie einen Mitarbeiter</option>
                        <?php
                        foreach ($ma as $b):
                            ?>
                            <option value="<?= $b->id ?>"><?= $b->name ?> <?= $b->vorname ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="border__bottom--dotted-gray pb-3 mb-3" id="dspMA">
                    <div class="oswald text-muted font-size-20 text-center">Sie haben noch keinen Mitarbeiter ausgewählt</div>
                </div>

                <div class="float-end">
                    <?php
                    Formular::submit("submit", "Vergleich anzeigen", "oswald font-weight-300 btn btn-primary");
                    ?>
                </div>
            </form>
            <?php
            Functions::htmlCloseDiv();
            Functions::htmlOpenDiv(10, "", "", "", "", "", "ps-3");
            ?>
            <div id="dspvergleich">

            </div>
            <div class="chart-container p-1 pb-3" style="position: relative; height:350px;">
                <canvas id="chart"></canvas>
            </div>
            <?php
            Functions::htmlCloseDiv();
            ?>
        </div><!-- row -->

    </div><!-- fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<!-- Zusätzliche Javascript Dateien -->
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>skin/plugins/chart.min.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/action.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/view.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>lib/Pages/Rotationsplan/MVC/View/js/submit.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
    })
    $('#ma').change(function () {
        const ima = $('#hma').val();
        const maid = $('#ma').val();
        $('#ma').prop('selectedIndex', 0);
        $.post("/rotationsplan/mitarbeiter/vergleich", {ma: "" + maid + "," + ima + ""}, function (responseText) {
            var a = responseText.split("|");
            $('#dspMA').html(a[0]);
            $('#hma').val(a[1]);
            // Schaltfläche aktivieren
            var lang = $('#hma').val().length;
            if(lang > 0){
                $('#sbmt').prop('disabled',false);
            }
        });
    })

</script>
<div id="morris">

</div>
</body>
</html>