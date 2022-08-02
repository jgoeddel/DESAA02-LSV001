<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\Home\IndexDatabase;

$_SESSION['seite']['id'] = 37;
$_SESSION['seite']['name'] = 'apqp';
$subid = 0;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung($_SESSION['seite']['id']);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];

// Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';
($seiteschreiben == 1) ? $col = '9' : $col = '12';
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
    <link rel="stylesheet" type="text/css"
          href="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/summernote.min.css">
</head>
<body class="d-flex flex-column h-100 apqp" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(1);
    Functions::dspNavigation("", 'apqp', 2, 1);
    ?>
</div>
<?php
Functions::dspParallaxSmall("INTRANET &bull; RHENUS AUTOMOTIVE", "{$_SESSION['text']['h_apqp']}");
?>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <div class="px-4 py-5"><!-- Inhalt -->

            <div class="row">
                <div class="col-12 col-xl-2 border__right--dotted-gray">
                    <div class="me-3">
                        <h3 class="border__bottom--dotted-gray pb-3 mb-3">
                            <span class="badge badge-primary me-3">1</span> <?= $_SESSION['text']['h_standort'] ?>
                        </h3>
                        <?php Functions::alert($_SESSION['text']['t_auswahlStandort']); ?>
                        <input type="hidden" name="citycode" id="citycode">
                        <input type="hidden" name="bereich" id="bereich">
                        <?php
                        foreach (IndexDatabase::getCMCitycode() as $cty):
                            if (IndexDatabase::checkRechteCitycode($cty->citycode) > 0):
                                ?>
                                <div class="border__bottom--dotted-gray pb-2 mb-2 px-2 pointer cc"
                                     id="<?= $cty->citycode ?>">
                                    <p class="m-0 p-0 oswald">
                                        <?= $cty->citycode ?>
                                        <i class="fa fa-check-square font-size-20 text-primary dspnone float-end"
                                           id="<?= $cty->citycode ?>_icon"></i>
                                    </p>
                                    <p class="m-0 p-0 italic font-size-12">
                                        <?= $cty->firma ?>
                                    </p>
                                </div>
                            <?php
                            endif;
                        endforeach;
                        ?>
                        <div id="dspBereich" class="dspnone">
                            <h3 class="border__bottom--dotted-gray pb-3 pt-2">
                                <span class="badge badge-primary me-3">2</span>
                                <?= $_SESSION['text']['h_bereich'] ?>
                            </h3>
                            <?php Functions::alert($_SESSION['text']['t_auswahlBereich']); ?>
                            <div class="border__bottom--dotted-gray pb-2 mb-2 px-2 pointer dd text-uppercase" id="evaluation">
                                <p class="m-0 p-0 oswald"><?= $_SESSION['text']['h_evaluation'] ?></p>
                            </div>
                            <div class="border__bottom--dotted-gray pb-2 mb-2 px-2 pointer dd text-uppercase" id="tracking">
                                <p class="m-0 p-0 oswald"><?= $_SESSION['text']['h_tracking'] ?></p>
                            </div>
                        </div>
                    </div><!-- me-3 -->
                </div><!-- col -->
                <div class="col-12 col-xl-2 border__right--dotted-gray dspnone" id="info">
                    <div class="px-3">
                        <p class="font-size-12"><?= $_SESSION['text']['i_apqp_01'] ?></p>
                        <p class="font-size-12"><?= $_SESSION['text']['i_apqp_02'] ?></p>
                    </div>
                </div><!-- col -->
                <div class="col-12 col-xl-8" id="source">
                    <div class="ps-3">
                        <div class="row p-0 m-0" id="zuordnung">

                        </div>
                    </div>
                </div>
            </div><!-- row -->

        </div><!-- px-4 -->
    </div><!-- container-fluid -->
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/Administration/MVC/View/js/view.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/Administration/MVC/View/js/action.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/summernote.min.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/lang/summernote-de-DE.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
        //heightHeader();

    });
</script>
</body>
</html>