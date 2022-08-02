<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

# Parameter
require_once "includes/parameter.inc.php";

$_SESSION['seite']['id'] = 36;
$_SESSION['seite']['name'] = 'neu';
$subid = 0;
$n_suche = '';

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
    Functions::dspNavigation("", "neu", 0, 0);
    ?>
</div>
<main class="w-100 bg__white--95 flex-shrink-0" id="main">
    <div class="container-fluid p-0">
        <?php include_once "includes/inc.sub.nav.php"; ?>
    </div><!-- fluid -->
    <div class="container-fluid">
        <div class="px-4 py-5"><!-- Inhalt -->
            <div class="row">
                <div class="col-12 col-xl-2 border__right--dotted-gray">
                    <div class="me-3">
                        <h3 class="oswald">
                            <span class="badge badge-primary me-3">1</span>
                            <?= $_SESSION['text']['h_standort'] ?> <span class="text-warning">*</span>
                        </h3>
                        <div class="alert alert-muted font-size-12">
                            <?= $_SESSION['text']['t_infoStandort'] ?>
                        </div>
                        <?php
                        foreach (IndexDatabase::getCMCitycode() as $cty):
                            if (IndexDatabase::checkRechteCitycode($cty->citycode) > 0):
                                ?>
                                <div class="border__bottom--dotted-gray pb-2 mb-2 px-2 pointer cc"
                                     id="<?= $cty->citycode ?>" onclick="setThisCitycode('<?= $cty->citycode ?>');">
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
                        include_once "includes/getExcelImportBanner.php";
                        ?>

                        <div class="dspnone mt-4" id="step2">
                            <h3 class="oswald">
                                <span class="badge badge-primary me-3">2</span>
                                <?= $_SESSION['text']['h_weitereAngaben'] ?>
                            </h3>
                            <?php
                            Functions::alert($_SESSION['text']['t_weitereAngaben']);
                            ?>
                            <div class="border__bottom--dotted-gray pb-2 mb-2 px-2">
                                <label class="font-size-12 text-muted italic"
                                       for="quelle"><?= $_SESSION['text']['h_aenderungsquelle'] ?></label>
                                <select name="squelle" id="squelle" class="invisible-formfield pt-1">
                                    <option value=""><?= $_SESSION['text']['t_selectOption'] ?></option>
                                    <?php foreach (ChangeManagementDatabase::getQuelle() as $row): ?>
                                        <option value="<?= $row->id ?>"><?= $_SESSION['text']['' . $row->quelle . ''] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div><!-- border -->
                            <div class="border__bottom--dotted-gray pb-2 mb-2 px-2">
                                <label class="font-size-12 text-muted italic"
                                       for="change_type"><?= $_SESSION['text']['t_changeType'] ?></label>
                                <select name="change_type" id="change_type" class="invisible-formfield pt-1">
                                    <option value=""><?= $_SESSION['text']['i_selectOption'] ?></option>
                                    <?php foreach (ChangeManagementDatabase::getChangeType() as $row): ?>
                                        <option value="<?= $row->id ?>"><?= $_SESSION['text']['' . $row->changetype . ''] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div><!-- border -->

                        </div><!-- px-3 -->
                    </div><!-- pe-3 -->
                </div><!-- col-3 -->
                <div class="col-12 col-xl-10">
                    <form class="needs-validation" method="post" id="neu">
                        <input type="hidden" name="citycode" id="citycode" value="">
                        <input type="hidden" name="pruefung" id="pruefung" value="2">
                        <input type="hidden" name="changetype" id="changetype" value="">
                        <input type="hidden" name="quelle" id="quelle" value="">
                        <div class="row m-0 p-0">
                            <div class="col-12 col-xl-4 border__right--dotted-gray">
                                <div class="mx-3 ps-1 dspnone" id="step3">
                                    <?php
                                    Functions::alert($_SESSION['text']['i_startdatum']);
                                    ?>
                                    <div class="row mx-0 px-0 mb-2 pb-2 border__bottom--dotted-gray">
                                        <div class="col-6 border__right--dotted-gray">
                                            <div class="pe-3">
                                                <label class="font-size-12 text-muted italic" for="start">
                                                    <?= $_SESSION['text']['h_startdatum'] ?> <span class="text-warning">*</span>
                                                </label>
                                                <?php Functions::invisibleInput("date", "start", "", "{$_SESSION['parameter']['heuteSQL']}", "", "required", "{$_SESSION['text']['h_startdatum']}", "{$_SESSION['parameter']['heuteSQL']}"); ?>
                                            </div>
                                        </div><!-- col-4 -->
                                        <div class="col-6">
                                            <div class="px-3">
                                                <label class="font-size-12 text-muted italic" for="ziel">
                                                    <?= $_SESSION['text']['h_zieldatum'] ?>
                                                </label>
                                                <?php Functions::invisibleInput("date", "ziel", "", "", "", "", "{$_SESSION['text']['h_zieldatum']}", "", "{$_SESSION['parameter']['heuteSQL']}"); ?>
                                            </div>
                                        </div><!-- col-4 -->
                                    </div><!-- row -->
                                    <div class="row mx-0 px-0 mb-2 pb-2 border__bottom--dotted-gray">
                                        <div class="col-12">
                                            <div class="">
                                                <label class="font-size-12 text-muted italic" for="deviation">
                                                    <?= $_SESSION['text']['s_deviation'] ?>
                                                </label>
                                                <?php Functions::invisibleInput("text", "deviation", "", "", "", "", "{$_SESSION['text']['s_deviation']}"); ?>
                                            </div>
                                        </div><!-- col-4 -->
                                    </div><!-- row -->
                                    <div class="row mx-0 px-0 mb-2 pb-2 border__bottom--dotted-gray">
                                        <div class="col-6 border__right--dotted-gray">
                                            <div class="pe-3">
                                                <label class="font-size-12 text-muted italic" for="nael">
                                                    <?= $_SESSION['text']['h_nael'] ?>
                                                </label>
                                                <?php Functions::invisibleInput("text", "nael", "", "", "", "", "{$_SESSION['text']['h_nael']}"); ?>
                                            </div>
                                        </div><!-- col-6 -->
                                        <div class="col-6">
                                            <div class="ps-3">
                                                <label class="font-size-12 text-muted italic" for="gmas">
                                                    <?= $_SESSION['text']['h_gmas'] ?>
                                                </label>
                                                <?php Functions::invisibleInput("text", "gmas", "", "", "", "", "{$_SESSION['text']['h_gmas']}"); ?>
                                            </div>
                                        </div><!-- col-6 -->
                                    </div><!-- row -->

                                    <div class="row mx-0 px-0 mb-2 pb-2 border__bottom--dotted-gray">
                                        <div class="col-12">
                                            <div class="">
                                                <label class="font-size-12 text-muted italic" for="lieferant">
                                                    <?= $_SESSION['text']['h_lieferant'] ?>
                                                </label>
                                                <div id="dspLieferanten">

                                                </div>
                                            </div>
                                        </div><!-- col-4 -->
                                    </div><!-- row -->
                                    <div class="border__bottom--dotted-gray pb-2 mb-2 px-2">
                                        <label class="font-size-12 text-muted italic"
                                               for="change_type"><?= $_SESSION['text']['t_teilenrErforderlich'] ?> <span class="text-warning">*</span></label>
                                        <select name="partno" id="partno" class="invisible-formfield pt-1" required="required">
                                            <option value=""><?= $_SESSION['text']['i_selectOption'] ?></option>
                                            <option value="1"><?= $_SESSION['text']['ja'] ?></option>
                                            <option value="0"><?= $_SESSION['text']['nein'] ?></option>
                                        </select>
                                    </div><!-- border -->

                                    <h3 class="oswald mt-5">
                                        <span class="badge badge-primary me-3">3</span>
                                        <?= $_SESSION['text']['h_aenderungsbeschreibung'] ?>
                                    </h3>

                                    <div class="row mx-0 px-0 mb-2 pb-2 border__bottom--dotted-gray">
                                        <div class="col-12">
                                            <div class="pe-3">
                                                <label class="font-size-12 text-muted italic" for="part_description">
                                                    <?= $_SESSION['text']['h_partDescription'] ?> <span
                                                            class="text-warning">*</span>
                                                </label>
                                                <?php Functions::invisibleInput("text", "part_description", "", "", "onblur='setHiddenField()'", "required", "{$_SESSION['text']['h_partDescription']}"); ?>
                                            </div>
                                        </div><!-- col-12 -->
                                    </div><!-- row -->
                                    <div class="row mx-0 px-0 mb-2 pb-2">
                                        <div class="col-12 m-0 p-0">
                                            <label class="font-size-12 text-muted italic" for="change_description">
                                                <?= $_SESSION['text']['h_changeDescription'] ?> <span
                                                        class="text-warning">*</span>
                                            </label>
                                            <textarea class="summernote" name="change_description"
                                                      placeholder="<?= $_SESSION['text']['ph_changeDescription'] ?>"
                                                      required></textarea>
                                        </div><!-- col-12 -->
                                    </div><!-- row -->
                                </div><!-- px-3 -->
                            </div><!-- col-3 -->
                            <div class="col-12 col-xl-8 dspnone" id="step5">
                                <div class="row m-0 p-0 pb-3 mb-3">
                                    <div class="col-6 border__right--dotted-gray">
                                        <div class="mx-3" id="dspEvaluation">

                                        </div><!-- ps-2 -->
                                    </div><!-- col-6 -->
                                    <div class="col-6">
                                        <div class="ms-3" id="dspTracking">

                                        </div><!-- ps-3 -->
                                    </div><!-- col-6 -->
                                </div><!-- row -->
                                <div class="text-end">
                                    <input type="submit" class="btn btn-primary oswald font-weight-300" value="<?= strtoupper($_SESSION['text']['b_neuInsert']) ?>">
                                </div>
                            </div><!-- col-3 -->
                        </div><!-- row -->
                    </form>
                </div><!-- col-9 -->
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
    });
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
</script>
</body>
</html>