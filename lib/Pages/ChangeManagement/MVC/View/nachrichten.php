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
    <?php if($edit === 1): ?>
        <link rel="stylesheet" type="text/css"
              href="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/summernote.min.css">
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
                        <h3 class="border__bottom--dotted-gray mb-3 pb-3"><?= $_SESSION['text']['h_logbuch'] ?></h3>
                        <?php
                        if(count($com) > 0):
                            foreach($com AS $ak):
                                $ersteller = IndexDatabase::getUserInfo($row->mid);
                                ?>
                                <div class="kommentar row border__bottom--dotted-gray m-0 p-0 mb-2 pb-2">
                                    <div class="col-2 text-center pt-2">
                                        <img src="<?= Functions::getBaseUrl() ?>/lib/Pages/Administration/MVC/View/files/images/<?= $ersteller->bild ?>"
                                             class="rund_small img-thumbnail img-fluid">
                                    </div>
                                    <div class="col-10 font-size-12 pt-2 pe-2">
                                        <b><?= $ak->name ?></b>
                                        <br><small class="text-muted"><?= $ak->am ?></small><br>
                                        <?= $ak->log ?>
                                    </div>
                                </div>
                            <?php
                            endforeach;
                        else:
                            Functions::alert($_SESSION['text']['i_keinKommentar']);
                        endif;
                        ?>
                    </div><!-- mx-3 -->
                </div><!-- col-4 -->
                <?php if($edit !== 1): ?>
                <div class="col-12 col-md-4 bg-light-lines"></div>
                <?php else: ?>
                <div class="col-4">
                    <div class="ms-3">
                        <h3 class="border__bottom--dotted-gray mb-3 pb-3"><?=  $_SESSION['text']['h_logbuchSchreiben'] ?></h3>
                        <?php Functions::alert($_SESSION['text']['i_kommentarText']); ?>
                        <form id="inachricht" method="post">
                            <input type="hidden" name="bid" id="bid" value="<?= $id ?>">
                            <textarea name="log" class="summernote" required></textarea>
                            <div class="text-end">
                                <input type="submit" class="btn btn-primary mt-3 oswald text-uppercase" value="<?= $_SESSION['text']['b_kommentarSpeichern'] ?>">
                            </div>
                        </form>
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