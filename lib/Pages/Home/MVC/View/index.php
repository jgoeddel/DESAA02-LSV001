<?php
/** (c) Joachim Göddel . Rhenus Automotive Services GmbH & Co. KG */

# Klassen
use App\Functions\Functions;

# Parameter
require_once "includes/parameter.inc.php";

$_SESSION['seite']['id'] = 0;
$_SESSION['seite']['title'] = 'Rhenus LMS Intranet';
$n_startseite = 'active';
$dspedit = '';

$em = (isset($_GET['e']) && $_GET['e'] == 1) ? '<div class="text-danger font-size-16" style="margin-top: -1.2rem;">'.$_SESSION['text']['i_keineRechte'].'</div>' : '';
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
<body class="d-flex flex-column h-100 index" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation($dspedit, 'index', '', 1);
    ?>
</div>
<?php
Functions::dspParallaxLogin("INTRANET", "RHENUS LMS GmbH$em");
?>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div id="pline" class="mb-2"></div>
    <div id="getProduktion" class="pt-1">

    </div><!-- getProduktion [AJAX] -->
    <?php
    if(isset($_SESSION['user']['id'])):
    ?>
    <div id="dspKalender">

    </div><!-- dspKalender [AJAX] -->
    <?php
    endif;
    ?>
    <div id="getAushang">

    </div><!-- getAushang [AJAX] -->
    <div id="getProduktionJahr">

    </div><!-- getProduktionJahr [AJAX] -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<!-- zusätzliche Javascript Bibliotheken -->
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>/skin/plugins/other/progressbar.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
        getDiv("ajaxGetProduktionIndex", "getProduktion");
        getDiv("ajaxGetAushangIndex", "getAushang");
        getDiv("ajaxGetProduktionJahr", "getProduktionJahr");
        getKalenderIndex(2);
        loop();

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
            getKalenderIndex(2);
            bar.set(0);
            loop();
        });
    }
</script>
</body>
</html>