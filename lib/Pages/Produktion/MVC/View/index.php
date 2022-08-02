<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

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
    <div id="pline" class="mb-2"></div>
    <div id="getProduktion" class="pt-1">

    </div><!-- getProduktion [AJAX] -->
    <div class="chart-container p-1 pb-3 border__bottom--dotted-gray_25" style="position: relative; height:30vh;">
        <canvas id="getProduktionChart"></canvas>
    </div>
    <div id="getProduktionKW">
        <?php include_once "includes/getProduktionKW.php"; ?>
    </div><!-- getProduktionKW [AJAX] -->

    <div id="getProduktionJahr">

    </div><!-- getProduktionJahr [AJAX] -->
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
        getDiv("ajaxGetProduktionIndex", "getProduktion");
        getDiv("ajaxGetProduktionJahr", "getProduktionJahr");
        getDiv("ajaxGetProduktionKW", "getProduktionKW");
        getDiv("ajaxGetProduktionChart", "getProduktionChart");
        loop();
    });
    var ctx = $('#getProduktionChart');
    var lineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?php echo $cl; ?>],
            datasets: [
                {
                    label: "Vorgabe",
                    data: [<?php echo $cv; ?>],
                    backgroundColor: [
                        'rgba(0,70,155,0.5)',
                    ],
                    borderColor: [
                        'rgba(0,70,155, 0.25)',
                    ],
                    borderWidth: 1
                },
                {
                    label: "Ergebnis",
                    data: [<?php echo $cf; ?>],
                    backgroundColor: [
                        'rgba(250,187,0,1)',
                    ],
                    borderColor: [
                        'rgba(250,187,0, 1)',
                    ],
                    borderWidth: 3
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    suggestedMin: 0,
                    suggestedMax: 1400
                }
            }
        }
    });
    var bar = new ProgressBar.Line(pline, {
        strokeWidth: 2,
        easing: 'linear',
        color: '#00469b',
        trailColor: 'rgba(255,255,255,.1)',
        trailWidth: 1,
        svgStyle: {width: '100%', height: '100%'}
    });

    function loop(cb) {
        bar.animate(1.0, {
            //duration: 10000
            duration: 60000
        }, function () {
            getDiv("ajaxGetProduktionIndex", "getProduktion");
            getDiv("ajaxGetProduktionJahr", "getProduktionJahr");
            getDiv("ajaxGetProduktionKW", "getProduktionKW");
            getDiv("ajaxGetProduktionChart", "getProduktionChart");
            bar.set(0);
            loop();
        });
    }
</script>
</body>
</html>