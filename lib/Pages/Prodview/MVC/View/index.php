<?php
# Seitenparameter
use App\Functions\Functions;
use App\Pages\Prodview\ProdviewDatabase;

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
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div id="pline" class="z4 relative mb-3" style="margin-top:37px; height: 3px;"></div> <!-- Progrss Bar -->
    <div class="container-fluid p-4 mt-2">

        <div class="row">
            <?php
            foreach ($ln as $line):
                echo "<div id='dspLine$line->Id' class='col-4'></div>";
            endforeach;
            ?>
        </div><!-- row -->

    </div>
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/Prodview/MVC/View/js/view.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>/skin/plugins/other/progressbar.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
        loop();
        <?php foreach ($ln as $line): ?>
            dspLine(<?= $line->Id ?>);
        <?php endforeach; ?>
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
            duration: 60000
        }, function () {
            bar.set(0);
            <?php foreach ($ln as $line): ?>
            dspLine(<?= $line->Id ?>);
            <?php endforeach; ?>
            loop();
        });
    }

</script>
</body>
</html>