<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;

$_SESSION['seite']['id'] = 36;
$_SESSION['seite']['name'] = 'details';
$subid = 0;
$n_suche = '';

# Seite lesen / schreiben / admin
$pb = Functions::getBerechtigung(36);

# Parameter setzen
$seitelesen = $pb[0];
$seiteschreiben = $pb[1];
$seiteadmin = $pb[2];
// Parameter für die Anzeige von Bearbeitungsfunktionen
($seiteschreiben == 1) ? $dspedit = '' : $dspedit = 'dspnone';

# Sind noch Änderungen möglich ?
$edit = ($row->name == $_SESSION['user']['dbname'] && $row->status < 6) ? 1 : 0;

# Part (1 = Evaluation, 2 = Tracking)
$part = 2;
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
<body class="d-flex flex-column h-100" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(0);
    Functions::dspNavigation($dspedit, "details", $id, 0);
    ?>
</div>
<input type="hidden" name="bid" id="bid" value="<?= $id ?>">
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <div class="px-4 py-5"><!-- Inhalt -->
            <div class="row">
                <div class="col-12 col-md-4 border__right--dotted-gray">
                    <div class="me-3">
                        <?php
                        include_once "includes/getBaseInformation.php";
                        include_once "includes/getBearbeitungsdauer.php";
                        include_once "includes/getStatusbox.php";
                        include_once "includes/getOrderbox.php";
                        include_once "includes/getDatebox.php";
                        # Sind noch Änderungen möglich ?
                        $edit = ($seiteschreiben == 1 && $row->status < 6) ? 1 : 0;
                        ?>

                    </div><!-- me-3 -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-4 border__right--dotted-gray">
                    <div class="mx-3">
                        <h3 class="border__bottom--dotted-gray mb-3 pb-3"><?= $_SESSION['text']['h_apqp'] ?> <?= $_SESSION['text']['h_tracking'] ?>
                            (<?= $evaluation ?>)</h3>
                        <?php
                        Functions::alert($_SESSION['text']['i_apqp']);
                        # Base
                        $base = ChangeManagementDatabase::getElement($id);
                        # Location
                        $loc = ChangeManagementDatabase::getLocationInfo($base->location);
                        # APQP
                        foreach (ChangeManagementDatabase::getAllApqpBereich($id, 2, $evaluation) as $apqp):
                            echo "<div id='apqp$apqp->apqp'></div>";
                        endforeach;
                        ?>
                    </div><!-- mx-3 -->
                </div><!-- col-12 -->
                <?php
                # Anzahl Kommentare
                $k = ChangeManagementDatabase::countComments(2, $id, $evaluation);
                if($k == 0 && $edit !== 1):
                    ?>
                    <div class="col-12 col-md-4 bg-light-lines"></div>
                <?php else: ?>
                    <div class="col-12 col-md-4">
                        <div class="ms-3">
                            <?php
                            Functions::alert("<b>" . $_SESSION['text']['i_kommentarTitel'] . "</b><br>" . $_SESSION['text']['i_kommentarText']);
                            include_once "includes/getFormKomAPQP.php";
                            ?>
                            <div id="komAPQP" class="mt-4">

                            </div>
                        </div><!-- ms-3 -->
                    </div><!-- col-12 -->
                <?php endif; ?>
            </div><!-- row -->
        </div>
    </div>
</main>
<?php
# Footer einbinden
Functions::getFooterBase();
Functions::getFooterJs();
?>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/ChangeManagement/MVC/View/js/view.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/ChangeManagement/MVC/View/js/action.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/summernote.min.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/lang/summernote-de-DE.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        heightMainContainer();
        heightHeader();

        // summernote
        $('.summernote').summernote({
            height: 160,
            lang: 'de-DE',
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['color', ['color']],
                ['para', ['ul', 'ol']]
            ],
            cleaner: {
                action: 'paste',
                keepHtml: false,
                keepClasses: false
            }
        });

        // Anzeigen
        dspStatus(<?= $id ?>, 'over', '<?= $row->location ?>');
        dspStatus(<?= $id ?>, 'study', '<?= $row->location ?>');
        dspStatus(<?= $id ?>, 'introduce', '<?= $row->location ?>');
        dspKomAPQP(1, <?= $id ?>, '<?= $evaluation ?>');

        <?php
        foreach (ChangeManagementDatabase::getAllApqpBereich($id, 2, $evaluation) as $apqp): ?>
        getAPQPElement(<?= $id ?>, 2, <?= $apqp->apqp ?>, '<?= $row->location ?>');
        <?php
        endforeach;
        ?>


    });
</script>
</body>
</html>