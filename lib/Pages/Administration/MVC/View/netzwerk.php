<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Home\IndexDatabase;

$_SESSION['seite']['id'] = 1;
$_SESSION['seite']['name'] = 'index';
$subid = 0;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung(24);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

// Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';
# Keine Berechtigung, dann zurück auf die Startseite
if (!isset($seitelesen) || $seitelesen != 1):
    header(header: 'Location: /?e=1');
endif;

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
<body class="d-flex flex-column h-100 netzwerk" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation($dspedit,'netzwerk','',1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_netzwerk']}");
?>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <div class="my-4">
            <div class="row">
                <div class="col-4 border__right--dotted-gray_50">
                    <div class="p-3">
                        <h3 class="oswald text-primary font-weight-100 py-3 mb-3 border__bottom--dotted-gray_50">Netzwerk:
                            ARUBA
                            <span class="text-muted font-size-14">STACK</span>
                        </h3>
                        <div id="pArubaStack" style="margin-top: -27px; height: 3px;"></div>
                        <div id="arubaStack" class="pt-3">
                            <?php Functions::warten(); ?>
                        </div>
                        <h3 class="oswald text-primary font-weight-100 py-3 mb-3 border__bottom--dotted-gray_50">Netzwerk:
                            ARUBA
                            <span class="text-muted font-size-14">SWITCH</span>
                        </h3>
                        <div id="pArubaSwitch" style="margin-top: -27px; height: 3px;"></div>
                        <div id="arubaSwitch" class="pt-3">
                            <?php Functions::warten(); ?>
                        </div>
                    </div><!-- p-3 -->
                </div><!-- col-4 -->
                <div class="col-4 border__right--dotted-gray_50">
                    <div class="p-3">
                        <h3 class="oswald text-primary font-weight-100 py-3 mb-3 border__bottom--dotted-gray_50">Netzwerk:
                            MICROSENS
                            <span class="text-muted font-size-14">RING 1 MOTORBAND</span>
                        </h3>
                        <div id="pRing1" style="margin-top: -27px; height: 3px;"></div>
                        <div id="microsensRing1" class="pt-3">
                            <?php Functions::warten(); ?>
                        </div>
                        <h3 class="oswald text-primary font-weight-100 py-3 mb-3 border__bottom--dotted-gray_50">Netzwerk:
                            MICROSENS
                            <span class="text-muted font-size-14">RING 2 MOTORBAND</span>
                        </h3>
                        <div id="pRing2" style="margin-top: -27px; height: 3px;"></div>
                        <div id="microsensRing2" class="pt-3">
                            <?php Functions::warten(); ?>
                        </div>
                        <h3 class="oswald text-primary font-weight-100 py-3 mb-3 border__bottom--dotted-gray_50">Netzwerk:
                            MICROSENS
                            <span class="text-muted font-size-14">RING 3 MOTORBAND</span>
                        </h3>
                        <div id="pRing3" style="margin-top: -27px; height: 3px;"></div>
                        <div id="microsensRing3" class="pt-3">
                            <?php Functions::warten(); ?>
                        </div>
                    </div><!-- p-3 -->
                </div><!-- col-4 -->
                <div class="col-4">
                    <div class="p-3">
                        <h3 class="oswald text-primary font-weight-100 py-3 mb-3 border__bottom--dotted-gray_50">Netzwerk:
                            MICROSENS
                            <span class="text-muted font-size-14">RING 5 FRONTCORNER</span>
                        </h3>
                        <div id="pRing5" style="margin-top: -27px; height: 3px;"></div>
                        <div id="microsensRing5" class="pt-3">
                            <?php Functions::warten(); ?>
                        </div>
                        <h3 class="oswald text-primary font-weight-100 py-3 mb-3 border__bottom--dotted-gray_50">Netzwerk:
                            MICROSENS
                            <span class="text-muted font-size-14">RING 6 KÜHLER / BOLSTER</span>
                        </h3>
                        <div id="pRing6" style="margin-top: -27px; height: 3px;"></div>
                        <div id="microsensRing6" class="pt-3">
                            <?php Functions::warten(); ?>
                        </div>
                        <h3 class="oswald text-primary font-weight-100 py-3 mb-3 border__bottom--dotted-gray_50">Netzwerk:
                            MICROSENS
                            <span class="text-muted font-size-14">RING 7 KÜHLER / BOLSTER</span>
                        </h3>
                        <div id="pRing7" style="margin-top: -27px; height: 3px;"></div>
                        <div id="microsensRing7" class="pt-3">
                            <?php Functions::warten(); ?>
                        </div>
                    </div>
                </div><!-- col-4 -->
            </div><!-- row -->
        </div><!-- my-4 -->
    </div><!-- container-fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/skin/plugins/other/progressbar.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/Administration/MVC/View/js/view.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/Administration/MVC/View/js/action.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
        loop();
        dspTableNetwork('aruba/stack', 'arubaStack', 0, 0);
        dspTableNetwork('aruba/switch', 'arubaSwitch', 0, 0);
        dspTableNetwork('microsens/ring1', 'microsensRing1', 0, 0);
        dspTableNetwork('microsens/ring2', 'microsensRing2', 0, 0);
        dspTableNetwork('microsens/ring3', 'microsensRing3', 0, 0);
        dspTableNetwork('microsens/ring5', 'microsensRing5', 0, 0);
        dspTableNetwork('microsens/ring6', 'microsensRing6', 0, 0);
        dspTableNetwork('microsens/ring7', 'microsensRing7', 0, 0);
    });
    <?php
    $options = "strokeWidth: 1, easing: 'linear', color: '#FABB00', trailColor: 'rgba(255,255,255,.1)', trailWidth: 1, svgStyle: {width: '100%', height: '100%'}";
    ?>
    // Bar
    const bar = new ProgressBar.Line(pArubaStack, {<?= $options ?>});
    const bar2 = new ProgressBar.Line(pArubaSwitch, {<?= $options ?>});
    const bar3 = new ProgressBar.Line(pRing1, {<?= $options ?>});
    const bar4 = new ProgressBar.Line(pRing2, {<?= $options ?>});
    const bar5 = new ProgressBar.Line(pRing3, {<?= $options ?>});
    const bar6 = new ProgressBar.Line(pRing5, {<?= $options ?>});
    const bar7 = new ProgressBar.Line(pRing6, {<?= $options ?>});
    const bar8 = new ProgressBar.Line(pRing7, {<?= $options ?>});

    // Loop
    function loop(cb) {
        bar.animate(1.0, {duration: 60000}, function () {
            bar.set(0);
            dspTableNetwork('aruba/stack', 'arubaStack', 0, 0);
            bar2.animate(1.0, {duration: 60000}, function () {
                bar2.set(0);
                dspTableNetwork('aruba/switch', 'arubaSwitch', 0, 0);
            });
            bar3.animate(1.0, {duration: 60000}, function () {
                bar3.set(0);
                dspTableNetwork('microsens/ring1', 'microsensRing1', 0, 0);
            });
            bar4.animate(1.0, {duration: 60000}, function () {
                bar4.set(0);
                dspTableNetwork('microsens/ring2', 'microsensRing2', 0, 0);
            });
            bar5.animate(1.0, {duration: 60000}, function () {
                bar5.set(0);
                dspTableNetwork('microsens/ring3', 'microsensRing3', 0, 0);
            });
            bar6.animate(1.0, {duration: 60000}, function () {
                bar6.set(0);
                dspTableNetwork('microsens/ring5', 'microsensRing5', 0, 0);
            });
            bar7.animate(1.0, {duration: 60000}, function () {
                bar7.set(0);
                dspTableNetwork('microsens/ring6', 'microsensRing6', 0, 0);
            });
            bar8.animate(1.0, {duration: 60000}, function () {
                bar8.set(0);
                dspTableNetwork('microsens/ring7', 'microsensRing7', 0, 0);
            });
            loop();
        });
    }
</script>
</body>
</html>