<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

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
        <link rel="stylesheet" href="<?= Functions::getBaseURL() ?>/skin/plugins/other/dropzone/dropzone.min.css">
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
                        <h3 class="border__bottom--dotted-gray mb-3 pb-3"><?= $_SESSION['text']['h_angebote'] ?></h3>
                        <div id="dspAngebote"></div>
                    </div><!-- mx-3 -->
                </div><!-- col-4 -->
                <?php if ($edit !== 1): ?>
                    <div class="col-12 col-md-4 bg-light-lines"></div>
                <?php else: ?>
                    <div class="col-4">
                        <div class="ms-3">
                            <h3 class="border__bottom--dotted-gray mb-3 pb-3"><?= $_SESSION['text']['h_uploadAngebote'] ?></h3>
                            <?php Functions::alert($_SESSION['text']['i_uploadAngebote']); ?>
                            <form action="#" class="dropzone custom_dropzone text-center mb-4"
                                  id="fileUpload<?= $id ?>">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <input type="hidden" name="art" value="Angebot">
                                <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                            </form>
                            <h3 class="border__bottom--dotted-gray mb-3 pb-3"><?= $_SESSION['text']['h_leseSchreibrecht'] ?></h3>
                            <div class='m-0 row pb-3 mb-3' id='dspMaAngebot'>
                                <!--
                                Ausgabe aller Mitarbeiter des jeweiligen Standortes.
                                Externe Berechtigungen sind hier noch nicht vorgesehen
                                -->
                            </div>
                        </div><!-- ms-3 -->
                    </div><!-- col-4 -->
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
        src="<?= Functions::getBaseURL() ?>/skin/plugins/other/dropzone/dropzone.min.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/ChangeManagement/MVC/View/js/view.js"></script>
<script type="text/javascript"
        src="<?= Functions::getBaseURL() ?>/lib/Pages/ChangeManagement/MVC/View/js/action.js"></script>
<script type="text/javascript">
    Dropzone.autoDiscover = false;
    $(document).ready(function () {
        heightMainContainer();
        heightHeader();
        dspMaAngebot('<?= $row->location ?>',<?= $id ?>);
        dspAngebote(<?= $id ?>);
        // Anzeigen
        dspStatus(<?= $id ?>, 'over', '<?= $row->location ?>');
        dspStatus(<?= $id ?>, 'study', '<?= $row->location ?>');
        dspStatus(<?= $id ?>, 'introduce', '<?= $row->location ?>');
        <?php if($edit === 1): ?>
        // DATEIUPLOAD
        $('#fileUpload<?= $id ?>').dropzone({
            url: "/changeManagement/setDatei",
            maxFilesize: 30000,
            paramName: "file",
            dictDefaultMessage: "<?= $_SESSION['text']['i_upload'] ?>",
            createImageThumbnails: false,
            acceptedFiles: ".pdf",
            previewTemplate: '<div class="dz-preview dz-file-preview">\n' +
                '  <div class="dz-details">\n' +
                '  </div>\n' +
                '</div>',
            init: function () {
                this.on('success', function (file, json) {
                    if (json == 1) {
                        swal.fire({
                            title: '<?= $_SESSION['text']['h_dateiGespeichert'] ?>',
                            text: '<?= $_SESSION['text']['t_dateiGespeichert'] ?>',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            backdrop: 'rgba(0,0,0,0.7)'
                        }).then(function () {
                            location.reload();
                        });
                    } else {
                        swal.fire({
                            title: '<?= $_SESSION['text']['h_dateiNichtGespeichert'] ?>',
                            text: '<?= $_SESSION['text']['t_dateiNichtGespeichert'] ?>',
                            icon: 'error',
                            timer: 1500,
                            showConfirmButton: false,
                            backdrop: 'rgba(0,0,0,0.7)'
                        })
                    }
                });
            }
        });
        <?php endif; ?>
    });
</script>
</body>
</html>