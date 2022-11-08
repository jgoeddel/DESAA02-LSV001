<?php
/** (c) Joachim Göddel . RLMS */

# Klassen
use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\PhpOffice\PhpSpreadsheet\Reader\Xlsx;

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
$_SESSION['wrk']['id'] = $_GET['id'];
$_SESSION['wrk']['loc'] = $_GET['loc'];
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
        <link rel="stylesheet" type="text/css"
              href="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/summernote.min.css">
    <?php endif; ?>
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
        <div class="p-4"><!-- Inhalt -->
            <div class="row">
                <div class="col-12 col-md-4 border__right--dotted-gray">
                    <div class="me-3">
                        <h3><?= $row->nr ?><span
                                    class="float-end"><?php ChangeManagementDatabase::dspArt($id); ?></span></h3>
                        <h3 class="border__bottom--dotted-gray border__top--dotted-gray py-3 my-3">
                            <?php
                            if ($edit === 1):
                                Functions::invisibleInput("text", "part_description", "", "$info->part_description", "onblur=\"sendValue('part_description',this.value,$id,'base2info','text')\"");
                            else:
                                Functions::invisibleInput("text", "", "", "$info->part_description", "", "disabled");
                            endif;
                            ?>
                        </h3>
                        <?php
                        if ($edit === 1):
                            ?>
                            <span id="edit_description" class="mb-3 pb-3"
                                  onblur="sendValue('cange_description',this.value,<?= $info->bid ?>,'base2info','editor')">
                                <textarea name="change_description" class="summernote mb-3"
                                          id="cd"><?= $info->change_description ?></textarea>
                                </span>
                        <?php
                        else:
                            echo "<div class='pb-3'>$info->change_description</div>";
                        endif;
                        ?>
                        <?php
                        include_once "includes/getBearbeitungsdauer.php";
                        include_once "includes/getStatusbox.php";
                        include_once "includes/getAdressErsteller.php";
                        ?>
                    </div><!-- me-3 -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-4 border__right--dotted-gray">
                    <div class="mb-3 mx-3">
                        <div class="row mb-3 pb-3 border__bottom--dotted-gray">
                            <div class="col-4 border__right--dotted-gray">
                                <div class="pe-3">
                                    <small class="text-muted italic"><?= $_SESSION['text']['h_location'] ?></small><br>
                                    <?= $row->location ?>
                                </div>
                            </div><!-- col-4 -->
                            <div class="col-4 border__right--dotted-gray">
                                <div class="px-3">
                                    <small class="text-muted italic"><?= $_SESSION['text']['h_inputDate'] ?></small><br>
                                    <?= $row->tag ?>
                                </div>
                            </div><!-- col-4 -->
                            <div class="col-4">
                                <div class="ps-3">
                                    <small class="text-muted italic"><?= $_SESSION['text']['h_zieldatum'] ?></small><br>
                                    <?php
                                    echo "<span id='dspziel'>$info->ziel</span>";
                                    if ($edit === 1):
                                        echo '<i class="fa fa-edit ps-3 pointer float-end" onclick="showZiel()"></i>';
                                    endif;
                                    ?>
                                </div>
                            </div><!-- col-4 -->
                        </div><!-- row -->
                        <div class="dspnone mb-3 pb-3" id="formularZiel">
                            <form id="fziel" method="post"
                                  class="row needs-validation pb-3 mb-3 border__bottom--dotted-gray">
                                <input type="hidden" name="bid" value="<?= $id ?>">
                                <div class="col-4 border__right--dotted-gray">
                                    <p class="font-size-12 pe-3"><?= $_SESSION['text']['i_aenderung'] ?></p>
                                    <?php Functions::invisibleInput("date", "zieldatum", "me-3", "$info->zieldatum", "", "", "{$_SESSION['text']['ph_ihreEingabe']}"); ?>
                                </div>
                                <div class="col-8">
                                    <textarea class="invisible-formfield font-size-12 ms-2" name="bemerkung"
                                              placeholder="<?= $_SESSION['text']['ph_grund'] ?>" required></textarea>
                                    <div class="text-end">
                                        <input type="submit" class="btn btn-primary btn-sm mt-1"
                                               value="<?= $_SESSION['text']['b_aenderung'] ?>">
                                    </div>
                                </div>
                            </form>
                        </div><!-- dspnone -->
                        <div class="row mb-3 pb-3 border__bottom--dotted-gray">
                            <div class="col-6 border__right--dotted-gray">
                                <div class="pe-2">
                                    <small class="text-muted italic"><?= $_SESSION['text']['h_gmas'] ?></small><br>
                                    <?php
                                    $a = '';
                                    if (count($gmas) > 0):
                                        foreach ($gmas as $val): $a .= $val->gmas . ", ";
                                        endforeach;
                                    else:
                                        $a = '-  ';
                                    endif;
                                    $a = substr($a, 0, -2);
                                    if ($edit === 1):
                                        if ($a == ''):
                                            Functions::invisibleInput("text", "gmas", "", "$a", "onblur=\"setValue('gmas',this.value,$id,'base2gmas','')\"");
                                        else:
                                            Functions::invisibleInput("text", "gmas", "", "$a", "onblur=\"sendValue('gmas',this.value,$id,'base2gmas','')\"");
                                        endif;
                                    else:
                                        Functions::invisibleInput("text", "gmas", "", "$a", "", "disabled");
                                    endif;
                                    ?>
                                </div><!-- pe-3 -->
                            </div><!-- col-6 -->
                            <div class="col-6">
                                <div class="ps-3">
                                    <small class="text-muted italic"><?= $_SESSION['text']['h_nael'] ?></small><br>
                                    <?php
                                    $a = '';
                                    if (count($nael) > 0): foreach ($nael as $val):
                                        $a .= $val->nael . ", ";
                                    endforeach;
                                    else:
                                        $a = '-  ';
                                    endif;
                                    $a = substr($a, 0, -2);
                                    if ($edit === 1):
                                        if ($a == ''):
                                            Functions::invisibleInput("text", "nael", "", "$a", "onblur=\"setValue('nael',this.value,$id,'base2nael','')\"");
                                        else:
                                            Functions::invisibleInput("text", "nael", "", "$a", "onblur=\"sendValue('nael',this.value,$id,'base2nael','')\"");
                                        endif;
                                    else:
                                        Functions::invisibleInput("text", "nael", "", "$a", "", "disabled");
                                    endif;
                                    ?>
                                </div><!-- ps-3 -->
                            </div><!-- col-6 -->
                        </div><!-- row -->
                        <div class="row mb-3 pb-3 border__bottom--dotted-gray">
                            <div class="col-6 border__right--dotted-gray">
                                <small class="text-muted italic"><?= $_SESSION['text']['h_aenderungsquelle'] ?></small><br>
                                <?php
                                if ($edit === 1): ?>
                                    <select name="quelle" class="invisible-formfield"
                                            onchange="sendValue('quelle',this.value,<?= $id ?>,'base2info','text')">
                                        <option value=""><?= $_SESSION['text']['i_selectOption'] ?></option>
                                        <?php foreach ($quelle as $q): ?>
                                            <option value="<?= $q->id ?>"
                                                    <?php if ($q->id == $info->quelle): ?>selected<?php endif; ?>>
                                                <?= $_SESSION['text']['' . $q->quelle . ''] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php
                                else:
                                    if(ChangeManagementDatabase::getFeldValue($info->quelle, "quelle") != ''):
                                    echo $_SESSION['text']['' . ChangeManagementDatabase::getFeldValue($info->quelle, "quelle") . ''];
                                    endif;
                                endif;
                                ?>
                            </div><!-- col-6 -->
                            <div class="col-6">
                                <div class="ps-3">
                                    <small class="text-muted italic"><?= $_SESSION['text']['h_changeType'] ?></small><br>
                                    <?php
                                    if ($edit === 1): ?>
                                        <select name="change_type" class="invisible-formfield"
                                                onchange="sendValue('change_type',this.value,<?= $id ?>,'base2info','text')">
                                            <option value=""><?= $_SESSION['text']['i_selectOption'] ?></option>
                                            <?php foreach ($changetype as $c): ?>
                                                <option value="<?= $c->id ?>"
                                                        <?php if ($c->id == $info->quelle): ?>selected<?php endif; ?>>
                                                    <?= $_SESSION['text']['' . $c->changetype . ''] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php
                                    else:
                                        $valct = ChangeManagementDatabase::getFeldValue($info->change_type, "changetype");
                                        if($valct):
                                            echo $_SESSION['text']['' . ChangeManagementDatabase::getFeldValue($info->change_type, "changetype") . ''];
                                        else:
                                            echo '-';
                                        endif;
                                    endif;
                                    ?>
                                </div><!-- ps-3 -->
                            </div><!-- col-6 -->
                        </div><!-- row -->
                        <div class="row mb-3 pb-3 border__bottom--dotted-gray">
                            <div class="col-12">
                                <div class="pe-3">
                                    <small class="text-muted italic"><?= $_SESSION['text']['s_deviation'] ?></small><br>
                                    <?php
                                    if ($edit === 1):
                                        if (empty($deviation)):
                                            Functions::invisibleInput("text", "deviation", "", "$deviation", "onblur=\"setValue('deviation',this.value,$id,'base2deviation','')\"");
                                        else:
                                            Functions::invisibleInput("text", "deviation", "", "$deviation", "onblur=\"sendValue('deviation',this.value,$id,'base2deviation','')\"");
                                        endif;
                                    else:
                                        if (empty($deviation)) $deviation = '-';
                                        Functions::invisibleInput("text", "deviation", "", "$deviation", "", "disabled");
                                    endif;
                                    ?>
                                </div><!-- pe-3 -->
                            </div><!-- col-6 -->
                        </div><!-- row -->

                        <div class="row pb-3 border__bottom--dotted-gray">
                            <div class="col-6 border__right--dotted-gray">
                                <div class="pe-3">
                                    <small class="text-muted italic"><?= $_SESSION['text']['h_lieferant'] ?></small><br>
                                    <?php
                                    $lieferant = ($lieferant === false) ? '-' : $lieferant->lieferant;
                                    ?>
                                    <?= $lieferant ?>
                                </div><!-- pe-3 -->
                            </div><!-- col-6 -->
                            <div class="col-6">
                                <div class="ps-3">
                                    <small class="text-muted italic"><?= $_SESSION['text']['h_status'] ?></small><br>
                                    <?php ChangeManagementDatabase::dspStatus($row->status); ?>
                                </div><!-- ps-3 -->
                            </div><!-- col-6 -->
                        </div><!-- row -->

                        <?php
                        if(ChangeManagementDatabase::checkPart($id,'partno') == 1):
                            include_once "includes/getDatebox.php";
                            include_once "includes/getOrderbox.php";
                        endif;
                        ?>
                    </div><!-- mx-3 -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-4">
                    <div class="mb-3 ms-3">
                        <?php
                        # Wenn noch keine Teilenummer eingetragen ist, darf hier noch nichts passieren

                        $pn = ChangeManagementDatabase::countPartNo($id);
                        $pne = ChangeManagementDatabase::checkPart($id,'partno');
                        if ($pn == 0 && $pne == 1):
                            Functions::alert($_SESSION['text']['t_keineTeilenummer']);
                        else:
                            $ce = ChangeManagementDatabase::checkPart($id, 'evaluation');
                            if (is_null($ce)):
                                $wrke = 'dspnone'; # Bearbeitungsfenster Evaluation ausblenden
                                $dspe = ''; # Hinweis einblenden
                            else:
                                if (!is_null($ce) && $ce == 1):
                                    $wrke = ''; # Bearbeitungsfenster Evaluation anzeigen
                                    $dspe = 'dspnone'; # Hinweis ausblenden
                                else:
                                    $wrke = 'dspnone';
                                    $dspe = '';
                                endif;
                            endif;
                            ?>
                            <h3 class="border__bottom--dotted-gray pb-3 mb-3">
                                <?= $_SESSION['text']['h_evaluation'] ?>
                                <span class="float-end">
                                    <span class="badge badge-primary font-weight-300 <?= $wrke ?>"><?= $_SESSION['text']['b_erforderlich'] ?></span>
                                </span>
                            </h3>

                            <div id="no_evaluation" class="<?= $dspe ?>">
                                <?php Functions::alert($_SESSION['text']['t_noEvaluation']); ?>
                            </div><!-- no-evaluation -->
                            <div id="wrk_evaluation" class="<?= $wrke ?>">
                                <?php if (changeManagementDatabase::countOpenEvaluation($id, 1) > 0):
                                    Functions::alert($_SESSION['text']['t_bearbeitung_ev']);
                                endif; ?>
                                <div class="row m-0 mb-3">
                                    <?php
                                    # Anzahl Elemente (Evaluation)
                                    $y = ChangeManagementDatabase::countAPQPElements($id, 'pla2evaluation');
                                    $i = 0;
                                    foreach (ChangeManagementDatabase::pla2evaluation($id) as $pla):
                                        $sk = ChangeManagementDatabase::summeKosten($pla->bereich, 1, $id);
                                        $an = ChangeManagementDatabase::summeAnmerkungenAPQP($pla->bereich, 1, $id);
                                        $punkte = ChangeManagementDatabase::sefAPQP(1, $pla->bereich, $id, '');
                                        $erledigt = ChangeManagementDatabase::sefAPQP(1, $pla->bereich, $id, 0);
                                        $border = ($i == 1) ? 'border__left--dotted-gray' : '';
                                        $m = ($i == 1) ? 'ms-3' : 'me-3';
                                        ?>
                                        <div class="col-6 <?= $border ?>">
                                            <div class="border__bottom--dotted-gray py-3 pointer row divhover <?= $m ?>"
                                                 onclick="top.location.href='/changeManagement/evaluation?id=<?= $cid ?>&amp;evaluation=<?= $pla->bereich ?>'">
                                                <div class="col-10">
                                                    <div class="oswald font-weight-300 font-size-18"><?php ChangeManagementDatabase::dspBereich($pla->bereich); ?></div>
                                                    <div class="font-size-11">
                                                        <?= $_SESSION['text']['h_apqp'] ?>
                                                        : <?php ChangeManagementDatabase::dspAnzAPQP(1, $pla->bereich, $id); ?>
                                                        <span class="px-2"><?php ChangeManagementDatabase::dspStatusEvaluation(1, $id, $pla->bereich); ?></span>
                                                        <?= $sk ?> <?= $loc->cur ?>
                                                        <span class="px-2 text-muted"><?php if ($an > 0) echo '<i class="fa fa-comment"></i>'; ?></span>
                                                    </div>
                                                </div><!-- col-10 -->
                                                <div class="col-2 text-end">
                                                    <?php
                                                    if ($erledigt > 0 && $erledigt < $punkte) echo '<i class="fa fa-cog fa-spin text-muted"></i>';
                                                    if ($erledigt > 0 && $erledigt == $punkte) echo '<i class="fa fa-lock text-muted"></i>';
                                                    ?>
                                                </div><!-- col-2 -->
                                            </div><!-- row -->
                                        </div><!-- col-6 -->
                                        <?php
                                        $i++;
                                        if ($i == 2) $i = 0;
                                    endforeach;
                                    ?>
                                </div><!-- row -->
                            </div><!-- wrk-evaluation -->

                            <?php
                            $endeEvaluation = ChangeManagementDatabase::checkAuftragBereich($id,'evaluation');
                            // Tracking ausblenden, bis alle Evaluationpunkte abgeschlossen sind
                            $showTracking = ChangeManagementDatabase::countOpenEvaluation($id, 1);
                            if ($evaluation === 1):
                                $str = ($showTracking == 0 && $row->status >= 5) ? '' : 'dspnone';
                            else:
                                $str = ($showTracking == 0) ? '' : 'dspnone';
                            endif;
                            $stri = ($showTracking == 0) ? 'dspnone' : '';
                            $stre = ($showTracking < $y) ? 'dspnone' : '';
                            if ($row->status == 3):
                                Functions::alertVar($_SESSION['text']['t_endeHerstellbarkeit'], $cid);
                            endif;
                            ?>
                            <form method="post" id="vd"
                                  class="<?= $stre ?> <?= $wrke ?> border__top--dotted-gray-50 my-3 pt-3">
                                <input type="hidden" name="bid" value="<?= $id ?>">
                                <h3><?= $_SESSION['text']['h_durchlauf'] ?></h3>
                                <?php Functions::alert($_SESSION['text']['t_durchlauf']); ?>
                                <div class="row py-3 border__top--dotted-gray border__bottom--dotted-gray font-size-12">
                                    <div class="col border__right--dotted-gray pe-3 text-center">
                                        <input type="radio" name="antwort" value="nio"
                                               required><br><?= $_SESSION['text']['nio'] ?>
                                    </div>
                                    <div class="col border__right--dotted-gray px-3 text-center">
                                        <input type="radio" name="antwort"
                                               value="no-impact"><br><?= $_SESSION['text']['t_noImpact'] ?>
                                    </div>
                                    <div class="col border__right--dotted-gray px-3 text-center">
                                        <input type="radio" name="antwort" value="io"><br><?= $_SESSION['text']['io'] ?>
                                    </div>
                                    <div class="col ps-3">
                                        <input type="password" id="password" name="password" class="invisible-formfield"
                                               placeholder="<?= $_SESSION['text']['h_passwort'] ?>?" required>
                                    </div>
                                </div>
                                <div class="text-end mt-2">
                                    <input type="submit" class="btn btn-primary"
                                           value="<?= $_SESSION['text']['b_durchlauf'] ?>">
                                </div>
                            </form>

                            <?php
                            if ($tracking === 1):
                                if($row->status < 3 && $dspe != ''):
                                Functions::alertIcon('info-circle', $_SESSION['text']['i_tracking']);
                                endif;
                                ?>
                                <div id="tracking" class="<?= $str ?> mt-5">
                                    <?php
                                    $ct = ChangeManagementDatabase::checkPart($row->id, 'tracking');
                                    if (is_null($ct)) :
                                        $wrkt = 'dspnone';
                                        $dspt = '';
                                    else:
                                        if (!is_null($ct) && $ct == 1) :
                                            $wrkt = '';
                                            $dspt = 'dspnone';
                                        else:
                                            $wrkt = 'dspnone';
                                            $dspt = '';
                                        endif;
                                    endif;
                                    ?>
                                    <h3 class="border__bottom--dotted-gray pb-3 mb-3">
                                        <?= $_SESSION['text']['h_tracking'] ?>
                                        <span class="float-end">
                                        <span class="badge badge-primary font-weight-300 <?= $wrkt ?>"><?= $_SESSION['text']['b_erforderlich'] ?></span>
                                    </span><!-- float-end -->
                                    </h3>
                                    <div id="no_tracking" class="<?= $dspt ?>">
                                        <?php Functions::alert($_SESSION['text']['t_no_tracking']); ?>
                                    </div>
                                    <div id="wrk_tracking" class="<?= $wrkt ?>">
                                        <?php if (changeManagementDatabase::countOpenEvaluation($id, 2) != 0):
                                            Functions::alert($_SESSION['text']['t_bearbeitung_ev']);
                                        endif; ?>
                                        <div class="row m-0">
                                            <?php
                                            $i = 0;
                                            # Anzahl Elemente (Evaluation)
                                            $y = ChangeManagementDatabase::countAPQPElements($id, 'imp2tracking');
                                            foreach (ChangeManagementDatabase::imp2tracking($id) as $pla):
                                                $sk = ChangeManagementDatabase::summeKosten($pla->bereich, 2, $id);
                                                $an = ChangeManagementDatabase::summeAnmerkungenAPQP($pla->bereich, 2, $id);
                                                $punkte = ChangeManagementDatabase::sefAPQP(2, $pla->bereich, $id, '');
                                                $erledigt = ChangeManagementDatabase::sefAPQP(2, $pla->bereich, $id, 0);
                                                $border = ($i == 1) ? 'border__left--dotted-gray' : '';
                                                $m = ($i == 1) ? 'ms-3' : 'me-3';
                                                ?>
                                                <div class="col-6 <?= $border ?>">
                                                    <div class="border__bottom--dotted-gray py-3 pointer row divhover <?= $m ?>"
                                                         onclick="top.location.href='/changeManagement/tracking?id=<?= $cid ?>&amp;tracking=<?= $pla->bereich ?>'">
                                                        <div class="col-10">
                                                            <div class="oswald font-weight-300 font-size-18"><?php ChangeManagementDatabase::dspBereich($pla->bereich); ?></div>
                                                            <div class="font-size-11">
                                                                <?= $_SESSION['text']['h_apqp'] ?>
                                                                : <?php ChangeManagementDatabase::dspAnzAPQP(2, $pla->bereich, $id); ?>
                                                                <span class="px-2"><?php ChangeManagementDatabase::dspStatusEvaluation(2, $id, $pla->bereich); ?></span>
                                                                <?= $sk ?> <?= $loc->cur ?>
                                                                <span class="px-2 text-muted"><?php if ($an > 0) echo '<i class="fa fa-comment"></i>'; ?></span>
                                                            </div>
                                                        </div><!-- col-10 -->
                                                        <div class="col-2 text-end">
                                                            <?php
                                                            if ($erledigt > 0 && $erledigt < $punkte) echo '<i class="fa fa-cog fa-spin text-muted"></i>';
                                                            if ($erledigt > 0 && $erledigt == $punkte) echo '<i class="fa fa-lock text-muted"></i>';
                                                            ?>
                                                        </div><!-- col-2 -->
                                                    </div><!-- row -->
                                                </div><!-- col-6 -->
                                                <?php
                                                $i++;
                                                if ($i == 2) $i = 0;
                                            endforeach;
                                            ?>
                                        </div><!-- row -->
                                    </div><!-- #wrk_tracking -->
                                </div><!-- #tracking -->
                            <?php
                            endif; # Tracking === 1
                        endif; # Keine Teilenummern
                        if ($row->status === 6):
                            Functions::alertIcon("exclamation-circle",$_SESSION['text']['t_infoCMEnde']);
                        endif; ?>
                    </div><!-- ms-3 -->
                </div><!-- col-12 -->
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
<?php if ($edit === 1): ?>
    <script type="text/javascript"
            src="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/summernote.min.js"></script>
    <script type="text/javascript"
            src="<?= Functions::getBaseUrl() ?>skin/plugins/node_modules/summernote/dist/lang/summernote-de-DE.min.js"></script>
<?php endif; ?>
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
            },
            callbacks: {
                onBlur: function () {
                    console.log('RAUS AUS DEM HAUS');
                    sendValue('change_description', '', <?= $id ?>, 'base2info', 'editor');
                }
            }
        });


        <?php endif; ?>
    });
</script>
</body>
</html>