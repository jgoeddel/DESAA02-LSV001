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
    <div class="container-fluid">
        <div class="container-fluid p-0">
            <?php include_once "includes/inc.sub.nav.php"; ?>
        </div><!-- fluid -->
        <div class="px-4 py-5"><!-- Inhalt -->

            <div class="row">
                <?php if($edit === 1): ?>
                <div class="col-4 border__right--dotted-gray">
                    <div class="pe-3">
                        <h3 class="mb-3 pb-3 border__bottom--dotted-gray">
                            Neue Teilenummer eintragen
                        </h3>
                        <form class="needs-validation" method="post" id="insertPartNo">
                            <input type="hidden" id="bid" name="bid" value="<?= $id ?>">
                            <div class="row border__bottom--dotted-gray pb-2 mb-2">
                                <div class="col-10 border__right--dotted-gray">
                                    <div class="pe-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_bezeichnung'] ?> <span class="text-warning">*</span>
                                        </label>
                                        <?php Functions::invisibleDataList("listBezeichnung", "bezeichnung"); ?>
                                        <datalist id="listBezeichnung">
                                            <?php
                                            foreach($partno AS $prt):
                                                echo "<option value='$prt->bezeichnung'>";
                                            endforeach;
                                            ?>
                                        </datalist>
                                    </div>
                                </div><!-- col -->
                                <div class="col-2">
                                    <div class="ps-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_anlage'] ?>
                                        </label>
                                        <?php Functions::invisibleInput("text", "anlage", "", "", "", "", "{$_SESSION['text']['h_anlage']}"); ?>
                                    </div>
                                </div><!-- col -->
                            </div><!-- row -->
                            <div class="row border__bottom--dotted-gray pb-2 mb-2">
                                <div class="col-6 border__right--dotted-gray">
                                    <div class="pe-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_partOut'] ?> <span class="text-warning">*</span>
                                        </label>
                                        <?php Functions::invisibleInput("text", "alt", "", "", "", "required", "{$_SESSION['text']['h_partOut']}"); ?>
                                    </div>
                                </div><!-- col -->
                                <div class="col-6">
                                    <div class="ps-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_partIn'] ?> <span class="text-warning">*</span>
                                        </label>
                                        <?php Functions::invisibleInput("text", "neu", "", "", "", "required", "{$_SESSION['text']['h_partIn']}"); ?>
                                    </div>
                                </div><!-- col -->
                            </div><!-- row -->
                            <div class="row border__bottom--dotted-gray pb-2 mb-2">
                                <div class="col-4 border__right--dotted-gray">
                                    <div class="pe-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_ziel'] ?>
                                        </label>
                                        <?php Functions::invisibleInput("date", "ziel", "", "", "", "", "{$_SESSION['text']['h_ziel']}","", "{$_SESSION['parameter']['heuteSQL']}"); ?>
                                    </div>
                                </div><!-- col -->
                                <div class="col-4 border__right--dotted-gray">
                                    <div class="px-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_lieferant'] ?>
                                        </label>
                                        <?php Functions::invisibleDataList("listLieferant", "lid"); ?>
                                        <datalist id="listLieferant">
                                            <?php
                                            foreach($lft AS $lf):
                                                $a = ChangeManagementDatabase::getLieferant($lf->lid);
                                                echo "<option value='$a->id'>$a->lieferant</option>";
                                            endforeach;
                                            ?>
                                        </datalist>
                                    </div>
                                </div><!-- col -->
                                <div class="col-4">
                                    <div class="ps-3">
                                        <label class="font-size-12 text-muted italic" for="start">
                                            <?= $_SESSION['text']['h_dsnr'] ?>
                                        </label>
                                        <select name="doppelsnr" class="invisible-formfield">
                                            <option value="0"><?= $_SESSION['text']['nein'] ?></option>
                                            <option value="1"><?= $_SESSION['text']['ja'] ?></option>
                                        </select>
                                    </div>
                                </div><!-- col -->
                            </div><!-- row -->
                            <div class="text-end">
                                <input type="submit" class="btn btn-primary oswald font-weight-300" value="<?= strtoupper($_SESSION['text']['b_neuPartno']) ?>">
                            </div>
                        </form>
                        <h3 class="mb-3 pb-3 mt-5 border__bottom--dotted-gray">
                            Neue Teilenummern importieren
                        </h3>
                        <?php
                        functions::alert("<b>Wichtig</b><br>verwenden Sie bitte nur die Excelvorlage, die Sie sich hier auf der Seite runterladen können.<br>Wenn Sie die Datei ausgefüllt und <b>als CSV Datei</b> gespeichert haben, ziehen Sie sie in das nachfolgende Feld oder klicken Sie das Feld an, um die Datei danach auszuwählen. Der Import startet in beiden Fällen dann automatisch.");
                        ?>
                        <div class="row">
                            <div class="col-7">
                                <form action="#" class="dropzone custom_dropzone text-center font-size-12" id="fileUpload">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <input type="hidden" name="ziel" value="<?= $info->zieldatum ?>">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                                </form>
                            </div>
                            <div class="col-5">
                                <div class="ps-3">
                                    <?php
                                    include_once "includes/getExcelImportBanner.php";
                                    ?>
                                </div>
                            </div>
                        </div>

                    </div><!-- pe-3 -->
                </div><!-- col-4 -->
                <div class="col-8">
                <?php else: ?>
                <div class="col-12">
                <?php endif; ?>
                    <div class="ps-3">
                        <h3 class="mb-1 pb-3 border__bottom--dotted-gray">
                            Eingetragene Teilenummern
                        </h3>
                        <?php
                        if ($edit !== 1 && ChangeManagementDatabase::countPartNoOpen($id) > 0):
                            Functions::alert("<b>".$_SESSION['text']['h_nichtUmgestellteTeilenummer']."</b><br>".$_SESSION['text']['t_nichtUmgestellteTeilenummer']);
                        endif;
                        if(ChangeManagementDatabase::countPartNo($id) > 0):
                        ?>
                        <table class="table table-sm table-bordered table-striped dTable font-size-12">
                            <thead class="thead-dark font-weight-300">
                            <tr>
                                <th><?= $_SESSION['text']['h_anlage'] ?></th>
                                <th><?= $_SESSION['text']['h_bezeichnung'] ?></th>
                                <th><?= $_SESSION['text']['h_partOut'] ?></th>
                                <th><?= $_SESSION['text']['h_partIn'] ?></th>
                                <th><?= $_SESSION['text']['h_dsnr'] ?></th>
                                <th><?= $_SESSION['text']['h_lieferant'] ?></th>
                                <th><?= $_SESSION['text']['h_status'] ?></th>
                                <th><?= $_SESSION['text']['h_ziel'] ?></th>
                                <th><?= $_SESSION['text']['h_umstellungstermin'] ?></th>
                                <th></th>
                                <?php if($edit === 1): ?>
                                <th></th>
                                <?php endif; ?>
                            </tr>
                            </thead>
                            <tbody id="dspTbody">

                            </tbody>
                        </table>
                        <?php
                        else:
                        Functions::alert("Aktuell sind noch keine Teilenummern in der Datenbank eingetragen!");
                        endif;
                        ?>
                    </div><!-- ps-3 -->
                </div><!-- col-8 -->
            </div>

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
    $(document).ready(function () {
        heightMainContainer();
        heightHeader();
        partnoTable(<?= $id ?>);
    });

    Dropzone.autoDiscover = false;
    $('#fileUpload').dropzone({
        url: "/changeManagement/partnoUpload",
        maxFilesize: 30000,
        paramName: "file",
        createImageThumbnails: false,
        acceptedFiles: ".csv",
        previewTemplate: '<div class="dz-preview dz-file-preview">\n' +
            '  <div class="dz-details">\n' +
            '  </div>\n' +
            '</div>',
        init: function() {
            this.on('success', function(json) {
                location.reload();
            });
        }
    });
</script>
</body>
</html>