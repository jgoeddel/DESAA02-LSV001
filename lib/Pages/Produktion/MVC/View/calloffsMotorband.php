<?php
# Seitenparameter
use App\Functions\Functions;
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
<body class="d-flex flex-column h-100" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(0);
    ?>
</div>
<main class="bg__white w-100">
    <div id="pline" class="z4 relative mb-3" style="margin-top:37px; height: 3px;"></div> <!-- Progrss Bar -->
    <div class="container-fluid mt-85">
        <div id="calloff" class="px-3 mt-2">
            <div id="number"></div>
            <div id="table" class="z-5 relative" style="z-index:99;"></div>
            <div class="chart-container p-1 pb-3 z-1 relative" style="height:80vh; z-index: 1" id="dspChart">

            </div>
        </div>
    </div><!-- fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<!-- Zusätzliche Javascript Dateien -->
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>skin/plugins/chart.min.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>/skin/plugins/other/progressbar.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        loop();
        $.post("/produktion/motorband/numberCalloffs", function (text) { $("#number").html(text); });
        $.post("/produktion/motorband/chartCalloffs", function (text) { $("#dspChart").html(text); });
        $.post("/produktion/motorband/tableCalloffs", function (text) { $("#table").html(text); });
    });
    var bar = new ProgressBar.Line(pline, {
        strokeWidth: 1,
        easing: 'linear',
        color: '#FABB00',
        trailColor: 'rgba(255,255,255,.1)',
        trailWidth: 1,
        svgStyle: {width: '100%', height: '100%'}
    });
    function loop(cb) {
        bar.animate(1.0, {
            duration: 30000
        }, function () {
            bar.set(0);
            $.post("/produktion/motorband/numberCalloffs", function (text) { $("#number").html(text); });
            $.post("/produktion/motorband/chartCalloffs", function (text) { $("#dspChart").html(text); });
            $.post("/produktion/motorband/tableCalloffs", function (text) { $("#table").html(text); });
            loop();
        });
    }
</script>
</body>
</html>
