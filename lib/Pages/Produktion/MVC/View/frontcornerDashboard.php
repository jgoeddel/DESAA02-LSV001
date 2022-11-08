<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

$_SESSION['seite']['id'] = 55;
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
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_frontcorner']}");
?>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div id="pline" class="mb-2"></div>
    <div class="container-fluid py-4">
        <div class="row" id="dspStations">
            <?php
            $x = 0;
            for ($c = 0; $c < count($y); $c++):
                foreach ($y[$c]->attributes() as $d => $e):
                    $$d[$c] = $e;
                endforeach;
                $color[$c] = (!isset($rot[$c]) || $rot[$c] == '') ? 'text-muted' : 'text-black';
                if(!isset($rot[$c]) || $rot[$c] == '') $rot[$c] = '&nbsp;';
                if(!isset($vin[$c]) || $vin[$c] == '') $vin[$c] = '&nbsp;';
                $fehler[$c] = ($fault[$c] == 'false') ? 'text-success' : 'text-danger';
                if($fault[$c] == 'true') $rot[$c] = '&nbsp;';
                if($fault[$c] == 'true') $vin[$c] = '&nbsp;';
                ?>
                <div class="col">
                    <div class="p-1">
                        <div class="col border__solid--gray_50">
                            <div class="row p-0 m-0">
                                <div class="col-9 border__right--solid-gray">
                                    <p class="p-0 m-0 oswald font-size-16 text-center line-height-10 pt-2" id="rot<?= $c ?>">

                                    </p>
                                    <div id="title<?= $c ?>">

                                    </div>
                                    <p class="p-0 m-0 oswald font-size-16 text-center pb-2 pt-1" id="vin<?= $c ?>">

                                    </p>
                                </div><!-- col-8 -->
                                <div class="col-3 bg__blue-gray--25">
                                    <div class="ps-2">
                                        <p class="font-size-12 oswald italic text-muted p-0 m-0 pt-2 pb-1 ">Betriebsart</p>
                                        <p class="font-size-18 oswald p-0 m-0" id="mode<?= $c ?>">

                                        </p>
                                        <div class="row pt-2">
                                            <div class="col-4 text-center" id="fault<?= $c ?>">

                                            </div>
                                            <div class="col-4 text-center" id="opreq<?= $c ?>">

                                            </div>
                                        </div><!-- row -->
                                    </div><!-- ps-2 -->
                                </div><!-- col-4 -->
                            </div><!-- row -->
                        </div><!-- col -->
                    </div><!-- p-1 -->
                </div><!-- col -->
                <?php
                $x++;
                if($x == 5): echo "<div class='w-100'></div>"; $x = 0; endif;
            endfor;
            ?>
        </div><!-- row -->
    </div><!-- container-fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/Produktion/MVC/View/js/view.js"></script>
<script type="text/javascript" src="<?= Functions::getBaseURL() ?>/skin/plugins/other/progressbar.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
        <?php
        for ($c = 0; $c < count($y); $c++):
            ?>
            dspStation(<?= $c ?>);
        <?php
        endfor;
        ?>
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
            duration: 15000
        }, function () {
            <?php
            for ($c = 0; $c < count($y); $c++):
            ?>
            dspStation(<?= $c ?>);
            <?php
            endfor;
            ?>
            bar.set(0);
            loop();
        });
    }
</script>
</body>
</html>