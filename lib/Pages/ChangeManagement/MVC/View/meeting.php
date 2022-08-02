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
$edit = ($seiteschreiben == 1 && $row->status < 6) ? 1 : 0;
$heute = DATE('Y-m-d');
?>
<!doctype html>
<html lang="de" class="h-100">
<head>
    <?php
    # Basis-Head Elemente einbinden
    Functions::getHeadBase();
    ?>
    <title><?= $_SESSION['page']['version'] ?></title>
    <?php if ($edit === 1): ?>
        <link rel="stylesheet" href="<?= Functions::getBaseURL() ?>/skin/plugins/node_modules/summernote/dist/summernote-bs5.min.css">
    <?php endif; ?>
</head>
<body class="d-flex flex-column h-100" id="body">
<div class="fixed-top z3">
    <?php
    # Basis Header einbinden
    Functions::getHeaderBasePage(0);
    Functions::dspNavigation($dspedit, 'details', $id, 0);
    ?>
</div>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <div class="px-4 py-5"><!-- Inhalt -->

            <div class="row">
                <div class="col-4 border__right--dotted-gray m-0">
                    <div class="me-3">
                        <h3><?= $row->nr ?><span
                                    class="float-end"><?php ChangeManagementDatabase::dspArt($id); ?></span></h3>
                        <h3 class="border__bottom--dotted-gray border__top--dotted-gray py-3 my-3"><?= $info->part_description ?></h3>
                        <div class="mb-3"><?= $info->change_description ?></div>

                        <?php
                        include_once "includes/getBearbeitungsdauer.php";
                        include_once "includes/getStatusbox.php";
                        ?>
                    </div><!-- me-3 -->
                </div><!-- col-4 -->
                <div class="col-4 border__right--dotted-gray">
                    <div class="mx-3">
                        <h3 class="border__bottom--dotted-gray mb-3 pb-3"><?= $_SESSION['text']['n_meeting'] ?></h3>
                        <div id="dspMeetings">

                        </div>
                    </div><!-- mx-3 -->
                </div>
                <?php if ($edit !== 1): ?>
                    <div class="col-12 col-md-4 bg-light-lines"></div>
                <?php else: ?>
                    <div class="col-4">
                        <div class="ms-3">
                            <h3 class="border__bottom--dotted-gray mb-3 pb-3"><?= $_SESSION['text']['b_neuerEintrag'] ?></h3>
                            <?php Functions::alert("Tragen Sie einen neuen Eintrag in das nachfolgende Formular ein und klicken Sie anschließend auf speichern. Sollte bereits ein Meeting für den heutigen Tag eingetragen sein, wird der neue Punkt diesem hinzugefügt. Wenn noch kein Meeting vorhanden ist, wird es automatisch mit dem ersten Eintrag erstellt.");
                            ?>
                            <form id="iprotokoll" method="post">
                                <input type="hidden" name="id" id="id" value="<?= $id ?>">
                                <textarea name="eintrag" class="summernote" required></textarea>
                                <div class="text-end">
                                    <input type="submit" class="btn btn-primary mt-3" value="<?= $_SESSION['text']['b_eintragSpeichern'] ?>">
                                </div>
                            </form>
                        </div><!-- mx-3 -->
                    </div><!-- col -->
                <?php endif; ?>
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
        // Anzeigen
        dspStatus(<?= $id ?>, 'over', '<?= $row->location ?>');
        dspStatus(<?= $id ?>, 'study', '<?= $row->location ?>');
        dspStatus(<?= $id ?>, 'introduce', '<?= $row->location ?>');
        dspMeetings(<?= $id ?>);
        <?php if($edit === 1): ?>
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
        <?php endif; ?>
    });
</script>
</body>
</html>